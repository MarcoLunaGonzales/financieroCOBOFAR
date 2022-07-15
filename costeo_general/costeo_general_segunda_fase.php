<?php 
require_once '../conexion_comercial2.php';
$fechaDesde=$_GET['fecha_desde'];
$fechahasta=$_GET['fecha_hasta'];

$horaInicio=date("d/m/Y H:i:s");
//aqui el proceso php
  $sql="CALL proceso_ingreso_traspaso_fase3_oficial('$fechaDesde 00:00:00','$fechahasta 23:59:59');";
  //echo $sql;
  mysqli_query($enlaceCon,$sql);


  $horaFin=date("d/m/Y H:i:s");
  echo "#####".$horaInicio." - ".$horaFin;