<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';


$dbh = new Conexion();

if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

$tipo=$_POST["tipo"];

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$cuenta_creditoFiscal=obtenerValorConfiguracion(3);
$dbh = new Conexion();
$sql="SELECT DATE_FORMAT(c.fecha,'%d/%m/%Y')as fecha,cod_tipocomprobante,c.numero,c.glosa,c.salvado_temporal,cd.codigo,cd.debe,cd.haber,cd.cod_comprobante,(select CONCAT_WS(' ',n.primer_nombre,n.paterno,n.materno) from personal n where n.codigo=c.created_by) as personal,c.created_at
from comprobantes c join comprobantes_detalle cd on c.codigo=cd.cod_comprobante
where c.fecha BETWEEN '$desde' and '$hasta' and c.cod_estadocomprobante<>2
and cd.cod_cuenta=$cuenta_creditoFiscal order by c.fecha desc";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="50" height="40" src="../assets/img/favicon.png">
            </div>
             <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>-->
             <h4 class="card-title text-center">Reportes Comprobantes Vs Facturas Compras</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">'.
                '<thead >'.
                '<tr class="text-center" style="background:#2e4053 ;color:#ffffff;">'.                
                  '<th ></th>'.                  
                  '<th >Numero</th>'.
                  '<th >Fecha</th>'.
                  '<th >Glosa</th>'.
                  '<th >Salvado Temporal</th>'.
                  '<th >Importe Cbte</th>'.
                  // '<th >Haber</th>'.
                  '<th >Cant. Facs.</th>'.
                  '<th >Monto Factura</th>'.
                  '<th >13 % Facturas</th>'.
                  '<th >Diferencia</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimportehaber=0;
              $totalimportefactura=0;
              $totalimportediferencia=0;
              $index=0;
              while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $sw_ver=false;
                  $index++;
                  $cod_comprobante=$rowComp['cod_comprobante'];
                  $nombreCbte=nombreComprobante($cod_comprobante);
                  $codigo=$rowComp['codigo'];
                  $fecha=$rowComp['fecha'];
                  $cod_tipocomprobante=$rowComp['cod_tipocomprobante'];
                  $numero=$rowComp['numero'];
                  $glosa=$rowComp['glosa'];
                  $salvado_temporal=$rowComp['salvado_temporal'];
                  if($salvado_temporal==1){
                    $salvado_temporal="Si";
                  }else{
                    $salvado_temporal="No";
                  }
                  $debe=$rowComp['debe'];
                  $haber=$rowComp['haber'];
                  $personal=$rowComp['personal'];
                  $created_at=$rowComp['created_at'];
                  $cuenta=nameCuenta($cuenta_creditoFiscal);
                  $sql="SELECT sum(importe)-sum(exento)-sum(ice)as importe, count(*) as cantidad from facturas_compra where cod_comprobantedetalle=$codigo";
                  $stmt5 = $dbh->prepare($sql);
                  $stmt5->execute();                      
                  $stmt5->bindColumn('importe', $importeX);
                  $stmt5->bindColumn('cantidad', $cantidadX);
                  $monto_factura=0;
                  $cantidad_factura=0;
                  while ($row = $stmt5->fetch(PDO::FETCH_BOUND)) {
                    $monto_factura=$importeX;
                    $cantidad_factura=$cantidadX;
                  }
                  $monto_diferencia=$haber+$debe-($monto_factura*0.13);
                  $monto_diferencia=round($monto_diferencia,2);
                  $totalimportehaber+=$haber;
                  $totalimportefactura+=$monto_factura;
                  $totalimportediferencia+=$monto_diferencia;
                  $label="";
                  
                  switch ($cod_tipocomprobante) {
                    case '1':
                      $label_row='style="background:white;"';   
                    break;
                    case '2':
                      $label_row='style="background:white;"';   
                    break;
                    case '3':
                      $label_row='style="background:#e5e7e9  ;"';   
                    break;
                  }
                  if($monto_diferencia!=0){
                    $sw_ver=true;
                    $label_row="style='background:#CD5C5C;'";
                  }else{
                    if($tipo==2){
                      $sw_ver=true;
                    }
                  }
                  if($sw_ver){
                    $html.='<tr '.$label_row.' >'.
                    '<td class="text-center font-weight-bold">'.$index.'</td>'.
                    '<td title="Elaborado Por: '.$personal.' El '.$created_at.'" class="text-left font-weight-bold">'.$nombreCbte.'</td>'.
                    '<td class="text-left font-weight-bold">'.$fecha.'</td>'.
                    '<td class="text-left font-weight-bold">'.$glosa.'</td>'.
                    '<td class="text-left font-weight-bold">'.$salvado_temporal.'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($debe+$haber).'</td>'.
                    '<td class="text-right font-weight-bold">'.$cantidad_factura.'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($monto_factura).'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($monto_factura*0.13).'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($monto_diferencia).'</td>'.
                    '</tr>';  
                  }
                                  
                }                    
                // $totalFactura=obtener_saldo_total_facturas();
                // $html.='<tr>'.
                //       '<td class="text-left font-weight-bold">-</td>'.
                //       '<td class="text-left font-weight-bold">-</td>'.
                //       '<td class="text-left font-weight-bold">-</td>'.
                //       '<td class="text-left font-weight-bold">TOTAL</td>'.
                //       '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportehaber).'</td>'.
                //       '<td class="text-left font-weight-bold">-</td>'.
                //       '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportefactura).'</td>'.
                //       '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportediferencia).'</td>'.
                //   '</tr>';
            $html.=    '</tbody></table>';
            echo $html;
            ?>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
