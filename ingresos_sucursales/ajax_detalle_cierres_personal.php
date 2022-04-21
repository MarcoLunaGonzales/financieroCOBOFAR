<?php
require_once '../functions.php';

$fecha=$_GET['fecha'];
$cod_ciudad=$_GET['cod_ciudad'];
require("../conexion_comercial.php");
$cod_tiposalida_efectivo=1001;
$sql="SELECT s.cod_almacen,s.fecha,s.cod_chofer
from salida_almacenes s 
where s.`cod_tiposalida`= $cod_tiposalida_efectivo and s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a
where a.`cod_ciudad`='$cod_ciudad' and cod_tipoalmacen=1) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fecha 00:00:00' and '$fecha 23:59:59'  
GROUP BY s.cod_chofer,s.fecha order by s.fecha,s.cod_chofer";
 // echo "<br><br><br>".$sql;
$index=1;
$totalEfectivo=0;
$totalTarjetas=0;
$totalAnuladas=0;
$total_ventas=0;
$totaldepositar=0;
$totaldepositado=0;
//echo $sql;
$resp=mysqli_query($dbh,$sql);
while($row=mysqli_fetch_array($resp)){ 
  $fechaVenta=$row['fecha'];
  $cod_personal=$row['cod_chofer'];
  $cod_almacen_x=$row['cod_almacen'];
  $cod_ciudad=obtener_codciudad_almacen_nuevosis($cod_almacen_x,1);
  $montoefectivo=obtenerMonto_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
  // echo  $montoefectivo."----";
  $montoTarjeta=obtenerMontoTarjeta_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
  $montodolarstring=obtenerMontodolares_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
  $montodolarArray=explode("###",$montodolarstring);
  $monto_dolar=$montodolarArray[0];
  $monto_dolar_bs=$montodolarArray[1];


  $montoAnulada=obtenerMontoAnuladas_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
  $monto_depositado=obtenerMontodepositado_nuevosis($fechaVenta,$cod_personal);
  $monto_depositado_dolar=obtenerMontodepositado_dolar_nuevosis($fechaVenta,$cod_personal);
  $nro_deposito=obtenerNrodepositado_nuevosis($fechaVenta,$cod_personal);
  $monto_venta=$montoefectivo+$montoTarjeta-$montoAnulada-$monto_dolar_bs;
  $monto_depositar=$montoefectivo-$montoAnulada-$monto_dolar_bs;
  $totalEfectivo+=$montoefectivo; 
  $totalTarjetas+=$montoTarjeta;  
  $totalAnuladas+=$montoAnulada;
  $totaldepositar+=$monto_depositar;
  $totaldepositado+=$monto_depositado;
  $total_ventas+=$monto_venta;
  $personalCliente=nombrePersonal_nuevosis($cod_personal);




  if(number_format($monto_depositar,2,".",",") == number_format($monto_depositado,2,".",",")){//bolivianos
    $label_style_bs='style="background:#c5cae9"';
  }else{
    // echo $monto_depositar."-".$monto_depositado;
    $label_style_bs='style="background:#ff867f"';
  }

  if(number_format($monto_dolar,2,".",",") == number_format($monto_depositado_dolar,2,".",",")){//dolares
    $label_style_usd='style="background:#c5cae9"';
  }else{
    $label_style_usd='style="background:#ff867f"';
  }
  ?>
  <tr>
    <td class="text text-left"><small><?=$personalCliente?></small></td>
    <td class="text text-right"><small><?=number_format($montoefectivo,2,".",",")?></small></td>
    <td class="text text-right"><small><?=number_format($montoTarjeta,2,".",",")?></small></td>
    <td class="text text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
    <td class="text text-right"><small><?=number_format($montoAnulada,2,".",",")?></small></td>
    <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositar,2,".",",")?></small></td>
    <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
    <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositado,2,".",",");?></small></td>
    <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_depositado_dolar,2,".",",");?></small></td>
    <td class="text text-right"><small><?=$nro_deposito?></small></td>
    <td align='right'><small><?=number_format($monto_venta,2,".",",");?></small></td>
    <td  class="td-actions text-right"><a  target='_blank' href='http://10.10.1.11:8080/cobofar_comercial/rptArqueoDiarioPDF.php?rpt_territorio=<?=$cod_ciudad?>&fecha_ini=<?=$fechaVenta?>&fecha_fin=<?=$fechaVenta?>&hora_ini=00:00&hora_fin=23:59&variableAdmin=1&rpt_funcionario=<?=$cod_personal?>'  class="btn btn-dark"  >
        <i class="material-icons" title="Ver Detalle">list</i>
      </a>
    </td>
  </tr>
<?php } ?>
<tr>
  <th>Total:</th>
  <th align='right'><?=number_format($totalEfectivo,2,".",",")?></th>
  <th align='right'><?=number_format($totalTarjetas,2,".",",")?></th>
  <th align='right'><?=number_format(0,2,".",",")?></th>
  <th align='right'><?=number_format($totalAnuladas,2,".",",")?></th>
  <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($totaldepositar,2,".",",")?></th>
  <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format(0,2,".",",")?></th>
  <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($totaldepositado,2,".",",")?></th>
  <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format(0,2,".",",")?></th>
  <th></th>
  <th align='right'><?=number_format($total_ventas,2,".",",")?></th>
  <th align='right'></th>
</tr>