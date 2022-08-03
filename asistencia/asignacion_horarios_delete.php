<?php
require_once '../conexion.php';
$dbh = new Conexion();
$horario=$_GET['cod_horario'];
$codigo=$_GET['codigo'];
$estado=$_GET['e'];
$sqlInsertDet="UPDATE horarios_detalle SET cod_estadoreferencial=$estado where codigo='$codigo';";    
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();
?>
<script type="text/javascript">
   window.location.href='../index.php?opcion=rpt_asignacion_horarios_from&codigo=<?=$horario?>';
</script>
