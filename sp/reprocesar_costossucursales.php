<?php

set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

require("../conexion_comercial.php");

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

$fecha_inicio='2021-07-01';
$fecha_fin='2021-07-31';

$sql="SELECT (select c.descripcion from ciudades c where c.cod_ciudad=ccd.cod_ciudad)as nombre_ciudad,ccd.cod_ciudad,ccd.fecha,ccd.cod_comprobante 
  from cierre_caja_diario ccd where ccd.fecha BETWEEN '$fecha_inicio' and '$fecha_fin' ";//and ccd.cod_comprobante=23
$resp=mysqli_query($dbh,$sql);
while($row=mysqli_fetch_array($resp)){ 
  $fecha=$row['fecha'];
  $nombre_ciudad=$row['nombre_ciudad'];
  $cod_ciudad=$row['cod_ciudad'];
  $cod_comprobante=$row['cod_comprobante'];
  $monto_costoventas=reprocesar_costoventas_sucursales($fecha,$cod_ciudad);
  echo "$nombre_ciudad $fecha ***CBT: $cod_comprobante*** Monto: $monto_costoventas<br>";

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