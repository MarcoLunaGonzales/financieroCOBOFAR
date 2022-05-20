<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_contrato=$_POST['cod_contrato'];
$cod_personal=$_POST['cod_personal'];
$cod_tipocontrato=$_POST['cod_tipocontrato'];
$fecha_inicio=$_POST['fecha_inicio'];
$fecha_fin=$_POST['fecha_fin'];
$cod_estadoreferencial=$_POST['cod_estadoreferencial'];
$observaciones=$_POST['observaciones'];


$stmtContrato = $dbhU->prepare("SELECT * from tipos_contrato_personal where codigo=:codigo");
$stmtContrato->bindParam(':codigo',$cod_tipocontrato);
$stmtContrato->execute();
$resultC=$stmtContrato->fetch();
$nombre_tipo_contrato=$resultC['nombre'];
$duracion_meses=$resultC['duracion_meses'];
//SACAMOS LOS VALORES DE CONFIGURACION
$stmtConfig = $dbhU->prepare("SELECT * from configuraciones where id_configuracion in (11,12)");
$stmtConfig->execute();
$stmtConfig->bindColumn('valor_configuracion', $valor_configuracion);	
$stmtConfig->bindColumn('id_configuracion', $id_configuracion);

$cod_defecto_contrato_otros=obtenerValorConfiguracion(79);
while ($row = $stmtConfig->fetch(PDO::FETCH_BOUND)) {
	switch ($id_configuracion) {
		case 11:
			$val_conf_dias_alerta_def=$valor_configuracion;
			break;
		case 12:
			$val_conf_meses_alerta_indef=$valor_configuracion;
			break;
		
		default:
			# code...
			break;
	}
}

if($nombre_tipo_contrato=="CONTRATO INDEFINIDO"){
	$fecha_fincontrato="INDEFINIDO";
	//sumo 1 día
	$fecha_evaluacioncontrato_x= date("Y-m-d",strtotime($fecha_inicio."+ ".$val_conf_meses_alerta_indef." month")); 
	$fecha_evaluacioncontrato = date("Y-m-d",strtotime($fecha_evaluacioncontrato_x."- 1 days")); 
	
}elseif($cod_tipocontrato==$cod_defecto_contrato_otros){//contratos otros,viene con fecha fin
	$fecha_fincontrato=$fecha_fin;	
	$fecha_evaluacioncontrato= date("Y-m-d",strtotime($fecha_fincontrato."- ".$val_conf_dias_alerta_def." days")); 
}else{
	$fecha_fincontrato_x= date("Y-m-d",strtotime($fecha_inicio."+ ".$duracion_meses." month")); 
	$fecha_fincontrato = date("Y-m-d",strtotime($fecha_fincontrato_x."- 1 days")); 
	$fecha_evaluacioncontrato= date("Y-m-d",strtotime($fecha_fincontrato."- ".$val_conf_dias_alerta_def." days")); 
}
	// Prepare
if($cod_estadoreferencial==1){//insertar
	//verificamos que no exita un contrato abierto
	$sqlControlador="SELECT count(*) as contador from personal_contratos where cod_personal=$cod_personal and cod_estadoreferencial=1 and cod_estadocontrato=1 ORDER BY codigo desc";
	$stmtControlador = $dbhU->prepare($sqlControlador);
	$stmtControlador->execute();
	$resultControlador=$stmtControlador->fetch();
	$contador=$resultControlador['contador'];
	// $cod_estadocontrato_aux=$resultControlador['cod_estadocontrato'];
	if($contador==0){//no existen contratos abiertos
		$cod_estadocontrato=1;
		$sql="INSERT INTO personal_contratos(cod_personal,cod_tipocontrato,fecha_iniciocontrato,fecha_fincontrato,fecha_evaluacioncontrato,cod_estadoreferencial,cod_estadocontrato) values(:cod_personal,:cod_tipocontrato,:fecha_iniciocontrato,:fecha_fincontrato,:fecha_evaluacioncontrato,:cod_estadoreferencial,:cod_estadocontrato) ";
		$stmtU = $dbhU->prepare($sql);
		// Bind
		$stmtU->bindParam(':cod_personal', $cod_personal);
		$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
		$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
		$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
		$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
		$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
		$stmtU->bindParam(':cod_estadocontrato', $cod_estadocontrato);
		$flagsucces=$stmtU->execute();
	}else{
		$flagsucces=false;
		$result =2;
	}
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE personal_contratos set cod_tipocontrato=:cod_tipocontrato,fecha_iniciocontrato=:fecha_iniciocontrato,fecha_fincontrato=:fecha_fincontrato,fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);
	$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
	$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
	$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
	$flagsucces=$stmtU->execute();
}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE personal_contratos set cod_estadoreferencial=2,cod_estadocontrato=2 where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);
	$flagsucces=$stmtU->execute();	
}elseif ($cod_estadoreferencial==4) {//actualizar fecha evaluacion
	$sql="UPDATE personal_contratos set fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);	
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_inicio);
	$flagsucces=$stmtU->execute();	
}elseif ($cod_estadoreferencial==5) {//retirar personal y finalizar contrato
	//echo "personal:".$cod_personal."- fecha :".$fecha_inicio."-cod_tipocontrato :".$cod_tipocontrato."-ober:".$observaciones;
	$cod_estadoreferencial=1;
	// $cod_estadoreferencialPersonal=2;
	// $cod_estadopersonal=3;
	$cod_estadocontrato=2;//**nuevo
	//verificamos si todos sus contratos estan fina,izados
	$sqlControlador="SELECT codigo,cod_estadocontrato from personal_contratos where cod_personal=$cod_personal and cod_estadoreferencial=1 ORDER BY codigo desc";
	$stmtControlador = $dbhU->prepare($sqlControlador);
	$stmtControlador->execute();
	$resultControlador=$stmtControlador->fetch();
	$cod_contrato_aux=$resultControlador['codigo'];
	$cod_estadocontrato_aux=$resultControlador['cod_estadocontrato'];
	if($cod_estadocontrato_aux==1){
		//finalizamos contrato
		$cod_estadocontrato=2;
		$fecha_finalizado=date("Y-m-d H:i:s");
		$sql="UPDATE personal_contratos set cod_estadocontrato=$cod_estadocontrato,fecha_finalizado='$fecha_finalizado' where codigo=$cod_contrato_aux";
		$stmtContrato = $dbhU->prepare($sql);
		$stmtContrato->execute();
		/**INSERTAMOS EN PERSONAL RETIROS*/
		$sql="INSERT INTO personal_retiros(cod_personal,cod_tiporetiro,fecha_retiro,observaciones,cod_estadoreferencial) values($cod_personal,$cod_tipocontrato,'$fecha_inicio','$observaciones',$cod_estadoreferencial)";
		$stmtRetiros = $dbhU->prepare($sql);
		$flagsucces=$stmtRetiros->execute();
		/**cambiamos de estado a personal*/
		$sqlpersonal="UPDATE personal set cod_estadopersonal=3 where codigo=$cod_personal";
		$stmtUP = $dbhU->prepare($sqlpersonal);
		$stmtUP->execute();
	}else{
		$flagsucces=false;
		$result=2;
	}
	
}else{//finalizar contrato
	$cod_estadocontrato=2;
	$fecha_finalizado=date("Y-m-d H:i:s");
	$sql="UPDATE personal_contratos set cod_estadocontrato=$cod_estadocontrato,fecha_finalizado='$fecha_finalizado' where codigo=$cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();
}
if($flagsucces){
      $result =1;
 }
echo $result;
$dbhU=null;

?>
