<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();

$fecha_inicio_modal=$_GET['fecha_inicio_modal'];
$fecha_final_modal=$_GET['fecha_final_modal'];

$saldo_modal=$_GET['saldo_modal'];


// $cantidadDomingos=domingosMes($fecha_inicio,$fecha_final);

// $total_dias_mes=obtenerTotalDias_fechas($fecha_inicio,$fecha_final);
$total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio_modal,$fecha_final_modal);
$total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio_modal,$fecha_final_modal);

$minutos=0;
$dateTimeObject1 = date_create($fecha_inicio_modal); 
$dateTimeObject2 = date_create($fecha_final_modal); 
$difference = date_diff($dateTimeObject1, $dateTimeObject2);   
$dias_obtenidos = $difference->days;

$dias_obtenidos+=1;

$dias_obtenidos=$dias_obtenidos-$total_domingos_mes-$total_feriados_mes;
if($saldo_modal>$dias_obtenidos){?>
    <input  type='number' style="color: green;" class='form-control'  id='dias_vacacion'  name='dias_vacacion' min="5" value="<?=$dias_obtenidos?>" required readonly>
<?php }else{?>
    <input  type='hidden'  id='dias_vacacion'  name='dias_vacacion' value="0" required>
    <input  type='text' style="color: red;" class='form-control'  value="Días Solicitadas Insufucientes" readonly>
<?php }
?>
