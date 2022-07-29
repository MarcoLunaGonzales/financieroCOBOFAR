<?php
require_once '../conexion.php';

$dbh = new Conexion();
$tipo=$_GET['tipo'];
$descripcion=$_GET['descripcion'];
$ingreso=$_GET['ingreso'];
$salida=$_GET['salida'];


 $sqlInsertDet="INSERT INTO horarios(descripcion, hora_ingreso,hora_salida,tipo,estado) 
    VALUES ('$descripcion','$ingreso','$salida','$tipo',1)";    
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccess=$stmtInsertDet->execute();

?>
