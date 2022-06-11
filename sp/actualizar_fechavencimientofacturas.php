<?php

set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
$dbhDet = new Conexion();
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title">Actualizacion de fecha de vencimiento de facturas</h4>
          </div>
          <div class="card-body">
                  
<?php

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6><br>";

  $sql="SELECT i.cod_comprobante,id.fecha_factura,id.fecha_vencimiento,id.factura
  from ingresos_almacen i join ingresos_almacen_detalle id on i.codigo=id.cod_ingresoalmacen
  where id.cod_proveedor=197 and cod_estado=1 and cod_comprobante>0
  order by id.fecha_factura";

$stmt = $dbhDet->query($sql);
while ($row = $stmt->fetch()){
  $cod_comprobante=$row['cod_comprobante'];
  $fecha_factura=$row['fecha_factura'];
  $fecha_vencimiento=$row['fecha_vencimiento'];
  $factura=$row['factura'];

  $sqlEC="SELECT codigo from estados_cuenta where glosa_auxiliar like '%$factura%' and cod_comprobantedetalle in ( 
  select codigo from comprobantes_detalle where cod_comprobante=$cod_comprobante)";
  $stmtEC = $dbhDet->query($sqlEC);
  while ($rowEC = $stmtEC->fetch()){
    $codigo_ec=$rowEC['codigo'];
  }
  if($codigo_ec>0){
    $sqlUpdate="UPDATE estados_cuenta set fecha_vencimiento='$fecha_vencimiento',fecha_factura='$fecha_factura' where codigo=$codigo_ec";
    $stmtUpdate = $dbhDet->prepare($sqlUpdate);
    $stmtUpdate->execute();  
  }
}
echo "<br><h6>HORA FIN PROCESO: " . date("Y-m-d H:i:s")."</h6>";
?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>