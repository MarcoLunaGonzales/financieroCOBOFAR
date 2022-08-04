<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$modal_horario=$_GET['modal_horario'];
$modal_persona=$_GET['modal_persona'];

$flagSuccess=false;
$mensaje="Error en sistema";

$existeFila=0;
$sqlVerificar="SELECT codigo FROM horarios_persona where estado=1 and cod_persona=$modal_persona;";
$stmtVerificar = $dbh->prepare($sqlVerificar);
$stmtVerificar->execute();   
while ($row = $stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
   $existeFila++;
}

if($existeFila==0){
   $sqlInsertDet="INSERT INTO horarios_persona(cod_horario,cod_persona,estado) VALUES ('$modal_horario','$modal_persona',1);";    
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccess=$stmtInsertDet->execute();
}



if($flagSuccess==true){
   echo "#####0#####";   
}else{
   echo "#####1#####".$mensaje;
}


?>
