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
            </div>
            <h4 class="card-title">Cargado de Estados de Cuenta</h4>
          </div>
          <div class="card-body">
<?php

echo "<h6>Hora Inicio Proceso : " . date("Y-m-d H:i:s")."</h6>";

$codComprobanteOrigen=16738; //COMP
$cod_cuenta=5084;

$sql="select c.cod_cuenta,ca.nombre,ca.cod_proveedorcliente,c.cod_cuentaauxiliar,sum(c.debe)debe,sum(c.haber)haber 
from comprobantes_detalle c join cuentas_auxiliares ca on c.cod_cuentaauxiliar=ca.codigo
WHERE c.cod_comprobante=$codComprobanteOrigen and c.cod_cuenta=$cod_cuenta
GROUP BY c.cod_cuentaauxiliar
order by c.cod_cuenta,ca.nombre";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nombreX=0;
$fechaX="2022-01-01";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $cod_cuentaauxiliar=$row['cod_cuentaauxiliar'];
  $debe=$row['debe'];
  $nombreAuxiliar=$row['nombre'];
  $codCuenta=$row['cod_cuenta'];
  $cod_proveedorcliente=$row['cod_proveedorcliente'];
  $glosaMostrar="BALANCE INICIAL 2022 - ".$nombreAuxiliar;
  if($debe>0){
    $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
    $insert_str = "('$codigoDetalleComprobante','$codComprobanteOrigen','$codCuenta','$cod_cuentaauxiliar','1','522','0','$debe','$glosaMostrar')"; 
    $sqlInsertDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa) VALUES ".$insert_str.";";
    // echo $sqlInsertDet;
    $stmtInsertDet=$dbh->prepare($sqlInsertDet);                  
    $flagSuccess2=$stmtInsertDet->execute();
    $sqlInsertEC="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar) values ('$codigoDetalleComprobante','$codCuenta','$debe','$cod_proveedorcliente','$fechaX','0','$cod_cuentaauxiliar','0','1','$glosaMostrar')";
    $stmtInsertEC = $dbh->prepare($sqlInsertEC);
    $stmtInsertEC->execute();
  }else{
    $haber=$row['haber'];
    $codigoDetalleComprobante=obtenerCodigoComprobanteDetalle();
    $insert_str = "('$codigoDetalleComprobante','$codComprobanteOrigen','$codCuenta','$cod_cuentaauxiliar','1','522','$haber','0','$glosaMostrar')"; 
    $sqlInsertDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa) VALUES ".$insert_str.";";
    // echo $sqlInsertDet;
    $stmtInsertDet=$dbh->prepare($sqlInsertDet);                  
    $flagSuccess2=$stmtInsertDet->execute();

    $sqlInsertEC="INSERT into estados_cuenta(cod_comprobantedetalle, cod_plancuenta, monto,  cod_proveedor, fecha, cod_comprobantedetalleorigen, cod_cuentaaux, cod_cajachicadetalle, cod_tipoestadocuenta, glosa_auxiliar) values ('$codigoDetalleComprobante','$codCuenta','$haber','$cod_proveedorcliente','$fechaX','0','$cod_cuentaauxiliar','0','1','$glosaMostrar')";
    $stmtInsertEC = $dbh->prepare($sqlInsertEC);
    $stmtInsertEC->execute();
  }
}
echo "<h6>HORA FIN PROCESO CARGADO : " . date("Y-m-d H:i:s")."</h6>";

?>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>