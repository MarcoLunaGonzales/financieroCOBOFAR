<?php
require_once '../conexion.php';


$cod_personal=$_GET["cod_personal"];
$dbh = new Conexion();


$sql="SELECT p.cod_tiporetiro
from personal_retiros p 
where p.cod_personal=$cod_personal order by p.codigo desc limit 1";
$stmtInfo = $dbh->prepare($sql);
$stmtInfo->execute();
$resultInfo = $stmtInfo->fetch();
$cod_tiporetiroX = $resultInfo['cod_tiporetiro'];


$sql="SELECT tr.codigo,tr.nombre
from  tipos_retiro_personal tr 
where  tr.cod_estadoreferencial=1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
?>
<select name="cod_tiporetiro" id="cod_tiporetiro" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required data-show-subtext="true" data-live-search="true">
<?php while ($row = $stmt->fetch()){ ?>
    <option  <?=($cod_tiporetiroX==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
<?php } ?>
</select>