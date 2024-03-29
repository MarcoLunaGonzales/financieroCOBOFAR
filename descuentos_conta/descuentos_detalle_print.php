<?php

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';

$codigo=$_GET['codigo'];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$glosa_cabecera="";
$mes_descuento="";
$stmtCeb = $dbh->prepare("SELECT fecha,glosa from descuentos_conta  where codigo=$codigo");
$stmtCeb->execute();
while ($rowCab = $stmtCeb->fetch(PDO::FETCH_ASSOC)) {
 $glosa_cabecera=$rowCab['glosa'];
 $fecha_cabecera=$rowCab['fecha'];

 $datos_fecha=explode('-',$fecha_cabecera);
  $mes_descuento=nombreMes($datos_fecha[1])." - ".$datos_fecha[0];
}


$sql="select  a.nombre as area,p.paterno,p.materno,p.primer_nombre,d.fecha,t.nombre as tipo_descuento,(select CONCAT_WS(' - ',p.nombre,p.numero) from plan_cuentas p where p.codigo=d.cod_contracuenta)as contracuenta,monto_sistema,monto_depositado,diferencia,glosa
  from descuentos_conta_detalle d join areas a on d.cod_area=a.codigo join personal p on d.cod_personal=p.codigo join tipos_descuentos_conta t on d.cod_tipodescuento=t.codigo
  where d.cod_descuento=$codigo";

$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('area', $area);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('tipo_descuento', $tipo_descuento);
$stmt->bindColumn('contracuenta', $contracuenta);
$stmt->bindColumn('monto_sistema', $monto_sistema);
$stmt->bindColumn('monto_depositado', $monto_depositado);
$stmt->bindColumn('diferencia', $diferencia);
$stmt->bindColumn('glosa', $glosa);
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <h4 class="card-title"><b>Descuentos Personal</b></h4>
          </div>
          <p style="padding:15px"><b>Glosa :</b><?=$glosa_cabecera?><br> <b>Fecha Descuento :</b> <?=$mes_descuento?></p>
          <div class="card-body">
            <div class="table-responsive">              
                <table class="table table-bordered table-condensed table-striped  table-sm table-secondary" id="tablePaginatorHead">
                  <thead>
                    <tr style="background:#1a5276;color:white;">
                      <th class="text-center small" width="5%">Sucursal</th>
                      <th class="text-center small" width="5%">Fecha</th>
                      <th class="text-center small" width="15%">Nombre Personal</th>
                      <th class="text-center small" width="10%">Tipo Descuento</th>
                      <th class="text-center small" width="15%">Contra Cuenta</th>
                      <th class="text-center small" width="7%">Monto Sis</th>
                      <th class="text-center small" width="7%">Depositado</th>
                      <th class="text-center small" width="7%">Descuento</th>
                      <th class="text-center small">Glosa</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;

                    $total_monto_sistema=0;
                    $total_monto_depositado=0;
                    $total_diferencia=0;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

                      $total_monto_sistema+=$monto_sistema;
                      $total_monto_depositado+=$monto_depositado;
                      $total_diferencia+=$diferencia;
                     ?>
                      <tr>
                        <td class="text-left"><small><?=$area?></small></td>
                          <td class="text-center"><small><?=$fecha;?></small></td>
                          <td class="text-left"><small><?=$primer_nombre?> <?=$paterno?> <?=$materno?></small></td>
                          <td class="text-left"><small><?=$tipo_descuento;?></small></td>
                          <td class="text-left" ><small><?=$contracuenta?></small></td>
                          <td class="text-right"><small><?=formatNumberDec($monto_sistema);?></small></td>
                          <td class="text-right"><small><?=formatNumberDec($monto_depositado);?></small></td>
                          <td class="text-right"><small><?=formatNumberDec($diferencia);?></small></td>
                          <td class="text-left"><small><?=$glosa;?></small></td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td class="text-left"><small></small></td>
                        <td class="text-center"><small></small></td>
                        <td class="text-left"><small></small></td>
                        <td class="text-left"><small></small></td>
                        <td class="text-left" ><small>TOTAL</small></td>
                        <td class="text-right"><small><?=formatNumberDec($total_monto_sistema);?></small></td>
                        <td class="text-right"><small><?=formatNumberDec($total_monto_depositado);?></small></td>
                        <td class="text-right"><small><?=formatNumberDec($total_diferencia);?></small></td>
                        <td class="text-left"><small><?=$glosa;?></small></td>
                    </tr>
                  </tfoot>
                </table>
              
            </div>
          </div>
        </div>
        
        
        
      </div>
    </div>  
  </div>
</div>

