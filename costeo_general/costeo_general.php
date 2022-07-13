<?php 
require_once '../conexion_comercial2.php';
$fechaDesde=$_GET['fecha_desde'];
$fechahasta=$_GET['fecha_hasta'];

$horaInicio=date("d/m/Y H:i:s");
//aqui el proceso php

  $sql="DELETE FROM costoscobofar.costo_promedio_mes where cod_mes=MONTH('$fechaDesde') and cod_gestion=YEAR('$fechaDesde');";  
  mysqli_query($enlaceCon,$sql);

  $sql="DELETE FROM costoscobofar.costo_transaccion where cod_mes=MONTH('$fechaDesde') and cod_gestion=YEAR('$fechaDesde');";  
  mysqli_query($enlaceCon,$sql);
  $sql="CALL proceso_costo_global(1,0,'$fechaDesde 00:00:00','$fechahasta 23:59:59');";
  //echo $sql;
  mysqli_query($enlaceCon,$sql);


  $horaFin=date("d/m/Y H:i:s");
  echo "#####".$horaInicio." - ".$horaFin;