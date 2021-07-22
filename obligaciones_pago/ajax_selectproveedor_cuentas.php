<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$cod_cuenta=$_GET['cod_cuenta'];

$sql="SELECT distinct(ca.codigo)as codigo, ca.nombre,ca.cod_cuenta 
	from estados_cuenta ec, cuentas_auxiliares ca  where ca.codigo=ec.cod_cuentaaux and ca.cod_cuenta in ($cod_cuenta) order by ca.nombre";  
	// echo $sql."--";
?>

<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" title="Seleccione una opcion" name="proveedor[]" id="proveedor" data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required data-live-search="true">
	<?php 

	 $stmt3 = $dbh->prepare($sql);
	 $stmt3->execute();
	 while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
	 	$nombre_cuenta=nameCuenta($rowSel['cod_cuenta']);
	  $codigoSel=$rowSel['codigo'];
	  $nombreSelX=$rowSel['nombre'];
	  ?><option value="<?=$codigoSel;?>" data-subtext="<?=$nombre_cuenta?>"><?=$nombreSelX?></option><?php 
	 }
	?>
</select>