<?php

require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../conexion.php';
$dbh = new Conexion();


$cod_sucursales=$_POST['cod_sucursal'];
$stringSucursales=implode(",",$cod_sucursales);
$fechaInicio=$_POST['fecha_inicio'];
$fechaFinal=$_POST['fecha_fin'];

?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-icon">
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Reporte de Marcación Consolidado<br><p style="font-size:15px">Fecha Inicio : <?=$fechaInicio?> Fecha fin : <?=$fechaFinal?></p></h4>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
            <center>
            <table class='table table-bordered table-condensed' style='width:80% !important'>
              <tr class='bg-info text-white' style='background:#A6F7C3 !important;color:#000 !important;height:30px;'>
                <!-- <th>Personal</th>
                <th>Sucursal</th>
                <th>Turno</th> -->
                <th>Sucursal</th>
                <th>Turno</th>
                <th>Paterno</th>
                <th>Materno</th>
                <th>Nombres</th>

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

              $sqlPersonal="SELECT ap.fecha,ap.cod_personal,sum(ap.minutos_asignados)as minutos_asignados,sum(ap.minutos_trabajados)as minutos_trabajados,sum(ap.minutos_atraso)as minutos_atraso,sum(ap.minutos_extras)as minutos_extras,sum(ap.minutos_abandono)as minutos_abandono,a.nombre as area,p.turno,p.paterno,p.materno,p.primer_nombre
                from asistencia_procesada ap join areas a on ap.cod_sucursal=a.codigo join personal p on ap.cod_personal=p.codigo
                where  ap.fecha between '2022-06-01' and '2022-06-30'
                GROUP BY p.codigo
                order by a.nombre,p.turno";


              $stmtPersonal = $dbh->prepare($sqlPersonal);
              $stmtPersonal->execute();
              $stmtPersonal->bindColumn('fecha', $fecha);
              $stmtPersonal->bindColumn('minutos_asignados', $minutos_asignados);
              $stmtPersonal->bindColumn('minutos_trabajados', $minutos_trabajados);
              $stmtPersonal->bindColumn('minutos_atraso', $minutos_atraso);
              $stmtPersonal->bindColumn('minutos_extras', $minutos_extras);
              $stmtPersonal->bindColumn('minutos_abandono', $minutos_abandono);
              $stmtPersonal->bindColumn('area', $area);
              $stmtPersonal->bindColumn('turno', $turno);
              $stmtPersonal->bindColumn('paterno', $paterno);
              $stmtPersonal->bindColumn('materno', $materno);
              $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
              while ($row = $stmtPersonal->fetch()) {
                
                  if($turno==1){
                    $nombreturno=" TM";
                  }elseif($turno==1){
                    $nombreturno=" TT";
                  }else{
                    $nombreturno="";
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
