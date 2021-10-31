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
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

$tipo=$_POST["tipo"];


$dbh = new Conexion();
$sql="SELECT c.codigo,DATE_FORMAT(c.fecha,'%d/%m/%Y')as fecha,(select CONCAT_WS(' ',n.primer_nombre,n.paterno,n.materno) from personal n where n.codigo=c.created_by) as personal,c.cod_tipocomprobante,c.glosa,sum(d.debe) as debe,sum(d.haber) as haber,c.salvado_temporal,c.created_at
from comprobantes c join comprobantes_detalle d on c.codigo=d.cod_comprobante
where c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'
GROUP BY d.cod_comprobante
order by c.fecha";
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
             <h4 class="card-title text-center">Reportes Comprobantes Incompletos</h4>
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
                  '<th >Debe</th>'.
                  '<th >Haber</th>'.
                  '<th >Diferencia</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimportehaber=0;
              $totalimportedebe=0;
              $totalimportediferencia=0;
              $index=0;
              while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  if($tipo==1){
                    $sw=false;
                  }else{
                    $sw=true;
                  }
                  $index++;
                  $cod_comprobante=$rowComp['codigo'];
                  $nombreCbte=nombreComprobante($cod_comprobante);
                  $fecha=$rowComp['fecha'];
                  $cod_tipocomprobante=$rowComp['cod_tipocomprobante'];
                  // $numero=$rowComp['numero'];
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
                  $monto_diferencia=$debe-$haber;
                  $monto_diferencia=round($monto_diferencia,2);
                  $totalimportedebe+=$debe;
                  $totalimportehaber+=$haber;
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
                    $sw=true;
                    $label_row="style='background:#CD5C5C;'";
                  }
                  if($sw){
                    $html.='<tr '.$label_row.' >'.
                    '<td class="text-center font-weight-bold">'.$index.'</td>'.
                    '<td title="Elaborado Por: '.$personal.' El '.$created_at.'" class="text-left font-weight-bold">'.$nombreCbte.'</td>'.
                    '<td class="text-left font-weight-bold">'.$fecha.'</td>'.
                    '<td class="text-left font-weight-bold">'.$glosa.'</td>'.
                    '<td class="text-left font-weight-bold">'.$salvado_temporal.'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($debe).'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($haber).'</td>'.
                    '<td class="text-right font-weight-bold">'.formatNumberDec($monto_diferencia).'</td>'.
                    '</tr>';         
                  }
                }                    
                $html.='<tr>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">TOTAL</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportedebe).'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportehaber).'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportediferencia).'</td>'.
                  '</tr>';
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
