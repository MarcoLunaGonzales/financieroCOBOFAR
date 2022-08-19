
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
require("../conexion_comercial_oficial.php");
// require("../conexion_comercial2.php");

$fechai=$_POST['fechainicio'];
$fechaf=$_POST['fechafin'];
$sucursal_origen=$_POST['sucursal_origen'];
$sucursal_destino=$_POST['sucursal_destino'];
$tipo=$_POST['tipo'];

$periodo_ingreso=$_POST['periodo_ingreso'];



$fecha_actual=date('Y-m-d');
$fecha_limite=date('Y-m-d',strtotime($fecha_actual.'-'.$periodo_ingreso.' day'));
//echo $fecha_limite."**";
$sucursalgStringDestino="";
foreach ($sucursal_destino as $sucursald) {   
  $sucursalgStringDestino.=$sucursald.",";
}
$sucursalgStringDestino=trim($sucursalgStringDestino,",");

$sucursalgStringOrigen="";
foreach ($sucursal_origen as $sucursalo) {   
  $sucursalgStringOrigen.=$sucursalo.",";
}
$sucursalgStringOrigen=trim($sucursalgStringOrigen,",");


if($tipo==2 || $tipo==3){
  include "auditoria_sucursales_print_tiempo.php";
}elseif($tipo==1){
$sql="SELECT s.cod_salida_almacenes,s.cod_almacen, s.fecha, ts.nombre_tiposalida, a.nombre_almacen, s.observaciones, s.nro_correlativo ,s.salida_anulada,s.observaciones_transito,(select al.nombre_almacen from almacenes al where al.cod_almacen=s.almacen_destino)as nombre_almacen_des,(select us.usuario from usuarios_sistema us where us.codigo_funcionario=s.cod_chofer)as nombre_responsable,s.hora_salida,s.cod_persona_entrega,(select IFNULL(us.usuario,'PROVEEDOR-SUPERVISOR') from usuarios_sistema us where us.codigo_funcionario=s.cod_persona_entrega)as nombre_recibido
  FROM salida_almacenes s, tipos_salida ts, almacenes a 
  where s.fecha between '$fechai' and '$fechaf' and s.cod_tiposalida=ts.cod_tiposalida and s.almacen_destino in (select a.cod_almacen from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgStringDestino)) and s.cod_almacen in (select a.cod_almacen from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgStringOrigen)) and s.estado_salida=1 and a.cod_almacen=s.cod_almacen and (s.salida_anulada=0 or s.salida_anulada is null) ORDER BY s.fecha desc, s.nro_correlativo desc ";

 // echo "<br><br><br>".$sql;

$index=1;
$totalEfectivo=0;
$totalTarjetas=0;
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
          <h4 class="card-title"><img  class="card-img-top"  src="../assets/img/favicon.png" style="width:100%; max-width:50px;">Auditoría Sucursales</h4>
          <h6 class="card-title">Fecha inicio: <?=$fechai?> - Fecha Fin: <?=$fechaf?></h6>
        </div>
        <div class="card-body ">
          <div class="table-responsive">
          <?php } ?>
            <table class="table table-condensed table-bordered">
              <thead>
                <tr>
                  <th><b>Suc. Origen</b></th>
                  <th><b>Responsable</b></th>
                  <th><b>Tipo de Salida(Origen)</b></th>
                  <th><b>Fecha Despacho</b></th>
                  <th><b>Nota de Remision(Origen)</b></th>
                  <th><b>Glosa</b></th>
                  <th><b>Suc. Destino</b></th>
                  <th><b>Monto</b></th>
                  <th><b>Personal Entrega</b></th>
                  <th><b></b></th>
                </tr>
                <tr>    
              </thead>
              <tbody >
                <?php
                $index=1;
                $resp=mysqli_query($dbh,$sql);
                while($dat=mysqli_fetch_array($resp)){ 
                    $codigo=$dat[0];
                    $cod_almacen_origen=$dat[1];
                    $fecha_salida=$dat[2];
                    $fecha_salida_mostrar="$fecha_salida[8]$fecha_salida[9]-$fecha_salida[5]$fecha_salida[6]-$fecha_salida[0]$fecha_salida[1]$fecha_salida[2]$fecha_salida[3]";
                    $nombre_tiposalida=$dat[3];
                    $nombre_almacen_origen=$dat[4];
                    $nombre_almacen_dest=$dat[9];
                    $obs_salida=$dat[5];
                    $nro_correlativo=$dat[6];
                    $salida_anulada=$dat[7];   
                    $nombre_responsable=$dat[10];
                    $hora_salida=$dat[11];
                    $cod_persona_entrega=$dat[12];
                    $nombre_recibido=$dat[13];

                    $monto_transaccion=obtenerMontoDetalleSalida($codigo);
                    $color_fondo = "";
                    if($fecha_salida<$fecha_limite){
                      $color_fondo = "style='background:#ff8080'";
                    } 
                    if($salida_anulada == 1) {
                      $color_fondo = "#ff8080";
                      $chk = "&nbsp;";
                      $obs_salida=$obs_salida."<br><b class='text-danger'>(".$dat[8].")</b>";
                    }
                    if($cod_persona_entrega==null || $cod_persona_entrega=="" || $cod_persona_entrega==0){
                      $nombre_recibido='PROVEEDOR-SUPERVISOR';
                    }
                    ?>
                    <tr <?=$color_fondo?>>
                      <td class="text-left"><?=$nombre_almacen_origen?></td>
                      <td class="text-left"><?=$nombre_responsable?></td>
                      <td class="text-left"><?=$nombre_tiposalida?></td>
                      <td align='center'><?=$fecha_salida_mostrar?> <?=$hora_salida?></td>
                      <td align='center'>T - <?=$nro_correlativo?></td>
                      <td class="text-left">&nbsp;<?=$obs_salida?></td>
                      <td class="text-left">&nbsp;<?=$nombre_almacen_dest?></td>
                      <td>&nbsp;<?=number_format($monto_transaccion,2)?></td>

                      <td class="text-left">&nbsp;<?=$nombre_recibido?></td>
                      <td class='td-actions text-right'><?php if($sw_excel==1){?> <a href='#' rel='tooltip' class='btn btn-warning' onclick='abrir_detalle_modal("<?=$codigo?>",0);return false;'><i class='material-icons' title='Ver Detalle'>list</i></a><?php }?></td>
                    </tr>
                <?php

                } ?>
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

<?php } 

}
if($sw_excel==1){
?>



<!-- small modal -->
<div class="modal fade modal-primary" id="modalDetalleFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
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
        <table class="table table-condensed">
          <thead>
            <tr>
              <th ><small><b>-</b></small></th>
              <th ><small><b>Proceso</b></small></th>
              <th ><small><b>COD</b></small></th>
              <th ><small><b>DES</b></small></th>
              <th ><small><b>CANTIDAD</b></small></th>
              <th ><small><b>MONTO</b></small></th>
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

<?php } ?>