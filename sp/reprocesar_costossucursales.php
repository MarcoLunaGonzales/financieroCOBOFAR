<?php

set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

// require("../conexion_comercial.php");

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
            <h4 class="card-title">Cargado de Costo De Ventas</h4>
          </div>
          <div class="card-body">
                  
<?php

echo "<h6>Hora Inicio Proceso ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";

$fecha_inicio='2021-08-01';
$fecha_fin='2021-09-30';

$sql="SELECT ccd.cod_ciudad,ccd.fecha,ccd.cod_comprobante 
  from ingresos_sucursales_comprobantes ccd where ccd.fecha BETWEEN '$fecha_inicio' and '$fecha_fin' ";//and ccd.cod_comprobante=23
// $sql="SELECT isc.cod_ciudad,isc.fecha,isc.cod_comprobante from comprobantes c join comprobantes_detalle  cd on c.codigo=cd.cod_comprobante join ingresos_sucursales_comprobantes isc on isc.cod_comprobante=c.codigo
//   where c.cod_estadocomprobante<>2 and c.cod_tipocomprobante=1 and c.fecha BETWEEN '$fecha_inicio' and '$fecha_fin' and cd.cod_cuenta in (5004) and debe=0";
$statementUO1 = $dbhDet->query($sql);
while ($row = $statementUO1->fetch()){
  $fecha=$row['fecha'];
  // $nombre_ciudad=$row['nombre_ciudad'];
  $cod_ciudad=$row['cod_ciudad'];
  $cod_comprobante=$row['cod_comprobante'];
  $monto_costoventas=reprocesar_costoventas_sucursales($fecha,$cod_ciudad);
  
  //echo "SUC: $cod_ciudad $fecha ***CBT: $cod_comprobante*** Monto: $monto_costoventas<br>";

  $sqlInsert="UPDATE comprobantes_detalle set debe=$monto_costoventas
    where cod_comprobante=$cod_comprobante and cod_cuenta=5004";
  //  echo $sqlInsert;
  $stmtInsert = $dbhDet->prepare($sqlInsert);
  $stmtInsert->execute();

  $sqlInsert2="UPDATE  comprobantes_detalle set haber=$monto_costoventas  
    where cod_comprobante=$cod_comprobante and cod_cuenta=1057";
  $stmtInsert2 = $dbhDet->prepare($sqlInsert2);
  $stmtInsert2->execute();

}

echo "<h6>HORA FIN PROCESO CARGADO ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>