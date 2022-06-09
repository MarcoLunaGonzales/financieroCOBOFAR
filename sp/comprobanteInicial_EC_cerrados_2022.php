<?php

set_time_limit(0);
error_reporting(-1);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>Relacionar Estados de Cuenta Pisados</h4>
          </div>
          <div class="card-body">
<?php

echo "<h6>Hora Inicio Proceso: " . date("Y-m-d H:i:s")."</h6>";
$cuenta=5084;
$sql="SELECT codigo,codigo_ant from estados_cuenta where cod_plancuenta=$cuenta and codigo_ant>0";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$index=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigo_nuevo=$row['codigo'];
  $codigo_ant=$row['codigo_ant'];
  $sql2="SELECT ec.codigo
  from comprobantes c join comprobantes_detalle cd on c.codigo=cd.cod_comprobante join estados_cuenta ec on cd.codigo=ec.cod_comprobantedetalle
  where ec.cod_comprobantedetalleorigen in ($codigo_ant)
  and c.fecha >'2022-01-01';";
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $codigoCerrado=$row2['codigo'];
    $sqlInsert="UPDATE estados_cuenta set cod_comprobantedetalleorigen=$codigo_nuevo where codigo=$codigoCerrado";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute(); 
    $index++; 
  }
}

echo "<h6>HORA FIN PROCESO: " . date("Y-m-d H:i:s")." Cont: $index</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>