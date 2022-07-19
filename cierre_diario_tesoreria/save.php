<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
session_start();
$tipo_cierre=$_POST['tipo_cierre'];
$comprobante=$_POST['comprobante'];
$cod_comprobante=$_POST['cod_comprobante'];
$token=$_POST['token'];
$nro_cheque=$_POST['nro_cheque'];
$glosa=$_POST['glosa'];
$importe=$_POST['importe'];
$creado_por=$_SESSION['globalUser'];
$fecha=date("Y-m-d");
//datos para el envio
$dbh = new Conexion();
$flagSuccess=false;
if(obtenerSaldoCierreTesoreria(date("Y-m-d"))>=$importe){
    $sql="INSERT INTO cierre_tesoreria (cod_tipocierre,cod_comprobante,comprobante,cod_personal,fecha,token,nro_trasaccion_cheque,glosa,importe,estado,created_by,created_at) 
VALUES('$tipo_cierre','$cod_comprobante','$comprobante','$creado_por','$fecha','$token','$nro_cheque','$glosa','$importe',1,'$creado_por',NOW())";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,"../".$urlList); 
?>