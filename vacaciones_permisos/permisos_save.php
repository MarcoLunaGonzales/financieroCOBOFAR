<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$q=$_POST["q"];
$a=$_POST["a"];
$s=$_POST["s"];
$cod_personal=$_POST["cod_personal"];
$cod_sucursal=$_POST["cod_sucursal"];


$fecha_inicio=$_POST["fecha_inicio"];
$hora_inicio=$_POST["hora_inicio"];
$fecha_final=$_POST["fecha_final"];
$hora_final=$_POST["hora_final"];
$motivo=$_POST["motivo"];
$observaciones=$_POST["observaciones"];
$dias_permiso=$_POST["dias_solicitadas"];
if (isset($_POST["fecha_evento"])) {
	$fecha_evento=$_POST["fecha_evento"];
}else{
	$fecha_evento=null;
}

$cod_estadoreferencial=1;
// Prepare
$sql="INSERT INTO personal_permisos (cod_personal, cod_tipopermiso,fecha_inicial,hora_inicial,fecha_final,hora_final,observaciones,cod_estado,created_at,created_by,fecha_evento,dias_permiso,cod_area) VALUES ($cod_personal,'$motivo' ,'$fecha_inicio','$hora_inicio','$fecha_final','$hora_final','$observaciones','$cod_estadoreferencial',NOW(),'$cod_personal','$fecha_evento','$dias_permiso','$cod_sucursal')";
 // echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

if($q>0){
	showAlertSuccessError($flagSuccess,'../index.php?opcion=permisosPersonalLista&q='.$q.'&a='.$a.'&s='.$s);	
}else{
	showAlertSuccessError($flagSuccess,'../index.php?opcion=permisosPersonalLista');
}

?>
