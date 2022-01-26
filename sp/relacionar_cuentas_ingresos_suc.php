<?php

set_time_limit(0);


require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../conexion2.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();
$dbh3 = new Conexion2();
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>Relacionar Cuentas Ingresos Sucursales</h4>
          </div>
          <div class="card-body">
<?php

echo "<h6>Hora Inicio Proceso ESTADO CUENTAS: " . date("Y-m-d H:i:s")."</h6>";


$sql="SELECT codigo,glosa from comprobantes_detalle where cod_cuenta=0 
and glosa like '%Ventas Suc.%'";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
$index=1;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoX=$row['codigo'];
  $glosaX=$row['glosa'];
  $glosaX_A=explode('DE FECHA', $glosaX);

  // var_dump($glosaX_A);
  $glosaX2=$glosaX_A[0];
  // $glosaX2.=$glosaX2." ";

  $sql="SELECT cod_cuenta from comprobantes_detalle where cod_cuenta>0 and glosa like '%$glosaX2%' and cod_cuenta not in (1023,5031,4004,2013,2014,5004,1057) order by codigo desc limit 1;";
  $stmtBuscar = $dbh3->prepare($sql);
  $stmtBuscar->execute();
  //echo $sql."<br>";
  while ($rowbuscar = $stmtBuscar->fetch(PDO::FETCH_ASSOC)) {
    $cod_cuenta=$rowbuscar['cod_cuenta'];
    $sqlInsert="UPDATE comprobantes_detalle set cod_cuenta=$cod_cuenta where codigo=$codigoX";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
    echo "$glosaX / $sqlInsert  <br>";
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