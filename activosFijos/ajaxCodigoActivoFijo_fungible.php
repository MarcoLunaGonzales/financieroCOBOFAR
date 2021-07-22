<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';

$codigo=$_GET["codigo"];

if($codigo==2){
	$db = new Conexion();
	$sqlCodAF="SELECT codigoactivo From activosfijos where tipo_af='$codigo' and cod_estadoactivofijo=1 order by codigo desc limit 1";
	$stmtCodAF = $db->prepare($sqlCodAF);
	//echo $sqlCodAF;
	$stmtCodAF->execute();
	$codigoActivoFijo="F-1";
	while ($rowCodAF = $stmtCodAF->fetch()){
		$codigoactivo=$rowCodAF["codigoactivo"];
		$array_codigo=explode("-", $rowCodAF["codigoactivo"]);
		$correlativo=$array_codigo[1];
		$correlativo++;
		$codigoActivoFijo="F-".$correlativo;
	}
	?>	
	<input type="text"  class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoActivoFijo;?>"/>
<?php
}else{?>
	<input type="text"   class="form-control" name="codigoactivo" id="codigoactivo" required="true" />
<?php }

 
 
?>
