<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$area=$_GET['area'];
$tipo_asignacionMultiple=$_GET['tipo_asignacion'];
$tipo_horario=$_GET['tipo_horario'];
$fecha_inicio=$_GET['fecha_inicio'];
$fecha_fin=$_GET['fecha_fin'];

$user=$_SESSION["globalUser"];


$queryINTO="";
$queryVALUES="";
for ($i=0; $i < count($tipo_horario); $i++) { 
   $queryINTO.=" ,ingreso_".$tipo_horario[$i].",salida_".$tipo_horario[$i];
   $queryVALUES.=" ,'".$_GET['ingreso_'.$tipo_horario[$i]]."','".$_GET['salida_'.$tipo_horario[$i]]."'";
   $existeTurnos++;
}
if($i==0){
   $queryINTO.=" ,ingreso_4,salida_4";
   $queryVALUES.=" ,'".$_GET['ingreso_4']."','".$_GET['salida_4']."'";
}

$codigoHorario=0;
$mensaje="Existio un error al guardar!";
$flagSuccess=false;

$queryMaestro="";
for ($j=0; $j < count($tipo_asignacionMultiple); $j++) { 
   $tipo_asignacion=$tipo_asignacionMultiple[$j];
   $sqlVerificar="SELECT codigo FROM horarios_areas where cod_area=$area and (fecha_fin>='$fecha_inicio' or fecha_inicio<='$fecha_fin') and cod_estadoreferencial=1 and cod_asignacion=$tipo_asignacion;";

   $stmtVerificar = $dbh->prepare($sqlVerificar);
   $stmtVerificar->execute();
   
   while ($row = $stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
     //$codigoHorario=$row['codigo'];
      $codigoHorario++;
   }

   $sqlInsertDet="INSERT INTO horarios_areas(cod_area,cod_asignacion, fecha_inicio,fecha_fin $queryINTO ,activo,created_by,created_at,cod_estadoreferencial) 
    VALUES ('$area','$tipo_asignacion','$fecha_inicio','$fecha_fin' $queryVALUES ,0,'$user',NOW(),1);";    
    $queryMaestro.=$sqlInsertDet;

}


if($codigoHorario>0){
   $mensaje="Ya existe un horario establecido para el rango de fechas y el/los tipo(s) asignación!";
}else{   
    $stmtInsertDet = $dbh->prepare($queryMaestro);
    $flagSuccess=$stmtInsertDet->execute();
}

if($flagSuccess==true){
   echo "#####0#####";   
}else{
   echo "#####1#####".$mensaje;
}


?>
