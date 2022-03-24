<?php
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';
//echo "<br><br><br><br>";


require("../conexion_comercial.php");


$fechai=$_POST['fechainicio'];
//$fechai=date('d/m/Y', strtotime($fechai));
//echo $fechai;
$fechaf=$_POST['fechafin'];
//$fechaf=date('d/m/Y', strtotime($fechaf));
$sucursal=$_POST['sucursal'];
$sucursalgString="";
foreach ($sucursal as $sucursali) {   
  // $cod_ciudad= obtener_codciudad_nuevosis($sucursali); 
    $sucursalgString.=$sucursali.",";
    # code...
}
$sucursalgString=trim($sucursalgString,",");

$cod_tiposalida_efectivo=1001;
$sql="SELECT s.cod_almacen,(select a.nombre_almacen from almacenes a where a.cod_almacen=s.cod_almacen)as nombre_almacen,s.fecha 
  from salida_almacenes s 
  where s.`cod_tiposalida`= $cod_tiposalida_efectivo  and s.`cod_almacen` in (select a.cod_almacen 
from almacenes a, ciudades c
where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgString)) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fechai 00:00:00' and '$fechaf 23:59:59' 
 GROUP BY s.cod_almacen,s.fecha 
 
 order by s.fecha,2";
// echo $sql;
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-icon">
          <div class="float-right col-sm-2">
            <h6 class="card-title">Exportar como:</h6>
          </div>
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Ingresos Sucursales</h4>
          <h6 class="card-title">Fecha inicio: <?=$fechai?> - Fecha Fin: <?=$fechaf?></h6>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
            <table class="table table-condensed table-bordered" id="tablePaginatorReport_facturasgeneradas">
              <thead>
                <tr>
                  <th><small>Sucursal</small></th>
                  <th><small>Fecha</small></th>
                  <th><small>V. Efectivo [Bs]</small></th>
                  <th><small>V. Tarjeta</small></th>
                  <th><small>V. Dolar</small></th>
                  <th><small>Anulados </small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositar[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositar[USD]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[USD]</small></th>
                  <th><small>TOTAL VENTAS [Bs]</small></th>
                  <th><small>-</small></th>
                </tr>
              </thead>
              <tbody>
                <?php $index=1;
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
                  $cod_almacen_x=$row['cod_almacen'];
                  $cod_ciudad=obtener_codciudad_almacen_nuevosis($cod_almacen_x,1);
                  $nombre_almacen=$row['nombre_almacen'];
                  $montoefectivo=obtenerMonto_ventas_nuevosis($fechaVenta,$cod_ciudad,-1000);
                  // echo "<br>****".$montoefectivo."***<br>";
                  $montoTarjeta=obtenerMontoTarjeta_ventas_nuevosis($fechaVenta,$cod_ciudad,-1000);
                  $montodolarstring=obtenerMontodolares_ventas_nuevosis($fechaVenta,$cod_ciudad,-1000);
                  $montodolarArray=explode("###",$montodolarstring);
                  $monto_dolar=$montodolarArray[0];
                  $monto_dolar_bs=$montodolarArray[1];
                  $montoAnulada=obtenerMontoAnuladas_ventas_nuevosis($fechaVenta,$cod_ciudad,-1000);
                  $monto_depositado=obtenerMontodepositado_general_nuevosis2($fechaVenta,$cod_ciudad);
                  $monto_depositado_dolar=obtenerMontodepositado_dolar_general_nuevosis2($fechaVenta,$cod_ciudad);
                  $monto_venta=$montoefectivo+$montoTarjeta-$montoAnulada-$monto_dolar_bs;
                  $monto_depositar=$montoefectivo-$montoAnulada-$monto_dolar_bs;
                  $totalEfectivo+=$montoefectivo; 
                  $totalTarjetas+=$montoTarjeta;  
                  $totalAnuladas+=$montoAnulada;
                  $totaldepositar+=$monto_depositar;
                  $totaldepositado+=$monto_depositado;
                  $total_ventas+=$monto_venta;
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
                    <td class="text-left"><small><?=$nombre_almacen?></small></td>
                    <td><small><?=$fechaVenta?></small></td>
                    <td class="text-right"><small><?=number_format($montoefectivo,2,".",",")?></small></td>
                    <td class="text-right"><small><?=number_format($montoTarjeta,2,".",",")?></small></td>
                    <td class="text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
                    <td class="text-right"><small><?=number_format($montoAnulada,2,".",",")?></small></td>
                    <td <?=$label_style_bs?> class="text-right"><small><?=number_format($monto_depositar,2,".",",")?></small></td>
                    <td <?=$label_style_usd?> class="text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
                    <td <?=$label_style_bs?> class="text-right"><small><?=number_format($monto_depositado,2,".",",");?></small></td>
                    <td <?=$label_style_usd?> class="text-right"><small><?=number_format($monto_depositado_dolar,2,".",",");?></small></td>
                    <td class="text-right"><small><?=number_format($monto_venta,2,".",",");?></small></td>
                    <td  class="td-actions text-right"><!-- <a href='#' rel="tooltip" class="btn btn-success" onclick="abrir_detalle_modal_cierres('<?=$fechaVenta?>','<?=$cod_ciudad?>');return false;" >
                          <i class="material-icons" title="Ver Detalle">list</i>
                        </a> -->
                      </td>
                  </tr><?php 
                } 
                mysqli_close($dbh);
                ?>
                <tr>
                  <td>&nbsp;</td>
                  <th>Total:</th>
                  <th class="text-right"><?=number_format($totalEfectivo,2,".",",")?></th>
                  <th class="text-right"><?=number_format($totalTarjetas,2,".",",")?></th>
                  <th class="text-right"><?=number_format(0,2,".",",")?></th>
                  <th class="text-right"><?=number_format($totalAnuladas,2,".",",")?></th>
                  <th style="background:#3f51b5;color:white;" class="text-right"><?=number_format($totaldepositar,2,".",",")?></th>
                  <th style="background:#3f51b5;color:white;" class="text-right"><?=number_format(0,2,".",",")?></th>
                  <th style="background:#3f51b5;color:white;" class="text-right"><?=number_format($totaldepositado,2,".",",")?></th>
                  <th style="background:#3f51b5;color:white;" class="text-right"><?=number_format(0,2,".",",")?></th>
                  <th class="text-right"><?=number_format($total_ventas,2,".",",")?></th>
                  <th class="text-right"></th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer fixed-bottom">
           <button rel="tooltip" class="btn btn-success" target="_blank" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','generar_comprobante_arqueo.php?fechai=<?=$fechai;?>&fechaf=<?=$fechaf;?>&cod_sucursal=<?=$sucursalgString;?>')">
            <i class="material-icons text-danger" >input</i>Generar Comprobante
          </button> 
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

<!-- small modal -->
<div class="modal fade modal-primary" id="modal_detalle_cierre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-warning card-header-icon">
        <div class="card-icon">
          <i class="material-icons">list</i>
        </div>
        <h4 class="card-title">Detalle Facturas</h4>
      </div>
      <div class="card-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="material-icons">close</i>
      </button>

        <table class="table table-condensed table-bordered">
          <thead>
            <tr id="cabecera_detalle_cierre">

              <!-- <th colspan="10"><small><b>Sucursal: Jcarrasco   Fecha: 16/05/2021</b></small></th> -->
            </tr>
            <tr>
              <th ><small><b>Personal</b></small></th>
              <th ><small><b>Efectivo[bs]</b></small></th>
              <th ><small><b>Tarjetas[bs]</b></small></th>
              <th ><small><b>Dolar[bs]</b></small></th>
              <th ><small><b>Anuladas[bs]</b></small></th>
              <th style="background:#3f51b5;color:white;"><small>Depositar[Bs]</small></th>
              <th style="background:#3f51b5;color:white;"><small>Depositar[USD]</small></th>
              <th style="background:#3f51b5;color:white;"><small>Depositado[Bs]</small></th>
              <th style="background:#3f51b5;color:white;"><small>Depositado[USD]</small></th>
              <th style="background:#c5cae9"><small>Nro. Dep.</small></th>
              <th ><small><b>Total Ventas</b></small></th>
            </tr>
            <tr>    
          </thead>
          <tbody id="tablasA_registradas">
            
          </tbody>
        </table>
      </div>
    </div>  
  </div>
</div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>