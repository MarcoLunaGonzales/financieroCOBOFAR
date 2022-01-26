<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';


session_start();
if(isset($_SESSION["globalUser"])){
	$fechaHoraSistema=date('Y-m-d');
	$globalUser=$_SESSION["globalUser"];
	$dbh = new Conexion();
	$codigo=$codigo;
	$flagSuccess=false;
	$sql="SELECT codigo from comprobantes  where codigo=$codigo and created_by=$globalUser limit 1";
	$stmtsel = $dbh->prepare($sql);
	$stmtsel->execute();
	while ($row = $stmtsel->fetch(PDO::FETCH_BOUND)) {
		$stmt = $dbh->prepare("UPDATE $table set cod_estadocomprobante=2,deleted_at=:fechaHoraSistema,deleted_by=:globalUser where codigo=:codigo and created_by=:globalUser");
		$stmt->bindParam(':codigo', $codigo);
		$stmt->bindParam(':fechaHoraSistema', $fechaHoraSistema);
		$stmt->bindParam(':globalUser', $globalUser);
		$flagSuccess=$stmt->execute();
	}
}else{
  $flagSuccess=false;
}

showAlertSuccessError($flagSuccess,$urlList);
?>