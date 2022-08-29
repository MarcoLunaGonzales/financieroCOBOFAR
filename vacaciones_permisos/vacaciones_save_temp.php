<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
// require_once '../layouts/bodylogin.php';
$dbh = new Conexion();
session_start();
$globalUser=$_SESSION["globalUser"];
//$globalUser=-100;

$cod_personal_p=$_POST["codigo_personal_modal"];
$fecha_inicial_p=$_POST["fecha_inicio_modal"];
// $hora_inicio=$_POST["hora_inicio"];
$hora_inicial_p='00:00';
$fecha_final_p=$_POST["fecha_final_modal"];
// $hora_final=$_POST["hora_final"];
$hora_final_p=$hora_inicio='00:00';
$tipo_vacacion=$_POST["tipo_vacacion"];
$dias_permiso_p=$_POST["dias_vacacion"];
$gestion=$_POST["gestion_modal"];
$observaciones_p=$_POST["observaciones_modal"];
$ing_planilla=$_POST["ing_planilla"];

$cod_estadoreferencial=1;
// Prepare
// $sql="INSERT INTO personal_vacaciones (cod_personal, fecha_inicial,hora_inicial,fecha_final,hora_final,cod_tipovacacion,cod_estadoreferencial,dias_vacacion,created_at,created_by,gestion,modulo) VALUES ($personal, '$fecha_inicio','$hora_inicio','$fecha_final','$hora_final','$tipo_vacacion','$cod_estadoreferencial','$dias_vacacion',NOW(),'$globalUser','$gestion',1)";
// // echo $sql;
// $stmt = $dbh->prepare($sql);
// $flagSuccess=$stmt->execute();

$flagSuccess=false;
//escalas
$stmtEscalas = $dbh->prepare("SELECT anios_inicio,anios_final,dias_vacacion from escalas_vacaciones where cod_estadoreferencial=1");
$stmtEscalas->execute();
$stmtEscalas->bindColumn('anios_inicio', $anios_inicio);
$stmtEscalas->bindColumn('anios_final', $anios_final);
$stmtEscalas->bindColumn('dias_vacacion', $dias_vacacion);  
$i=0;
while ($rowEscalas = $stmtEscalas->fetch(PDO::FETCH_ASSOC))
{
  $array_escalas[$i]=$anios_inicio.",".$anios_final.",".$dias_vacacion;
  $i++;
}
//obtenemos los datos del permiso
// $stmtPermiso = $dbh->prepare("SELECT pp.cod_personal,pp.fecha_inicial,pp.fecha_final,pp.hora_inicial,pp.hora_final,pp.observaciones,pp.dias_permiso,p.ing_planilla
// 	from personal_permisos pp join personal p on pp.cod_personal=p.codigo
// 	where pp.codigo=$codigo");
// $stmtPermiso->execute();

// $resultPermiso = $stmtPermiso->fetch();

// $cod_personal_p=$resultPermiso['cod_personal'];
// $fecha_inicial_p=$resultPermiso['fecha_inicial'];
// $fecha_final_p=$resultPermiso['fecha_final'];
// $hora_inicial_p=$resultPermiso['hora_inicial'];
// $hora_final_p=$resultPermiso['hora_final'];
// $observaciones_p=$resultPermiso['observaciones'];
// $dias_permiso_p=$resultPermiso['dias_permiso'];
// $ing_planilla=$resultPermiso['ing_planilla'];
// $cod_estadoreferencial=1;

$fecha_actual=date('Y-m-d');
$fechainicio=$ing_planilla;
$fechainicio_x=$fechainicio;
$sw_aux=true;
while(($fechainicio<=$fecha_actual) and $sw_aux){
	$date1 = new DateTime($fechainicio_x);
	$date2 = new DateTime($fechainicio);
	$diff = $date1->diff($date2);    
	$diferencia_anios=$diff->y;
  for($i=0; $i < count($array_escalas); $i++){
		$datos=explode(',', $array_escalas[$i]);
		$anios_inicio=$datos[0];
		$anios_final=$datos[1];
		$dias_vacacion=$datos[2];
  	if($anios_inicio<=$diferencia_anios and $diferencia_anios<$anios_final){
        $gestion=date('Y', strtotime($fechainicio));
        $dias_utilizadas=obtenerDiasVacacionUzadas($cod_personal_p,$gestion);
        $saldo=$dias_vacacion-$dias_utilizadas;
        if($saldo>0){//todavia hay saldo
        	if($saldo>$dias_permiso_p){
        		$sql="INSERT INTO personal_vacaciones (cod_personal, fecha_inicial,hora_inicial,fecha_final,hora_final,observaciones,cod_estadoreferencial,dias_vacacion,created_at,created_by,gestion,modulo,cod_tipovacacion) VALUES ($cod_personal_p, '$fecha_inicial_p','$hora_inicial_p','$fecha_final_p','$hora_final_p','$observaciones_p','$cod_estadoreferencial','$dias_permiso_p',NOW(),'$globalUser','$gestion',2,'$tipo_vacacion')";
        		$stmt = $dbh->prepare($sql);
					$flagSuccess=$stmt->execute();
					$sw_aux=false;
        	}else{
     			$dias_permiso_p=$dias_permiso_p-$saldo;	
     			$sql="INSERT INTO personal_vacaciones (cod_personal, fecha_inicial,hora_inicial,fecha_final,hora_final,observaciones,cod_estadoreferencial,dias_vacacion,created_at,created_by,gestion,modulo,cod_tipovacacion) VALUES ($cod_personal_p, '$fecha_inicial_p','$hora_inicial_p','$fecha_final_p','$hora_final_p','$observaciones_p','$cod_estadoreferencial','$saldo',NOW(),'$globalUser','$gestion',2,'$tipo_vacacion')";
     			$stmt = $dbh->prepare($sql);
     			$flagSuccess=$stmt->execute();
        	}
        }
			break;
   	}
  }
 $fechainicio=date('Y-m-d',strtotime($fechainicio.'+1 year'));  
}




if($flagSuccess){
	echo 1;
}else{
	echo 2;
}

?>
