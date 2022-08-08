<?php
require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];
$dbh = new Conexion();

$sql_add=" where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 ";
if(isset($_GET['cf'])){
  $codigo_personal_x=$_GET['cf'];
  $sql_add=" where p.codigo in ($codigo_personal_x)";
}

$stmt = $dbh->prepare("SELECT p.codigo,p.cod_unidadorganizacional,(select a.nombre from areas a where a.codigo=p.cod_area) as areas,p.turno,p.paterno,p.materno,p.primer_nombre,date_format(p.ing_planilla,'%d/%m/%Y') as fecha_ingreso,p.ing_planilla,(select pr.fecha_retiro from personal_retiros pr where pr.cod_personal=p.codigo and pr.cod_estadoreferencial=1) as fecha_retiro
from personal p 
$sql_add
order by p.cod_unidadorganizacional,areas,p.turno,p.paterno");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('areas', $areas);
$stmt->bindColumn('turno', $turno);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('fecha_ingreso', $fecha_ingreso);
$stmt->bindColumn('ing_planilla', $ing_planilla);

$stmt->bindColumn('fecha_retiro', $fecha_retiro);

$fecha_actual=date('Y-m-d');

// $fecha_actual

$stmtEscalas = $dbh->prepare("SELECT anios_inicio,anios_final,dias_vacacion from escalas_vacaciones where cod_estadoreferencial=1");
$stmtEscalas->execute();
$stmtEscalas->bindColumn('anios_inicio', $anios_inicio);
$stmtEscalas->bindColumn('anios_final', $anios_final);
$stmtEscalas->bindColumn('dias_vacacion', $dias_vacacion);  
$i=0;
while ($rowEscalas = $stmtEscalas->fetch(PDO::FETCH_ASSOC))
{
  $array_escalas[$i]=$anios_inicio.",".$anios_final.",".$dias_vacacion;
  $i++;
}    
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-rose card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title">Vacaciones Personal</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator100" class="table table-condensed table-bordered">
                <thead>
                  <tr  class='bg-dark text-white'>
                    <th class="text-left">#</th>
                    <th class="text-center">Area/Sucursal</th>
                    <th class="text-center">Personal</th>
                    <th class="text-center">F. Ingreso</th>
                    <th class="text-center">Días V.</th>
                    <th class="text-center">Días Ut.</th>
                    <th class="text-center">Saldo</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $index = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      if($fecha_retiro<>"" || $fecha_retiro<> null){
                        $fecha_actual= $fecha_retiro;
                      }

                      $turno_nombre="";
                      switch ($turno) {
                        case 1:
                          $turno_nombre="TM";
                          break;
                        case 2:
                          $turno_nombre="TT";
                          break;
                        case 3:
                          $turno_nombre="";
                          break;
                      }
                      $datos_array=explode('#',obtenerDiasVacacion($ing_planilla,$fecha_actual,$array_escalas));
                      $total_dias_vacacion=$datos_array[0];
                      $diferencia_mes_sobrante=$datos_array[1];
                      $diferencia_dias_sobrante=$datos_array[2];
                      $dias_uzadas=obtenerDiasVacacionUzadas($codigo,-100);
                      $saldo_vacacion=$total_dias_vacacion-$dias_uzadas;
                     ?>
                    <tr>
                      <td class="text-center"><?=$index; ?></td>
                      <td class="text-left"><?=$areas; ?> <?=$turno_nombre?></td>
                      <td class="text-left"><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?></td>
                      <td class="text-center"><?=$fecha_ingreso;?></td>
                      <td class="text-center"><?=$total_dias_vacacion;?></td>
                      <td class="text-center"><?=$dias_uzadas; ?></td>
                      <td class="text-center"><?=$saldo_vacacion; ?></td>
                      <td class="td-actions">
                        <a href='index.php?opcion=vacaciones_detalle&codigo=<?=$codigo;?>&ing_planilla=<?=$ing_planilla?>&fecha_actual=<?=$fecha_actual?>'  rel="tooltip" class="btn btn-info btn-sm">
                            <i class="material-icons" style="color:black">visibility</i>
                          </a>
                      </td>
                    </tr>
                  <?php
                    $index++;
                   }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php if ($globalAdmin == 1) { ?>
          <!-- <div class="card-footer fixed-bottom">
            <button class="btn btn-success" onClick="location.href='index.php?opcion=vacacionesPersonalForm'">Registrar Permiso x Vacación</button>
            <a class="btn btn-warning" href="vacaciones_permisos/vacaciones_historico.php" target="_blank">Histórico Permiso x Vacación</a>
          </div> -->
        <?php } ?>
      </div>
    </div>
  </div>
</div>