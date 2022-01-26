<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';
require_once '../functionsGeneral.php';

$result_x=0;
$dbh = new Conexion();
session_start();
 $globalUser=$_SESSION["globalUser"];
//RECIBIMOS LAS VARIABLES
$cod_planilla=$_POST['cod_planilla'];
$cod_estadoplanilla=$_POST['sw'];
$sw=$_POST['sw'];

$stmtDatosPlanilla = $dbh->prepare("SELECT (select g.nombre from gestiones  g where g.codigo=p.cod_gestion)as gestion ,p.cod_gestion,p.cod_mes from planillas_indemnizaciones p
where p.codigo=$cod_planilla");
$stmtDatosPlanilla->execute();
$resultDatosPlanilla =  $stmtDatosPlanilla->fetch();
$gestion_x = $resultDatosPlanilla['gestion'];
$cod_gestion_x = $resultDatosPlanilla['cod_gestion'];
$cod_mes_x = $resultDatosPlanilla['cod_mes'];
$fecha_planilla="01-".$cod_mes_x."-".$gestion_x;

$date = $gestion_x."-".$cod_mes_x."-01";
$fecha_planilla_indemnizacion=date("Y-m-t", strtotime($date));

//resto 1 mes
$mes3=$cod_mes_x;
$mes2 = date("m",strtotime($fecha_planilla."- 1 month"));
$mes1 = date("m",strtotime($fecha_planilla."- 2 month"));

$created_by=$globalUser;
$modified_by=$globalUser;

if($sw==2 || $sw==1){//procesar o reprocesar planilla
	$cod_planilla_1=obtener_id_planilla($cod_gestion_x,$mes1);
	$cod_planilla_2=obtener_id_planilla($cod_gestion_x,$mes2);
	$cod_planilla_3=obtener_id_planilla($cod_gestion_x,$mes3);

	if($sw==2){
		//actualizamos estado
		$stmtU = $dbh->prepare("UPDATE planillas_indemnizaciones 
		set cod_estadoplanilla=:cod_estadoplanilla
		where codigo=:cod_planilla");
		$stmtU->bindParam(':cod_planilla', $cod_planilla);
		$stmtU->bindParam(':cod_estadoplanilla', $cod_estadoplanilla);
		$flagSuccess=$stmtU->execute();
	}
	$stmtDelete = $dbh->prepare("DELETE FROM planillas_indemnizaciones_detalle where cod_planilla=$cod_planilla");
	$stmtDelete->execute();	
	//============select del personal
	$sql = "SELECT codigo,ing_planilla,cod_area from personal where cod_estadoreferencial=1 and cod_estadopersonal=1";
	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('codigo', $codigo_personal);
	$stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
	$stmtPersonal->bindColumn('cod_area', $cod_area);
	while ($rowC = $stmtPersonal->fetch()) 
	{	
		// $anio_actual= date('Y');
		// $fecha_fin=obtener_fecha_fin_contrato_personal($codigo_personal);
		// if($fecha_fin=='INDEFINIDO'){
		// 	$fecha_fin=$anio_actual.'-12-31';
		// }
		$datos_planilla1=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_1));
		$datos_planilla2=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_2));
		$datos_planilla3=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_3));
		$liquido_mes1=$datos_planilla1[3];
		$liquido_mes2=$datos_planilla2[3];
		$liquido_mes3=$datos_planilla3[3];

		if($liquido_mes1==null || $liquido_mes1==""){
			$liquido_mes1=0;
		}
		if($liquido_mes2==null || $liquido_mes2==""){
			$liquido_mes2=0;
		}
		if($liquido_mes3==null || $liquido_mes3==""){
			$liquido_mes3=0;
		}

		$promedio_ganado=($liquido_mes1+$liquido_mes2+$liquido_mes3)/3;
		$date1 = new DateTime($ing_planilla);
		$date2 = new DateTime($fecha_planilla_indemnizacion);
		$diff = $date1->diff($date2);
		$anios_antiguedad=$diff->y;
		$meses_indemnizacion=($diff->m);
		$dias_indemnizacion=($diff->d);
		$quinquenios_pagados=obtenerQuinquenioPagadoPersonal($codigo_personal);
		$anios_indemnizacion=$anios_antiguedad-$quinquenios_pagados;
		$monto_anios=$promedio_ganado*$anios_indemnizacion;
		$monto_meses=($promedio_ganado/12)*$meses_indemnizacion;
		$monto_dias=($promedio_ganado/360)*$dias_indemnizacion;
		$total_indemnizacion=$monto_anios+$monto_meses+$monto_dias;
		$prevision=$total_indemnizacion;
		//==== insert de panillas de personal mes
		$sqlInsertPlanillas="INSERT into planillas_indemnizaciones_detalle(cod_planilla,cod_personal,cod_area,sueldo_1,sueldo_2,sueldo_3,promedio_ganado,anios_antiguedad,quinquenios_pagados,anios_indemnizacion,meses_indemnizacion,dias_indemnizacion,monto_anios,monto_meses,monto_dias,total_indemnizacion,prevision,created_at,created_by,modified_at,modified_by)
		 values(:cod_planilla,:cod_personal,:cod_area,:sueldo_1,:sueldo_2,:sueldo_3,:promedio_ganado,:anios_antiguedad,:quinquenios_pagados,:anios_indemnizacion,:meses_indemnizacion,:dias_indemnizacion,:monto_anios,:monto_meses,:monto_dias,:total_indemnizacion,:prevision,:created_at,:created_by,:modified_at,:modified_by)";
		$stmtInsertPlanillas = $dbh->prepare($sqlInsertPlanillas);
		$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillas->bindParam(':cod_personal',$codigo_personal);
		$stmtInsertPlanillas->bindParam(':cod_area',$cod_area);
		$stmtInsertPlanillas->bindParam(':sueldo_1',$liquido_mes1);
		$stmtInsertPlanillas->bindParam(':sueldo_2',$liquido_mes2);
		$stmtInsertPlanillas->bindParam(':sueldo_3',$liquido_mes3);
		$stmtInsertPlanillas->bindParam(':promedio_ganado',$promedio_ganado);
		$stmtInsertPlanillas->bindParam(':anios_antiguedad',$anios_antiguedad);
		$stmtInsertPlanillas->bindParam(':quinquenios_pagados',$quinquenios_pagados);
		$stmtInsertPlanillas->bindParam(':anios_indemnizacion',$anios_indemnizacion);
		$stmtInsertPlanillas->bindParam(':meses_indemnizacion',$meses_indemnizacion);
		$stmtInsertPlanillas->bindParam(':dias_indemnizacion',$dias_indemnizacion);
		$stmtInsertPlanillas->bindParam(':monto_anios',$monto_anios);
		$stmtInsertPlanillas->bindParam(':monto_meses',$monto_meses);
		$stmtInsertPlanillas->bindParam(':monto_dias',$monto_dias);
		$stmtInsertPlanillas->bindParam(':total_indemnizacion',$total_indemnizacion);
		$stmtInsertPlanillas->bindParam(':prevision',$prevision);
		$stmtInsertPlanillas->bindParam(':created_at',$created_at);
		$stmtInsertPlanillas->bindParam(':created_by',$created_by);
		$stmtInsertPlanillas->bindParam(':modified_at',$modified_at);
		$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
		$flagSuccessIP=$stmtInsertPlanillas->execute();	
	}
}elseif($sw==3)
{//cerrar planilla	
	// Prepare
	$stmtU = $dbh->prepare("UPDATE planillas_indemnizaciones 
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
$stmtU=null;
$stmtInsertPlanillas=null;
$stmtDatosPlanilla=null;

echo $result_x;
?>