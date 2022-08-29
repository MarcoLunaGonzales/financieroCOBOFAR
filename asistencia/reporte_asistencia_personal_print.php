<?php

require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../conexion.php';
require_once 'reporte_asistencia_personal_print_aux.php';
$dbh = new Conexion();

if(isset($_POST['cod_personal'])){
  $cod_personal=$_POST['cod_personal'];
  $fechaInicio=$_POST['fecha_inicio'];
  $fechaFinal=$_POST['fecha_fin'];
  $sw_otrosistema=$_POST['sw_otrosistema'];
}else{
  $cod_personal=$_GET['cod_personal'];
  $fechaInicio=$_GET['fecha_inicio'];
  $fechaFinal=$_GET['fecha_fin'];
  $sw_otrosistema=0;
}

$sql="SELECT p.codigo,p.identificacion,p.primer_nombre,p.paterno,p.materno,a.nombre as area,p.turno,p.haber_basico,p.cod_area
from personal p join areas a on p.cod_area=a.codigo
where p.codigo in ($cod_personal)";
$stmtg = $dbh->prepare($sql);
$stmtg->execute();
while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
  $turno=$rowg['turno'];
  $area=$rowg['area'];
  $haber_basico=$rowg['haber_basico'];
  $cod_area=$rowg['cod_area'];
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
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Reporte de Marcación Por Persona<br><p style="font-size:15px">Fecha Inicio : <?=$fechaInicio?> Fecha fin : <?=$fechaFinal?> <br> Sucursal/Area : <?=$area?> <br> Personal : <?=$nombrePer?><br> Turno : Mañana <br> </p></h4>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
            <center>
            <table class='table table-bordered table-condensed' style='width:80% !important'>
              
              <?php
              echo obtenerDetalleAtrasosPersonal($cod_personal,$fechaInicio,$fechaFinal,$cod_area,0,$sw_otrosistema,$dbh);//marcacion de sucrusal que pertenece
              ?>
              <tr style='background:#808b96 !important;color:#000 !important;height:30px;'>
                <td colspan="11"><b>OTROS MARCADOS</b></td>
              </tr>
              <?php
              echo obtenerDetalleAtrasosPersonal($cod_personal,$fechaInicio,$fechaFinal,$cod_area,-1000,$sw_otrosistema,$dbh);//marcacion de otras sucursales
              ?>

            </table></center>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>


<!-- modal editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#A6F7C3 !important;color:#000 !important">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><b>Editar Horario</b><br>
          <input type="text" class="form-control" name="cabecera_e" id="cabecera_e" style="background:white;color:blue;text-align: center;" readonly="true"></h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="codigo_asistencia_e" id="codigo_asistencia_e" value="0">
        <input type="hidden" name="fecha_inicio_e" id="fecha_inicio_e" value="0">
        <input type="hidden" name="fecha_fin_e" id="fecha_fin_e" value="0">
        <input type="hidden" name="cod_personal_e" id="cod_personal_e" value="0">
        <div class="row">
          <label class="col-sm-3 col-form-label text-dark font-weight-bold"><small>Hora Ingreso</small></label>
          <div class="col-sm-3">
            <div class="form-group">              
              <input type="time" class="form-control" name="hora_ingreso_e" id="hora_ingreso_e" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-3 col-form-label text-dark font-weight-bold"><small>Hora Salida</small></label>
          <div class="col-sm-3">
            <div class="form-group" >              
              <input type="time" class="form-control" name="hora_salida_e" id="hora_salida_e" style="background-color:white">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info btn-sm" id="guardar_edit_ingreso_alm" name="guardar_edit_ingreso_alm" data-dismiss="modal">Guardar Cambios</button>
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> Cancelar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#guardar_edit_ingreso_alm').click(function(){
      
      var codigo_asistencia_e=document.getElementById("codigo_asistencia_e").value;
      var fecha_inicio=document.getElementById("fecha_inicio_e").value;
      var fecha_fin=document.getElementById("fecha_fin_e").value;

      var cod_personal=document.getElementById("cod_personal_e").value;
      var hora_ingreso_e=$('#hora_ingreso_e').val();
      var hora_salida_e=$('#hora_salida_e').val();
      if(hora_ingreso_e==null || hora_ingreso_e==0 || hora_ingreso_e=='' || hora_ingreso_e==' '){
        Swal.fire("Informativo!", "Por favor introduzca Hora Ingreso.", "warning");
       }else{
          // if(hora_salida_e==null || hora_salida_e==0 || hora_salida_e=='' || hora_salida_e==' '){
          //   Swal.fire("Informativo!", "Por favor introduzca Hora Salida.", "warning");
          // }else{
            guardar_edit_horario(codigo_asistencia_e,hora_ingreso_e,hora_salida_e,fecha_inicio,fecha_fin,cod_personal);    
          // }
        
       }      
    });    
  });
</script>