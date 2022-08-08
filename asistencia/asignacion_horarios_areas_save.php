<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$modal_horario=$_GET['modal_horario'];
$modal_area=$_GET['modal_area'];

$flagSuccess=false;
$mensaje="Error en sistema";

$existeFila=0;
$sqlVerificar="SELECT codigo FROM horarios_area where estado=1 and cod_area=$modal_area;";
$stmtVerificar = $dbh->prepare($sqlVerificar);
$stmtVerificar->execute();   
while ($row = $stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
   $existeFila++;
}

if($existeFila==0){
   $sqlInsertDet="INSERT INTO horarios_area(cod_horario,cod_area,estado) VALUES ('$modal_horario','$modal_area',1);";    
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccess=$stmtInsertDet->execute();

   //personal
   $sqlInsertDet="UPDATE horarios_persona SET estado=0 where cod_persona in (SELECT codigo FROM personal where cod_area=$modal_area and cod_estadoreferencial=1 and cod_estadopersonal=1) and estado=1;";    
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccess=$stmtInsertDet->execute();
   $sqlInsertDet="INSERT INTO horarios_persona(cod_horario,cod_persona,estado)  
   SELECT $modal_horario as cod_horario,codigo, 1 as estado FROM personal where cod_area=$modal_area and cod_estadoreferencial=1 and cod_estadopersonal=1;";    
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccess=$stmtInsertDet->execute(); 
}



if($flagSuccess==true){
   echo "#####0#####";   
}else{
   echo "#####1#####".$mensaje;
}


?>
