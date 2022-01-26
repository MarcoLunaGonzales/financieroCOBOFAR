<?php
set_time_limit(0);

require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functions.php';
require("../conexion_comercial2.php");

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

$sql="select s.cod_salida_almacenes,(select a.nombre_almacen from almacenes a where a.cod_almacen= s.cod_almacen) as almacen,(select ts.nombre_tiposalida from tipos_salida ts where ts.cod_tiposalida=s.cod_tiposalida)as tipo,s.cod_tipo_doc,s.fecha,s.nro_correlativo,(select m.descripcion_material from material_apoyo m where m.codigo_material=sd.cod_material)as material,sd.cod_material,sd.cantidad_unitaria,sd.cantidad_envase,sd.precio_unitario,sd.costo_almacen,'S'
from salida_detalle_almacenes sd join salida_almacenes s on sd.cod_salida_almacen=s.cod_salida_almacenes
where sd.costo_almacen>sd.precio_unitario and sd.cod_material>0 and s.salida_anulada=0 and s.fecha BETWEEN '$desde' and '$hasta' 
union
select i.cod_ingreso_almacen,(select a.nombre_almacen from almacenes a where a.cod_almacen= i.cod_almacen) as almacen,(select ts.nombre_tipoingreso from tipos_ingreso ts where ts.cod_tipoingreso=i.cod_tipoingreso)as tipo,i.cod_tipo_doc,i.fecha,i.nro_correlativo,(select m.descripcion_material from material_apoyo m where m.codigo_material=id.cod_material)as material,id.cod_material,id.cantidad_unitaria,id.cantidad_envase,id.precio_bruto as precio_unitario,id.costo_almacen,'I'
from ingreso_detalle_almacenes id join ingreso_almacenes i on id.cod_ingreso_almacen=i.cod_ingreso_almacen
where id.costo_almacen>id.precio_bruto and id.cod_material>0 and i.ingreso_anulado=0 
and i.fecha BETWEEN '$desde' and '$hasta' 
order by fecha";
  // echo $sql;
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
             <h4 class="card-title text-center">Reporte Ingresos/Salidas Costo > Precio</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">'.
                '<thead >'.
                '<tr class="text-center" style="background:#2e4053 ;color:#ffffff;">'.                
                  '<th ></th>'.                  
                  '<th >Almacen</th>'.
                  '<th >I/S</th>'.
                  '<th >Tipo</th>'.
                  '<th >Fecha</th>'.
                  '<th >Nro</th>'.
                  '<th >Material</th>'.
                  '<th >CU</th>'.
                  '<th >CE</th>'.
                  '<th >PU</th>'.
                  '<th >Costo</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $index=0;
              $resp=mysqli_query($enlaceCon,$sql);
              while($row=mysqli_fetch_array($resp)){
                $index++;
                $html.='<tr>'.
                '<td class="text-center small">'.$index.'</td>'.
                '<td class="text-left small">'.$row['almacen'].'</td>'.
                '<td class="text-left small">'.$row['S'].'</td>'.
                '<td class="text-left small">'.$row['tipo_salida'].'</td>'.
                '<td class="text-left small">'.$row['fecha'].'</td>'.
                '<td class="text-left small">'.$row['nro_correlativo'].'</td>'.
                '<td class="text-left small">'.$row['material']." (".$row['cod_material'].')</td>'.
                '<td class="text-left small">'.$row['cantidad_unitaria'].'</td>'.
                '<td class="text-right small">'.$row['cantidad_envase'].'</td>'.
                '<td class="text-right small">'.$row['precio_unitario'].'</td>'.
                '<td class="text-right small">'.$row['costo_almacen'].'</td>'.
                '</tr>';
              }
            $html.='</tbody></table>';
            echo $html;
            ?>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
