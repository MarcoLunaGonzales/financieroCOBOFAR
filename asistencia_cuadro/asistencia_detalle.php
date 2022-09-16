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
  // $cod_mes=date('m');

  $cod_mes=$_GET['cod_mes'];
  $cod_gestion=$_GET['cod_gestion'];
  // $cod_mes=7;//obtener el ultimo procesado
  // $cod_gestion=date('Y');
  //verificar si está el proceso corrido hasta el ultimo dia del mes
  $mes_valido=true;
}else{
  $cod_mes=$_GET['cod_mes'];
  $cod_gestion=$_GET['cod_gestion'];
  $mes_valido=true;
}

$sql="SELECT codigo from asistencia_personal where cod_estado<>2 and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_sucursal in ($s) limit 1";
$stmtVerif=$dbh->prepare($sql);
$stmtVerif->execute();
$estado_asistencia=0;
while ($rowVerif = $stmtVerif->fetch(PDO::FETCH_ASSOC)) {
  $estado_asistencia=$rowVerif['codigo'];
}
$sw_validado=true;
//validación
if(!$mes_valido){//validamos  si se corrio el proceso hasta el ultimo día
  require_once '../functionsGeneral.php';
  require_once '../layouts/bodylogin2.php';
  $sw_validado=false;
  ?>
  <script type="text/javascript">
    Swal.fire({
      title: 'A ocurrido un error :(',
      text: "La Asistencia de este mes todavía no fue procesada",
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
}
if($estado_asistencia<>0 and $codigo==0){// registrar planilla mes 
  require_once '../functionsGeneral.php';
  require_once '../layouts/bodylogin2.php';
  $sw_validado=false;
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
}
if($sw_validado){
  ?>

  <?php
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
              <div class="card-icon " style="background:#138d75;">
                <img class="" width="40" height="40" src="../assets/img/favicon.png">
              </div>
              <h4 class="card-title" style="color:#138d75;"><b>CUADRO DE ASISTENCIA<br>Sucursal: <?=nameArea($s)?> / <?=$nombre_mes?> - <?=$nombreGestion?></B></span></h4>
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
                <table id="tablePaginatorHeaderFooterColStatic" class="table table-bordered table-condensed table-striped  table-sm table-secondary">
                  <thead>                              
                    <tr style="background:#138d75;color:white;">
                      <th style="background:#64a095;color:white;"><small>Nro</small></th>
                      <th style="background:#64a095;color:white;"><small>Apellidos y Nombres</small></th>
                      <th style="background:#64a095;color:white;"><small>Cargo</small></th>
                      <th ><small>Dias<br>Normls L-S</small></th>
                      <th ><small>Faltas</small></th>
                      <th ><small>Fecha Falta</small></th>
                      <th ><small>Bajas Medicas</small></th>
                      <th ><small>F.Inicio</small></th>
                      <th ><small>F.Fin</small></th>
                      <th ><small>Dias Vacacion</small></th>
                      <th ><small>F.Inicio</small></th>
                      <th ><small>F.Fin</small></th>
                      <th ><small>Domingos Normal</small></th>
                      <th ><small>F. Dom</small></th>
                      <th ><small>Feriados Normal</small></th>
                      <th ><small>F Feria</small></th>
                      <th ><small>Noche Normal</small></th>
                      <!-- <th ><small>Horas Extras</small></th> -->
                      <th ><small>Domingo Reemp</small></th>
                      <th ><small>Feriado Reemp</small></th>
                      <th ><small>Reemp</small></th>
                      <th ><small>Tipo de Reemp</small></th>
                      <th ><small>Fecha</small></th>
                      <th ><small>Nombre de Personal que Reemp</small></th>
                      <th ><small>HXDOMINGO EXTRAS</small></th>
                      <th ><small>HXFERIADO EXTRAS</small></th>
                      <th ><small>HXDNORMAL EXTRAS</small></th>
                      <th><small>Observaciones (Reint, Domigs y/o hrs extras no pagadas)</small></th>
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
                      if($codigo==0){//obtener los datos del proceso de asistencia
                        $datosAsistencia=obtenerDatosAsistenciaPersonal($codigo,$codigo_personal,$dias_trabajado);
                      }else{
                        $datosAsistencia=obtenerDatosAsistenciaPersonal($codigo,$codigo_personal,$dias_trabajado);
                      }
                      $dias_normales=$datosAsistencia[0];
                      $faltas=$datosAsistencia[1];
                      $fecha_faltas=$datosAsistencia[2];
                      $bajas_medicas=$datosAsistencia[3];
                      $bajas_medicas_fi=$datosAsistencia[4];
                      $bajas_medicas_ff=$datosAsistencia[5];
                      $dias_vacacion=$datosAsistencia[6];
                      $dias_vacacion_fi=$datosAsistencia[7];
                      $dias_vacacion_ff=$datosAsistencia[8];
                      $domingos=$datosAsistencia[9];
                      $fecha_domingos=$datosAsistencia[10];
                      $feriados=$datosAsistencia[11];
                      $fecha_feriados=$datosAsistencia[12];
                      $noches=$datosAsistencia[13];
                      $domingo_reemp=$datosAsistencia[14];
                      $feriado_reemp=$datosAsistencia[15];
                      $reemp=$datosAsistencia[16];
                      $tipo_reemplazo=$datosAsistencia[17];
                      $fecha_reemp=$datosAsistencia[18];
                      $nombre_per_reemp=$datosAsistencia[19];
                      $horas_extras_dom=$datosAsistencia[20];
                      $horas_extras_feri=$datosAsistencia[21];
                      $horas_extras=$datosAsistencia[22];
                      $observaciones=$datosAsistencia[23];
                      // if($turno==1){
                      //   $estilo_left="style='background:#5882d5;color:white;'";
                      // }else{
                      //   $estilo_left="style='background:#5882d5;color:white;'";
                      // }
                      
                      ?>
                      <tr>
                        <td class="text text-left"><small><?=$index?></small></td>
                        <td class="text text-left" ><small><?=$personal?><input type="hidden" name="codigo_personal_<?=$index?>" id="codigo_personal_<?=$index?>" value="<?=$codigo_personal?>"></small></td>
                        <td class="text text-left" ><small><?=$cargo?></small></td>
                        <td class="text text-center"><small><?=$dias_normales?></small></td>
                        <td><input type="text" name="faltas_<?=$index?>" value="<?=$faltas?>" <?=$estilo_input?> ></td>
                        <td><textarea name="fecha_faltas_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_faltas?></textarea></td>
                        <td><input type="text" name="bajas_medicas_<?=$index?>" value="<?=$bajas_medicas?>" <?=$estilo_input?>></td>
                        <td><textarea name="bajas_medicas_fi_<?=$index?>" <?=$estilo_glosa?>><?=$bajas_medicas_fi?></textarea></td>
                        <td><textarea name="bajas_medicas_ff_<?=$index?>" <?=$estilo_glosa?>><?=$bajas_medicas_ff?></textarea></td>

                        <td><input type="text" name="dias_vacacion_<?=$index?>" value="<?=$dias_vacacion?>" <?=$estilo_input?>></td>
                        <td><textarea name="dias_vacacion_fi_<?=$index?>" <?=$estilo_glosa?>><?=$dias_vacacion_fi?></textarea></td>
                        <td><textarea name="dias_vacacion_ff_<?=$index?>" <?=$estilo_glosa?>><?=$dias_vacacion_ff?></textarea></td>

                        <td><input type="text" name="domingos_<?=$index?>" value="<?=$domingos?>" <?=$estilo_input?>></td>
                        <td><textarea name="fecha_domingos_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_domingos?></textarea></td>
                        <td><input type="text" name="feriados_<?=$index?>" value="<?=$feriados?>" <?=$estilo_input?>></td>
                        <td><textarea name="fecha_feriados_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_feriados?></textarea></td>
                        <td><input type="text" name="noches_<?=$index?>" value="<?=$noches?>" <?=$estilo_input?>></td>

                        <td><input type="text" name="domingo_reemp_<?=$index?>" value="<?=$domingo_reemp?>" <?=$estilo_input?>></td>
                        <td><input type="text" name="feriado_reemp_<?=$index?>" value="<?=$feriado_reemp?>" <?=$estilo_input?>></td>
                        
                        <td><input type="text" name="reemp_<?=$index?>" value="<?=$reemp?>" <?=$estilo_input?>></td>
                        <td><textarea name="tipo_reemplazo_<?=$index?>" <?=$estilo_glosa?>><?=$tipo_reemplazo?></textarea></td>

                        <td><textarea name="fecha_reemp_<?=$index?>" <?=$estilo_glosa?>><?=$fecha_reemp?></textarea></td>
                        <td><textarea name="nombre_per_reemp_<?=$index?>" <?=$estilo_glosa?>><?=$nombre_per_reemp?></textarea></td>

                        <td><input type="text" name="horas_extras_dom_<?=$index?>" value="<?=$horas_extras_dom?>" <?=$estilo_input?>></td>
                        <td><input type="text" name="horas_extras_feri_<?=$index?>" value="<?=$horas_extras_feri?>" <?=$estilo_input?>></td>
                        <td><input type="text" name="horas_extras_<?=$index?>" value="<?=$horas_extras?>" <?=$estilo_input?>></td>
                        <td><textarea name="observaciones_<?=$index?>" style='width:200px !important;height:40px !important;text-align:right;font-size:11px;'><?=$observaciones?></textarea></td>
                      </tr>
                      <?php
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer fixed-bottom">
              <button type="submit" class="btn btn-sm btn-round" style="background:#138d75;">Guardar</button>
              <a href="../index.php?opcion=asistenciaPersonalLista&q=<?=$q?>&s=<?=$s?>" class="btn btn-danger btn-sm btn-round">Volver</a>
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

