<?php
require_once '../conexion.php';
$dbh = new Conexion();
$codigo=$_GET['codigo'];
$estado=$_GET['e'];
$sqlInsertDet="UPDATE horarios_area SET estado=$estado where codigo='$codigo';";    
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();

//personal
$modal_area=0;
$sqlVerificar="SELECT cod_area FROM horarios_area where codigo='$codigo';";
$stmtVerificar = $dbh->prepare($sqlVerificar);
$stmtVerificar->execute();   
while ($row = $stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
   $modal_area=$row['cod_area'];
}

$sqlInsertDet="UPDATE horarios_persona SET estado=0 where cod_persona in (SELECT codigo FROM personal where cod_area=$modal_area and cod_estadoreferencial=1 and cod_estadopersonal=1) and estado=1;";    
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccess=$stmtInsertDet->execute();
?>
<script type="text/javascript">
   window.location.href='../index.php?opcion=rpt_asignacion_horarios_areas';
</script>
