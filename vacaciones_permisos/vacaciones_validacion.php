<?php

require_once 'conexion.php';
// require_once '../functionsGeneral.php';
$dbh = new Conexion();


$codigo=$_GET["cp"];
$fi=$_GET["ip"];
$fa=$_GET["fa"];

// Prepare
$sql="UPDATE personal set fecha_validacion_vacaciones=NOW() where codigo=$codigo";
// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,'index.php?opcion=vacaciones_detalle&codigo='.$codigo.'&ing_planilla='.$fi.'&fecha_actual='.$fa);
?>
