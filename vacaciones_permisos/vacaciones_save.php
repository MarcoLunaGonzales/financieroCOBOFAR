<?php

require_once '../conexion.php';
// require_once '../functionsGeneral.php';
$dbh = new Conexion();
session_start();
$globalUser=$_SESSION["globalUser"];
//$globalUser=-100;

$personal=$_POST["codigo_personal_modal"];
$fecha_inicio=$_POST["fecha_inicio_modal"];
// $hora_inicio=$_POST["hora_inicio"];
$hora_inicio='00:00';
$fecha_final=$_POST["fecha_final_modal"];
// $hora_final=$_POST["hora_final"];
$hora_final=$hora_inicio='00:00';
$tipo_vacacion=$_POST["tipo_vacacion"];
$dias_vacacion=$_POST["dias_vacacion"];
$gestion=$_POST["gestion_modal"];
$cod_estadoreferencial=1;
// Prepare
$sql="INSERT INTO personal_vacaciones (cod_personal, fecha_inicial,hora_inicial,fecha_final,hora_final,cod_tipovacacion,cod_estadoreferencial,dias_vacacion,created_at,created_by,gestion,modulo) VALUES ($personal, '$fecha_inicio','$hora_inicio','$fecha_final','$hora_final','$tipo_vacacion','$cod_estadoreferencial','$dias_vacacion',NOW(),'$globalUser','$gestion',1)";
// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
// showAlertSuccessError($flagSuccess,'../index.php?opcion=vacacionesPersonalLista');
if($flagSuccess){
	echo 1;
}else{
	echo 2;
}

?>
