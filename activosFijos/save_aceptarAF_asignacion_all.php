<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';
require_once '../perspectivas/configModule.php';
$stmt = $dbh->prepare("SELECT cod_activosfijos,cod_personal
 FROM activofijos_asignaciones
  where cod_estadoasignacionaf=2 and cod_personal=:cod_personal ");
// Bind
$stmt->bindParam(':cod_personal', $cod_personal);
$stmt->execute();
$stmt->bindColumn('cod_activosfijos', $cod_activosfijos);
$stmt->bindColumn('cod_personal', $cod_personal);

$cont=1;
$cod_estadoasignacionaf=5;
$fecha_devolucion=date("Y-m-d H:i:s");

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
	$observacion=$_POST["observacionD".$cont];

	$stmtU = $dbh->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_devolucion=:observacion,fecha_devolucion=:fecha_devolucion
	where cod_activosfijos=:cod_af and cod_personal = :cod_personal");
	// Bind
	$stmtU->bindParam(':cod_af', $cod_activosfijos);
	$stmtU->bindParam(':cod_personal', $cod_personal);
	$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
	$stmtU->bindParam(':fecha_devolucion', $fecha_devolucion);
	$stmtU->bindParam(':observacion', $observacion);
	$stmtU->execute();
	


	$cod_area=obtenerAreaActivo_asig($cod_asignacion);
	$cod_uo=obtenerUOActivo_asig($cod_asignacion);
	$sql="UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf='$cod_estadoasignacionaf',observaciones_recepcion='$observacion',fecha_recepcion='$fecha_recepcion'
	where codigo=$cod_asignacion";
	//echo $sql;
	$stmtU2 = $dbhU->prepare($sql);
	$succees=$stmtU2->execute();
	if($succees){
		$sql="UPDATE activosfijos 
		set cod_responsables_responsable=$cod_personal,cod_area=$cod_area,cod_unidadorganizacional=$cod_uo
		where codigo=$cod_af";
		//echo $sql;
		$stmtU = $dbhU->prepare($sql);	
	}


	

}

$a=true;
$b="../index.php?opcion=afEnCustodia";
showAlertSuccessError($a,$b);


?>
