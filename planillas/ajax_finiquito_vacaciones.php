<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


$cod_personal=$_GET["cod_personal"];
$dbh = new Conexion();

// $sql="SELECT ing_planilla from personal where codigo=$cod_personal";
$sql="SELECT pr.fecha_retiro,p.ing_planilla
FROM personal p,personal_retiros pr
WHERE pr.cod_personal=p.codigo and pr.cod_estadoreferencial=1 and p.codigo=$cod_personal  limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetch();
$ing_planilla = $result['ing_planilla'];
// $fecha_actual = date('Y-m-d');
$fecha_retiro=$result['fecha_retiro'];

$datos=obtenerSaldoVacacionPersona($cod_personal,$ing_planilla,$fecha_retiro);

$dias_vacaciones_pagar=$datos[0];
$duodecimas=formatNumberDec($datos[1]);

?>

 <div class="row">
    <label class="col-sm-2 col-form-label">Días de Vacaciones a Pagar</label>
    <div class="col-sm-8">
    <div class="form-group" >
        <input class="form-control" type="number" name="vacaciones_pagar" id="vacaciones_pagar" required="true" value="<?=$dias_vacaciones_pagar?>" readonly/>
    </div>
    </div>
</div>
<div class="row">
    <label class="col-sm-2 col-form-label">Doudécimas a Pagar</label>
    <div class="col-sm-8">
    <div class="form-group">
        <input class="form-control" type="number" step="0.01" name="duodecimas" id="duodecimas" required="true" value="<?=$duodecimas?>" readonly/>
    </div>
    </div>
</div>
<!-- 
 <input class="form-control" type="number" name="vacaciones_pagar" id="vacaciones_pagar" required="true" value="<?=$dias_vacaciones_pagar?>" readonly />
 -->