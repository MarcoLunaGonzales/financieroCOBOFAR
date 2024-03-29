<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

//datos de cabecera
$cantidadProveedores=$_POST['cantidad_proveedores'];//total de intems

$nombre_lote=$_POST['nombre_lote'];
$porFecha = explode("/", $_POST['fecha_pago']);
$fecha_pago=$porFecha[2]."-".$porFecha[1]."-".$porFecha[0];
$observaciones_pago=$_POST['observaciones_pago'];

$cod_pagolote=$_POST['cod_pagoloteedit'];
$sqlInsert="UPDATE pagos_lotes SET nombre='$nombre_lote', fecha='$fecha_pago' WHERE codigo=$cod_pagolote"; 
$stmtInsert = $dbh->prepare($sqlInsert);
$stmtInsert->execute();
//ya se insertó la cabecera
$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;
$sqlDelete="DELETE FROM pagos_proveedoresdetalle where cod_pagoproveedor in (SELECT codigo from pagos_proveedores where cod_pagolote=$cod_pagolote)";
$stmtInsert = $dbh->prepare($sqlDelete);
$stmtInsert->execute();
$sqlDelete="DELETE FROM pagos_proveedores where cod_pagolote=$cod_pagolote";
$stmtInsert = $dbh->prepare($sqlDelete);
$stmtInsert->execute();

for ($pro=1; $pro <= $cantidadProveedores ; $pro++){
  $codigo_auxiliar_s=$_POST["codigo_auxiliar_s".$pro];//codigo estado de cuenta relacionado
  $monto_pago_s=$_POST["monto_pago_s".$pro];
  $glosa_detalle_s=$_POST["glosa_detalle_s".$pro];
  $cod_tipopagoproveedor=48;

  $sql="SELECT cod_comprobantedetalle,cod_plancuenta,cod_proveedor,cod_cuentaaux from estados_cuenta where codigo='$codigo_auxiliar_s'";
  // echo "<br>..".$sql;
  $stmtEstaCueSele = $dbh->prepare($sql);
  $stmtEstaCueSele->execute();                    
  $stmtEstaCueSele->bindColumn('cod_comprobantedetalle', $cod_comprobantedetalle);
  $stmtEstaCueSele->bindColumn('cod_plancuenta', $cod_plancuenta);
  $stmtEstaCueSele->bindColumn('cod_proveedor', $cod_proveedor);
  $stmtEstaCueSele->bindColumn('cod_cuentaaux', $cod_cuentaaux);
  $cod_comprobantedetalle="";
  $cod_plancuenta="";
  $cod_proveedor="";
  $cod_cuentaaux="";
  while ($rowDetalleX = $stmtEstaCueSele->fetch(PDO::FETCH_BOUND)){ 
    $cod_comprobantedetalle=$cod_comprobantedetalle;
    $cod_plancuenta=$cod_plancuenta;
    $cod_proveedor=$cod_proveedor;
    $cod_cuentaaux=$cod_cuentaaux;
  }
  if($codigo_auxiliar_s>0){
      $cod_pagoproveedor=obtenerCodigoPagoProveedor();
      $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa,cod_cajachicadetalle,cod_pagolote) 
      VALUES ('".$cod_pagoproveedor."','".$fecha_pago."','".$observaciones_pago."','0',3,0,0,".$cod_pagolote.")";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $stmtInsert->execute();
      $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
      $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha) 
       VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$cod_proveedor."','".$codigo_auxiliar_s."','".$cod_comprobantedetalle."','".$cod_tipopagoproveedor."','".$monto_pago_s."','".$glosa_detalle_s."','".$fecha_pago."')";
      $stmtInsert2 = $dbh->prepare($sqlInsert2);
      $flagSuccess=$stmtInsert2->execute();
  }
}
$contadorCheque=1000;
if($contadorCheque==$contadorChequeFilas){
  //creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
    $codGestion=date("Y");
    $tipoComprobante=2;
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,3);
    $fechaHoraActual=date("Y-m-d H:i:s");

    //$glosa="Beneficiario: ".obtenerProveedorSolicitudRecursos($codigo)." ".$datosServicio."  F/".$facturaCabecera." ".$nombreCliente." SR ".$numeroSol;
    $glosa="Beneficiario: ".nameProveedor($proveedor)." ".$observaciones_pago;
    $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_atte
    VALUES ('$codComprobante', '1', '$globalUnidad', '$codGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fechaHoraActual', '$userSolicitud', '$fechaHoraActual', '$userSolicitud')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();
        //fin de comprobante

    $sqlInsert4="UPDATE pagos_lotes SET cod_comprobante=$codComprobante,cod_estadopagolote=5 where codigo=$cod_pagolote";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();
   for ($pro=1; $pro <= $cantidadProveedores ; $pro++) { 
     if(isset($_POST['cantidad_filas'.$pro])){
         $sqlInsert4="UPDATE pagos_proveedores SET cod_comprobante=$codComprobante,cod_estadopago=5 where cod_pagolote=$cod_pagolote";
         $stmtInsert4 = $dbh->prepare($sqlInsert4);
         $stmtInsert4->execute();

        for ($i=1;$i<=$cantidadFilas;$i++){  
    //$proveedor=$_POST['codigo_proveedor'.$i."PPPP".$pro];         
    $cod_plancuenta=$_POST['codigo_plancuenta'.$i."PPPP".$pro];
  $monto_pago=$_POST["monto_pago".$i."PPPP".$pro];
  $totalPago+=$monto_pago;
  $cod_solicitud=$_POST["codigo_solicitud".$i."PPPP".$pro];
  $codigo_detalle=$_POST["codigo_solicitudDetalle".$i."PPPP".$pro];
  $glosa_detalle=$_POST["glosa_detalle".$i."PPPP".$pro];
  if(!($monto_pago==0 || $monto_pago=="")){
    $porFecha2 = explode("/", $_POST["fecha_pago".$i."PPPP".$pro]);
    $fecha_pagoDet=$porFecha2[2]."-".$porFecha2[1]."-".$porFecha2[0];
    $tipo_pago=$_POST["tipo_pago".$i."PPPP".$pro];

    //comprobante detalle
    $cuenta=obtenerCuentaPasivaSolicitudesRecursos($cod_plancuenta);
    $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorCliente(1,$proveedor);

    /*$cuenta=obtenerValorConfiguracion(37);
    $cuentaAuxiliar=0;*/
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

        $debe=$monto_pago;
        $haber=0;
        
        $facturaCabecera=obtenerNumeroFacturaSolicitudRecursos($cod_solicitud);

        
        
        $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
        if($glosaDetalle==""){
          $glosaDetalle=$glosa." - ".$glosa_detalle;
        }  

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '1')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
       
       //haber
        $cuenta=obtenerValorConfiguracion(38);
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

        $debe=0;
        $haber=$monto_pago;
        $glosaDetalle=obtenerGlosaSolicitudRecursoDetalle($codigo_detalle);
        if($glosaDetalle==""){
          $glosaDetalle=$glosa." - ".$glosa_detalle;
        }

        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '2')";
        $stmtDetalle = $dbh->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
       //fin comprobante detalle
        $codComprobanteDetalleOrigen=obtenerCodigoEstadoCuentaSolicitudRecursosDetalle($codigo_detalle);
            //estado de cuentas devengado
              $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux) 
              VALUES ('$codComprobanteDetalle', '$cuenta', '$haber', '0', '$fecha_pago','$codComprobanteDetalleOrigen','$cuentaAuxiliar')";
              $stmtDetalleEstadoCuenta = $dbh->prepare($sqlDetalleEstadoCuenta);
              $stmtDetalleEstadoCuenta->execute();
      }
    }
     } 
   }   
    
}else{

}         

if($flagSuccess==true){
  showAlertSuccessError(true,"../".$urlListPagoLotes);  
}else{
  showAlertSuccessError(false,"../".$urlListPagoLotes);
}

?>
