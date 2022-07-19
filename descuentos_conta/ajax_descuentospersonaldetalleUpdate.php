<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

// $global_mes=$_SESSION["globalMes"];
// $globalNombreGestion=$_SESSION["globalNombreGestion"];
$global_mes=$_GET['mes'];
$globalNombreGestion=$_GET['gestion'];
$codigo_detalle=$_GET["codigo_detalle"];
$dbh = new Conexion();
$query = "SELECT d.mes,d.gestion,d.monto,d.cod_estado from descuentos_conta_detalle_mes d where d.cod_descuento_detalle=$codigo_detalle
    order by d.gestion,d.mes";
$stmt = $dbh->query($query);
$array_descuentos_mes=[];
$i=0;
while ($row = $stmt->fetch()){ 
  $mes=$row["mes"];
  $gestion=$row["gestion"];
  $monto=$row["monto"];
  $cod_estado=$row["cod_estado"];

  // $array_descuentos_mes_det['mes']=$mes;
  // $array_descuentos_mes_det['gestion']=$gestion;
  $array_descuentos_mes_det['monto']=$monto;
  $array_descuentos_mes_det['cod_estado']=$cod_estado;

  $array_descuentos_mes[$mes.$gestion]=$array_descuentos_mes_det;
}

$gestionsiguiente=(int)$globalNombreGestion+1;
$array_mes[1]=array(1,"Enero",$globalNombreGestion);
$array_mes[2]=array(2,"Febrero",$globalNombreGestion);
$array_mes[3]=array(3,"Marzo",$globalNombreGestion);
$array_mes[4]=array(4,"Abril",$globalNombreGestion);
$array_mes[5]=array(5,"Mayo",$globalNombreGestion);
$array_mes[6]=array(6,"Junio",$globalNombreGestion);
$array_mes[7]=array(7,"Julio",$globalNombreGestion);
$array_mes[8]=array(8,"Agosto",$globalNombreGestion);
$array_mes[9]=array(9,"Septiembre",$globalNombreGestion);
$array_mes[10]=array(10,"Octubre",$globalNombreGestion);
$array_mes[11]=array(11,"Noviembre",$globalNombreGestion);
$array_mes[12]=array(12,"Diciembre",$globalNombreGestion);
$array_mes[13]=array(12,"Enero ".$gestionsiguiente,$gestionsiguiente);
$contadorMes=count($array_mes);
$TotalMes=0;
for ($i=1; $i <=$contadorMes ; $i++) {
    $codigoMes=$array_mes[$i][0];
    $nombreMes=$array_mes[$i][1];
    $gestionMes=$array_mes[$i][2];
    $montoDescuento=0;
    if(isset($array_descuentos_mes[$codigoMes.$gestionMes])){
      $montoDescuento=$array_descuentos_mes[$codigoMes.$gestionMes]['monto'];
      $TotalMes+=$montoDescuento;
    }   
    ?>
    <tr>
      <td><small><?=$i?></small></td>
      <td class="text-left"><small><?=$nombreMes?></small></td>
      <td><small>
        <?php
        if($i>=$global_mes){?>
          <input type="number" step="0.01" id="monto_mesdescuento_<?=$i?>" name="monto_mesdescuento_<?=$i?>" value="<?=$montoDescuento?>" onkeyup="sumaDescuentosDetalleMes()">
        <?php }else{?>
          <input type="hidden" step="0.01" id="monto_mesdescuento_<?=$i?>" name="monto_mesdescuento_<?=$i?>" value="<?=$montoDescuento?>">
          <?=$montoDescuento?>
        <?php }
        ?></small></td>
    </tr>

<?php }
?>

<tr style="background:white;color:#cd6155;">
  <td><small>-</small></td>
  <td><small>TOTAL</small></td>
  <td><small><input type="number" step="0.01" id="sumaDescuentos_detallemes" name="sumaDescuentos_detallemes" value="<?=$TotalMes?>" readonly></small></td>
</tr>
