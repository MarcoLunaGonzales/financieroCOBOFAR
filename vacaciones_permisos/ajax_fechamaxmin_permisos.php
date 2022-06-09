<?php
require_once '../conexion.php';
// require_once 'configModule.php';
require_once '../functions.php';

$dbh = new Conexion();
//header('Content-Type: application/json');

$codigo_permiso = $_GET["codigo_permiso"];
$codigo_personal = $_GET['cod_personal'];
$fecha_actual=date('Y-m-d');

$sql="select dias_min,dias_max from tipos_permisos_personal where codigo=$codigo_permiso";
$stmt = $dbh->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch()){ 
    $dias_min=$row['dias_min'];
    $dias_max=$row['dias_max'];
}


if($codigo_permiso==7){//tipo permiso a cuenta de vacaciones
    // $sql="SELECT ing_planilla FROM personal where codigo=$codigo_personal";
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute();
    // while ($row = $stmt->fetch()){ 
    //     $ing_planilla=$row['ing_planilla'];
    // }
 
    // $stmtEscalas = $dbh->prepare("SELECT anios_inicio,anios_final,dias_vacacion from escalas_vacaciones where cod_estadoreferencial=1");
    // $stmtEscalas->execute();
    // $stmtEscalas->bindColumn('anios_inicio', $anios_inicio);
    // $stmtEscalas->bindColumn('anios_final', $anios_final);
    // $stmtEscalas->bindColumn('dias_vacacion', $dias_vacacion);  
    // $i=0;
    // while ($rowEscalas = $stmtEscalas->fetch(PDO::FETCH_ASSOC))
    // {
    //   $array_escalas[$i]=$anios_inicio.",".$anios_final.",".$dias_vacacion;
    //   $i++;
    // }
    // $datos_array=explode('#',obtenerDiasVacacion($ing_planilla,$fecha_actual,$array_escalas));
    // $total_dias_vacacion=$datos_array[0];
    // // $diferencia_mes_sobrante=$datos_array[1];
    // // $diferencia_dias_sobrante=$datos_array[2];
    // $dias_uzadas=obtenerDiasVacacionUzadas($codigo_personal,-100);
    // $saldo_vacacion=$total_dias_vacacion-$dias_uzadas;

}
?>


<?php
if($codigo_permiso == 1 or $codigo_permiso == 2 or $codigo_permiso == 3){ //paternidad, luto , matrimonio?>
    <div class="row" >
      <label class="col-sm-2 col-form-label" style="color:black;">Fecha de Acontecimiento *</label>
      <div class="col-sm-2">
        <div class="form-group">
          <input  type='date' class='form-control'  id='fecha_evento' name='fecha_evento' value="<?=$fecha_actual?>" required>
        </div>
      </div>
    </div>
<?php }
?>

<div class="row" >
  <label class="col-sm-2 col-form-label" style="color:black;">Inicio *</label>
  <div class="col-sm-2">
	<div class="form-group">
	  <input  type='date' class='form-control'  id='fecha_inicio' name='fecha_inicio' value="<?=$fecha_actual?>" required>
	</div>
  </div>
  <div class="col-sm-1">
	<div class="form-group">
	  <input  type='time' class='form-control'  id='hora_inicio' name='hora_inicio' value="06:00" required>
	</div>
  </div>

  <label class="col-sm-1 col-form-label" style="color:black;">Fin *</label>
  <div class="col-sm-2">
	<div class="form-group">
	  <input  type='date' class='form-control'  id='fecha_final' name='fecha_final' value="<?=$fecha_actual?>" required>
	</div>
  </div>
  <div class="col-sm-1">
	<div class="form-group">
	  <input  type='time' class='form-control'  id='hora_final' name='hora_final' value="06:00"  required >
	</div>
  </div>
  <div class="col-sm-1">
	<div class="form-group">
	  <button  title="Calcular días Permiso" class="btn btn-success btn-sm btn-round btn-fab" onclick="ajaxCalcularDiasPermiso();return false;"> <i class="material-icons" style="color:black">cached</i></button>
	</div>
  </div>
  <div class="col-sm-2">
	<div class="form-group" id="div_comentario_permisos_obtenidos">
		<input  type='text' class='form-control' readonly="true" style="background: white;color:green;font-size: 18px;"  value="Total días solicitadas: 0">
		<input  type='hidden' id="dias_solicitadas" name="dias_solicitadas" value="0">
	</div>
  </div>
</div>
