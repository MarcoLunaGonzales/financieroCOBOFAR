<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT i.codigo,i.fecha,i.nro_correlativo,i.glosa,i.cod_estado,i.cod_comprobante,(select c.cod_estadocomprobante from comprobantes c where c.codigo=i.cod_comprobante)as estado_comprobante from ingresos_almacen i order by i.nro_correlativo desc limit 50";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('nro_correlativo', $nro_correlativo);
$stmt->bindColumn('glosa', $glosa);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);

$stmt->bindColumn('estado_comprobante', $cod_estadocomprobante);

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title"><?=$moduleNamePlural?></h4>
          </div>
          <div class="card-body">
            <center>
                  <?php
            if($globalAdmin==1){
            ?>           
              <a class="btn btn-success text-white" style="background:#F1C40F;height: 100px;padding-top: 25px;" target="_blank" onClick="mostrarTraspasosSucursalesCosteo('<?=$globalUser?>')"><i class="material-icons" style="font-size: 60px;">post_add</i><br><br><br>GENERACION DE COMPROBANTES</a>
              <a class="btn btn-warning text-white" target="blank" style="background: #A00C75;height: 100px;padding-top: 25px;" onClick="proceso_costeo_general_sucursales()"><i class="material-icons" style="font-size: 60px;">price_change</i><br><br>
              <br>PROCESO DE COSTEO</a>
              <a class="btn btn-info" target="blank" style="background: #08B8BA;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_movimientos_costo_sucursales.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>REPORTE VALORADO SUCURSALES</a>
              <a class="btn btn-info" target="blank" style="background: #828282;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_movimientos_costo_proveedor.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>REPORTE VALORADO SUCURSAL - PROVEEDORES</a>
              <a class="btn btn-info" target="blank" style="background: #828282;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_movimientos_costo.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>REPORTE VALORADO SUCURSAL - PRODUCTOS</a>
              <a class="btn btn-info" target="blank" style="background: #828282;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_ingresos_costo_cero.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>INGRESO COSTO CERO</a>
              <a class="btn btn-info" target="blank" style="background: #828282;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_salidas_costo_cero.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>SALIDA COSTO CERO</a>
              <!-- <a class="btn btn-info" target="blank" style="background: #828282;height: 100px;padding-top: 25px;" href="http://10.10.1.23/financieroCobofar/reportes_comercial/rptOp_costo_vs_venta.php"><i class="material-icons" style="font-size: 60px;">query_stats</i><br><br><br>COSTO MAYOR AL PRECIO</a> -->

            <?php
            }
            ?>
            </center>
          </div>
        </div>
        
      </div>
    </div>  
  </div>
</div>


<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos...</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>
