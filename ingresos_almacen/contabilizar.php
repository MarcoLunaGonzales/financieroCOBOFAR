<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fechaHoraActual=date("Y-m-d H:i:s");

$tipoComprobante=3;
//CREAR EL COMPROBANTE DEVENGADO
//INICIO DE VARIABLES
$glosaGeneral="";
$fechaHoraActualSitema=date("Y-m-d H:i:s");
//fecha hora actual para el comprobante (SESIONES)
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");
$codMesActiva=$_SESSION['globalMes']; 
$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));
if((int)$globalNombreGestion<(int)$anioActual){
  $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaHoraActual=date("Y-m-d");
  }else{
    $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
  } 
}
// FIN DE LA FECHA

$cod_unidadX=2;
$cod_areaX=512; 

//FIN DE DATOS
$montoFactura=0;
$lista= obtenerIngresoPendienteDatos($codigo);
foreach ($lista->lista as $listas) {
  $codigoIngreso=$listas->cod_ingreso_almacen;
  $codProveedor=$listas->cod_proveedor;
  $numeroIngTitulo=$listas->correlativo;
  $montoFactura=number_format($listas->monto_factura_proveedor,2,'.','');
  $fechaIngreso=$listas->fecha;
  $tipoIngreso=$listas->tipo_ingreso;
  $provIngreso=$listas->proveedor;
  $obsIngreso=$listas->observaciones;
  $nroFactura=$listas->nro_factura_proveedor;
}

$glosaGeneral.=$provIngreso." FACT.".$nroFactura."/G NOTA INGRESO A-".$numeroIngTitulo;

//FIN DE LA SOLICITUD CABECERA


//CREAR COMPROBANTE EN LA OFICINA CONFIGURADA DEFECTO  LA PAZ
$nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$cod_unidadX,$tipoComprobante,$globalMes);    

//CREACION DEL COMPROBANTE
    if(isset($_GET['existe'])&&verificarEdicionComprobanteUsuario($globalUser)!=0){
      $codComprobante=$_GET['existe'];   
       $sqlUpdateComprobantes="UPDATE comprobantes SET modified_at='$fechaHoraActualSitema',modified_by=$globalUser where codigo=$codComprobante";
       $stmtUpdateComprobante = $dbh->prepare($sqlUpdateComprobantes);
       $flagSuccessComprobante=$stmtUpdateComprobante->execute();

       $sqlEstadosCuenta="SELECT codigo from comprobantes_detalle where cod_comprobante=$codComprobante";
       $stmtEstadosCuenta = $dbh->prepare($sqlEstadosCuenta);
       $stmtEstadosCuenta->execute();
       while ($rowEsta = $stmtEstadosCuenta->fetch(PDO::FETCH_ASSOC)) {
        $codigoDetalle=$rowEsta['codigo'];
        $sqlDelete="DELETE from estados_cuenta where cod_comprobantedetalle='$codigoDetalle'";
        $stmtDel = $dbh->prepare($sqlDelete);
        $stmtDel->execute();
       }      
    }else{
      $codComprobante=obtenerCodigoComprobante();
      $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
      VALUES ('$codComprobante', '1', '$cod_unidadX', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosaGeneral', '$fechaHoraActualSitema', '$globalUser', '$fechaHoraActualSitema', '$globalUser')";
      //echo $sqlInsert;
      $stmtInsert = $dbh->prepare($sqlInsert);
      $flagSuccessComprobante=$stmtInsert->execute();
    }

    if($flagSuccessComprobante==true){

    //actualizar comprobante en ingreso    
    actualizarIngresoComprobante($codigo,$codComprobante);


    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();

//FIN CREACION CABECERA COMPROBANTE
    $iva=13;
    $descuentoIva=($iva*$montoFactura)/100;
    $debe=$montoFactura-$descuentoIva;
    $haber=0;
    $cuentaDetalle=457; 
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaDetalle', '0', '$cod_unidadX', '$cod_areaX', '$debe', '$haber', '$glosaGeneral', 1)";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();  

    $debe=$descuentoIva;
    $haber=0;
    $cuentaDetalle=63; 
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaDetalle', '0', '$cod_unidadX', '$cod_areaX', '$debe', '$haber', '$glosaGeneral', 1)";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();  


    $debe=0;
    $haber=$montoFactura;
    $cuentaDetalle=153; 
    $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$codProveedor,$cuentaDetalle);
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle(); 
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cuentaDetalle', '0', '$cod_unidadX', '$cod_areaX', '$debe', '$haber', '$glosaGeneral', 1)";
    $stmtDetalle = $dbh->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();   
  }
   if($flagSuccess==true){
      showAlertSuccessError(true,"../".$urlList4);  
   }else{
      showAlertSuccessError(false,"../".$urlList4);
   }
?>