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
$tipo_pago=$_POST["tipo_pago_s"];
//datos de cabecera
$cantidadItems=$_POST['cantidad_proveedores'];//total de intems

$nombre_lote=$_POST['nombre_lote'];
$porFecha = explode("/", $_POST['fecha_pago']);
$fecha_pago=$porFecha[2]."-".$porFecha[1]."-".$porFecha[0];
$observaciones_pago=$_POST['observaciones_pago'];
$cod_pagolote=obtenerCodigoPagoLote();
$nro_correlativo=obtenerCorrelativoPagoLote();
$sqlInsert="INSERT INTO pagos_lotes (codigo,nombre,abreviatura, fecha,cod_comprobante,cod_estadopagolote,cod_ebisalote,cod_estadoreferencial,observaciones,nro_correlativo,created_at, created_by) 
VALUES ('".$cod_pagolote."','".$nombre_lote."','','".$fecha_pago."','0',1,".$tipo_pago.",1,'".$observaciones_pago."',".$nro_correlativo.",NOW(), $globalUser)";
$stmtInsert = $dbh->prepare($sqlInsert);
$stmtInsert->execute();
//ya se insertó la cebecera
$totalPago=0;
$contadorCheque=0;$contadorChequeFilas=0;
$cod_proveedor=0;
$total_pago=0;
for ($pro=1; $pro <= $cantidadItems ; $pro++){
  $codigo_auxiliar_s=$_POST["codigo_auxiliar_s".$pro];//codigo estado de cuenta relacionado
  $monto_pago_s=$_POST["monto_pago_s".$pro];
  $glosa_detalle_s=$_POST["glosa_detalle_s".$pro];
  $fecha_ex_s=$_POST["fecha_ex_s".$pro];

  

  // $patron15 = "/[^a-zA-Z0-9]+/";//solo numeros,letras M y m, tildes y la ñ
  $patron1="[\n|\r|\n\r]";
  $glosa_detalle_s = preg_replace($patron1, ", ", $glosa_detalle_s);//quitamos salto de linea
  $glosa_detalle_s = str_replace('"', " ", $glosa_detalle_s);//quitamos comillas dobles  
  $glosa_detalle_s = str_replace("'", " ", $glosa_detalle_s);//quitamos comillas simples
  $glosa_detalle_s = str_replace('<', "(", $glosa_detalle_s);//quitamos comillas dobles
  $glosa_detalle_s = str_replace('>', ")", $glosa_detalle_s);//quitamos comillas dobles

  // $cod_tipopagoproveedor=48;
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
    $pronto_pago=$_POST["pronto_pago_s".$pro];
    // echo "<br><br><br>".$pronto_pago;
      $cod_pagoproveedor=obtenerCodigoPagoProveedor();
      $sqlInsert="INSERT INTO pagos_proveedores (codigo, fecha,observaciones,cod_comprobante,cod_estadopago,cod_ebisa,cod_cajachicadetalle,cod_pagolote) 
      VALUES ('".$cod_pagoproveedor."','".$fecha_pago."','".$observaciones_pago."','0',3,0,0,".$cod_pagolote.")";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $stmtInsert->execute();
      $cod_pagoproveedordetalle=obtenerCodigoPagoProveedorDetalle();
      $sqlInsert2="INSERT INTO pagos_proveedoresdetalle (codigo,cod_pagoproveedor,cod_proveedor,cod_solicitudrecursos,cod_solicitudrecursosdetalle,cod_tipopagoproveedor,monto,observaciones,fecha,pronto_pago) 
       VALUES ('".$cod_pagoproveedordetalle."','".$cod_pagoproveedor."','".$cod_proveedor."','".$codigo_auxiliar_s."','".$cod_comprobantedetalle."','".$tipo_pago."','".$monto_pago_s."','".$glosa_detalle_s."','".$fecha_ex_s."','".$pronto_pago."')";
      $stmtInsert2 = $dbh->prepare($sqlInsert2);
      $flagSuccess=$stmtInsert2->execute();

      $total_pago+=$monto_pago_s;
  }
}

//verificamos si tiene algun cheque 

if($tipo_pago==1){          
  $banco=$_POST['banco_pago_s'];
  $cheque=$_POST['emitidos_pago_s'];
  $numero_cheque=$_POST['numero_cheque_s'];
  $nombre_ben=$_POST['beneficiario_s'];
  $sqlInsert3="INSERT INTO cheques_emitidos(cod_cheque,fecha,nombre_beneficiario,monto,cod_pagodetalle,cod_estadoreferencial,numero) 
      VALUES ('".$cheque."','".$fecha_pago."','".$nombre_ben."','".$total_pago."','".$cod_pagolote."',1,$numero_cheque)";
  $stmtInsert3 = $dbh->prepare($sqlInsert3);
  $stmtInsert3->execute();
  $sqlInsert4="UPDATE cheques SET nro_cheque=$numero_cheque where codigo=$cheque";
  $stmtInsert4 = $dbh->prepare($sqlInsert4);
  $stmtInsert4->execute();
}      

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlListPagoLotes);	
}else{
	showAlertSuccessError(false,"../".$urlListPagoLotes);
}

?>
