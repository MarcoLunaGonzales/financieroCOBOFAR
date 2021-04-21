<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../perspectivas/configModule.php';

$result=0;
$stmtU=false;

$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_asignacion=$_POST['cod_af'];//llega el codigo de asignacion
$cod_af=obtener_codigoAF_asignacion($cod_asignacion);
$cod_personal=$_POST['cod_personal'];
$cod_estadoasignacionaf=$_POST['cod_estadoasignacionaf'];
$observacion=$_POST['observacion'];

//echo "llega ".$cod_asignacion;

$fecha_recepcion=date("Y-m-d H:i:s");
if($cod_estadoasignacionaf==5){
	//cuando devuelve AF
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_devolucion=:observacion,fecha_devolucion=:fecha_devolucion
	where codigo= :cod_asignacion");
	// Bind
	$stmtU->bindParam(':cod_asignacion', $cod_asignacion);
	$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
	$stmtU->bindParam(':fecha_devolucion', $fecha_recepcion);
	$stmtU->bindParam(':observacion', $observacion);

}elseif($cod_estadoasignacionaf==6){
	//cuando se acepta devolucion de AF
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=4
	where codigo = :cod_asignacion");
	// Bind
	$stmtU->bindParam(':cod_asignacion', $cod_asignacion);
	//$stmtU->execute();
}elseif($cod_estadoasignacionaf==7){
	//cuando se rechaza devolucion AF
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=2
	where codigo=:cod_asignacion");
	// Bind
	$stmtU->bindParam(':cod_asignacion', $cod_asignacion);
	
//$stmtU->execute();

}elseif($cod_estadoasignacionaf==2)//acepta recepcion de af
{
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
else{
	// Prepare
	$stmtU = $dbhU->prepare("UPDATE activofijos_asignaciones 
	set cod_estadoasignacionaf=:cod_estadoasignacionaf,observaciones_recepcion=:observacion,fecha_recepcion=:fecha_recepcion
	where codigo=:cod_asignacion");
	// Bind
	$stmtU->bindParam(':cod_asignacion', $cod_asignacion);
	$stmtU->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
	$stmtU->bindParam(':fecha_recepcion', $fecha_recepcion);
	$stmtU->bindParam(':observacion', $observacion);
	//$stmtU->execute();
}
if($stmtU->execute()){
  $result =1;
}
echo $result;
$dbhU=null;

?>