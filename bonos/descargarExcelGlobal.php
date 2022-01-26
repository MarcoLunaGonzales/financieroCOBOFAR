
<meta charset="utf-8">
<?php
//echo "<br><br><br><br>";
header("Pragma: public");
header("Expires: 0");
$fecha_actual=date('Ymd');
$filename = "Plantilla_Planillas_".$fecha_actual.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

// require_once '../styles.php';
// require_once '../functions.php';
require_once("../conexion.php");

$dbh = new Conexion();
$sql="SELECT p.codigo,(select a.nombre from areas a where a.codigo=p.cod_area)as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.turno,p.cod_unidadorganizacional
  FROM personal p
  where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1
  order by p.cod_unidadorganizacional,2,p.turno,p.paterno";
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
      <!-- <th><small><b>COMISION VENTAS (BS)</b></small></th> -->
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
      <!-- <th><small><b>APORTE SIND (BS)</b></small></th> -->
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
        if($cod_unidadorganizacional!=1){
          if($turno==1){
            $areas=$areas." TM";
          }else{
            $areas=$areas." TT";
          }
        }
        ?>
        <tr>
          <td class="text text-left"><small><?=$codigo?></small></td>
          <td class="text text-left"><small><?=$identificacion?></small></td>
          <td class="text text-left"><small><?=$personal?></small></td>
          <td class="text text-left"><small><?=$areas?></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td>
          <td class="text text-left"><small></small></td><?php
          for ($x=0; $x <$contador ; $x++) { ?>
            <td class="text text-left"><small></small></td>
          <?php }
          ?>
          <!-- <td class="text text-left"><small></small></td> -->
        </tr>
      <?php } ?>
  </tbody>
</table>
