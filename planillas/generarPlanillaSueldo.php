<?php //ESTADO FINALIZADO

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


session_start();
$dbh = new Conexion();
$mes_actual=$_SESSION['globalMes'];
$anio_actual=$_SESSION['globalNombreGestion'];

$cod_gestion=$_SESSION['globalGestion'];
$dias_trabajado=$_POST['dias_trabajado'];
//obteniendo codigo de gestion para el registro de planilla
// $stmt = $dbh->prepare("SELECT * from gestiones where nombre=$anio_actual");
// $stmt->execute();
// $result= $stmt->fetch();
// $cod_gestion=$result['codigo'];

$cod_mes=(integer)$mes_actual;
$cod_estadoplanilla=1;
$created_by=1;
$modified_by=1;
// echo "mes ".$mes_actual;
//$fecha_actual=date('Y-m-d');
$cont=0;
$comprobante=0;
//verificamos si exite registro de planilla en este mes
$stmtPlanillas = $dbh->prepare("SELECT codigo from planillas where cod_gestion=$cod_gestion and cod_mes=$cod_mes");
$stmtPlanillas->execute();
$stmtPlanillas->bindColumn('codigo',$codigo_planilla);
while ($row = $stmtPlanillas->fetch())
{
  $cont+=1; 
}
if($cont==0){//insert - cuando no existe planilla
  $sqlInsert="INSERT into planillas(cod_gestion,cod_mes,cod_estadoplanilla,created_by,modified_by,comprobante,dias_trabajo) values(:cod_gestion,:cod_mes,:cod_estadoplanilla,:created_by,:modified_by,:comprobante,:dias_trabajo)";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_mes',$cod_mes);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $stmtInsert->bindParam(':comprobante',$comprobante);
  $stmtInsert->bindParam(':dias_trabajo',$dias_trabajado);
  $flagSuccess=$stmtInsert->execute();
  $estado=2;
  if($flagSuccess){
    $sql="update configuraciones_planillas set valor_configuracion=$dias_trabajado where id_configuracion=22";
    $stmtUpdate = $dbh->prepare($sql);
    $flagSuccess=$stmtUpdate->execute();    
    if($flagSuccess){
      $estado=1;//correcto
    }else{
      $estado=2;//error
    }
  }else{
    $estado=2;//error
  }
  echo $estado;
}else{
  echo 0;//alerta indicando que ya existe planilla del mes
}





?>