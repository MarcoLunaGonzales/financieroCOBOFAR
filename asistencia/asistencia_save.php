<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
$q=$_POST["q"];
$s=$_POST["s"];
$codigo=$_POST["codigo"];
$cod_mes=$_POST["cod_mes"];
$cod_gestion=$_POST["cod_gestion"];
$dias_trabajado=$_POST["dias_trabajado"];
$contador_personal=$_POST["contador_personal"];

if ($codigo==0) {
	$cod_estadoreferencial=1;
	$cod_estado=1;
	$cod_asistenciapersonal=obtenerCodigoAsitenciaPersonal();
	$sqlCabecera="INSERT INTO asistencia_personal(codigo,cod_gestion,cod_mes,cod_estadoreferencial,created_by,created_at,cod_sucursal,cod_estado ) VALUES ('$cod_asistenciapersonal','$cod_gestion','$cod_mes','$cod_estadoreferencial','$q',NOW(),'$s','$cod_estado')";
}else{
	$cod_asistenciapersonal=$codigo;
	$sqlCabecera="UPDATE asistencia_personal set modified_by='$q',modified_at=NOW() where codigo=$cod_asistenciapersonal";
}
$stmtCabecera = $dbh->prepare($sqlCabecera);
$flagSuccess=$stmtCabecera->execute();
$sqlDelete="DELETE from asistencia_personal_detalle where cod_asistenciapersonal=$cod_asistenciapersonal";
$stmtDelete = $dbh->prepare($sqlDelete);
$stmtDelete->execute();
if($flagSuccess){
	for ($i=1; $i <=$contador_personal ; $i++) { 
		$codigo_personal=$_POST["codigo_personal_".$i];	
		$faltas=$_POST["faltas_".$i];	
		$fecha_faltas=$_POST["fecha_faltas_".$i];	
		$bajas_medicas=$_POST["bajas_medicas_".$i];	
		$dias_vacacion=$_POST["dias_vacacion_".$i];	
		$domingos=$_POST["domingos_".$i];	
		$fecha_domingos=$_POST["fecha_domingos_".$i];	
		$feriados=$_POST["feriados_".$i];
		$fecha_feriados=$_POST["fecha_feriados_".$i];
		$horas_extras=$_POST["horas_extras_".$i];
		$noches=$_POST["noches_".$i];
		$observaciones=$_POST["observaciones_".$i];
		$sql="INSERT INTO asistencia_personal_detalle (cod_asistenciapersonal,cod_personal,dias_normales,faltas,fecha_faltas,baja_medicas,dias_vacacion,domingos,fecha_domingos,feriados,fecha_feriados,horas_extras,noches,observaciones) VALUES ('$cod_asistenciapersonal','$codigo_personal','$dias_trabajado','$faltas','$fecha_faltas','$bajas_medicas','$dias_vacacion','$domingos','$fecha_domingos','$feriados','$fecha_feriados','$horas_extras','$noches','$observaciones')";	
	 	// echo $sql;
		$stmt = $dbh->prepare($sql);
		$flagSuccess=$stmt->execute();
	}

}


showAlertSuccessError($flagSuccess,'../index.php?opcion=asistenciaPersonalLista&q='.$q.'&s='.$s);	

?>
