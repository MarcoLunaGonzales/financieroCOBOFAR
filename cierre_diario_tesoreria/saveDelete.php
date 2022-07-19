<?php
require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'functionsGeneral.php';
require_once 'functions.php';

$codigo=$_GET['cod'];

//datos para el envio
$dbhB = new Conexion();
  $sqlB="UPDATE cierre_tesoreria SET estado=2 WHERE codigo=$codigo";
 $stmtB = $dbhB->prepare($sqlB);
 $flagSuccess=$stmtB->execute();
showAlertSuccessError($flagSuccess,$urlList); 
?>