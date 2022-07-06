
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

$sql_add=" and s.cod_tipopago in (1)";


//query de anulaciones
$array_anulaciones=[];
$sql="SELECT sum(round(CAST(s.monto_final as DECIMAL(9,2)),1)) as monto,s.cod_chofer_anulacion,DATE_FORMAT(s.fecha_anulacion,'%Y-%m-%d')as fecha,a.cod_ciudad
from `salida_almacenes` s join almacenes a on s.cod_almacen=a.cod_almacen
where s.`cod_tiposalida`=1001 and s.salida_anulada!=0 
and s.fecha_anulacion BETWEEN '$fechai 06:00:00' and '$fechaf 23:59:59' and s.cod_tipopago=1 
group by 3,s.cod_chofer_anulacion,s.cod_almacen";  
$resp=mysqli_query($dbh,$sql);
while($row=mysqli_fetch_array($resp)){   
  $monto=$row['monto'];
  $cod_chofer_anulacion=$row['cod_chofer_anulacion'];
  $fecha_anulacion=$row['fecha'];
  $cod_ciudad=$row['cod_ciudad'];
  $codigonuevo=$cod_chofer_anulacion."-".$fecha_anulacion."-".$cod_ciudad;
  $array_anulaciones[$codigonuevo]=$monto;
}
// $sql="SELECT s.cod_almacen,a.cod_ciudad,a.nombre_almacen,s.fecha,s.cod_chofer,(select CONCAT_WS(' ',f.nombres,f.paterno,f.materno) from funcionarios f where f.codigo_funcionario=s.cod_chofer) as personal 
// from salida_almacenes s join almacenes a on s.cod_almacen=a.cod_almacen 
// where s.`cod_tiposalida`= 1001 and a.cod_ciudad in ($sucursalgString) and s.fecha between '$fechai' and '$fechaf'
// $sql_add
// GROUP BY s.cod_chofer,s.fecha 
// order by s.fecha,s.cod_almacen,s.cod_chofer";


$sql="SELECT sum(round(CAST(s.monto_final as DECIMAL(9,2)),1)) as monto,sum(round(CAST(s.monto_cancelado_usd as DECIMAL(9,2)),1)) as monto_usd,sum(round(CAST(s.monto_cancelado_usd as DECIMAL(9,2)),1)*s.tipo_cambio) as monto_usd_bs,a.nombre_almacen,(select CONCAT_WS(' ',f.nombres,f.paterno,f.materno) from funcionarios f where f.codigo_funcionario=s.cod_chofer) as personal,s.fecha,s.cod_chofer,a.cod_ciudad

from `salida_almacenes` s join almacenes a on s.cod_almacen=a.cod_almacen
where s.`cod_tiposalida`=1001 and a.`cod_ciudad` in ($sucursalgString) and s.fecha BETWEEN '$fechai' and '$fechaf'  
$sql_add
GROUP BY s.fecha,s.cod_chofer
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
          <h4 class="card-title"> <img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Diferencia de Depósitos</h4>
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
                  <th style="background:#3f51b5;color:white;"><small>Depositar[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositar[USD]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[Bs]</small></th>
                  <th style="background:#3f51b5;color:white;"><small>Depositado[USD]</small></th>
                  <th ><small>Diferencia Bs</small></th>
                  <th ><small>Diferencia USD</small></th>
                  <th style="background:#d98880;"><small>Nro. Dep.</small></th>
                </tr>
                <tr>    
              </thead>
              <tbody>
                <?php
                $resp=mysqli_query($dbh,$sql);
                  while($row=mysqli_fetch_array($resp)){
                    $fechaVenta=$row['fecha'];
                    $cod_personal=$row['cod_chofer'];
                    $nombre_almacen_x=$row['nombre_almacen'];
                    $personal_x=$row['personal'];
                    $cod_ciudad=$row['cod_ciudad'];

                    $montoefectivo=$row['monto'];
                    $monto_dolar=number_format($row['monto_usd'],2,".","");
                    $monto_dolar_bs=$row['monto_usd_bs'];
                    // $montoAnulada=obtenerMontoAnuladas_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);//anulada efectivo
                    $codigonuevo=$cod_personal."-".$fechaVenta."-".$cod_ciudad;
                    $montoAnulada=$array_anulaciones[$codigonuevo];
                    $monto_depositado_string=obtenerMontodepositado_nuevosis_bajas($fechaVenta,$cod_personal);                    
                    $depositadoArray=explode("###",$monto_depositado_string);
                    $monto_depositado=number_format($depositadoArray[0],2,".","");
                    $monto_depositado_dolar=number_format($depositadoArray[1],2,".","");
                    $nro_deposito=$depositadoArray[2];
                    $monto_depositar=$montoefectivo-$montoAnulada-$monto_dolar_bs;                    
                    $monto_depositar=number_format($monto_depositar,2,".","");
                    if($monto_depositar == $monto_depositado){//bolivianos
                      $label_style_bs='style="background:#c5cae9"';
                      $auxiliar_bs=false;
                      $diferencia_bs=0;
                    }else{
                      $diferencia_bs=$monto_depositar-$monto_depositado;
                      $auxiliar_bs=true;
                      $label_style_bs='style="background:#ff867f"';
                    }

                    if($monto_dolar == $monto_depositado_dolar){//dolares
                      $auxiliar_usd=false;
                      $label_style_usd='style="background:#c5cae9"';
                      $diferencia_usd=0;
                    }else{
                      $diferencia_usd=$monto_dolar-$monto_depositado_dolar;
                      $auxiliar_usd=true;
                      $label_style_usd='style="background:#ff867f"';
                    }
                    
                    if($auxiliar_bs || $auxiliar_usd){
                      $totaldepositar+=$monto_depositar;
                      $totaldepositado+=$monto_depositado;
                      $totaldepositar_usd+=$monto_dolar;
                      $totaldepositado_usd+=$monto_depositado_dolar;
                      $total_diff_bs+=$diferencia_bs;
                      $total_diff_usd+=$diferencia_usd;
                      ?>
                      <tr>
                        <td class="text text-left"><small><?=$nombre_almacen_x?></small></td>
                        <td class="text text-left"><small><?=$fechaVenta?></small></td>
                        <td class="text text-left"><small><?=$personal_x?></small></td>
                        
                        <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositar,2,".",",")?></small></td>
                        <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_dolar,2,".",",")?></small></td>
                        <td <?=$label_style_bs?> class="text text-right"><small><?=number_format($monto_depositado,2,".",",");?></small></td>
                        <td <?=$label_style_usd?> class="text text-right"><small><?=number_format($monto_depositado_dolar,2,".",",");?></small></td>
                        <td align='right'><small><?=number_format($diferencia_bs,2,".",",");?></small></td>
                        <td align='right'><small><?=number_format($diferencia_usd,2,".",",");?></small></td>
                        <td style="background:#f1948a;" class="text text-right"><small><?=$nro_deposito?></small></td>
                        
                      </tr>
                    <?php }
                  } ?>
                  <tr>
                    <th colspan="3">Total:</th>
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($totaldepositar,2,".",",")?></th>
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format(0,2,".",",")?></th>
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($totaldepositado,2,".",",")?></th>
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format(0,2,".",",")?></th>
                    
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($total_diff_bs,2,".",",")?></th>
                    <th style="background:#3f51b5;color:white;" class="text-right" align='right'><?=number_format($total_diff_usd,2,".",",")?></th>
                    <th></th>
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