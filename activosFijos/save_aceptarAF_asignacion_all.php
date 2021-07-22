<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';
require_once '../perspectivas/configModule.php';
$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT codigo,cod_activosfijos,cod_personal,cod_personal2
 FROM activofijos_asignaciones
  where cod_estadoasignacionaf=1 and cod_personal <>''");
// Bind
$stmt->execute();
$stmt->bindColumn('codigo', $cod_asignacion);
$stmt->bindColumn('cod_activosfijos', $cod_activosfijos);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('cod_personal2', $cod_personal2);
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
	$stmt2 = $dbh->prepare("UPDATE activofijos_asignaciones set cod_estadoasignacionaf=2,fecha_recepcion=NOW() where codigo=$cod_asignacion");
	$succees=$stmt2->execute();
	if($succees){
		$cod_area=obtenerAreaActivo_asig($cod_asignacion);
		$cod_uo=obtenerUOActivo_asig($cod_asignacion);
		$sql="UPDATE activosfijos 
		set cod_responsables_responsable='$cod_personal',cod_responsables_responsable2='$cod_personal2',cod_area=$cod_area,cod_unidadorganizacional=$cod_uo
		where codigo=$cod_activosfijos";
		//echo $sql;
		$stmtU = $dbh->prepare($sql);	
		$succees1=$stmtU->execute();
	}
}
$b="../index.php?opcion=afEnCustodia";
showAlertSuccessError($succees1,$b);


?>
