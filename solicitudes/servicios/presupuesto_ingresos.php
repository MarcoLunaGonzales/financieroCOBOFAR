<?php
require_once '../functions.php';

$dbh = new Conexion();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$idp=$_GET['idp'];
$unidad=$_GET['unidad'];
$area=$_GET['area'];
$valor=$_GET['valor2'];


$anio=date("Y");
$anio_pasado=(int)$anio;
 
$monto = calcularCostosPresupuestariosValor($idp,$unidad,$area,$anio_pasado,$valor);
echo trim($monto);
?>
