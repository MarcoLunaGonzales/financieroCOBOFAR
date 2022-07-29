<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$codigo=$_GET['codigo'];


$sql="SELECT cod_asignacion,fecha_inicio,fecha_fin,ingreso_1,salida_1,ingreso_2,salida_2,ingreso_3,salida_3,ingreso_4,salida_4 FROM horarios_areas where codigo='$codigo';";        
$stmt = $dbh->prepare($sql);
$stmt->execute();
$existe=0;
$tiposHoras="";
while ($row = $stmt->fetch()) {
   $cod_asignacion=$row['cod_asignacion'];
   $fecha_inicio=$row['fecha_inicio'];
   $fecha_fin=$row['fecha_fin'];
   $ingreso_1=$row['ingreso_1'];
   $salida_1=$row['salida_1'];
   $ingreso_2=$row['ingreso_2'];
   $salida_2=$row['salida_2'];
   $ingreso_3=$row['ingreso_3'];
   $salida_3=$row['salida_3'];
   $ingreso_4=$row['ingreso_4'];
   $salida_4=$row['salida_4'];   

   if($ingreso_1!=""&&$salida_1!=""){
      $tiposHoras.="1,";
   }
   if($ingreso_2!=""&&$salida_2!=""){
      $tiposHoras.="2,";
   }
   if($ingreso_3!=""&&$salida_3!=""){
      $tiposHoras.="3,";
   }
   if($ingreso_4!=""&&$salida_4!=""){
      $tiposHoras.="4,";
   }
   $existe++;
}
$tiposHoras=trim($tiposHoras,",");
if($existe>0){
   echo "#####0#####".$fecha_inicio."#####".$fecha_fin."#####".$ingreso_1."#####".$salida_1."#####".$ingreso_2."#####".$salida_2."#####".$ingreso_3."#####".$salida_3."#####".$ingreso_4."#####".$salida_4."#####".$tiposHoras."#####".$cod_asignacion;   
}else{   
    echo "#####0#####";   
}
?>
