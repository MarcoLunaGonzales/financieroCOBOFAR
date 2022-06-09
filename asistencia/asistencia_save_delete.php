<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$codigo=$_GET["codigo"];
$q=$_GET["q"];
$s=$_GET["s"];

$cod_estadoreferencial=1;
// Prepare
$sql="UPDATE asistencia_personal set cod_estado=2 where codigo=$codigo";
 // echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

showAlertSuccessError($flagSuccess,'../index.php?opcion=asistenciaPersonalLista&q='.$q.'&s='.$s);


?>
