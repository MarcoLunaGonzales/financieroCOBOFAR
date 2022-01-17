<?php

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../conexion_sql.php'; 

$server=obtenerValorConfiguracion(104);
$bdname=obtenerValorConfiguracion(105);
$user=obtenerValorConfiguracion(106);
$pass=obtenerValorConfiguracion(107);
$dbh=ConexionFarma_all($server,$bdname,$user,$pass);


$dcto_edit=$_POST['dcto_edit'];
$porcionesFechaDesde = explode("-", $_POST["fecha_edit"]);
$desde=$porcionesFechaDesde[2]."/".$porcionesFechaDesde[1]."/".$porcionesFechaDesde[0];



$factura_edit=$_POST['factura_edit'];
// $monto_edit=$_POST['monto_edit'];
$nit_edit=$_POST['nit_edit'];
$autoriza_edit=$_POST['autoriza_edit'];
$codigocontrol_edit=$_POST['codigocontrol_edit'];

// Prepare
$sql="UPDATE  AMAESTRO 
SET  REFE='$codigocontrol_edit', REFE1='$autoriza_edit', RUC='$nit_edit',FECHA1='$desde',DOCUM='$factura_edit'
WHERE  DCTO=$dcto_edit AND TIPO='A'";

// echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

if($flagSuccess){
    echo 1;
}else echo 2;


?>