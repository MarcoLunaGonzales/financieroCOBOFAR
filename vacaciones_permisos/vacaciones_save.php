<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';


$dbh = new Conexion();
$globalUser=$_SESSION["globalUser"];

$personal=$_POST["personal"];
$fecha_inicio=$_POST["fecha_inicio"];
// $hora_inicio=$_POST["hora_inicio"];
$hora_inicio='00:00';
$fecha_final=$_POST["fecha_final"];
// $hora_final=$_POST["hora_final"];
$hora_final=$hora_inicio='00:00';
$observaciones=$_POST["observaciones"];
$dias_vacacion=$_POST["dias_vacacion"];
$cod_estadoreferencial=1;
// Prepare
$sql="INSERT INTO personal_vacaciones (cod_personal, fecha_inicial,hora_inicial,fecha_final,hora_final,observaciones,cod_estadoreferencial,dias_vacacion,created_at,created_by) VALUES ($personal, '$fecha_inicio','$hora_inicio','$fecha_final','$hora_final','$observaciones','$cod_estadoreferencial','$dias_vacacion',NOW(),'$globalUser')";
// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,'../index.php?opcion=vacacionesPersonalLista');

?>
