<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestion"];
$cuenta = $_POST["cuenta"];
$unidad = $_POST["unidad"];
$proveedores=$_POST["proveedores"];
$fecha_desde=$_POST["fecha_desde"];
$fecha=$_POST["fecha_hasta"];
$desde=$fecha_desde;
$hasta=$fecha;
$tipo_cp=$_POST["tipo_cp"];
$ver_saldo=$_POST["ver_saldo"];

if($ver_saldo==3){//saldos Generales
   
   include "reportesEstadoCuentasPrint_saldos.php";
}else{
$proveedoresString=implode(",", $proveedores);
$proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";
if(count($proveedores)==(int)$_POST["numero_proveedores"]){
  $proveedoresStringAux="";
}

$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);

$stringGeneraCuentas="";

foreach ($cuenta as $cuentai ) {    
    $stringGeneraCuentas.=nameCuenta($cuentai).",";
    # code...
}
$stringGeneraUnidades="";
foreach ($unidad as $unidadi ) {    
    $stringGeneraUnidades.=" ".abrevUnidad($unidadi)." ";
    # code...
}

$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];

$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                        <div class="float-right col-sm-2">
                            <!-- <h6 class="card-title">Exportar como:</h6> -->
                        </div>
                        <h4 class="card-title"> 
                            <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                            Estado de Cuentas
                        </h4>
                      <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                      <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                      <h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6>             
                      <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php 
                            $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas100">'.
                                '<thead>'.
                                    '<tr class="">'.
                                        '<th class="text-left">Of.</th>'.
                                        '<th class="text-left">CC</th>'.
                                        '<th class="text-left">Tipo/#</th>'.
                                        '<th class="text-left">FechaComp</th>'.
                                        '<th class="text-left">FechaEC</th>'.
                                        '<th class="text-left">Proveedor/Cliente</th>'.
                                        '<th class="text-left">Glosa</th>'.
                                        '<th class="text-right">Debe</th>'.
                                        '<th class="text-right">Haber</th>'.
                                        '<th class="text-right">Saldo</th>'.
                                    '</tr>'.
                                '</thead>'.
                                '<tbody>';
                                    
                                    foreach ($cuenta as $cuentai ) {
                                        $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta
                                        $html.='<tr style="background-color:#9F81F7;">                                    
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td class="text-left small" colspan="5">CUENTA</td>
                                            <td class="text-left small" colspan="5">'.$nombreCuenta.'</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                        </tr>'; 
                                        
                                        $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                            
                                        if(isset($_POST['cierre_anterior'])){
                                          $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
                                        }

                                        $sql="SELECT e.codigo,e.fecha,e.monto,d.glosa,e.glosa_auxiliar,e.cod_cuentaaux,d.haber,d.debe,cc.fecha as fecha_com, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos,(select ca.nombre from cuentas_auxiliares ca where ca.codigo=e.cod_cuentaaux) as nombreCuentaAuxiliarX,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber,(SELECT uo.abreviatura FROM unidades_organizacionales uo where uo.codigo = d.cod_unidadorganizacional) as nombreUnidadCosto,(SELECT a.abreviatura FROM areas a where a.codigo = d.cod_area) as nombreAreaCentroCosto
                                            FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) order by e.fecha"; //ca.nombre, 
                                        //echo $sql;
                                        $stmtUO = $dbh->prepare($sql);
                                        $stmtUO->execute();
                                        $codPlanCuentaAuxiliarPivotX=-10000;
                                        while ($row = $stmtUO->fetch()) {
                                            $codigoX=$row['codigo'];
                                            $existeCuentas=0;
                                            $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad
                                                from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
                                            $stmtCantidad->execute();
                                            while ($rowCantidad = $stmtCantidad->fetch()) {
                                                $existeCuentas=$rowCantidad['cantidad'];
                                            }
                                            $existeCuentas2=0;
                                            $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($proveedoresString) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
                                            $stmtCantidad->execute();
                                            while ($rowCantidad = $stmtCantidad->fetch()) {
                                                $existeCuentas2=$rowCantidad['cantidad'];
                                            }
                                            $mostrarFilasEstado="";
                                            $estiloFilasEstado="";
                                            $estiloFilasEstadoSaldo="";
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

                                            $montoX=$row['monto'];
                                            $fechaX=$row['fecha'];
                                            $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
                                            $glosaAuxiliar=$row['glosa_auxiliar'];
                                            $glosaX=$row['glosa'];
                                            $debeX=$row['debe'];
                                            $haberX=$row['haber'];
                                            // $codigoExtra=$row['extra'];
                                            $codPlanCuentaAuxiliarX=$row['cod_cuentaaux'];
                                            $codigoComprobanteX=$row['codigocomprobante'];
                                            $nombreUnidadCosto=$row['nombreUnidadCosto'];
                                            $nombreAreaCentroCosto=$row['nombreAreaCentroCosto'];
                                            $nombreComprobanteX=nombreComprobante($codigoComprobanteX);
                                            $fechaComprobante=$row['fecha_com'];
                                            $nombreCuentaAuxiliarX=$row['nombreCuentaAuxiliarX'];
                                            $tipoDebeHaber=$row['tipoDebeHaber'];
                                            //$nombreCuentaAuxiliarX=nameCuentaAuxiliar($codPlanCuentaAuxiliarX);
                                            //$tipoDebeHaber=verificarTipoEstadoCuenta($codPlanCuentaX);
                                            if($codPlanCuentaAuxiliarX!=$codPlanCuentaAuxiliarPivotX){
                                                $saldo=0;
                                                $codPlanCuentaAuxiliarPivotX=$codPlanCuentaAuxiliarX;
                                            }
                                            $glosaMostrar="";
                                            if($glosaAuxiliar!="" && $glosaAuxiliar!=" " && $glosaAuxiliar!=null){
                                                $glosaMostrar=$glosaAuxiliar;
                                            }else{
                                                $glosaMostrar=$glosaX;
                                            }
                                            $nombreUnidadCabecera="";
                                            $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
                                            $sqlFechaEstadoCuentaPosterior="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                            if(isset($_POST['cierre_posterior'])){
                                              $sqlFechaEstadoCuentaPosterior="and e.fecha >= '$desde 00:00:00'";  
                                            }
                                            $saldo+=$montoX;//-$montoContra;
                                            $montoEstado=0;$estiloEstados="";
                                            $stmtSaldo = $dbh->prepare("SELECT sum(e.monto) as monto
                                                from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 $sqlFechaEstadoCuentaPosterior and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX");
                                            $stmtSaldo->execute();
                                            while ($rowSaldo = $stmtSaldo->fetch()) {
                                                $montoEstado=$rowSaldo['monto'];
                                            }
                                            if(formatNumberDec($montoX)==formatNumberDec($montoEstado)&&$ver_saldo==1){
                                                 //validacion para saldos 0 si esta filtrado
                                                $estiloEstados="d-none";
                                            }   

                                            if($tipoDebeHaber==2){//proveedor
                                                if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
                                                   $totalCredito=$totalCredito+$montoX;
                                                }
                                                //$nombreProveedorX=nameProveedor($codProveedor);
                                                $html.='<tr class="bg-white det-estados '.$estiloEstados.' '.$mostrarFilasEstado.'" '.$estiloFilasEstado.'>
                                                    <td class="text-left small">'.$nombreUnidadCabecera.'</td>
                                                    <td class="text-left small">'.$nombreUnidadCosto.'-'.$nombreAreaCentroCosto.'</td>
                                                    <td class="text-center small">'.$nombreComprobanteX.'</td>
                                                    <td class="text-left small">'.$fechaComprobante.'</td>
                                                    <td class="text-left small">'.$fechaX.'</td>
                                                    
                                                    <td class="text-left small">'.$nombreCuentaAuxiliarX.'</td>
                                                    <td class="text-left small">'.$glosaMostrar.'</td>
                                                    <td class="text-right text-muted font-weight-bold small">'.formatNumberDec($montoEstado).'</td>
                                                    <td class="text-right small">'.formatNumberDec($montoX).'</td>
                                                    <td class="text-right small font-weight-bold" '.$estiloFilasEstadoSaldo.'>'.formatNumberDec($montoX-$montoEstado).'</td>
                                                </tr>'; 
                                            }else{ //cliente
                                                // $nombreProveedorX=namecliente($codProveedor);
                                                if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
                                                  $totalDebito=$totalDebito+$montoX;
                                                 }
                                                
                                                 $html.='<tr class="bg-white det-estados '.$estiloEstados.' '.$mostrarFilasEstado.'" '.$estiloFilasEstado.'>
                                                    <td class="text-left small">'.$nombreUnidadCabecera.'</td>
                                                    <td class="text-left small">'.$nombreUnidadCosto.'-'.$nombreAreaCentroCosto.'</td>
                                                    <td class="text-center small">'.$nombreComprobanteX.'</td>
                                                    <td class="text-left small">'.$fechaComprobante.'</td>
                                                    <td class="text-left small">'.$fechaX.'</td>
                                                    
                                                    <td class="text-left small">'.$nombreCuentaAuxiliarX.'</td>
                                                    <td class="text-left small">'.$glosaMostrar.'</td>
                                                    <td class="text-right small">'.formatNumberDec($montoX).'</td>
                                                    <td class="text-right text-muted font-weight-bold small">'.formatNumberDec($montoEstado).'</td>
                                                    <td class="text-right small font-weight-bold" '.$estiloFilasEstadoSaldo.'>'.formatNumberDec($montoX-$montoEstado).'</td>
                                                </tr>';

                                            }    
                                            //pagos parciales 
                                            $sql="SELECT e.monto,e.fecha,e.glosa_auxiliar,d.glosa,d.haber,d.debe,c.fecha as fecha_com, c.codigo as codigocomprobante
                                              from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 $sqlFechaEstadoCuentaPosterior and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX";      
                                            $stmt_d = $dbh->prepare($sql);
                                            $stmt_d->execute();
                                            while ($row_d = $stmt_d->fetch()) {
                                                $montoX_d=$row_d['monto'];
                                                $fechaX_d=$row_d['fecha'];
                                                $fechaX_d=strftime('%d/%m/%Y',strtotime($fechaX_d));
                                                $glosaAuxiliar_d=$row_d['glosa_auxiliar'];
                                                $glosaX_d=$row_d['glosa'];
                                                $debeX_d=$row_d['debe'];
                                                $fecha_com=$row_d['fecha_com'];

                                                $codigoComprobanteY=$row_d['codigocomprobante'];
                                                $tituloMontoDebe=formatNumberDec($montoX_d);
                                                if($montoX_d!=$debeX_d){
                                                  $tituloMontoDebe=formatNumberDec($montoX_d).' <b class="text-danger">(*'.formatNumberDec($debeX_d).'*)</b>';
                                                }
                                                $nombreComprobanteY=nombreComprobante($codigoComprobanteY);
                                                $glosaMostrar_d="";
                                                if($glosaAuxiliar_d!=""){
                                                  $glosaMostrar_d=$glosaAuxiliar_d;
                                                }else{
                                                  $glosaMostrar_d=$glosaX_d;
                                                }

                                                $fechaComprobante_d=strftime('%d/%m/%Y',strtotime($fecha_com));
                                                 $saldo=$saldo-$montoX_d;
                                                if($tipoDebeHaber==2){//proveedor
                                                    // $nombreProveedorX_d=nameProveedor($codProveedor_d);
                                                    $nombreProveedorX_d=$nombreCuentaAuxiliarX;
                                                    if($mostrarFilasEstado!="d-none"&&$estiloEstados==""){
                                                      $totalDebito=$totalDebito+$montoX_d;    
                                                    }
                                                    
                                                    $html.='<tr style="background-color:#ECCEF5;" class="'.$estiloEstados.' '.$mostrarFilasEstado.' text-muted">
                                                        <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                        <td class="text-left small"></td>
                                                        <td class="text-center small">'.$nombreComprobanteY.'</td>
                                                        <td class="text-left small">'.$fechaComprobante_d.'</td>
                                                        <td class="text-left small">'.$fechaX_d.'</td>
                                                        <td class="text-left small">'.$nombreProveedorX_d.'</td>  
                                                        <td class="text-left small">'.$glosaMostrar_d.'</td>
                                                        <td class="text-right small">'.$tituloMontoDebe.'</td>
                                                        <td class="text-right small">'.formatNumberDec(0).'</td>
                                                        <td class="text-right small font-weight-bold"></td>
                                                    </tr>';/*formatNumberDec($saldo)*/
                                                }else{ //cliente
                                                    //$nombreProveedorX_d=namecliente($codProveedor_d);
                                                    $nombreProveedorX_d=$nombreCuentaAuxiliarX;
                                                    //if($nombreProveedorX_d=='0')$nombreProveedorX_d=nameProveedor($codProveedor_d);

                                                    if($mostrarFilasEstado!="d-none"&&$estiloEstados==""){
                                                      $totalCredito=$totalCredito+$montoX_d;    
                                                    }
                                                    
                                                    $html.='<tr  style="background-color:#ECCEF5;" class="'.$estiloEstados.' '.$mostrarFilasEstado.' text-muted">
                                                        <td class="text-left small">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                        <td class="text-left small"></td>
                                                        <td class="text-center small">'.$nombreComprobanteY.'</td>
                                                        <td class="text-left small">'.$fechaComprobante_d.'</td>
                                                        <td class="text-left small">'.$fechaX_d.'</td>
                                                        <td class="text-left small">'.$nombreProveedorX_d.'</td>  
                                                        <td class="text-left small">'.$glosaMostrar_d.'</td>
                                                        <td class="text-right small">'.formatNumberDec(0).'</td>
                                                        <td class="text-right small">'.formatNumberDec($montoX_d).'</td>
                                                        <td class="text-right small font-weight-bold"></td>
                                                    </tr>';/*formatNumberDec($saldo)*/

                                                }
                                            }
                                        }    
                                        $i++;
                                        $indice++;
                                    }
                                    $totalSaldo=$totalDebito-$totalCredito;
                                    if($totalSaldo<0){
                                        $totalSaldo=$totalSaldo*(-1);
                                    }                                        
                                    $html.='<tr>                                            
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td class="text-right small" colspan="7">Total:</td>
                                        <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
                                        <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
                                        <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
                                    </tr>   
           

                                </tbody>
                            </table>';
                            echo $html;

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>


<?php

}

?>
