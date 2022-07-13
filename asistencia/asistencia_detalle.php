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

$sql="SELECT codigo from asistencia_personal where cod_estado<>2 and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_sucursal in ($s) limit 1";
$stmtVerif=$dbh->prepare($sql);
$stmtVerif->execute();
$estado_asistencia=0;
while ($rowVerif = $stmtVerif->fetch(PDO::FETCH_ASSOC)) {
  $estado_asistencia=$rowVerif['codigo'];
}
//validacion
if($estado_asistencia<>0 and $codigo==0){ // registrar planilla mes 
  require_once '../functionsGeneral.php';
  require_once '../layouts/bodylogin2.php';
  ?>
  <script type="text/javascript">
    Swal.fire({
      title: 'A ocurrido un error :(',
      text: "La Asistencia de este mes ya fue Generada.",
      type: 'warning',
      confirmButtonClass: 'btn btn-warning',
      confirmButtonText: 'Aceptar',
      buttonsStyling: false
      }).then((result) => {
        if (result.value) {
          window.open('../index.php?opcion=asistenciaPersonalLista&q=<?=$q?>&s=<?=$s?>', '_blank'); 
          window.close();
          return(false);
        } 
      });
   </script><?php

}else{


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
        <form id="form1" class="form-horizontal" action="asistencia_save.php" method="post"  enctype="multipart/form-data">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/favicon.png">
            </div>
            <h3 class="card-title text-center"><center><br><b>Cuadro de Asistencia<br><span style="font-size: 18px"><b>Sucursal: <?=nameArea($s)?><br><?=$nombre_mes?> - <?=$nombreGestion?></b></span></center></h3>

            <input type="hidden" name="q" id="q" value="<?=$q?>">
            <input type="hidden" name="s" id="s" value="<?=$s?>">
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo?>">
            <input type="hidden" name="cod_mes" id="cod_mes" value="<?=$cod_mes?>">
            <input type="hidden" name="cod_gestion" id="cod_gestion" value="<?=$cod_gestion?>">
            <input type="hidden" name="dias_trabajado" id="dias_trabajado" value="<?=$dias_trabajado?>">

            <input type="hidden" name="contador_personal" id="contador_personal" value="0">
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
                    <th width="8%"><small><b>Fecha Falta</b></small></th>
                    <th width="5%"><small><b>Bajas Medicas</b></small></th>
                    <th width="5%"><small><b>Dias Vacacion</b></small></th>
                    <th width="5%"><small><b>N° Domingos</b></small></th>
                    <th width="8%"><small><b>F. Dom</b></small></th>
                    <th width="5%"><small><b>N° Feriados</b></small></th>
                    <th width="8%"><small><b>F Feria</b></small></th>
                    <th width="5%"><small><b>Horas Extras</b></small></th>
                    <th width="5%"><small><b>Noches</b></small></th>
                    <th><small><b>Observaciones (Reintregro, Domingos y/o horas extras no pagadas)</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                  $sql="SELECT p.codigo,p.primer_nombre,p.paterno,p.materno,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,p.turno
                    from personal p 
                    where p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 and p.cod_area=$s
                    order by p.turno,p.paterno";
                  // echo $sql;
                  $stmtDet = $dbh->prepare($sql);
                  $stmtDet->execute();
                  
                  $estilo_input="style='width:40px !important;text-align: right; color:000' data-toggle='tooltip'";
                  $estilo_glosa="style='width:120px !important;height:40px !important;text-align: right;color:000;font-size:12px;' data-toggle='tooltip' ";
                  while ($row = $stmtDet->fetch(PDO::FETCH_ASSOC)) {
                    $index++;
                    $codigo_personal=$row['codigo'];
                    $primer_nombre=$row['primer_nombre'];
                    $paterno=$row['paterno'];
                    $materno=$row['materno'];
                    $cargo=$row['cargo'];
                    $turno=$row['turno'];
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
                    
                    if($turno==1){
                      $estilo_left="style='background:#5882d5;color:white;'";
                    }else{
                      $estilo_left="style='background:#5882d5;color:white;'";
                    }
                    
                    ?>
                    <tr>
                      <td class="text text-left" <?=$estilo_left?>><small><?=$index?></small></td>
                      <td class="text text-left" <?=$estilo_left?>><small><?=$personal?><input type="hidden" name="codigo_personal_<?=$index?>" id="codigo_personal_<?=$index?>" value="<?=$codigo_personal?>"></small></td>
                      <td class="text text-left" <?=$estilo_left?>><small><?=$cargo?></small></td>
                      <td class="text text-center" style="background:#5d6b89;color:white;"><small><?=$dias_normales?></small></td> 
                      <td><input type="text" name="faltas_<?=$index?>" value="<?=$faltas?>" <?=$estilo_input?> ></td>
                      <td><textarea name="fecha_faltas_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_faltas?></textarea></td>
                      <td><input type="text" name="bajas_medicas_<?=$index?>" value="<?=$bajas_medicas?>" <?=$estilo_input?>></td>
                      <td><input type="text" name="dias_vacacion_<?=$index?>" value="<?=$dias_vacacion?>" <?=$estilo_input?>></td>
                      <td><input type="text" name="domingos_<?=$index?>" value="<?=$domingos?>" <?=$estilo_input?>></td>
                      <td><textarea name="fecha_domingos_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_domingos?></textarea></td>
                      <td><input type="text" name="feriados_<?=$index?>" value="<?=$feriados?>" <?=$estilo_input?>></td>
                      <td><textarea name="fecha_feriados_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_feriados?></textarea></td>
                      <td><input type="text" name="horas_extras_<?=$index?>" value="<?=$horas_extras?>" <?=$estilo_input?>></td>
                      <td><input type="text" name="noches_<?=$index?>" value="<?=$noches?>" <?=$estilo_input?>></td>
                      <td><textarea name="observaciones_<?=$index?>" style='width:200px !important;height:40px !important;text-align:right;font-size:11px;'><?=$observaciones?></textarea></td>
                    </tr>
                    <?php
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer fixed-bottom">
            <button type="submit" class="btn btn-success btn-sm">Guardar</button>
            <a href="../index.php?opcion=asistenciaPersonalLista&q=<?=$q?>&s=<?=$s?>" class="btn btn-danger btn-sm">Volver</a>
          </div>
        </div>
        </form>
      </div>
    </div>  
  </div>
</div>

<?php
echo '<script type="text/javascript">
  document.getElementById("contador_personal").value='.$index.';
</script>';

} //cerramos validacion
?>
