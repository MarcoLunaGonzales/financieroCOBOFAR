<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$cuenta=$_POST["cuenta_auto_id"];
$tipo=$_POST["tipo"];
$credito=$_POST["credito"];

$porciones = explode("$$", $cuenta);
if($porciones[1]=="AUX"){
	$codPlan="";
	$codAux=$porciones[0];
}else{
	$codPlan=$porciones[0];
	$codAux="";
}
$cod_estadoreferencial=1;
// Prepare
$stmt = $dbh->prepare("INSERT INTO $table (cod_plancuenta, cod_cuentaaux,tipo, cod_tipoestadocuenta,cod_estadoreferencial) VALUES (:cod_plancuenta, :cod_cuentaaux, :tipo, :cod_tipoestadocuenta,:cod_estadoreferencial)");
// Bind
$stmt->bindParam(':cod_plancuenta', $codPlan);
$stmt->bindParam(':cod_cuentaaux', $codAux);
$stmt->bindParam(':tipo', $credito);
$stmt->bindParam(':cod_tipoestadocuenta', $tipo);
$stmt->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
$flagSuccess=$stmt->execute();


if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList2);	
}else{
	showAlertSuccessError(false,"../".$urlList2);
}
?>
