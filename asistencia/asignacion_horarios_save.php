<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$cod_horario=$_GET['cod_horario'];
$tipo_asignacionMultiple=$_GET['tipo_asignacion'];
$tipo_horario=$_GET['tipo_horario'];


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

   // $sqlVerificar="SELECT codigo FROM horarios_areas where cod_area=$area and (fecha_fin>='$fecha_inicio' or fecha_inicio<='$fecha_fin') and cod_estadoreferencial=1 and cod_asignacion=$tipo_asignacion;";
   $sqlVerificar="SELECT codigo FROM horarios_detalle where cod_horario=$cod_horario and cod_estadoreferencial=1 and cod_asignacion=$tipo_asignacion;";

   $stmtVerificar = $dbh->prepare($sqlVerificar);
   $stmtVerificar->execute();
   
   while ($row = $stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
     //$codigoHorario=$row['codigo'];
      $codigoHorario++;
   }

   $sqlInsertDet="INSERT INTO horarios_detalle(cod_horario,cod_asignacion $queryINTO ,cod_estadoreferencial) 
    VALUES ('$cod_horario','$tipo_asignacion' $queryVALUES ,1);";    
    $queryMaestro.=$sqlInsertDet;

}


if($codigoHorario>0){
   $mensaje="Ya existe un horario establecido con el/los tipo(s) de asignación!";
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
