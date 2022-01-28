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
$sql="SELECT c.codigo,DATE_FORMAT(c.fecha,'%d/%m/%Y')as fecha,(select CONCAT_WS(' ',n.primer_nombre,n.paterno,n.materno) from personal n where n.codigo=c.created_by) as personal_c,(select CONCAT_WS(' ',n.primer_nombre,n.paterno,n.materno) from personal n where n.codigo=c.modified_by) as personal_m,c.cod_tipocomprobante,c.glosa,c.created_at,c.modified_at,c.salvado_temporal
 from comprobantes  c 
 where c.created_at<>c.modified_at and c.cod_estadocomprobante<>2  and c.modified_at BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and c.cod_tipocomprobante in ($tipo)
 order by c.fecha,c.codigo";
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
             <h4 class="card-title text-center">Comprobantes Modificados</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">'.
                '<thead >'.
                '<tr class="text-center" style="background:#2e4053 ;color:#ffffff;">'.                
                  '<th ></th>'.                  
                  '<th >Cbte</th>'.
                  '<th >Fecha</th>'.
                  '<th >Glosa</th>'.
                  '<th >Fecha Creación</th>'.
                  '<th >Fecha Ult. Modificación</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimportehaber=0;
              $totalimportedebe=0;
              $totalimportediferencia=0;
              $index=0;
              while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $index++;
                  $cod_comprobante=$rowComp['codigo'];
                  $nombreCbte=nombreComprobante($cod_comprobante);
                  $fecha=$rowComp['fecha'];
                  $glosa=$rowComp['glosa'];
                  $cod_tipocomprobante=$rowComp['cod_tipocomprobante'];
                  $personal_c=$rowComp['personal_c'];
                  $personal_m=$rowComp['personal_m'];

                  $created_at=$rowComp['created_at'];
                  $modified_at=$rowComp['modified_at'];
                  $salvado_temporal=$rowComp['salvado_temporal'];


                  
                  $label_row="";
                  switch ($cod_tipocomprobante) {
                    case '1':
                      $label_row='style="background:white;"';   
                    break;
                    case '2':
                      $label_row='style="background:#e5eaea;"';   
                    break;
                    case '3':
                      $label_row='style="background:#e5e7e9;"';   
                    break;
                  }
                  
                  
                    $html.='<tr '.$label_row.' >'.
                    '<td class="text-center small">'.$index.'</td>'.
                    '<td class="text-left small">'.$nombreCbte.'</td>'.
                    '<td class="text-left small">'.$fecha.'</td>'.
                    '<td class="text-left small">'.$glosa.'</td>'.
                    
                    
                    '<td class="text-right small" title="Creado Por: '.$personal_c.'">'.$created_at.'</td>'.
                    '<td class="text-right small" title="Modificado Por: '.$personal_m.'">'.$modified_at.'</td>'.
                    '</tr>';         
                  
                }                    
                
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
