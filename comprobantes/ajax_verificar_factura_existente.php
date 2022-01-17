<?php
require_once '../conexion.php';
require_once '../functions.php';
// require_once '../perspectivas/configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$autorizacion=$_GET['autorizacion'];//llega el codigo de asignacion
$nro_factura=$_GET['nro_factura'];

$query1="SELECT f.codigo from facturas_compra f join comprobantes_detalle cd on cd.codigo=f.cod_comprobantedetalle join comprobantes c on c.codigo=cd.cod_comprobante
where f.nro_autorizacion='$autorizacion' and f.nro_factura='$nro_factura'
and c.cod_estadocomprobante<>2";
// echo  $query1;
$stmt = $dbh->prepare($query1);
$stmt->execute();
$contador=0;
while ($rowCount = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $contador++;
}
echo $contador;
?>