<?php

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../conexion_comercial_oficial.php';

$dcto_edit=$_POST['dcto_edit'];
// $porcionesFechaDesde = explode("-", $_POST["fecha_edit"]);
// $desde=$porcionesFechaDesde[2]."/".$porcionesFechaDesde[1]."/".$porcionesFechaDesde[0];
$fecha=$_POST["fecha_edit"];
$factura_edit=$_POST['factura_edit'];
// $monto_edit=$_POST['monto_edit'];
$nit_edit=$_POST['nit_edit'];
$autoriza_edit=$_POST['autoriza_edit'];
$codigocontrol_edit=$_POST['codigocontrol_edit'];
// Prepare
$sql="UPDATE ingreso_almacenes set con_factura_proveedor='$codigocontrol_edit',aut_factura_proveedor='$autoriza_edit',nit_factura_proveedor='$nit_edit',f_factura_proveedor='$fecha',nro_factura_proveedor='$factura_edit'
where cod_ingreso_almacen=$dcto_edit";
// echo $sql;
$sql_inserta = mysqli_query($dbh,$sql);
if($sql_inserta){
    echo 1;
}else echo 2;


?>