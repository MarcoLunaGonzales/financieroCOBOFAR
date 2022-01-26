<?php
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'conexion.php';
// tiempo de la sesion por 8 horas

$dbh = new Conexion();
session_start();

date_default_timezone_set('America/La_Paz');

$user=$_POST["user"];
$password=$_POST["password"];

//OBTENEMOS EL VALOR DE LA CONFIGURACION 1 -> LOGIN PROPIO DE MONITOREO    2-> LOGIN POR SERVICIO WEB

$banderaLogin=0;

	$sql="";
		$sql="SELECT p.codigo, CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, pd.perfil,pd.admin
			from personal p, personal_datosadicionales pd 
			where p.codigo=pd.cod_personal and pd.usuario='$user' and pd.contrasena='$password'";
	// echo $sql;

	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	$stmt->bindColumn('codigo', $codigo);
	$stmt->bindColumn('nombre', $nombre);
	$stmt->bindColumn('cod_area', $codArea);
	$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
	$stmt->bindColumn('perfil', $perfil);
	$stmt->bindColumn('admin', $admin_x);

	while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
		//echo "ENTRO A DETALLE";
		//echo "aqui";
		$nombreUnidad=abrevUnidad($codUnidad);
		$nombreArea=abrevArea($codArea);

		//echo $nombreArea;
		//SACAMOS LA GESTION ACTIVA
		$sqlGestion="SELECT cod_gestion FROM gestiones_datosadicionales where cod_estado=1";
		$stmtGestion = $dbh->prepare($sqlGestion);
		$stmtGestion->execute();
		while ($rowGestion = $stmtGestion->fetch(PDO::FETCH_ASSOC)) {
			$codGestionActiva=$rowGestion['cod_gestion'];

			$sql1="SELECT cod_mes from meses_trabajo where cod_gestion='$codGestionActiva' and cod_estadomesestrabajo=3";
	        $stmt1 = $dbh->prepare($sql1);
	        $stmt1->execute();
	        while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
	          $codMesActiva=$row1['cod_mes'];
	        }
		}
		$nombreGestion=nameGestion($codGestionActiva);
		$_SESSION['globalUser']=$codigo;
		$_SESSION['globalNameUser']=$nombre;
		$_SESSION['globalGestion']=$codGestionActiva;
		$_SESSION['globalMes']=$codMesActiva;
		$_SESSION['globalNombreGestion']=$nombreGestion;
		$_SESSION['globalUnidad']=$codUnidad;
		$_SESSION['globalNombreUnidad']=$nombreUnidad;

		$_SESSION['globalArea']=$codArea;
		$_SESSION['globalNombreArea']=$nombreArea;
		$_SESSION['logueado']=1;
		$_SESSION['globalPerfil']=$perfil;
		$_SESSION['globalAdmin']=$admin_x;
		
	}


 header("location:index.php");

?>