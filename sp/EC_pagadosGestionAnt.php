<?php //ESTADO FINALIZADO
set_time_limit(0);
error_reporting(-1);
require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">Estados cuenta</h4>
          </div>
          <div class="card-body">
<?php

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6>";
echo "CONEXION ESTABLECIDA!!!!<br>";


//RECIBIMOS LAS VARIABLES
$NombreGestion = 2021;//gestion que se cargará
$fecha_desde=$NombreGestion."-01-01";
$fecha=$NombreGestion."-12-31";
$desde=$fecha_desde;
$hasta=$fecha;
$ver_saldo=1;
$StringUnidades="1,2,3";

$cuenta[0] = 2035;//cuentas que se cargarán;
$proveedoresString="";
// $proveedoresStringAux="and e.cod_cuentaaux in ($proveedoresString)";
$proveedoresStringAux="";
$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCostoArray="1,2,3";
$areaCostoArray = "1,2,3,4,5,6,7,8,9,11,12,13,14,19,20,21,22,26,27,29,34,39,41,48,50,52,53,55,56,61,66,68,69,73,74,75,77,79,80,82,83,85,87,88,89,92,94,95,96,100,102,104,106,108,112,113,114,116,119,120,122,123,124,125,126,127,128,129,130,131,132,133,500,501,502,503,504,505,506,507,508,509,510,511,512,518,522,523,524,525,528,529,530,531,532,533,536,537,540,542,543,546,547";


foreach ($cuenta as $cuentai ) {
    //$nombreCuenta=nameCuenta($cuentai);//nombre de cuenta        
    // $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
    // if(isset($_POST['cierre_anterior'])){
      $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
     // }

    $sql="SELECT e.codigo,e.fecha,e.monto,d.glosa,e.glosa_auxiliar,e.cod_cuentaaux,d.haber,d.debe,cc.fecha as fecha_com, d.cod_cuenta, ca.nombre, cc.codigo as codigocomprobante, cc.cod_unidadorganizacional as cod_unidad_cab, d.cod_area as area_centro_costos,(select ca.nombre from cuentas_auxiliares ca where ca.codigo=e.cod_cuentaaux) as nombreCuentaAuxiliarX,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber,(SELECT uo.abreviatura FROM unidades_organizacionales uo where uo.codigo = d.cod_unidadorganizacional) as nombreUnidadCosto,(SELECT a.abreviatura FROM areas a where a.codigo = d.cod_area) as nombreAreaCentroCosto,ca.cod_proveedorcliente
        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray)  order by e.fecha"; //ca.nombre, 
    // echo $sql;
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
          $stmtCantidad = $dbh->prepare("SELECT count(*) as cantidad FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and cc.cod_unidadorganizacional in ($StringUnidades)  and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) and e.codigo=$codigoX order by ca.nombre, cc.fecha");
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
        // $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
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
        $cod_proveedorcliente=$row['cod_proveedorcliente'];
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
        // $sqlFechaEstadoCuentaPosterior="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
        // if(isset($_POST['cierre_posterior'])){
          $sqlFechaEstadoCuentaPosterior="and e.fecha >= '$desde 00:00:00'";  
         // }
        //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
        // $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";
        // //echo $sqlContra;
        // $stmtContra = $dbh->prepare($sqlContra);
        // $stmtContra->execute();                                    
        $saldo+=$montoX;//-$montoContra;                                    
        // echo "tipo:".$cod_tipoCuenta;
        $montoEstado=0;$estiloEstados="";
        $fechaEstadoX="";
        $stmtSaldo = $dbh->prepare("SELECT e.codigo,sum(e.monto) as monto,e.fecha
                from estados_cuenta e, comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2  and e.cod_comprobantedetalle=d.codigo and e.cod_comprobantedetalleorigen=$codigoX and e.fecha>'2022-01-01'");
        $stmtSaldo->execute();
        while ($rowSaldo = $stmtSaldo->fetch()) {
            $codigoEstadoX=$rowSaldo['codigo'];
            $montoEstado=$rowSaldo['monto'];
            $fechaEstadoX=$rowSaldo['fecha'];
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
            if($estiloEstados !="d-none" && $mostrarFilasEstado !="d-none" ){
                $saldo=$montoX-$montoEstado;
                // $codigo_ant=$codigoX;
                // echo $fechaX."***".$saldo."<br>";
                // $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                // $insert_str = "('$codigoDetalleComprobante','$cod_comprobante','$cuentai','$codPlanCuentaAuxiliarX','1','522','0','$saldo','$glosaMostrar')"; 
                // $sqlInsertComp="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa) VALUES ".$insert_str.";";
                //  //echo $sqlInsertComp."<br>";
                // $stmtInsertComp=$dbh->prepare($sqlInsertComp);
                // $flagSuccess2=$stmtInsertComp->execute();
                // //estado cuenta
                // // $proveedor=obtenerCodigoProveedorCuentaAux($codPlanCuentaAuxiliarX);
                // $sqlInsertEC="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar,codigo_ant) values ('$codigoDetalleComprobante','$cuentai','$saldo','$cod_proveedorcliente','$fechaX','0','$codPlanCuentaAuxiliarX','0','1','$glosaMostrar','$codigoX')";
                // $stmtInsertEC = $dbh->prepare($sqlInsertEC);
                // $stmtInsertEC->execute();
            }else{
                echo $codigoEstadoX."<br>";

            }
        }else{ //cliente
            // $nombreProveedorX=namecliente($codProveedor);
            if($mostrarFilasEstado!="d-none"&&$estiloFilasEstado==""&&$estiloEstados==""){
              $totalDebito=$totalDebito+$montoX;
             }
            if($estiloEstados !="d-none" && $mostrarFilasEstado !="d-none" ){
                //  $fechaX//fecha estado cuenta
                // $nombreCuentaAuxiliarX
                // $codPlanCuentaAuxiliarX//codigo auxiliar
                // $glosaMostrar= //glosa
                $saldo=$montoX-$montoEstado;
                // $glosaMostrar=$glosaMostrar; //glosa
                // $codigo_ant=$codigoX;
                // $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
                // $insert_str = "('$codigoDetalleComprobante','$cod_comprobante','$cuentai','$codPlanCuentaAuxiliarX','1','522','$saldo','0','$glosaMostrar')"; 
                // $sqlInsertDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa) VALUES ".$insert_str.";";
                // // echo $sqlInsertDet;
                // $stmtInsertDet=$dbh->prepare($sqlInsertDet);                  
                // $flagSuccess2=$stmtInsertDet->execute();

                //  //estado cuenta
                // // $proveedor=obtenerCodigoProveedorCuentaAux($codPlanCuentaAuxiliarX);
                // $sqlInsertEC="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar,codigo_ant) values ('$codigoDetalleComprobante','$cuentai','$saldo','$cod_proveedorcliente','$fechaX','0','$codPlanCuentaAuxiliarX','0','1','$glosaMostrar','$codigoX')";
                // $stmtInsertEC = $dbh->prepare($sqlInsertEC);
                // $stmtInsertEC->execute();

            }
        }        
    }
    $i++;
    $indice++;
}
                                      

echo "<h6>HORA FIN PROCESO CARGADO INICIAL COMPROBANTES CON EC: " . date("Y-m-d H:i:s")."</h6>";
?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
