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





$codComprobanteOrigen = "16737"; //COMP 
$cuenta=5084;
$sql="SELECT codigo,cod_plancuenta,monto,cod_cuentaaux from estados_cuenta where cod_comprobantedetalle in (
select codigo from comprobantes_detalle where cod_comprobante=16737) and cod_plancuenta=$cuenta";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
$index=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $montoX=$row['monto'];
  $cod_cuentaauxX=$row['cod_cuentaaux'];
  //$haberX=$row['haber'];
  $cod_plancuentaX=$row['cod_plancuenta'];
  // $cod_proveedorX=$row['cod_proveedor'];

  $sql2="SELECT codigo from estados_cuenta where cod_comprobantedetalle in (
select codigo from comprobantes_detalle where cod_comprobante=16738) and cod_plancuenta=$cuenta
and cod_cuentaaux=$cod_cuentaauxX and glosa_auxiliar like '%BALANCE INICIAL 2022%'";

  // echo $sql2."<br>";
  $stmt2 = $dbh->prepare($sql2);
  $stmt2->execute();
  $contador=0;
  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
    $codigoX2=$row2['codigo'];
    $contador++;
  }

  if($contador==1){
    $sqlInsert="UPDATE estados_cuenta set cod_comprobantedetalleorigen=$codigoX where codigo=$codigoX2";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
    $index++;
  }else{
    echo "<Br>".$sql2."<Br>";
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