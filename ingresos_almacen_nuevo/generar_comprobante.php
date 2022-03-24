<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
require_once '../conexion2.php';
$dbh_detalle = new Conexion2();
$dbh = new Conexion();
$codigo=$_GET["codigo"];
session_start();
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fecha_pago=date("Y-m-d H:i:s");

$flagSuccess=false;
$sw=verificar_relacion_comprobante_ingresoAlm($codigo);
if($sw==0){
    $anioActual=date("Y");
    $mesActual=date("m");
    $diaActual=date("d");
    $codMesActiva=$_SESSION['globalMes']; 
    $month = $globalNombreGestion."-".$codMesActiva;
    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
    $diaUltimo = date('d', strtotime("{$aux} - 1 day"));
    $horasActual=date("H:i:s");
    if((int)$globalNombreGestion<(int)$anioActual){
      $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
    }else{
      if((int)$mesActual==(int)$codMesActiva){
          $fechaHoraActual=date("Y-m-d H:i:s");
      }else{
        $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
      } 
    }
    $tipoComprobante=3;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,$tipoComprobante,$globalMes);
    $glosa=obtenerGlosaIngresoAlmacen($codigo);
    // $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;

    $sw_comprobante=0;
    while ($sw_comprobante==0) {
        $codComprobante=obtenerCodigoComprobante();    
        if(verificarExistenciaComprobante($codComprobante)==0){
            $sw_comprobante=1;
        }
    }
    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fecha_pago', '$globalUser')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();

    $sqlInsert4="UPDATE ingresos_almacen set cod_comprobante=$codComprobante where codigo=$codigo;";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();
    $indexCompro=1;
    
    $total_monto_ingreso=obtenerTotalIngresosAlmacen($codigo);
    //contra cuenta Inventarios Almacen Central La Paz
    $cuenta=obtenerValorConfiguracion(30);
    $cuentaAuxiliar=0;
    $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
    $inicioNumero=$numeroCuenta[0];
    $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
    if($unidadarea[0]==0){
        $unidadDetalle=$unidadSol;
        $area=$areaSol;
    }else{
        $unidadDetalle=$unidadarea[0];
        $area=$unidadarea[1];
    }
    $haber=0;
    $debe=$total_monto_ingreso*0.87;
    $glosaDetalle=nameCuenta($cuenta);
    
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
    $glosaDetalle=$glosa." - 87 %";;
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
    VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
    //echo $sqlDetalle."RRRR"; 
    $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();
    $indexCompro++;

    //credito fiscal iva 
    $cuenta=obtenerValorConfiguracion(3);
    $cuentaAuxiliar=0;
    $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
    $inicioNumero=$numeroCuenta[0];
    $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
    if($unidadarea[0]==0){
        $unidadDetalle=$unidadSol;
        $area=$areaSol;
    }else{
        $unidadDetalle=$unidadarea[0];
        $area=$unidadarea[1];
    }
    $haber=0;
    $debe=$total_monto_ingreso*0.13;
    $glosaDetalle=nameCuenta($cuenta);    
    $codComprobanteDetalle_iva=obtenerCodigoComprobanteDetalle();
    $glosaDetalle=$glosa." - 13 %";
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
    VALUES ('$codComprobanteDetalle_iva','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
    //echo $sqlDetalle."RRRR"; 
    $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();
    $indexCompro++;

    $datosIngresoDetalle = listaDetalleIngresosAlmacen($codigo);
    $monto_total_prontopago=0;
    while ($row = $datosIngresoDetalle->fetch(PDO::FETCH_ASSOC)) {
        $cod_proveedor=$row['cod_proveedor'];
        $factura=$row['factura'];  
        $fecha_factura=$row['fecha_factura'];  
        $nit=$row['nit'];  
        $autorizacion=$row['autorizacion'];  
        $codigo_control=$row['codigo_control'];  
        $monto_factura=$row['monto_factura'];  
        $monto_descuento=$row['desc_total'];  
        $razon_social=nameProveedor($cod_proveedor);
        $cod_plancuenta_proveedores=obtenerValorConfiguracion(36);//proveedores
        $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$cod_proveedor,$cod_plancuenta_proveedores);
        $numeroCuenta=trim(obtieneNumeroCuenta($cod_plancuenta_proveedores));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        $glosaDetalle=$glosa." - F:".$factura;
        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $haber=$monto_factura;
        $debe=0;
        // $total_monto_debe+=$debe;
        // $total_monto_haber+=$haber;
        $sqlDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_plancuenta_proveedores', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
        //echo $sqlDet."DDDDD";
        $stmtDet = $dbh_detalle->prepare($sqlDet);
        $stmtDet->execute();
        //INGRESAMOS FACTURAS RELACIONADAS
        $sqlDetalleCompra="INSERT INTO facturas_compra(cod_comprobantedetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero,tipo_compra,desc_total) VALUES ($codComprobanteDetalle_iva,$nit,$factura,'$fecha_factura','$razon_social',$monto_factura,0,'$autorizacion','$codigo_control',0,0,1,$monto_descuento)";
        //echo "<br>".$sqlDetalleCompra."ECECEC";
        $stmtDetalleFacturas = $dbh_detalle->prepare($sqlDetalleCompra);
        $stmtDetalleFacturas->execute();
        //INGRESAMOS ESTADOS DE CUENTA
        $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
          VALUES ('$codComprobanteDetalle', '$cod_plancuenta_proveedores', '$haber', '$cod_proveedor', '$fechaHoraActual','0','$cuentaAuxiliar','$glosaDetalle')";
        $stmtDetalleEstadoCuenta = $dbh_detalle->prepare($sqlDetalleEstadoCuenta);
        $stmtDetalleEstadoCuenta->execute();
        
        $indexCompro++;
    }
    $dbh="";
    $dbh_detalle="";
}

showAlertSuccessErrorComprobanteIngresosAlm($flagSuccess,"../".$urlList);
   
?>