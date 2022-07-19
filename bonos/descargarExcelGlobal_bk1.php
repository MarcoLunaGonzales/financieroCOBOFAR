<?php
//Antes de modulo descuentos 19/07/2022

session_start();
require_once '../functions.php';
require_once("../conexion.php");


$dbh = new Conexion();

$codGestionActiva=$_SESSION['globalGestion'];
$cod_mes=$_SESSION['globalMes'];
$nombreGestion=$_SESSION['globalNombreGestion'];
$nombre_mes=nombreMes($cod_mes);
$estado_planilla=0;
$sql="SELECT cod_estadoplanilla from planillas where cod_mes=$cod_mes and cod_gestion=$codGestionActiva";
// echo $sql; 
$stmtVerifPlani=$dbh->prepare($sql);
$stmtVerifPlani->execute();
$estado_planilla=0;
while ($rowVerifPlani = $stmtVerifPlani->fetch(PDO::FETCH_ASSOC)) {
  $estado_planilla=$rowVerifPlani['cod_estadoplanilla'];
}
if($estado_planilla==0){ // registrar plaanilla mes 
  require_once '../functionsGeneral.php';
  require_once '../layouts/bodylogin2.php';
  ?>
  <script type="text/javascript">
    Swal.fire({
        title: 'A ocurrido un error :(',
        text: "Por favor, Registrar la PLANILLA del mes en curso.",
        type: 'warning',
        confirmButtonClass: 'btn btn-warning',
        confirmButtonText: 'Aceptar',
        buttonsStyling: false
        }).then((result) => {
          if (result.value) {
            window.close();
            return(false);
          } 
        });
   </script><?php
}else{
  if($estado_planilla==3){//planilla cerrada 
    require_once '../functionsGeneral.php';
    require_once '../layouts/bodylogin2.php';
    ?>
    <script type="text/javascript">
      Swal.fire({
        title: 'LO SIENTO :("',
        text: "La Planilla No se encuentra disponible.",
        type: 'error',
        confirmButtonClass: 'btn btn-danger',
        confirmButtonText: 'Aceptar',
        buttonsStyling: false
        }).then((result) => {
          if (result.value) {
            window.close();
            return(false);
          } 
      });
     </script>
    <?php
  }else{ ?>
<meta charset="utf-8">
<?php
//echo "<br><br><br><br>";
header("Pragma: public");
header("Expires: 0");
$fecha_actual=date('dmY');
$filename = "Plantilla_Planillas_".$nombre_mes."_".$nombreGestion.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
$dias_trabajados_mes_x = obtenerValorConfiguracionPlanillas(22); //dias ingresados del mes

$cod_mes = str_pad($cod_mes, 2, "0", STR_PAD_LEFT);//
$fecha_inicio=$nombreGestion.'-'.$cod_mes.'-01';
$fecha_final=date('Y-m-t',strtotime($fecha_inicio));

$total_dias_mes=obtenerTotalDias_fechas($fecha_inicio,$fecha_final);
$total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio,$fecha_final);
$total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio,$fecha_final);

// $sql="SELECT p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.turno,p.cod_unidadorganizacional FROM personal p where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 order by p.cod_unidadorganizacional,2,p.turno,p.paterno";
$sql="SELECT p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.turno,p.cod_unidadorganizacional,p.ing_contr as fecha,1 as tipo
  FROM personal p
  where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 and p.ing_planilla<'$fecha_final'
  UNION
  select p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.turno,p.cod_unidadorganizacional,pr.fecha_retiro as fecha,2
  from personal p join personal_retiros pr on p.codigo=pr.cod_personal
  where pr.fecha_retiro BETWEEN '$fecha_inicio' and '$fecha_final'
  order by 6,2,5,4";
  // echo $sql;
?>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th><small><b>CODIGO</b></small></th>
      <th><small><b>CI</b></small></th>
      <th><small><b>Apellidos y Nombres</b></small></th>
      <th><small><b>SUCURSAL</b></small></th>
      <th><small><b>Faltas</b></small></th>
      <th><small><b>Permisos Sin Desc</b></small></th>
      <th><small><b>Dias Vac.</b></small></th>
      <th><small><b>Dias Trabajados L_S</b></small></th>
      <th><small><b>Domingos Normal</b></small></th>
      <th><small><b>Feriado Normal</b></small></th>
      <th><small><b>Noche Normal</b></small></th>
      <th><small><b>Domingo Reem</b></small></th>
      <th><small><b>Feriado Reem</b></small></th>
      <th><small><b>Ordinario Reem</b></small></th>
      <th><small><b>HXDOMINGO EXTRAS</b></small></th>
      <th><small><b>HXFERIADO EXTRAS</b></small></th>
      <th><small><b>HXDNORMAL EXTRAS</b></small></th>
      <th><small><b>REINTEGRO (BS)</b></small></th>
      <th><small><b>OBS REINTEGRO (BS)</b></small></th>
      <th><small><b>ANTICIPOS</b></small></th>
    <?php
      $sqldes="SELECT codigo,nombre from descuentos where cod_estadoreferencial=1 and tipo_descuento=1 order by codigo";
      $contador=0;
      $stmtDes = $dbh->prepare($sqldes);
      $stmtDes->execute();
      while ($row1 = $stmtDes->fetch(PDO::FETCH_ASSOC)) { 
        $nombre_descuento=$row1['nombre'];
        ?>
        <th><small><b><?=$nombre_descuento?></b></small></th><?php   
        $contador++;
      }
      ?>
    </tr>
    
  </thead>
  <tbody>
    <?php
    $stmtDet = $dbh->prepare($sql);
    $stmtDet->execute();
    while ($row = $stmtDet->fetch(PDO::FETCH_ASSOC)) { 
      $codigo=$row['codigo'];
      $areas=$row['areas'];
      $identificacion=$row['identificacion'];
      $personal=$row['personal'];
      $turno=$row['turno'];
      $cod_unidadorganizacional=$row['cod_unidadorganizacional'];
      $fecha=$row['fecha']; //fecha ingreso o retiro
      $tipo=$row['tipo'];//1 ingreso 2 retiro
      if($tipo==1 and $fecha>$fecha_inicio){//ingreso durante el mes
        $fecha_inicio_x=$fecha;
        $fecha_final_x=date('Y-m-t',strtotime($fecha_inicio));
        $total_dias_mes=obtenerTotalDias_fechas($fecha_inicio_x,$fecha_final_x);
        $total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio_x,$fecha_final_x);
        $total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio_x,$fecha_final_x);
        $dias_trabajados_mes=$total_dias_mes-$total_domingos_mes-$total_feriados_mes;
      }elseif($tipo==2){//se retiro durante el mes
        $fecha_inicio_x=$nombreGestion.'-'.$cod_mes.'-01';
        $fecha_final_x=$fecha;
        $total_dias_mes=obtenerTotalDias_fechas($fecha_inicio_x,$fecha_final_x);
        $total_domingos_mes=obtenerTotaldomingos_fechas($fecha_inicio_x,$fecha_final_x);
        $total_feriados_mes=obtenerTotalferiados_fechas($fecha_inicio_x,$fecha_final_x);
        $dias_trabajados_mes=$total_dias_mes-$total_domingos_mes-$total_feriados_mes;
      }else{
        $dias_trabajados_mes=$dias_trabajados_mes_x;
      }
      // $dias_trabajados_mes=$total_dias_mes-$total_domingos_mes-$total_feriados_mes;
      if($cod_unidadorganizacional!=1){
        if($turno==1){
          $areas=$areas." TM";
        }else{
          $areas=$areas." TT";
        }
        $datosAsistencia=obtenerDatosAsistenciaPersonal_planilla($cod_mes,$nombreGestion,$codigo);
        $faltas=$datosAsistencia[0];
        $permisos_sin_desc=$datosAsistencia[1];
        $dias_vacacion=$datosAsistencia[2];
        $domingos_normal=$datosAsistencia[3];
        $feriado_normal=$datosAsistencia[4];
        $noche_normal=$datosAsistencia[6];
        $domingo_reemplazo=0;
        $feriado_reemplazo=0;
        $ordinario_reemplazo=0;
        $hxdomingo_extras=0;
        $hxferiado_extras=0;
        $hxdnnormal_extras=$datosAsistencia[5];
        $reintegro=0;
        $reintegro_obs="";
        $anticipos=0;
      }else{//oficina central
        // $datosAsistencia=obtenerDatosAsistenciaPersonal_planilla($cod_mes,$nombreGestion,$codigo);
        // $faltas=$datosAsistencia[0];
        // $permisos_sin_desc=$datosAsistencia[1];
        // $dias_vacacion=$datosAsistencia[2];
        // $domingos_normal=$datosAsistencia[3];
        // $feriado_normal=$datosAsistencia[4];
        // $noche_normal=$datosAsistencia[6];
        $faltas=0;
        $permisos_sin_desc=0;
        $dias_vacacion=0;
        $domingos_normal=0;
        $feriado_normal=0;
        $noche_normal=0;
        $domingo_reemplazo=0;
        $feriado_reemplazo=0;
        $ordinario_reemplazo=0;
        $hxdomingo_extras=0;
        $hxferiado_extras=0;
        $hxdnnormal_extras=0;
        $reintegro=0;
        $reintegro_obs="";
        $anticipos=0;
      }
      ?>
      <tr>
        <td class="text text-left"><small><?=$codigo?></small></td>
        <td class="text text-left"><small><?=$identificacion?></small></td>
        <td class="text text-left"><small><?=$personal?></small></td>
        <td class="text text-left"><small><?=$areas?></small></td>
        <td class="text text-left"><small><?=$faltas?></small></td>
        <td class="text text-left"><small><?=$permisos_sin_desc?></small></td>
        <td class="text text-left"><small><?=$dias_vacacion?></small></td>
        <td class="text text-left"><small><?=$dias_trabajados_mes?></small></td>
        <td class="text text-left"><small><?=$domingos_normal?></small></td>
        <td class="text text-left"><small><?=$feriado_normal?></small></td>
        <td class="text text-left"><small><?=$noche_normal?></small></td>
        <td class="text text-left"><small><?=$domingo_reemplazo?></small></td>
        <td class="text text-left"><small><?=$feriado_reemplazo?></small></td>
        <td class="text text-left"><small><?=$ordinario_reemplazo?></small></td>
        <td class="text text-left"><small><?=$hxdomingo_extras?></small></td>
        <td class="text text-left"><small><?=$hxferiado_extras?></small></td>
        <td class="text text-left"><small><?=$hxdnnormal_extras?></small></td>
        <td class="text text-left"><small><?=$reintegro?></small></td>
        <td class="text text-left"><small><?=$reintegro_obs?></small></td>
        <td class="text text-left"><small><?=$anticipos?></small></td><?php
        for ($x=0; $x <$contador ; $x++) { ?>
          <td class="text text-left"><small></small></td>
        <?php }
        ?>
      </tr>
      <?php } ?>
  </tbody>
</table>


<?php


  }
}


?>