<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();


$fecha_inicio=$_GET['fecha_inicio'];
$hora_inicio=$_GET['hora_inicio'];
$fecha_final=$_GET['fecha_final'];
$hora_final=$_GET['hora_final'];

// $cantidadDomingos=domingosMes($fecha_inicio,$fecha_final);

// $total_dias_mes=obtenerTotalDias_fechas($fecha_inicio,$fecha_final);
$total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio,$fecha_final);
$total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio,$fecha_final);

$date1 = new DateTime($fecha_inicio." ".$hora_inicio);
$date2 = new DateTime($fecha_final." ".$hora_final);
$diff = $date1->diff($date2);    
$dias_obtenidos=$diff->days;
$horas_obtenidos=$diff->h;

if($horas_obtenidos>=4){
    $dias_obtenidos+=0.5;
}
$dias_obtenidos=$dias_obtenidos-$total_domingos_mes-$total_feriados_mes;
?>
<input  type='text' class='form-control' readonly="true" style="background: white;color:green;font-size: 18px;"  value="Total días solicitadas : <?=$dias_obtenidos?>">
<input  type='hidden' id="dias_solicitadas" name="dias_solicitadas" value="<?=$dias_obtenidos?>">