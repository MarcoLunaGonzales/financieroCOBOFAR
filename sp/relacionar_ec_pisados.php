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

echo "<h6>Hora Inicio Proceso ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";





$codComprobanteOrigen = "13101"; //COMP 
$cod_cuenta=2035;//cuenta cuentas corrientes del personal (confg EC)  tipo personal 



$sql="SELECT d.codigo,d.cod_comprobante,d.cod_cuenta,d.cod_cuentaauxiliar,d.debe,d.glosa,e.codigo as cod_ec 
from comprobantes_detalle d join estados_cuenta e on d.codigo=e.cod_comprobantedetalle
where d.cod_cuenta=$cod_cuenta and d.debe>0 and d.cod_comprobante in ($codComprobanteOrigen) and d.codigo=305067";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
$index=1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $cod_comprobanteX=$row['cod_comprobante'];
  $codCuentaAuxiliarX=$row['cod_cuentaauxiliar'];
  $debeX=$row['debe'];
  //$haberX=$row['haber'];
  $glosaX=$row['glosa'];
  $cod_ecX=$row['cod_ec'];
  $glosaX2=trim($glosaX,"PAGOS   - ");

  $sql2="SELECT d.codigo,e.codigo as cod_ec,e.cod_comprobantedetalleorigen from comprobantes_detalle d join estados_cuenta e on d.codigo=e.cod_comprobantedetalle
  where d.glosa like '%$glosaX2%' and d.cod_cuenta=$cod_cuenta and d.haber=$debeX and d.cod_cuentaauxiliar=$codCuentaAuxiliarX";
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $codigoX2=0;
  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $codigoX2=$row2['codigo'];
    $cod_ecX2=$row2['cod_ec'];
    $cod_comprobantedetalleorigenX2=$row2['cod_comprobantedetalleorigen'];
    $sw_existencia=obtenerCod_comprobanteDetalleorigen($cod_comprobantedetalleorigenX2);
    if($sw_existencia==0){
      $sqlInsert="UPDATE estados_cuenta set cod_comprobantedetalleorigen=$cod_ecX2 where codigo=$cod_ecX";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $stmtInsert->execute();
      echo "$glosaX $sqlInsert  / $debeX <br>";
    }
    $index++;
  }

}

echo "<h6>HORA FIN PROCESO CARGADO ESTADO CUENTAS: " . date("Y-m-d H:i:s")." Cont: $index</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>