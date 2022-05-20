<?php
require_once '../conexion.php';
$cod_cuenta = $_GET["cod_cuenta"];
$array_cuentas=explode(',', $cod_cuenta);
$string_cuentas="";
foreach ($array_cuentas as $cuenta ) {
    $cuenta_aux_array=explode('@',$cuenta);
    $cuenta_aux=$cuenta_aux_array[0];
    $string_cuentas.=$cuenta_aux.",";
}
$string_cuentas=trim($string_cuentas,",");


$db = new Conexion();

$sql="select codigo,nombre from cuentas_auxiliares where cod_cuenta in ($string_cuentas) and cod_estadoreferencial=1 order by nombre";  
$stmt = $db->prepare($sql);
$stmt->execute();
?>
	
<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" title="Seleccione una opcion" name="cuentas_auxiliares_select[]" id="cuentas_auxiliares_select" data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required data-live-search="true">
  <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
      $nombreX=$row['nombre'];
    ?>
    <option value="<?=$codigoX;?>"><?=$nombreX;?></option>
    <?php 
    $indexPr++;
    }
  ?>
</select>

