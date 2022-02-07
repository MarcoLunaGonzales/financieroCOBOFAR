
<?php

require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();

echo "<div class='content'>
  <div class='container-fluid'>
    <div class='row'>
    <div class='col-md-12'>
      <div class='card'>";

echo "<div class='card-header $colorCard card-header-icon'>
  <h4 class='card-title'> <img  class='card-img-top'  src='../assets/img/favicon.png' style='width:100%; max-width:50px;'>Historico  Permiso por Vacación</h4>
</div>";

echo "<div class='card-body'>";   
echo "<div class='table-responsive'>";
echo "<table class='table table-condensed table-bordered' id='tablePaginatorReport_facturasgeneradas'>";
echo "<thead><tr class='bg-info text-white'>
  <th width='2%'>#</th><th width='6%'>Area/sucursal</th><th width='15%'>Personal</th><th width='5%'>F.Inicial</th><th width='5%'>F.Final</th><th>Motivo</th>
  <th width='2%'>Dias Vacacion</th><th width='5%'>Fecha Solicitada</th></tr></thead> <tbody>";
$consulta = "SELECT p.primer_nombre,p.paterno,p.materno,(select a.nombre from areas a where a.codigo=p.cod_area) as areas,p.turno,DATE_FORMAT(pv.fecha_inicial,'%d/%m/%Y')as fecha_inicial,DATE_FORMAT(pv.fecha_final,'%d/%m/%Y')as fecha_final,pv.observaciones,pv.dias_vacacion,DATE_FORMAT(pv.created_at,'%d/%m/%Y')as created_at,(select CONCAT_WS(' ',pp.primer_nombre,pp.paterno,pp.materno) from personal pp where pp.codigo=pv.created_by)as created_by
from personal_vacaciones pv join personal p on pv.cod_personal=p.codigo
where pv.cod_estadoreferencial=1 order by pv.codigo";
$stmt = $dbh->prepare($consulta);
$stmt->execute();
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('fecha_inicial', $fecha_inicial);
$stmt->bindColumn('fecha_final', $fecha_final);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('dias_vacacion', $dias_vacacion);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('areas', $areas);
$stmt->bindColumn('turno', $turno);
$index=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
  $index++;
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
    echo "<tr>";
    echo "<td>$index</td>";
    echo "<td class='text-left'>$areas $turno_nombre</td>";

    echo "<td class='text-left'>$paterno $materno $primer_nombre</td>";
    echo "<td class='text-center'>$fecha_inicial</td>
          <td class='text-center'>$fecha_final</td>";
    echo "<td class='text-left'><b>$observaciones</b></td>";
    echo "<td class='text-right'>$dias_vacacion</td>
          <td title='Generado por : $created_by'>$created_at</td>";
  echo "</tr>";
}
echo " </tbody></table>";
echo "</div>";
echo "</div>";


echo "</div>
    </div>
  </div>
  </div>
</div>";


?>




