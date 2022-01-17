<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();


$urlRedirect="../index.php?opcion=configuracion_edit_sf";
session_start();
$globalUser=$_SESSION["globalUser"];
// if (isset($_POST["modal_check_f"])) {
// 	$modal_check_f=$_POST["modal_check_f"];	
// }else{
// 	$modal_check_f=false;
// }
// if (isset($_POST["modal_check_sf"])) {
// 	$modal_check_sf=$_POST["modal_check_sf"];
// }else{
// 	$modal_check_sf=false;
// }

if (isset($_POST["modal_check_lb"])) {
	$modal_check_lb=$_POST["modal_check_lb"];
}else{
	$modal_check_lb=false;
}

// if($modal_check_sf){
// 	$modal_check_sf_x=1;
// }else{
// 	$modal_check_sf_x=0;
// }
// if($modal_check_f){
// 	$modal_check_f_x=1;
// }else{
// 	$modal_check_f_x=0;
// }

if($modal_check_lb){
	$modal_check_lb_x=1;
}else{
	$modal_check_lb_x=0;
}

$contra_cuentapagos=$_POST["contra_cuentas_pagos"];
$tipo_comprobante_pagoproveedores=$_POST["tipo_comprobante_pagoproveedores"];



// $stmtSF = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$modal_check_sf_x' where id_configuracion=76");//VARIABLE CONFIGURACION PARA ACTIVAR EDIT FORMA DE PAGO SF
// $flagSuccess=$stmtSF->execute();
// $stmtF = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$modal_check_f_x' where id_configuracion=77");//VARIABLE CONFIGURACION PARA ACTIVAR EDIT RS FACTURAS
// $flagSuccess=$stmtF->execute();

$stmtLB = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$modal_check_lb_x',modified_at=NOW(),modified_by=$globalUser where id_configuracion=90");//VARIABLE CONFIGURACION PARA ACTIVAR VALIDACION DE LIBRETAS EN COMPROBANTES
$flagSuccess=$stmtLB->execute();

$stmtLB = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$contra_cuentapagos',modified_at=NOW(),modified_by=$globalUser where id_configuracion=38");//VARIABLE CONFIGURACION PARA ACTIVAR VALIDACION DE LIBRETAS EN COMPROBANTES
$flagSuccess=$stmtLB->execute();

$stmtLB = $dbh->prepare("UPDATE configuraciones set valor_configuracion='$tipo_comprobante_pagoproveedores',modified_at=NOW(),modified_by=$globalUser where id_configuracion=108");//VARIABLE CONFIGURACION PARA ACTIVAR VALIDACION DE LIBRETAS EN COMPROBANTES
$flagSuccess=$stmtLB->execute();


showAlertSuccessError($flagSuccess,$urlRedirect);	

?>
