<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../rrhh/configModule.php';

require_once '../functionsGeneral.php';



$result_x=0;


// $dbhU = new Conexion();

$dbh = new Conexion();



session_start();
$globalUser=$_SESSION["globalUser"];


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

	$created_by=$globalUser;
	$modified_by=$globalUser;
		
	//============select del personal
	$sql = "SELECT codigo,ing_planilla from personal where cod_estadoreferencial=1 and cod_estadopersonal=1";

	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('codigo', $codigo_personal);
	$stmtPersonal->bindColumn('ing_planilla', $ing_planilla);	

	while ($rowC = $stmtPersonal->fetch()) 
	{	
		$anio_actual= date('Y');
		$fecha_fin=obtener_fecha_fin_contrato_personal($codigo_personal);
		if($fecha_fin=='INDEFINIDO'){
			$fecha_fin=$anio_actual.'-12-31';
		}
		if($ing_planilla==$anio_actual.'-10-01'){
			//$haber_basico."@@@".$bono_antiguedad."@@@".$bonos_otros."@@@".$total_ganado
			$datos_planilla1=explode("@@@", "0@@@0@@@0@@@0");
			$datos_planilla2=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_2));
			$datos_planilla3=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_3));

			$liquido_mes1=$datos_planilla1[3];//no se genero en este caso
			$liquido_mes2=$datos_planilla2[3];
			$liquido_mes3=$datos_planilla3[3];
			$sumatoria_sueldos=$liquido_mes1+$liquido_mes2+$liquido_mes3;
			$promedio_sueldos=$sumatoria_sueldos/2;

			$promedio_basico=($datos_planilla1[0]+$datos_planilla2[0]+$datos_planilla3[0])/2;
			$promedio_antiguedad=($datos_planilla1[1]+$datos_planilla2[1]+$datos_planilla3[1])/2;
			$promedio_obonos=($datos_planilla1[2]+$datos_planilla2[2]+$datos_planilla3[2])/2;
		}elseif($ing_planilla<$anio_actual.'-10-01'){
			$datos_planilla1=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_1));
			$datos_planilla2=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_2));
			$datos_planilla3=explode("@@@", obtenerdatos_planilla($codigo_personal,$cod_planilla_3));

			$liquido_mes1=$datos_planilla1[3];//no se genero en este caso
			$liquido_mes2=$datos_planilla2[3];
			$liquido_mes3=$datos_planilla3[3];
			$sumatoria_sueldos=$liquido_mes1+$liquido_mes2+$liquido_mes3;
			$promedio_sueldos=$sumatoria_sueldos/3;

			$promedio_basico=($datos_planilla1[0]+$datos_planilla2[0]+$datos_planilla3[0])/3;
			$promedio_antiguedad=($datos_planilla1[1]+$datos_planilla2[1]+$datos_planilla3[1])/3;
			$promedio_obonos=($datos_planilla1[2]+$datos_planilla2[2]+$datos_planilla3[2])/3;
		}elseif($ing_planilla>$anio_actual.'-10-01'){
			$liquido_mes1=0;
			$liquido_mes2=0;
			$liquido_mes3=0;
			$sumatoria_sueldos=0;
			$promedio_sueldos=0;

			$promedio_basico=0;
			$promedio_antiguedad=0;
			$promedio_obonos=0;
		}


		if($ing_planilla<$anio_actual.'-01-01'){
			$dias_360=formatNumberDec(days_360($anio_actual.'-01-01',$fecha_fin)/30);
		}else{
			$dias_360=formatNumberDec(days_360($ing_planilla,$fecha_fin)/30);
		}
		// echo $dias_360."<br>";
		$total_pago_aguinaldo=$promedio_sueldos*$dias_360/12;
		//==== insert de panillas de personal mes
		$sqlInsertPlanillas="INSERT into planillas_aguinaldos_detalle(cod_planilla,cod_personal,sueldo_1,sueldo_2,sueldo_3,total_aguinaldo,created_by,modified_by,dias_360,sumatoria_ganado,promedio_ganado,promedio_basico,promedio_antiguedad,promedio_obonos)
		 values(:cod_planilla,:codigo_personal,:sueldo1,:sueldo2,:sueldo3,:total_aguinaldo,:created_by,:modified_by,:dias_360,:sumatoria_ganado,:promedio_ganado,:promedio_basico,:promedio_antiguedad,:promedio_obonos)";
		$stmtInsertPlanillas = $dbh->prepare($sqlInsertPlanillas);
		$stmtInsertPlanillas->bindParam(':cod_planilla', $cod_planilla);
		$stmtInsertPlanillas->bindParam(':codigo_personal',$codigo_personal);
		$stmtInsertPlanillas->bindParam(':sueldo1',$liquido_mes1);
		$stmtInsertPlanillas->bindParam(':sueldo2',$liquido_mes2);
		$stmtInsertPlanillas->bindParam(':sueldo3',$liquido_mes3);
		// $stmtInsertPlanillas->bindParam(':meses_trabajados',$meses_trabajados_del_anio);	
		// $stmtInsertPlanillas->bindParam(':dias_trabajados',$dias_trabajados_del_anio);		
		$stmtInsertPlanillas->bindParam(':total_aguinaldo',$total_pago_aguinaldo);
		$stmtInsertPlanillas->bindParam(':created_by',$created_by);
		$stmtInsertPlanillas->bindParam(':modified_by',$modified_by);
		$stmtInsertPlanillas->bindParam(':dias_360',$dias_360);
		$stmtInsertPlanillas->bindParam(':sumatoria_ganado',$sumatoria_sueldos);
		$stmtInsertPlanillas->bindParam(':promedio_ganado',$promedio_sueldos);

		$stmtInsertPlanillas->bindParam(':promedio_basico',$promedio_basico);
		$stmtInsertPlanillas->bindParam(':promedio_antiguedad',$promedio_antiguedad);
		$stmtInsertPlanillas->bindParam(':promedio_obonos',$promedio_obonos);

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
$stmtU=null;
$stmtInsertPlanillas=null;
$stmtDatosPlanilla=null;

echo $result_x;
?>