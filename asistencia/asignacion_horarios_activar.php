<?php
require_once '../conexion.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
$estado=$_GET['e'];
if($estado==0){
	$estadoactivo=1;
}else{
	$estadoactivo=0;
}
$sqlInsertDet="UPDATE horarios_areas SET activo=$estadoactivo where codigo='$codigo';";    
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();
?>
<script type="text/javascript">
   window.location.href='../index.php?opcion=rpt_asignacion_horarios_from';
</script>
