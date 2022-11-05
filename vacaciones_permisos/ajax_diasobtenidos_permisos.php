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

$minutos=0;
$dateTimeObject1 = date_create($fecha_inicio." ".$hora_inicio); 
$dateTimeObject2 = date_create($fecha_final." ".$hora_final); 
$difference = date_diff($dateTimeObject1, $dateTimeObject2);   
$dias_obtenidos = $difference->days;

$dias_obtenidos+=1;
$dif_hora= $difference->h;
$minutos += $difference->h * 60;
$minutos += $difference->i;

if($dif_hora==0){
    $minutos = $difference->i;
}

$minutos_obtenidos=$minutos;
$horas_obtenidos=number_format($minutos/60, 2, '.', '');

// $date1 = new DateTime($fecha_inicio." ".$hora_inicio);
// $date2 = new DateTime($fecha_final." ".$hora_final);
// $diff = $date1->diff($date2);    
// $dias_obtenidos=$diff->days;
// $horas_obtenidos=$diff->h;
// $minutos_obtenidos=$diff->m;

// if($horas_obtenidos>=4){
//     $dias_obtenidos+=0.5;
// }

$dias_obtenidos=$dias_obtenidos-$total_domingos_mes-$total_feriados_mes;

// $string_hora="Solicitar: Días: $dias_obtenidos, Minutos : $minutos_obtenidos ($horas_obtenidos Hrs) ";
$string_hora="$dias_obtenidos Días, $minutos_obtenidos minutos  ($horas_obtenidos Hrs) ";
?>
<input  type='text' class='form-control' readonly="true" style="background: white;color:green;font-size: 18px;"  value="<?=$string_hora?>">
<input  type='hidden' id="dias_solicitadas" name="dias_solicitadas" value="<?=$dias_obtenidos?>">

<input  type='hidden' id="minutos_solicitados" name="minutos_solicitados" value=<?=$minutos_obtenidos?>">