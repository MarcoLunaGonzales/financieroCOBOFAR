<?php
require_once '../conexion.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
$estado=$_GET['e'];
$sqlInsertDet="UPDATE horarios_persona SET estado=$estado where codigo='$codigo';";    
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();
?>
<script type="text/javascript">
   window.location.href='../index.php?opcion=rpt_asignacion_horarios_personas';
</script>
