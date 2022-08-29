<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'activosFijos/configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];

$cod_personal=$_GET['cod_personal'];
$ing_planilla=$_GET['ing_planilla'];
$fecha_actual=$_GET['fecha_actual'];
// $anios_antiguedad=$_GET['anios_antiguedad'];
// Prepare
$stmt = $dbh->prepare("UPDATE personal_vacaciones set cod_estadoreferencial=2 where codigo=$codigo");
$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,'?opcion=vacaciones_detalle&codigo='.$cod_personal.'&ing_planilla='.$ing_planilla.'&fecha_actual='.$fecha_actual.'');

?>