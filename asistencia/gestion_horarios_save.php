<?php
require_once '../conexion.php';
$dbh = new Conexion();
session_start();
$descripcion=$_GET['descripcion'];
$inicio=$_GET['inicio'];
$salida=$_GET['salida'];
$user=$_SESSION["globalUser"];

 $sqlInsertDet="INSERT INTO horarios(descripcion, fecha_inicio,fecha_fin,created_by,created_at,activo,cod_estadoreferencial) 
    VALUES ('$descripcion','$inicio','$salida','$user',NOW(),1,1)";    
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccess=$stmtInsertDet->execute();

?>
