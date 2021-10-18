
<?php

require("../conexion_comercial2.php");
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';

$fechai=$_POST['fechainicio'];
//$fechai=date('d/m/Y', strtotime($fechai));
//echo $fechai;
$fechaf=$_POST['fechafin'];
//$fechaf=date('d/m/Y', strtotime($fechaf));

$sucursal=$_POST['sucursal'];

$personal=$_POST['personal'];
$personalgString="";
foreach ($personal as $personali) {   
  $personalgString.=$personali.",";
}
$personalgString=trim($personalgString,",");


echo "<div class='content'>
  <div class='container-fluid'>
    <div class='row'>
    <div class='col-md-12'>
      <div class='card'>";

echo "<div class='card-header $colorCard card-header-icon'>
  <h4 class='card-title'> <img  class='card-img-top'  src='../assets/img/favicon.png' style='width:100%; max-width:50px;'>Reporte Facturas Generadas X Persona</h4>
  <h6 class='card-title'>Sucursal: .$nombre_suc</h6>
  <h6 class='card-title'>Fechas:$fechai - $fechaf</h6>
</div>";

echo "<div class='card-body'>";   
echo "<div class='table-responsive'>";
echo "<table class='table table-condensed table-bordered' id='tablePaginatorReport_facturasgeneradas'>";
echo "<thead><tr class='bg-info text-white'><th>Proceso</th><th>Nro. Factura</th><th>Fecha/hora<br>Registro Salida</th><th>Caja</th><th>Monto</th>
  <th>Razon Social</th><th>NIT</th><th>Pago</th><th>Datos A.</th><th>&nbsp;</th></tr></thead> <tbody>";
$consulta = "
  SELECT s.cod_salida_almacenes, s.fecha, s.hora_salida, ts.nombre_tiposalida, 
  (select a.nombre_almacen from almacenes a where a.`cod_almacen`=s.almacen_destino), s.observaciones, 
  s.estado_salida, s.nro_correlativo, s.salida_anulada, s.almacen_destino, 
  (select c.nombre_cliente from clientes c where c.cod_cliente = s.cod_cliente), s.cod_tipo_doc, razon_social, nit,s.cod_tipopago,s.monto_final,(SELECT count(*) from registro_depositos where cod_funcionario=s.cod_chofer and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN CONCAT(fecha,' ',hora,':00') and CONCAT(fechaf,' ',horaf,':00') and cod_estadoreferencial=1)AS depositado,(SELECT cod_medico from recetas_salidas where cod_salida_almacen=s.cod_salida_almacenes LIMIT 1)cod_medico,monto_cancelado_usd,(select us.usuario from usuarios_sistema us where us.codigo_funcionario=s.cod_chofer_anulacion)as personal_anulacion,s.fecha_anulacion,(select us2.usuario from usuarios_sistema us2 where us2.codigo_funcionario=s.cod_chofer)as nombre_responsable
  FROM salida_almacenes s, tipos_salida ts 
  WHERE s.cod_tiposalida = ts.cod_tiposalida AND s.cod_almacen in (select a.cod_almacen from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursal)) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fechai 00:00:00' and '$fechaf 23:59:59' and s.cod_tiposalida=1001 and s.cod_chofer in ($personalgString) ORDER BY s.fecha desc, s.nro_correlativo ";

//echo $consulta;
//
$resp = mysqli_query($enlaceCon,$consulta);
while ($dat = mysqli_fetch_array($resp)) {
    $codigo = $dat[0];
    $fecha_salida = $dat[1];
    $fecha_salida_mostrar = "$fecha_salida[8]$fecha_salida[9]-$fecha_salida[5]$fecha_salida[6]-$fecha_salida[0]$fecha_salida[1]$fecha_salida[2]$fecha_salida[3]";
    $hora_salida = $dat[2];
    // $nombre_tiposalida = $dat[3];
    $nombre_almacen = $dat[4];
    $obs_salida = $dat[5];
    $estado_almacen = $dat[6];
    $nro_correlativo = $dat[7];
    $salida_anulada = $dat[8];
    $cod_almacen_destino = $dat[9];
  $nombreCliente=$dat[10];
  $codTipoDoc=$dat[11];
  $razonSocial=$dat[12];
  $nitCli=$dat[13];
  $depositado=$dat['depositado'];
  $codMedico=$dat['cod_medico'];
  $montoCanceladoUSD=$dat['monto_cancelado_usd'];
  $personal_anulacion=$dat['personal_anulacion'];
  $fecha_anulacion=$dat['fecha_anulacion'];
  $nombreResponsable=$dat['nombre_responsable'];
  $montoFactura=number_format($dat['monto_final'],1,'.',',')."0";
  if($codTipoDoc==4){
      $nro_correlativo="<i class=\"text-danger\">M-$nro_correlativo</i>";
  }else{
      $nro_correlativo="F-$nro_correlativo";
  }
  $colorReceta="#14982C";
  if($codMedico==""){
    $codMedico=0;         
  }
  if($codMedico==0){
      $colorReceta="#652BE9"; 
  }
  $stikea="";
  $stikeb="";
  $stikec="";
  if($salida_anulada==1){
      $stikea="<strike class='text-danger'>";
      $stikeb="</strike>";
      $stikec=" (ANULADO)</strike>";
  }
  
    echo "<tr>";
    echo "<td>$stikea&nbsp;P-$codigo$stikeb</td>";
    echo "<td align='center'>$stikea<b>$nro_correlativo</b>$stikeb</td>";
    echo "<td align='center'>$stikea$fecha_salida_mostrar $hora_salida$stikeb</td><td class='text-left'>$stikea&nbsp;$nombreResponsable</td>";
    
    echo "<td align='right'>$stikea<b>$montoFactura</b>$stikeb</td>";
    echo "<td class='text-left'>$stikea&nbsp;$razonSocial$stikeb</td><td>$stikea&nbsp;$nitCli$stikeb</td>";
    $codTarjeta=$dat['cod_tipopago'];
    if($codTarjeta==2){
        echo "<td class='text-primary'>$stikea&nbsp;<b>Tarjeta</b></td>";
    }else{
        echo "<td class='text-success'>$stikea&nbsp;<b>Efectivo</b></td>";
    }  
    echo "<td class='text-success'>$stikea&nbsp;F: $fecha_anulacion /  Caja: $personal_anulacion</td>";
    echo "<td   class='td-actions text-right'> <a href='#' rel='tooltip' class='btn btn-warning' onclick='abrir_detalle_modal($codigo,0);return false;'><i class='material-icons' title='Ver Detalle Factura'>list</i></a></td>";
  echo "</tr>";
}
echo " </tbody></table>";
echo "</div>";
echo "</div>";


echo "</div>
    </div>
  </div>
  </div>
</div>";


?>



<?php 
mysqli_close($enlaceCon);
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