<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];
$cod_personal=$_POST["cod_personal"];


$sql="INSERT INTO descuentos_conta_consolidado(mes,gestion,cod_estado,cod_comprobante,created_at,created_by)
values ('$mes','$gestion',1,0,NOW(),'$cod_personal')";
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
if($flagSuccess){
    echo 1;
}else{
    echo 2;
}
?>
