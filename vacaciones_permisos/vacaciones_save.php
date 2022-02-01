<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$personal=$_POST["personal"];
$fecha_inicio=$_POST["fecha_inicio"];
$hora_inicio=$_POST["hora_inicio"];
$fecha_final=$_POST["fecha_final"];
$hora_final=$_POST["hora_final"];
$observaciones=$_POST["observaciones"];


// Prepare
$stmt = $dbh->prepare("INSERT INTO $table_dotaciones (nombre, abreviatura,descripcion,nro_meses,fecha_inicio,fecha_fin,cod_estadoreferencial) VALUES (:nombre,:abreviatura,:descripcion, :nro_meses, :fecha_inicio, :fecha_fin, :cod_estado)");
// Bind
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':abreviatura', $abreviatura);
$stmt->bindParam(':descripcion', $descripcion);
$stmt->bindParam(':nro_meses', $nroMeses);
$stmt->bindParam(':fecha_inicio', $fechaInicio);
$stmt->bindParam(':fecha_fin', $fechaFin);
$stmt->bindParam(':cod_estado', $codEstado);

$flagSuccess=$stmt->execute();
showAlertSuccessError($flagSuccess,$urlList2);

?>
