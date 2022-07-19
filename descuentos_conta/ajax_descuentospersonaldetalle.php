<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

$mesGlobal=$_GET['mes'];
$gestionGlobal=$_GET['gestion'];
$cod_personal=$_GET['cod_personal'];

$nombre_personal=$_GET['nombre_personal'];
$query = "SELECT dd.codigo,dd.cod_personal,dd.diferencia,dd.cod_tipodescuento,td.nombre as tipo_descuento,dd.glosa
    from descuentos_conta_detalle dd join descuentos_conta d on d.codigo=dd.cod_descuento join tipos_descuentos_conta td on dd.cod_tipodescuento=td.codigo
    where  d.cod_estado=3 and dd.cod_personal=$cod_personal
    order by dd.fecha";
  $stmt = $dbh->query($query);
?>
<?php
$index=1;
$totalDescuento=0;
$totalDescontado=0;
while ($row = $stmt->fetch()){
  $codigo=$row["codigo"];
  $tipo_descuento=$row["tipo_descuento"];
  $descuento=$row["diferencia"];
  $glosa=$row["glosa"];
  // $mes=$row["mes"];
  $descontado=0;
  $saldo=$descuento-$descontado;
  $totalDescuento+=$descuento;  
  $datos_modal=$codigo."###".$tipo_descuento."###".$descuento."###".$nombre_personal."###".$mesGlobal."###".$gestionGlobal;
  ?>
  <tr style="background:#ccd1d1">
    <td><small><?=$index?></small></td>
    <td class="text-left"><small><?=$tipo_descuento?></small></td>
    <td><small><?=$descuento?></small></td>
    <td><small><?=$descontado?></small></td>
    <td><small><?=$saldo?></small></td>
    <td class="text-left"><small><?=$glosa?></small></td>
    <td class="td-actions">
      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalDetalleDescuentosUpdate" onclick="modalDetalleDescuentosUpdate('<?=$datos_modal;?>')">
          <i class="material-icons" title="Ver Detalle">article</i>
      </button>
    </td>
  </tr>
<?php 
  //detalle de pago
  $queryDet = "SELECT mes,gestion,monto,cod_estado from descuentos_conta_detalle_mes where cod_descuento_detalle=$codigo order by gestion,mes";
  $stmtDet = $dbh->query($queryDet);  
  while ($rowDet = $stmtDet->fetch()){
    $mes=$rowDet["mes"];
    $gestion=$rowDet["gestion"];
    $montoDet=$rowDet["monto"];
    $cod_estadoDet=$rowDet["cod_estado"];
    $saldo=$saldo-$montoDet;
    $totalDescontado+=$montoDet;
    $glosaDet="Gestion : ".$gestion." - Mes : ".$mes;
    switch ($cod_estadoDet) {
      case 1://registrado
        $label="style='color:red;'";
      break;
      case 3://validado
        $label="style='color:blue;'";
        $glosaDet.=" (Validado)";
      break;
      case 4://aprobado
        $label="style='color:green;'";
        $glosaDet.=" (Aprobado)";
      break;
      case 5://Contabilizado
        $label="style='color:green;'";
        $glosaDet.=" (Contabilizado)";
      break;
    } 
    if($mes==$mesGlobal and $gestion==$gestionGlobal){
      $label="style='color:purple;'";
    }
    ?>
    <tr <?=$label?>>
      <td><small>-</small></td>
      <td class="text-left"><small>-</small></td>
      <td><small>-</small></td>
      <td><small><?=$montoDet?></small></td>
      <td><small><?=$saldo?></small></td>
      <td class="text-left"><small><?=$glosaDet?></small></td>
      <td class="td-actions">-</td>
    </tr> <?php 
  }
  $index++;
}
$totalSaldo=$totalDescuento-$totalDescontado;

?>
  
 <tr style="background:white; color: #45b39d">
    <td>-</td>
    <td colspan="1">TOTAL SALDO</td>              
    <td><?=formatNumberDec($totalDescuento,2)?></td>
    <td><?=formatNumberDec($totalDescontado,2)?></td>
    <td><?=formatNumberDec($totalSaldo,2)?></td>
    <td>-</td>
    <td>-</td>
  </tr>        

