<?php

require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
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

                <!-- <th>Fecha</th>
                <th>Hora Asign</th>
                <th>Hora Marca</th>
                <th>Hora Asigna</th>
                <th>Hora Marca</th> -->
                <th>Asignados [Min]</th>
                <th>Trabajado [Min]</th>
                <th>Atraso [Min]</th>
                <th>Extras [Min ]</th>
                <th>Abandono [Min]</th>
                <th>Descuento Atraso</th> 
                <th>-</th>
              </tr>
              <?php
              $sqlPersonal="SELECT p.codigo,ap.fecha,ap.cod_personal,sum(ap.minutos_asignados)as minutos_asignados,sum(ap.minutos_trabajados)as minutos_trabajados,sum(ap.minutos_atraso)as minutos_atraso,sum(ap.minutos_extras)as minutos_extras,sum(ap.minutos_abandono)as minutos_abandono,a.nombre as area,p.turno,p.paterno,p.materno,p.primer_nombre,p.haber_basico
                from asistencia_procesada ap join areas a on ap.cod_sucursal=a.codigo join personal p on ap.cod_personal=p.codigo
                where  ap.fecha between '$fechaInicio' and '$fechaFinal' and a.codigo in ($stringSucursales) 
                GROUP BY p.codigo
                order by a.nombre,p.turno";
              $stmtPersonal = $dbh->prepare($sqlPersonal);
              $stmtPersonal->execute();
              $stmtPersonal->bindColumn('codigo', $cod_personal);
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
              $stmtPersonal->bindColumn('haber_basico', $haber_basico);
              while ($row = $stmtPersonal->fetch()) {
                $label_atraso="";
                $label_extras="";
                $label_abandono="";
                if($turno==1){
                  $nombreturno=" TM";
                }elseif($turno==2){
                  $nombreturno=" TT";
                }else{
                  $nombreturno="";
                }
                if($minutos_atraso>0){                  
                  $label_atraso="style='color:red;font-weight:bold;'";
                }
                if($minutos_extras>0){                  
                  $label_extras="style='color:green;font-weight:bold;'";
                }
                if($minutos_abandono>0){                  
                  $label_abandono="style='color:red;font-weight:bold;'";
                }

                
                $descuento_atraso=obtenerDescuentoMinutosPersonal($minutos_atraso,$haber_basico);
                ?>
                <tr >
                  <td style='background:#A6B1F7' class="text-left"><b><?=$area?></b></td>
                  <td class="text-left"><?=$nombreturno?></td>
                  <td class="text-left"><?=$paterno?></td>
                  <td class="text-left"><?=$materno?></td>
                  <td class="text-left"><?=$primer_nombre?></td>
                  <td><?=$minutos_asignados?></td>
                  <td><?=$minutos_trabajados?></td>
                  <td <?=$label_atraso?>><?=$minutos_atraso?></td>
                  <td <?=$label_atraso?>><?=$minutos_atraso?></td>
                  <td <?=$label_extras?>><?=$minutos_extras?></td>
                  <td <?=$label_abandono?> class="text-right"><?=formatNumberDec($descuento_atraso)?></td>
                  <td  class="td-actions text-right"><a  target='_blank' href='reporte_asistencia_personal_print.php?cod_personal=<?=$cod_personal?>&fecha_inicio=<?=$fechaInicio?>&fecha_fin=<?=$fechaFinal?>'  class="btn btn-dark"  >
                      <i class="material-icons" title="Ver Detalle">visibility</i>
                    </a>
                  </td>
                </tr>
              <?php
              }
              ?>
            </table></center>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
