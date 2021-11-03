

<meta charset="utf-8">
<?php
$sw_excel=0;
header("Pragma: public");
header("Expires: 0");
$fecha_actual=date('Ymd');
$filename = "Plantilla_bonosGlobal_".$fecha_actual.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

require_once '../styles.php';
require_once '../functions.php';
require("../conexion.php");

$dbh = new Conexion();
$sql="SELECT p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal
	FROM personal p
	where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1
	order by p.paterno";
?>
<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th><small><b>Codigo Personal</b></small></th>
      <th><small><b>Area</b></small></th>
      <th><small><b>CI</b></small></th>
      <th><small><b>Apellidos y Nombres</b></small></th>
    <?php
      $stmt = $dbh->prepare("SELECT codigo,nombre from descuentos where cod_estadoreferencial=1 and tipo_descuento=1 order by codigo");
  		$stmt->execute();
  		$stmt->bindColumn('codigo', $codigo_descuento);
  		$stmt->bindColumn('nombre', $nombre_descuento);
  		$contador=0;
  		while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
  			<th><small><b><?=$nombre_descuento?></b></small></th><?php   
  			$contador++;
  		}
      ?>
    </tr>
    <tr>
  </thead>
  <tbody>
    <?php
    $resp=mysqli_query($dbh,$sql);
    while($row=mysqli_fetch_array($resp)){ 
        $codigo=$row['codigo'];
        $areas=$row['areas'];
        $identificacion=$row['identificacion'];
        $personal=$row['personal'];
        ?>
        <tr>
          <td class="text text-left"><small><?=$codigo?></small></td>
          <td class="text text-left"><small><?=$areas?></small></td>
          <td class="text text-left"><small><?=$identificacion?></small></td>
          <td class="text text-left"><small><?=$personal?></small></td>
          <td class="text text-left" colspan="<?=$contador?>"><small></small></td>
        </tr>
      <?php } ?>
  </tbody>
</table>
