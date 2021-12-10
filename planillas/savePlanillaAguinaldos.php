<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../rrhh/configModule.php';

require_once '../functionsGeneral.php';



$result_x=0;


// $dbhU = new Conexion();

$dbh = new Conexion();
$dbhI = new Conexion();


//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];

$stmtDatosPlanilla = $dbh->prepare("SELECT cod_gestion from planillas_aguinaldos where codigo=$cod_planilla");
$stmtDatosPlanilla->execute();
$resultDatosPlanilla =  $stmtDatosPlanilla->fetch();
$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];

if($sw==2 || $sw==1){//procesar o reprocesar planilla
	$cod_planilla_1=obtener_id_planilla($cod_gestion_x,9);
	$cod_planilla_2=obtener_id_planilla($cod_gestion_x,10);
	$cod_planilla_3=obtener_id_planilla($cod_gestion_x,11);

	if($sw==2){
		//actualizamos estado
		$stmtU = $dbh->prepare("UPDATE planillas_aguinaldos 
		set cod_estadoplanilla=:cod_estadoplanilla
		where codigo=:cod_planilla");
		$stmtU->bindParam(':cod_planilla', $cod_planilla);
		$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
		$flagSuccess=$stmtU->execute();
	}
	$stmtDelete = $dbh->prepare("DELETE  FROM planillas_aguinaldos_detalle where cod_planilla=$cod_planilla");
	$stmtDelete->execute();

	$created_by=1;
	$modified_by=1;
		
	//============select del personal
	$sql = "SELECT codigo,ing_planilla from personal where cod_estadoreferencial=1 and cod_estadopersonal=1";

	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('codigo', $codigo_personal);
	$stmtPersonal->bindColumn('ing_planilla', $ing_planilla);	
	while ($rowC = $stmtPersonal->fetch()) 
	{

		$anios_trabajados=obtener_anios_trabajados($ing_planilla);
		$meses_trabajados=obtener_meses_trabajados($ing_planilla);
		$dias_trabajados=obtener_dias_trabajados($ing_planilla);
		if($anios_trabajados>0){
			$meses_trabajados_del_anio=12;
			$dias_trabajados_del_anio=0;
			$liquido_mes1=obtenerSueldomes($codigo_personal,$cod_planilla_1);//obtener sueldo de sept
			$liquido_mes2=obtenerSueldomes($codigo_personal,$cod_planilla_2);//obtener sueldo de octub
			$liquido_mes3=obtenerSueldomes($codigo_personal,$cod_planilla_3);//obtener sueldo de nov
			
		}elseif($meses_trabajados>2){
			$meses_trabajados_del_anio=$meses_trabajados;
			$dias_trabajados_del_anio=$dias_trabajados;
			$liquido_mes1=obtenerSueldomes($codigo_personal,$cod_planilla_1);
			$liquido_mes2=obtenerSueldomes($codigo_personal,$cod_planilla_2);
			$liquido_mes3=obtenerSueldomes($codigo_personal,$cod_planilla_3);
			
		}elseif($meses_trabajados==2 && $dias_trabajados==29){//si entra el 1 octubre
			$meses_trabajados_del_anio=3;
			$dias_trabajados_del_anio=0;
			$liquido_mes1=0;//no se genero en este caso
			$liquido_mes2=obtenerSueldomes($codigo_personal,$cod_planilla_2);
			$liquido_mes3=obtenerSueldomes($codigo_personal,$cod_planilla_3);
		}else{//
			$meses_trabajados_del_anio=$meses_trabajados;
			$dias_trabajados_del_anio=$dias_trabajados;
			$liquido_mes1=0;
			$liquido_mes2=0;
			$liquido_mes3=0;
		}
		$promedio_sueldos=($liquido_mes1+$liquido_mes2+$liquido_mes3)/3;
		$dias_sueldo=$promedio_sueldos/360*$dias_trabajados_del_anio;
		$meses_sueldo=$promedio_sueldos/12*$meses_trabajados_del_anio;
		$total_pago_aguinaldo=$dias_sueldo+$meses_sueldo;
		//==== insert de panillas de  personal mes
		$sqlInsertPlanillas="INSERT into planillas_aguinaldos_detalle(cod_planilla,cod_personal,sueldo_1,sueldo_2,sueldo_3,
		  meses_trabajados,dias_trabajados,total_aguinaldo,created_by,modified_by)
		 values(:cod_planilla,:codigo_personal,:sueldo1,:sueldo2,:sueldo3,:meses_trabajados,:dias_trabajados,
		 	:total_aguinaldo,:created_by,:modified_by)";
		$stmtInsertPlanillas = $dbhI->prepare($sqlInsertPlanillas);
		$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
		$stmtInsertPlanillas->bindParam(':sueldo1',$liquido_mes1);
		$stmtInsertPlanillas->bindParam(':sueldo2',$liquido_mes2);
		$stmtInsertPlanillas->bindParam(':sueldo3',$liquido_mes3);
		$stmtInsertPlanillas->bindParam(':meses_trabajados',$meses_trabajados_del_anio);	
		$stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados_del_anio);		
		$stmtInsertPlanillas->bindParam(':total_aguinaldo',$total_pago_aguinaldo);
		$stmtInsertPlanillas->bindParam(':created_by',$created_by);
		$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
		$flagSuccessIP=$stmtInsertPlanillas->execute();		
	}

}elseif($sw==3)
{//cerrar planilla	
	// Prepare
	$stmtU = $dbh->prepare("UPDATE planillas_aguinaldos 
	set cod_estadoplanilla=:cod_estadoplanilla
	where codigo=:cod_planilla");
	// Bind
	$stmtU->bindParam(':cod_planilla', $cod_planilla);
	$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
	$flagSuccessIP=$stmtU->execute();
}
if($flagSuccessIP){
	$result_x = 1;
}

$dbh=null;
$dbhI=null;
$stmtU=null;
$stmtInsertPlanillas=null;
$stmtDatosPlanilla=null;

echo $result_x;
?>