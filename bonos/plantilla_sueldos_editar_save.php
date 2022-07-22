<?php

// require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
session_start();
$globalUser=0;
if(isset($_SESSION['globalUser'])){
	$globalUser=$_SESSION['globalUser'];
}


$codigo_e=$_POST['codigo_e'];
$cod_gestion_e=$_POST['cod_gestion_e'];
$cod_mes_e=$_POST['cod_mes_e'];

$dias_trabajados_e=$_POST['dias_trabajados_e'];
$faltas_e=$_POST['faltas_e'];
$faltas_sin_descuento_e=$_POST['faltas_sin_descuento_e'];
$dias_vacacion_e=$_POST['dias_vacacion_e'];
$domingos_e=$_POST['domingos_e'];
$feriados_e=$_POST['feriados_e'];
$noches_e=$_POST['noches_e'];
$domingo_reemp_e=$_POST['domingo_reemp_e'];
$feriado_reemp_e=$_POST['feriado_reemp_e'];
$ordinario_reemp_e=$_POST['ordinario_reemp_e'];
$hxdomingo_e=$_POST['hxdomingo_e'];
$hxferiado_e=$_POST['hxferiado_e'];
$hxdianormal_e=$_POST['hxdianormal_e'];
$reintegro_e=$_POST['reintegro_e'];
$obs_reintegro_e=$_POST['obs_reintegro_e'];

$anticipo_e=$_POST['anticipo_e'];

$prestamos_e=$_POST['prestamos_e'];
$inventarios_e=$_POST['inventarios_e'];
$vencidos_e=$_POST['vencidos_e'];
$atrasos_e=$_POST['atrasos_e'];
$faltante_caja_e=$_POST['faltante_caja_e'];
$otros_descuentos_e=$_POST['otros_descuentos_e'];
$aporte_sindicato_e=$_POST['aporte_sindicato_e'];

 $sqlKardex="UPDATE personal_kardex_mes set faltas='$faltas_e',faltas_sin_descuento='$faltas_sin_descuento_e',dias_vacacion='$dias_vacacion_e',dias_trabajados='$dias_trabajados_e',domingos_trabajados_normal='$domingos_e',feriado_normal='$feriados_e',noche_normal='$noches_e',domingo_reemplazo='$domingo_reemp_e',feriado_reemplazo='$feriado_reemp_e',ordianrio_reemplazo='$ordinario_reemp_e',hxdomingo_extras='$hxdomingo_e',hxferiado_extras='$hxferiado_e',hxdnnormal_extras='$hxdianormal_e',reintegro='$reintegro_e',obs_reintegro='$obs_reintegro_e',modified_at=NOW(),modified_by='$globalUser'
 	where cod_personal=$codigo_e and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1";
//echo $sqlKardex;
$stmtKardex = $dbh->prepare($sqlKardex);
$flagSuccess=$stmtKardex->execute();                    
if($flagSuccess){
	//**INGRESAMOS ANTICIPOS
	$sql="UPDATE anticipos_personal set  monto='$anticipo_e'
	where cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1 and cod_personal='$codigo_e'";
	// echo $sql;
	$stmtAnticipos = $dbh->prepare($sql);
	$flagSuccess=$stmtAnticipos->execute();
	//prestamos
	$stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$prestamos_e
	where cod_descuento=1 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	$flagSuccess=$stmtDescuentos->execute();    
	//inventarios
	// $stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$inventarios_e
	// where cod_descuento=2 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	// $flagSuccess=$stmtDescuentos->execute();    
	//vencidos
	// $stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$vencidos_e
	// where cod_descuento=3 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	// $flagSuccess=$stmtDescuentos->execute();    
	//atrasos
	$stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$atrasos_e
	where cod_descuento=4 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	$flagSuccess=$stmtDescuentos->execute();    
	//faltante caja
	// $stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$faltante_caja_e
	// where cod_descuento=5 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	// $flagSuccess=$stmtDescuentos->execute();    
	//otros descuentos
	// $stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$otros_descuentos_e
	// where cod_descuento=6 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	// $flagSuccess=$stmtDescuentos->execute();    
	//aporte sindicado
	// $stmtDescuentos = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$aporte_sindicato_e
	// where cod_descuento=100 and cod_personal='$codigo_e' and cod_gestion='$cod_gestion_e' and cod_mes='$cod_mes_e' and cod_estadoreferencial=1");
	// $flagSuccess=$stmtDescuentos->execute();    
}

if($flagSuccess){
    echo 1;
}else echo 2;

?>
