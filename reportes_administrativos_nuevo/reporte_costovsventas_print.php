<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';




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


$dbh_cab = new Conexion();
$sql="SELECT c.codigo,c.glosa,(SELECT ccd.fecha from ingresos_sucursales_comprobantes ccd where  cod_comprobante=c.codigo)as fecha,sum(d.debe)as costo,sum(d.haber)as venta,(SELECT ccd.cod_ciudad from ingresos_sucursales_comprobantes ccd where  cod_comprobante=c.codigo)as ciudad 
  from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
  where  d.cod_cuenta in (4004,5004) 
  and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'
  GROUP BY d.cod_comprobante HAVING costo>venta order by c.fecha";
// echo $sql;
$stmt = $dbh_cab->prepare($sql);
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
                  '<th >Comprobante</th>'.
                  '<th >Fecha</th>'.
                  '<th >Glosa</th>'.
                  
                  '<th >Costo</th>'.
                  '<th >Venta</th>'.
                  '<th >Diferencia</th>'.
                  '<th >Cod Producto</th>'.
                  '<th >Producto</th>'.
                  '<th >venta P</th>'.
                  '<th >Costo P</th>'.
                  '<th >Dif P</th>'.
                  
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimportehaber=0;
              $totalimportefactura=0;
              $totalimportediferencia=0;
              $index=0;
              while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $index++;
                  $codigo=$rowComp['codigo'];
                  $nombreCbte=nombreComprobante($codigo);
                  $fecha=$rowComp['fecha'];
                  $glosa=$rowComp['glosa'];
                  $costo=$rowComp['costo'];
                  $venta=$rowComp['venta'];
                  $ciudad=$rowComp['ciudad'];
                  if($ciudad>0){
                    $sql="SELECT DISTINCT sad.cod_material,sad.precio_unitario,( select costo from costo_temp ct where ct.cod_material=sad.cod_material)as costo,(select m.descripcion_material from material_apoyo m where m.codigo_material=sad.cod_material)as nombre_material
                    from salida_almacenes sa INNER JOIN salida_detalle_almacenes sad on sad.cod_salida_almacen=sa.cod_salida_almacenes
                    where sa.fecha = '$fecha' and sa.cod_tiposalida=1001 and sa.salida_anulada=0 and sa.`cod_almacen` in (select a.cod_almacen from almacenes a
                    where a.cod_ciudad='$ciudad' and a.cod_tipoalmacen=1)
                    HAVING sad.precio_unitario<costo";
                    
                    $cod_material_det=0;
                    $precio_unitario_det=0;
                    $costo_det=0;
                    $nombre_material_det="";
                    require("../conexion_comercial.php");
                    $resp=mysqli_query($dbh,$sql);
                    while($row=mysqli_fetch_array($resp)){ 
                      $cod_material_det=$row['cod_material'];
                      $precio_unitario_det=$row['precio_unitario'];
                      $costo_det=$row['costo'];
                      $nombre_material_det=$row['nombre_material'];
                      $html.='<tr '.$label_row.' >'.
                      '<td class="text-center font-weight-bold">'.$index.'</td>'.
                      '<td class="text-left font-weight-bold">'.$nombreCbte.'</td>'.
                      '<td class="text-left font-weight-bold">'.$fecha.'</td>'.
                      '<td class="text-left font-weight-bold">'.$glosa.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($venta).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($costo).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($venta-$costo).'</td>'.
                    
                      '<td class="text-right font-weight-bold">'.$cod_material_det.'</td>'.
                      '<td class="text-right font-weight-bold">'.$nombre_material_det.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($precio_unitario_det).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($costo_det).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($precio_unitario_det-$costo_det).'</td>'.
                      '</tr>';
                    }
                  }else{
                    $html.='<tr '.$label_row.' >'.
                      '<td class="text-center font-weight-bold">'.$index.'</td>'.
                      '<td class="text-left font-weight-bold">'.$nombreCbte.'</td>'.
                      '<td class="text-left font-weight-bold">'.$fecha.'</td>'.
                      '<td class="text-left font-weight-bold">'.$glosa.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($venta).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($costo).'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($venta-$costo).'</td>'.
                    
                      '<td class="text-right font-weight-bold"></td>'.
                      '<td class="text-right font-weight-bold"></td>'.
                      '<td class="text-right font-weight-bold"></td>'.
                      '<td class="text-right font-weight-bold"></td>'.
                      '<td class="text-right font-weight-bold"></td>'.
                      '</tr>';
                  }

                  

                  
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
