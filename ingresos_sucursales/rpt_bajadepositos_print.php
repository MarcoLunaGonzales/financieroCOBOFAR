
<?php
if (isset($_POST["check_rs_cierres"])) {
  $check_rs_cierres=$_POST["check_rs_cierres"]; 
  if($check_rs_cierres){
    ?>
    <meta charset="utf-8">
    <?php
    $sw_excel=0;
    header("Pragma: public");
    header("Expires: 0");
    $filename = "reporte_baja_depositos.xls";
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  }else{
    $sw_excel=1;
  }
}else{
  $sw_excel=1;
}

require_once '../styles.php';

if($sw_excel==1){
  require_once '../layouts/bodylogin2.php';
}
require_once '../functions.php';
require("../conexion_comercial.php");

$fechai=$_POST['fechainicio'];
$fechaf=$_POST['fechafin'];
$sucursal=$_POST['sucursal'];
$sucursalgString="";
foreach ($sucursal as $sucursali) {   
  $sucursalgString.=$sucursali.",";
}
$sucursalgString=trim($sucursalgString,",");



$cod_tiposalida_efectivo=1001;
$sql="SELECT s.cod_almacen,a.nombre_almacen,s.fecha,s.cod_chofer,(select CONCAT_WS(' ',f.nombres,f.paterno,f.materno) from funcionarios f where f.codigo_funcionario=s.cod_chofer) as personal 
from salida_almacenes s join almacenes a on s.cod_almacen=a.cod_almacen 
where s.`cod_tiposalida`= $cod_tiposalida_efectivo and a.cod_ciudad in (select c.cod_ciudad from  ciudades c where  c.cod_area in ($sucursalgString)) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fechai 00:00:00' and '$fechaf 23:59:59' and s.cod_tipopago=1 GROUP BY s.cod_chofer,s.cod_tipopago,s.fecha 
order by s.fecha,s.cod_almacen,s.cod_chofer";
 // echo "<br><br><br>".$sql;
$index=1;
$totalEfectivo=0;
$totalTarjetas=0;
$totalTransferencia=0;
$totalAnuladas=0;
$total_ventas=0;
$totaldepositar=0;
$totaldepositado=0;
//echo $sql;

if($sw_excel==1){?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-icon">
          <div class="float-right col-sm-2">
            <!-- <h6 class="card-title">Exportar como:</h6> -->
          </div>
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Ingresos Sucursales</h4>
          <h6 class="card-title">Fecha inicio: <?=$fechai?> - Fecha Fin: <?=$fechaf?></h6>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
          <?php } ?>

            <table class="table table-condensed table-bordered">
              <thead>
                <tr>
                  <th ><small><b>Sucursal</b></small></th>
                  <th ><small><b>Fecha</b></small></th>
                  <th ><small><b>Personal</b></small></th>
                  <th ><small><b>Efectivo[bs]</b></small></th>
                  <th ><small><b>Tarjetas[bs]</b></small></th>
                  <th ><small><b>Transfer[bs]</b></small></th>
                  <th ><small><b>Dolar[bs]</b></small></th>
                  <th ><small><b>Anuladas[bs]</b></small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositar[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositar[USD]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[USD]</small></th>
                  <th style="background:#d98880;"><small>Nro. Dep.</small></th>
                  <th ><small><b>Total Ventas</b></small></th>
                </tr>
                <tr>    
              </thead>
              <tbody >
                <?php
                $resp=mysqli_query($dbh,$sql);
                  while($row=mysqli_fetch_array($resp)){ 
                    $fechaVenta=$row['fecha'];
                    $cod_personal=$row['cod_chofer'];
                    $cod_almacen_x=$row['cod_almacen'];
                    $nombre_almacen_x=$row['nombre_almacen'];
                    $personal_x=$row['personal'];
                    $cod_ciudad=obtener_codciudad_almacen_nuevosis($cod_almacen_x,1);



                    $srting_montos=obtenerMonto_ventas_nuevosis_neto($fechaVenta,$cod_ciudad,$cod_personal);
                    $montosArray=explode("###",$srting_montos);
                    $montoefectivo=$montosArray[0];
                    $montoTarjeta=$montosArray[1];
                    $montoTrasferencia=$montosArray[2];



                    // $montoefectivo=obtenerMonto_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
                    // $montoTarjeta=obtenerMontoTarjeta_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
                    // $montoTrasferencia=obtenerMonto_ventas_nuevosis_neto($fechaVenta,$cod_ciudad,$cod_personal);//transferencia



                    $montodolarstring=obtenerMontodolares_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
                    $montodolarArray=explode("###",$montodolarstring);
                    $monto_dolar=$montodolarArray[0];
                    $monto_dolar_bs=$montodolarArray[1];



                    $montoAnulada=obtenerMontoAnuladas_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);

                    $monto_depositado=obtenerMontodepositado_nuevosis($fechaVenta,$cod_personal);
                    $monto_depositado_dolar=obtenerMontodepositado_dolar_nuevosis($fechaVenta,$cod_personal);
                    $nro_deposito=obtenerNrodepositado_nuevosis($fechaVenta,$cod_personal);
                    $monto_venta=$montoefectivo+$montoTarjeta+$montoTrasferencia-$montoAnulada-$monto_dolar_bs;
                    $monto_depositar=$montoefectivo-$montoAnulada-$monto_dolar_bs;
                    $totalEfectivo+=$montoefectivo; 
                    $totalTarjetas+=$montoTarjeta; 
                    $totalTransferencia+=$montoTrasferencia;  
                    $totalAnuladas+=$montoAnulada;
                    $totaldepositar+=$monto_depositar;
                    $totaldepositado+=$monto_depositado;
                    $total_ventas+=$monto_venta;
                    //$personalCliente=nombrePersonal_nuevosis($cod_personal);

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
                      <td class="text text-left"><small><?=$nombre_almacen_x?></small></td>
                      <td class="text text-left"><small><?=$fechaVenta?></small></td>
                      <td class="text text-left"><small><?=$personal_x?></small></td>
                      <td class="text text-right"><small><?=number_format($montoefectivo,2,".",",")?></small></td>
                      <td class="text text-right"><small><?=number_format($montoTarjeta,2,".",",")?></small></td>
                      <td class="text text-right"><small><?=number_format($montoTrasferencia,2,".",",")?></small></td>
                      <td class="text text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
                      <td class="text text-right"><small><?=number_format($montoAnulada,2,".",",")?></small></td>
                      <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositar,2,".",",")?></small></td>
                      <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
                      <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositado,2,".",",");?></small></td>
                      <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_depositado_dolar,2,".",",");?></small></td>
                      <td style="background:#f1948a;" class="text text-right"><small><?=$nro_deposito?></small></td>
                      <td align='right'><small><?=number_format($monto_venta,2,".",",");?></small></td>
                      <td  class="td-actions text-right"><?php if($sw_excel==1){?>
                        <a  target='_blank' href='http://10.10.1.10/cobofar_comercial/rptArqueoDiarioPDF.php?rpt_territorio=<?=$cod_ciudad?>&fecha_ini=<?=$fechaVenta?>&fecha_fin=<?=$fechaVenta?>&hora_ini=00:00&hora_fin=23:59&variableAdmin=1&rpt_funcionario=<?=$cod_personal?>'  class="btn btn-dark"  >
                          <i class="material-icons" title="Ver Detalle">list</i>
                        </a>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } ?>
                  <tr>
                    <th colspan="3">Total:</th>
                    <th align='right'><?=number_format($totalEfectivo,2,".",",")?></th>
                    <th align='right'><?=number_format($totalTarjetas,2,".",",")?></th>
                    <th align='right'><?=number_format($totalTransferencia,2,".",",")?></th>
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
              </tbody>
            </table>
          <?php if($sw_excel==1){?>

          </div>
        </div>
        
      </div>
    </div>
  </div>
  </div>
</div>

<?php } ?>