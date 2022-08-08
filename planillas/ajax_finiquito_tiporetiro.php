<?php
require_once '../conexion.php';


$cod_personal=$_GET["cod_personal"];
$dbh = new Conexion();

$sql="SELECT tr.codigo,tr.nombre
from personal_retiros p join tipos_retiro_personal tr on p.cod_tiporetiro=tr.codigo
where p.cod_personal=$cod_personal and p.cod_estadoreferencial=1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
?>

<select name="cod_tiporetiro" id="cod_tiporetiro" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required data-show-subtext="true" data-live-search="true">
<?php while ($row = $stmt->fetch()){ ?>
    <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
<?php } ?>
</select>