<?php

require_once 'conexion.php';
require_once 'functionsGeneral.php';
$dbh = new Conexion();
// session_start();
// $globalUser=$_SESSION["globalUser"];
// $globalUser=-100;
$codigo=$_GET["codigo"];
if(isset($_GET['q'])){
	$q=$_GET["q"];
	$a=$_GET["a"];
	$s=$_GET["s"];
}

$cod_estadoreferencial=1;
// Prepare
$sql="UPDATE personal_permisos set cod_estado=2 where codigo=$codigo";
 // echo $sql;
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();
if(isset($_GET['q'])){
	showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalLista&q='.$q.'&a='.$a.'&s='.$s);
}else{
	showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalLista');
}

?>
