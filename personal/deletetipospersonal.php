<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;

// Prepare
$stmt = $dbh->prepare("UPDATE tipos_personal set cod_estadoreferencial=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlListTipospersonal);

?>