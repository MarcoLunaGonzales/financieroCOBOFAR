<?php //ESTADO FINALIZADO

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';



$dbh = new Conexion();

$globalCodUnidad=$_SESSION["globalUnidad"];
$globalUser=$_SESSION["globalUser"];
$mes_actual=$_SESSION['globalMes'];
$anio_actual=$_SESSION['globalNombreGestion'];
//obteniendo codigo de gestion para el registro de planilla
$stmt = $dbh->prepare("SELECT codigo from gestiones where nombre=$anio_actual");
$stmt->execute();
$result= $stmt->fetch();
$cod_gestion=$result['codigo'];

$cod_mes=(integer)$mes_actual;
$cod_estadoplanilla=1;
$created_by=$globalUser;
$modified_by=$globalUser;
$cont=0;
//verificamos si exite registro de planilla en este mes
$sql="SELECT codigo from planillas_indemnizaciones where cod_gestion=$cod_gestion and cod_mes=$cod_mes";
// echo "<br><br><br>".$sql; 
$stmtPlanillas = $dbh->prepare($sql);
$stmtPlanillas->execute();
$stmtPlanillas->bindColumn('codigo',$codigo_planilla);
while ($row = $stmtPlanillas->fetch())
{
  $cont+=1; 
}
if($cont==0){//insert - cuando no existe planilla
  $sqlInsert="INSERT into planillas_indemnizaciones(cod_gestion,cod_mes,cod_estadoplanilla,created_by,modified_by) values(:cod_gestion,:cod_mes,:cod_estadoplanilla,:created_by,:modified_by)";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_mes',$cod_mes);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $flagSuccess=$stmtInsert->execute();
}else{
  $flagSuccess=0;//alerta indicando que ya existe planilla del mes
}
$dbh=null;

$stmt=null;
$stmtPlanillas=null;
$stmtInsert=null;

showAlertSuccessError3($flagSuccess,'index.php?opcion=planillasIndemnizacionesPersonal');



?>