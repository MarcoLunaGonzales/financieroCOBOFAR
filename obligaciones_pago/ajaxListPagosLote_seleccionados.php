<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$contador_check=$_GET['contador_check'];
$codigos_aux=$_GET['codigos_aux'];
$codigos_string = str_replace("PPPPP", ",", $codigos_aux);
$codigos_string=trim($codigos_string,",");

$proveedoresString=$_GET['prov'];
$proveedoresString=implode(",", $proveedoresString);

$cuentas=$_GET['cuentas'];
$cuentas=implode(",", $cuentas);
// $codigo_formateado = str_replace("PPPPP", ",", $codigos_aux);
// $codigo_formateado=trim($codigo_formateado,",");
// $contador_items=0;
$contador_items=$_GET['cantidad_proveedores'];
  
$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;
$codPlanCuentaAuxiliarPivotX=-10000;
$ver_saldo=1;
$sql="SELECT e.*,d.glosa,d.haber,d.debe,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos 
FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentas) and e.cod_comprobantedetalleorigen=0  and e.codigo in ($codigos_string) order by e.fecha,d.glosa";
$stmt = $dbh->prepare($sql);
//echo $sql;
$stmt->execute();

$total_monto=0;
$total_montoEstado=0;
$total_saldo=0;
while ($row = $stmt->fetch()) {
  $codigoX=$row['codigo'];
  //echo $sql;   
  $existeCuentas=0;
  $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad
    from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
  $stmtCantidad->execute();
  while ($rowCantidad = $stmtCantidad->fetch()) {
    $existeCuentas=$rowCantidad['cantidad'];
  }
  $existeCuentas2=0;
  $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentas) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux in ($proveedoresString) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
  $stmtCantidad->execute();
  while ($rowCantidad = $stmtCantidad->fetch()) {
    $existeCuentas2=$rowCantidad['cantidad'];
  }
  $mostrarFilasEstado="";
  $estiloFilasEstado="";
  $estiloFilasEstadoSaldo="";
  $sqlFechaEstadoCuenta="";
  if($sqlFechaEstadoCuenta==""){
    if($existeCuentas==0){
      if($existeCuentas2==0){
         $mostrarFilasEstado="d-none";
      }
    }else{
      if($existeCuentas2==0){
       $estiloFilasEstado="style='background:#F9F9FC !important;color:#D6D6DA  !important;'";
       $estiloFilasEstadoSaldo="style='color:red !important;'";
      }
    }
  }
  $codCompDetX=$row['cod_comprobantedetalle'];
  $codPlanCuentaX=$row['cod_cuenta'];
  $codProveedor=$row['cod_proveedor'];
  $montoX=$row['monto'];
  $fechaX=$row['fecha'];
  $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
  $glosaAuxiliar=$row['glosa_auxiliar'];
  $glosaX=$row['glosa'];
  $debeX=$row['debe'];
  $haberX=$row['haber'];
  $codigoExtra=$row['extra'];
  $codPlanCuentaAuxiliarX=$row['cod_cuentaaux'];
  $codigoComprobanteX=$row['codigocomprobante'];
  $codUnidadCabecera=$row['cod_unidad_cab'];
  $codAreaCentroCosto=$row['area_centro_costos'];

  $nombreComprobanteX=nombreComprobante($codigoComprobanteX);
  $nombreCuentaAuxiliarX=nameCuentaAuxiliar($codPlanCuentaAuxiliarX);
  $tipoDebeHaber=verificarTipoEstadoCuenta($codPlanCuentaX);


  if($codPlanCuentaAuxiliarX!=$codPlanCuentaAuxiliarPivotX){
    $saldo=0;
    $codPlanCuentaAuxiliarPivotX=$codPlanCuentaAuxiliarX;
  }
  $glosaMostrar="";
  if($glosaAuxiliar!=""){
      $glosaMostrar=$glosaAuxiliar;
  }else{
      $glosaMostrar=$glosaX;
  }
  list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
  // $nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;

  $nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);
  $nombreUnidadCabecera=abrevUnidad_solo($codUnidadCabecera);
  $nombreAreaCentroCosto=abrevArea_solo($codAreaCentroCosto);

  $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
  //SACAMOS CUANdO SE PAGO DEL ESTADO DE CUENTA.
  $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";
  //echo $sqlContra;
  $stmtContra = $dbh->prepare($sqlContra);
  $stmtContra->execute();                                    
  $saldo+=$montoX;                                            
  $montoEstado=0;$estiloEstados="";
  $stmtSaldo = $dbh->prepare("SELECT sum(e.monto) as monto
          from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
  $stmtSaldo->execute();
  while ($rowSaldo = $stmtSaldo->fetch()) {
      $montoEstado=$rowSaldo['monto'];
  }
  if(formatNumberDec($montoX)==formatNumberDec($montoEstado)&&$ver_saldo==1){
       //validacion para saldos 0 si esta filtrado
      $estiloEstados="d-none";
  }   
  if($tipoDebeHaber==2){//haber
    if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
       $totalCredito=$totalCredito+$montoX;
       $contador_items++;
    }
    $montodebe_x=$montoEstado;
    $montohaber_x=$montoX;
    $saldo_x=$montohaber_x-$montodebe_x;
    $nombreProveedorX=nameProveedor($codProveedor);?>  
    <tr class="bg-white det-estados <?=$estiloEstados?> <?=$mostrarFilasEstado?>" <?=$estiloFilasEstado?> >
        <!-- <td class="text-left small"><?=$nombreUnidadCabecera?></td> -->
        <td class="text-left small"><input type="hidden" id="codigo_auxiliar_s<?=$contador_items?>" name="codigo_auxiliar_s<?=$contador_items?>"  value="<?=$codigoX?>"><?=$nombreUnidadO?>-<?=$nombreAreaCentroCosto?></td>
        <td class="text-center small"><?=$nombreComprobanteX?></td>
        <td class="text-left small"><?=$fechaComprobante?></td>
        <td class="text-left small"><?=$fechaX?></td>          
        <td class="text-left small"><?=$nombreCuentaAuxiliarX?></td>
        <td class="text-left small"><input type="hidden" id="glosa_detalle_s<?=$contador_items?>" name="glosa_detalle_s<?=$contador_items?>"  value="<?=$glosaMostrar?>"><?=$glosaMostrar?></td>
        <td class="text-right text-muted font-weight-bold small"><?=formatNumberDec($montodebe_x)?></td>
        <td class="text-right small"><?=formatNumberDec($montohaber_x)?></td>
        <td class="text-right small font-weight-bold" <?=$estiloFilasEstadoSaldo?>><?=formatNumberDec($saldo_x)?></td>
        <td class="text-right">
          <?php 
          if(($montoX-$montoEstado)>0){
            ?>
            <input type="number" step="any" required class="form-control text-right text-success" value="<?=$saldo_x?>" id="monto_pago_s<?=$contador_items?>" name="monto_pago_s<?=$contador_items?>"><?php
          }else{ ?>
            <input type="number" step="any" required class="form-control text-right text-success" readonly value="<?=$saldo_x?>" id="monto_pago_s<?=$contador_items?>" name="monto_pago_s<?=$contador_items?>"> <?php
          } ?>
        </td>
        <td class="text-right">
          <input type="hidden" id="pronto_pago_s<?=$contador_items?>" name="pronto_pago_s<?=$contador_items?>"  value="0">
          <div class="togglebutton">
             <label>
               <input type="checkbox" title="Tipo Pronto Pago" id="pagos_seleccionados_2<?=$contador_items?>" name="pagos_seleccionados_2<?=$contador_items?>" onchange="activar_input_prontopago(<?=$contador_items?>)">
               <span class="toggle"></span>
             </label>
         </div>          
        </td>
    </tr>
    <?php 
  }else{
    if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
      $totalCredito=$totalCredito+$montoX;
      $contador_items++;
    }
    $montodebe_x=$montoX;
    $montohaber_x=$montoEstado;
    $saldo_x=$montodebe_x-$montohaber_x;
    $nombreProveedorX=nameProveedor($codProveedor);?>  
    <tr class="bg-white det-estados <?=$estiloEstados?> <?=$mostrarFilasEstado?>" <?=$estiloFilasEstado?> >
      <!-- <td class="text-left small"><?=$nombreUnidadCabecera?></td> -->
      <td class="text-left small"><input type="hidden" id="codigo_auxiliar_s<?=$contador_items?>" name="codigo_auxiliar_s<?=$contador_items?>"  value="<?=$codigoX?>"><?=$nombreUnidadO?>-<?=$nombreAreaCentroCosto?></td>
      <td class="text-center small"><?=$nombreComprobanteX?></td>
      <td class="text-left small"><?=$fechaComprobante?></td>
      <td class="text-left small"><?=$fechaX?></td>          
      <td class="text-left small"><?=$nombreCuentaAuxiliarX?></td>
      <td class="text-left small"><input type="hidden" id="glosa_detalle_s<?=$contador_items?>" name="glosa_detalle_s<?=$contador_items?>"  value="<?=$glosaMostrar?>"><?=$glosaMostrar?></td>
      <td class="text-right text-muted font-weight-bold small"><?=formatNumberDec($montodebe_x)?></td>
      <td class="text-right small"><?=formatNumberDec($montohaber_x)?></td>
      <td class="text-right small font-weight-bold" <?=$estiloFilasEstadoSaldo?>><?=formatNumberDec($saldo_x)?></td>
      <td class="text-right">
        <?php 
        if(($montoX-$montoEstado)>0){
          ?>
          <input type="number" step="any" required class="form-control text-right text-success" value="<?=$saldo_x?>" id="monto_pago_s<?=$contador_items?>" name="monto_pago_s<?=$contador_items?>"><?php
        }else{ ?>
          <input type="number" step="any" required class="form-control text-right text-success" readonly value="<?=$saldo_x?>" id="monto_pago_s<?=$contador_items?>" name="monto_pago_s<?=$contador_items?>"> <?php
        } ?>
      </td>
      <td class="text-right"> 
        <input type="hidden" id="pronto_pago_s<?=$contador_items?>" name="pronto_pago_s<?=$contador_items?>"  value="0">
        <div class="togglebutton" >
           <label>
             <input type="checkbox" id="pagos_seleccionados_2<?=$contador_items?>" name="pagos_seleccionados_2<?=$contador_items?>" onchange="activar_input_prontopago(<?=$contador_items?>)">
             <span class="toggle"></span>
           </label>
        </div>         
      </td>
    </tr>
    <?php
  }
  $total_monto+=$montodebe_x;
  $total_montoEstado+=$montohaber_x;
  $total_saldo+=$saldo_x;
  $i++;
  $indice++;
} ?>

<!-- <script>$("#cantidad_proveedores").val(<?=$contador_items?>);</script> -->
<script>$("#cantidad_proveedores").val(parseInt($("#cantidad_proveedores").val())+<?=$contador_items?>);</script>

<script type="text/javascript">
  $(document).ready(function(e) {
    if(!($("body").hasClass("sidebar-mini"))){
      $("#minimizeSidebar").click()
    } 
  });
</script>

