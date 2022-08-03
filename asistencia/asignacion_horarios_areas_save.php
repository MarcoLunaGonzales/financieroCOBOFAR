<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$modal_horario=$_GET['modal_horario'];
$modal_area=$_GET['modal_area'];

$flagSuccess=false;

$mensaje="Error en sistema";
$sqlInsertDet="INSERT INTO horarios_area(cod_horario,cod_area) 
VALUES ('$modal_horario','$modal_area');";    

$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();

if($flagSuccess==true){
   echo "#####0#####";   
}else{
   echo "#####1#####".$mensaje;
}


?>
