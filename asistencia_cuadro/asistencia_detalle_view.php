<?php //ESTADO FINALIZADO


require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$codigo=$_GET['codigo'];
$q=$_GET['q'];
$s=$_GET['s'];
if($codigo==0){
  $cod_mes=date('m');
  $cod_gestion=date('Y');
}else{
  $cod_mes=$_GET['cod_mes'];
  $cod_gestion=$_GET['cod_gestion'];
}
$nombre_mes=nombreMes($cod_mes);
$nombreGestion=$cod_gestion;

$fecha_inicio_x=$cod_gestion."-".$cod_mes."-01";
$fecha_final_x=date('Y-m-t',strtotime($fecha_inicio_x));

$total_dias_mes=obtenerTotalDias_fechas($fecha_inicio_x,$fecha_final_x);
$total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio_x,$fecha_final_x);
$total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio_x,$fecha_final_x);

$dias_trabajado=$total_dias_mes-$total_domingos_mes-$total_feriados_mes;
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/favicon.png">
            </div>
            <h3 class="card-title text-center"><center><br><b>Cuadro de Asistencia<br><span style="font-size: 18px"><b>Sucursal: <?=nameArea($s)?><br><?=$nombre_mes?> - <?=$nombreGestion?></b></span></center></h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed table-striped  table-sm table-secondary" style="width:100%">
                <thead>                              
                  <tr style="background:#5d6b89;color:white;">
                    <th width="2%"><small><b>Nro</b></small></th>
                    <th width="15%"><small><b>Apellidos y Nombres</b></small></th>
                    <th width="10%"><small><b>Cargo</b></small></th>
                    <th width="5%"><small><b>Dias<br>Normales<br>L_S</b></small></th>
                    <th width="5%"><small><b>Faltas</b></small></th>
                    <th width="6%"><small><b>Fecha Falta</b></small></th>
                    <th width="5%"><small><b>Bajas Medicas</b></small></th>
                    <th width="5%"><small><b>Dias Vacacion</b></small></th>
                    <th width="5%"><small><b>N° Domingos</b></small></th>
                    <th width="6%"><small><b>F. Dom</b></small></th>
                    <th width="5%"><small><b>N° Feriados</b></small></th>
                    <th width="6%"><small><b>F Feria</b></small></th>
                    <th width="5%"><small><b>Horas Extras</b></small></th>
                    <th width="5%"><small><b>Noches</b></small></th>
                    <th><small><b>Observaciones (Reintregro, Domingos y/o horas extras no pagadas)</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                  $sql="SELECT p.codigo,p.primer_nombre,p.paterno,p.materno,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo
                    from personal p 
                    where p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 and p.cod_area=$s
                    order by p.turno,p.paterno";
                  // echo $sql;
                  $stmtDet = $dbh->prepare($sql);
                  $stmtDet->execute();
                  while ($row = $stmtDet->fetch(PDO::FETCH_ASSOC)) {
                    $index++;
                    $codigo_personal=$row['codigo'];
                    $primer_nombre=$row['primer_nombre'];
                    $paterno=$row['paterno'];
                    $materno=$row['materno'];
                    $cargo=$row['cargo'];
                    $personal=$paterno." ".$materno." ".$primer_nombre;
                    
                    $datosAsistencia=obtenerDatosAsistenciaPersonal($codigo,$codigo_personal,$dias_trabajado);
                    $dias_normales=$datosAsistencia[0];
                    $faltas=$datosAsistencia[1];
                    $fecha_faltas=$datosAsistencia[2];
                    $bajas_medicas=$datosAsistencia[3];
                    $dias_vacacion=$datosAsistencia[4];
                    $domingos=$datosAsistencia[5];
                    $fecha_domingos=$datosAsistencia[6];
                    $feriados=$datosAsistencia[7];
                    $fecha_feriados=$datosAsistencia[8];
                    $horas_extras=$datosAsistencia[9];
                    $noches=$datosAsistencia[10];
                    $observaciones=$datosAsistencia[11];

                    $estilo_input="style='width:40px !important;text-align: right; color:000' data-toggle='tooltip'";
                    $estilo_glosa="style='width:120px !important;height:40px !important;text-align: right;color:000;font-size:12px;' data-toggle='tooltip' ";
                    ?>
                    <tr>
                      <td class="text text-left"><small><?=$index?></small></td>
                      <td class="text text-left"><small><?=$personal?></small></td>
                      <td class="text text-left"><small><?=$cargo?></small></td>
                      <td class="text text-center" style="background:#5d6b89;color:white;"><small><?=$dias_normales?></small></td> 
                      <td><?=$faltas?></td>
                      <td><?=$fecha_faltas?></td>
                      <td><?=$bajas_medicas?></td>
                      <td><?=$dias_vacacion?></td>
                      <td><?=$domingos?></td>
                      <td><?=$fecha_domingos?></td>
                      <td><?=$feriados?></td>
                      <td><?=$fecha_feriados?></td>
                      <td><?=$horas_extras?></td>
                      <td><?=$noches?></td>
                      <td><?=$observaciones?></td>
                    </tr>
                    <?php
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer fixed-bottom">
            <?php
            if(!isset($_GET['t'])){?>
              <a href="../index.php?opcion=asistenciaPersonalLista&q=<?=$q?>&s=<?=$s?>" class="btn btn-danger btn-round">Volver</a>
            <?php }
            ?>
            
          </div>
        </div>
        </form>
      </div>
    </div>  
  </div>
</div>

