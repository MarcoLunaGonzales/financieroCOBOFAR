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

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6><br>";

$fecha_inicio='2022-07-01';
$fecha_fin='2022-07-31';

//para el costo promedio***
$cod_mes=1;
$cod_gestion=2022;
//****

//correr por fechas
$sql="SELECT ccd.cod_ciudad,ccd.fecha,ccd.cod_comprobante 
  from ingresos_sucursales_comprobantes ccd join comprobantes c on c.codigo=ccd.cod_comprobante
  where c.cod_estadocomprobante<>2 and ccd.fecha BETWEEN '$fecha_inicio' and '$fecha_fin' order by ccd.fecha ";//and ccd.cod_comprobante=23

// correr por ciudad
// $sql="SELECT ccd.cod_ciudad,ccd.fecha,ccd.cod_comprobante 
//   from ingresos_sucursales_comprobantes ccd join comprobantes c on c.codigo=ccd.cod_comprobante
//   where c.cod_estadocomprobante<>2 and ccd.fecha BETWEEN '$fecha_inicio' and '$fecha_fin' and ccd.cod_ciudad in (85,84) order by ccd.fecha ";//and ccd.cod_comprobante=23

//correr por comprobante
  // $sql="SELECT ccd.cod_ciudad,ccd.fecha,ccd.cod_comprobante 
  // from ingresos_sucursales_comprobantes ccd join comprobantes c on c.codigo=ccd.cod_comprobante
  // where c.cod_estadocomprobante<>2 and c.codigo in (35531,35532,35533,35534,35535,35536,35537,35538,35539,35540)";

$statementUO1 = $dbhDet->query($sql);
while ($row = $statementUO1->fetch()){
  $fecha=$row['fecha'];
  // $nombre_ciudad=$row['nombre_ciudad'];
  $cod_ciudad=$row['cod_ciudad'];
  $cod_comprobante=$row['cod_comprobante'];
  // $monto_costoventas=reprocesar_costoventas_sucursales($fecha,$cod_ciudad,$cod_mes,$cod_gestion);//costo promedio mes
  $monto_costoventas=reprocesar_costoventas_sucursales_2($fecha,$cod_ciudad);//costo transaccion
  //$monto_costoventas=reprocesar_costoventas_sucursales_3($fecha,$cod_ciudad);//costo transaccion bk otras tablas
  if($monto_costoventas>0){
    $sqlInsert="UPDATE comprobantes_detalle set debe=$monto_costoventas
    where cod_comprobante=$cod_comprobante and cod_cuenta=5004";
    //  echo $sqlInsert;
    $stmtInsert = $dbhDet->prepare($sqlInsert);
    $stmtInsert->execute();

    $sqlInsert2="UPDATE  comprobantes_detalle set haber=$monto_costoventas  
      where cod_comprobante=$cod_comprobante and cod_cuenta=1057";
    $stmtInsert2 = $dbhDet->prepare($sqlInsert2);
    $stmtInsert2->execute();  

    echo "SUC: $cod_ciudad F: $fecha <br>";
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