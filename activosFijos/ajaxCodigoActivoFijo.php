<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';

$cod_depreciaciones=$_GET["cod_depreciaciones"];
$cod_tiposbienes=$_GET["cod_tiposbienes"];


$db = new Conexion();
$sqlCodAF="SELECT codigoactivo From activosfijos  where cod_depreciaciones=$cod_depreciaciones and cod_tiposbienes=$cod_tiposbienes and tipo_af=1 order by codigoactivo desc limit 1";
$stmtCodAF = $db->prepare($sqlCodAF);
//echo $sqlCodAF;

$stmtCodAF->execute();
$cod_tiposbienes_alterno=obtenercodigoalterno_tipobienes($cod_tiposbienes);
$cod_depreciaciones=str_pad($cod_depreciaciones, 2, '0', STR_PAD_LEFT);
$cod_tiposbienes_alterno=str_pad($cod_tiposbienes_alterno, 2, '0', STR_PAD_LEFT);
$codigoActivoFijo=$cod_depreciaciones."-".$cod_tiposbienes_alterno."-".str_pad(1, 4, '0', STR_PAD_LEFT);
while ($rowCodAF = $stmtCodAF->fetch()){	
	$codigoactivo=$rowCodAF["codigoactivo"];
	$array_codigo=explode("-", $rowCodAF["codigoactivo"]);
	$correlativo=$array_codigo[2];
	$correlativo= str_pad($correlativo, 4, '0', STR_PAD_LEFT);
	$correlativo++;
	$codigoActivoFijo=$cod_depreciaciones."-".$cod_tiposbienes_alterno."-".$correlativo;
?>	
<?php  }  ?>
<input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoActivoFijo;?>"/>
