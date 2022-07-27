<?php

require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../conexion.php';
$dbh = new Conexion();


$stringPersonas=$_POST['cod_personal'];
$fechaInicio=$_POST['fecha_inicio'];
$fechaFinal=$_POST['fecha_fin'];

// require("estilos.inc");
// require("funcion_nombres.php");

// $fechaInicio=$_POST['exafinicial'];
// $fechaFinal=$_POST['exaffinal'];

// $personal=$_POST['rpt_personal'];
// $stringPersonas=implode(",",$personal);

// $fechaInicio='2022-06-01';
// $fechaFinal='2022-06-30';
// $stringPersonas=246;



$sql="SELECT p.codigo,p.identificacion,p.primer_nombre,p.paterno,p.materno,a.nombre as area,p.turno
from personal p join areas a on p.cod_area=a.codigo
where p.codigo in ($stringPersonas)";
$stmtg = $dbh->prepare($sql);
$stmtg->execute();
while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
  $turno=$rowg['turno'];
  $area=$rowg['area'];
  $nombrePer=$rowg['primer_nombre']." ".$rowg['paterno']." ".$rowg['materno'];
  $NombreTurno="";
  switch ($turno) {
    case 1://mañana
      $NombreTurno="Mañana";
    break;
    case 2://tarde
      $NombreTurno="Tarde";
    break;
    case 3://of central
      $NombreTurno="Of. Central";
    break;
  }
}

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-icon">
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Reporte de Marcación Por Persona<br><p style="font-size:15px">Fecha Inicio : <?=$fechaInicio?> Fecha fin : <?=$fechaFinal?> <br> Sucursal/Area : <?=$area?> <br> Personal : <?=$nombrePer?><br> Turno : Mañana </p></h4>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
            <center>
            <table class='table table-bordered table-condensed' style='width:80% !important'>
              <tr class='bg-info text-white' style='background:#A6F7C3 !important;color:#000 !important;height:30px;'>
                <!-- <th>Personal</th>
                <th>Sucursal</th>
                <th>Turno</th> -->
                <th>Fecha</th>
                <th>Hora Asign</th>
                <th>Hora Marca</th>
                <th>Hora Asigna</th>
                <th>Hora Marca</th>
                <th>Min Asignados</th>
                <th>Min Trabajado</th>
                <th>Min Atraso</th>
                <th>Min Extras</th>
                <th>Min Abandono</th>
              </tr>
              <?php

              $totalAsignados=0;
              $totalTrabajados=0;
              $totalAtrasos=0;              
              $totalExtras=0;
              $totalAbandono=0;

              $sqlPersonal="SELECT ap.fecha,ap.cod_personal,ap.cod_sucursal,ap.cod_horario,ap.entrada_asig,ap.salida_asig,ap.marcado1,ap.marcado2,ap.minutos_asignados,ap.minutos_trabajados,ap.minutos_atraso,ap.minutos_extras,ap.minutos_abandono,ap.estado_asistencia
              from asistencia_procesada ap
              where ap.cod_personal=$stringPersonas and fecha between '$fechaInicio' and '$fechaFinal' ";
              $stmtPersonal = $dbh->prepare($sqlPersonal);
              $stmtPersonal->execute();
              $stmtPersonal->bindColumn('fecha', $fecha);
              $stmtPersonal->bindColumn('entrada_asig', $entrada_asig);
              $stmtPersonal->bindColumn('salida_asig', $salida_asig);
              $stmtPersonal->bindColumn('marcado1', $marcado1);
              $stmtPersonal->bindColumn('marcado2', $marcado2);
              $stmtPersonal->bindColumn('minutos_asignados', $minutos_asignados);
              $stmtPersonal->bindColumn('minutos_trabajados', $minutos_trabajados);
              $stmtPersonal->bindColumn('minutos_atraso', $minutos_atraso);
              $stmtPersonal->bindColumn('minutos_extras', $minutos_extras);
              $stmtPersonal->bindColumn('minutos_abandono', $minutos_abandono);
              $stmtPersonal->bindColumn('estado_asistencia', $estado_asistencia);
              while ($row = $stmtPersonal->fetch()) { 

                switch ($estado_asistencia) {
                  case 0://sin asistencia
                    $marcado1="X";
                    $marcado2="X";
                    $label_entrada="style='color:red;font-weight:bold;'";
                    $label_salida="style='color:red;font-weight:bold;'";
                  break;
                  case 1://Asistencia
                    if($minutos_atraso>0){
                      $label_entrada="style='color:red;font-weight:bold;'";
                    }else{
                      $label_entrada="style='color:green;font-weight:bold;'";
                    }

                    if($minutos_abandono>0){
                      $label_salida="style='color:red;font-weight:bold;'";
                    }else{
                      $label_salida="style='color:green;font-weight:bold;'";  
                    }
                  break;
                  case 2://Dia sin trabajo
                    $marcado1="X";
                    $marcado2="X";
                    $entrada_asig="X";
                    $salida_asig="X";
                    $label_entrada="style='color:blue;font-weight:bold;'";
                    $label_salida="style='color:blue;font-weight:bold;'";
                  break;
                  default:
                    
                  break;
                }
               
                

                $totalAsignados+=$minutos_asignados;
                $totalTrabajados+=$minutos_trabajados;
                $totalAtrasos+=$minutos_atraso;
                $totalExtras+=$minutos_extras;
                $totalAbandono+=$minutos_abandono;                

                ?>
                <tr >
                  <td style='background:#A6B1F7' class="text-left"><b><?=nombreDia(date('N',strtotime($fecha)))?> <?=date('d',strtotime($fecha))?> <?=abrevMes(date('m',strtotime($fecha)))?></b></td>
                  <td><?=$entrada_asig?></td>
                  <td <?=$label_entrada?>><?=$marcado1?></td>
                  <td><?=$salida_asig?></td>
                  <td <?=$label_salida?>><?=$marcado2?></td>
                  <td><?=$minutos_asignados?></td>
                  <td><?=$minutos_trabajados?></td>
                  <td><?=$minutos_atraso?></td>
                  <td><?=$minutos_extras?></td>
                  <td><?=$minutos_abandono?></td>
                </tr>
              <?php
              }
              ?>
              <tr style='background:#A6F7C3 !important;color:#000 !important;height:30px;'>
                <td ><b>- </b></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td><?=$totalAsignados?></td>
                <td><?=$totalTrabajados?></td>
                <td><?=$totalAtrasos?></td>
                <td><?=$totalExtras?></td>
                <td><?=$totalAbandono?></td>
              </tr>
            </table></center>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
