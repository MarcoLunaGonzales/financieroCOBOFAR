<?php

require_once '../functions.php';
require("../conexion_comercial2.php");

$codigo_salida=$_GET['codigo_salida'];

$sql_detalle="select s.cod_material, m.descripcion_material, s.lote, s.fecha_vencimiento, 
    s.cantidad_unitaria, s.precio_unitario, s.`descuento_unitario`, s.`monto_unitario` 
    from salida_detalle_almacenes s, material_apoyo m
    where s.cod_salida_almacen='$codigo_salida' and s.cod_material=m.codigo_material";
// echo "<br><br><br>".$sql;
$sum_cantidad=0;
$sum_monto=0;
$index=0;

$resp_detalle=mysqli_query($enlaceCon,$sql_detalle);

while($dat_detalle=mysqli_fetch_array($resp_detalle))
{
  $cod_material=$dat_detalle[0];
  $nombre_material=$dat_detalle[1];
  $loteProducto=$dat_detalle[2];
  $fechaVencimiento=$dat_detalle[3];
  $cantidad_unitaria=number_format($dat_detalle[4],0,'.','');
  // $precioUnitario=$dat_detalle[5];
  // $precioUnitario=redondear2($precioUnitario);
  // $descuentoUnitario=$dat_detalle[6];
  // $descuentoUnitario=redondear2($descuentoUnitario);
  $montoUnitario=$dat_detalle[7];
  $montoUnitario=redondearDecimal($montoUnitario);

  $index++;

  $sum_cantidad+=$CANTIDAD;
  $sum_monto+=$montoUnitario;
  ?>
  <tr>
    <td><small><?=$index?></small></td>
    <td><small><?=$codigo_salida?></small></td>
    <td><small><?=$cod_material?></small></td>
    <td><small><?=$nombre_material?></small></td>
    <td><small><?=$cantidad_unitaria?></small></td>
    <td><small><?=number_format($montoUnitario,2)?></small></td>
  </tr>
  <?php
}
?>
 <tr>
    <td style="color:blue;"><small></small></td>
    <td style="color:blue;"><small></small></td>
    <td style="color:blue;"><small></small></td>
    <td style="color:blue;"><small>TOTAL</small></td>
    <td style="color:blue;"><small><?=$sum_cantidad?></small></td>
    <td style="color:blue;"><small><?=number_format(round($sum_monto,1, PHP_ROUND_HALF_UP),2)?></small></td>
  </tr>