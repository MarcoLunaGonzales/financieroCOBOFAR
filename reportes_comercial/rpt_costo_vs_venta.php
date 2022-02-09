<?php
set_time_limit(0);

require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functions.php';
require("../conexion_comercial2.php");

$dbh = new Conexion();
$tipo=$_POST['tabla_id'];

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

if($tipo==1){
  $sql="SELECT DISTINCT c.cod_material,m.descripcion_material,m.cantidad_presentacion,c.costo as costo_unitario,(SELECT precio from precios where codigo_material=c.cod_material and cod_precio=1 and cod_ciudad=-1 limit 1) as precio 
FROM costoscobofar.costo_promedio_mes c join material_apoyo m on m.codigo_material=c.cod_material
where c.cod_gestion=year('$desde') and c.cod_mes=MONTH('$hasta') having costo_unitario>precio AND precio>0 order by c.cod_material;
";
}else{
    $sql="SELECT DISTINCT c.cod_material,m.descripcion_material,m.cantidad_presentacion,c.costo_unitario,(SELECT precio from precios where codigo_material=c.cod_material and cod_precio=1 and cod_ciudad=-1 limit 1) as precio 
FROM costoscobofar.costo_transaccion c join material_apoyo m on m.codigo_material=c.cod_material
where c.cod_gestion=year('$desde') and c.cod_mes=MONTH('$hasta') having costo_unitario>precio AND precio>0 order by c.cod_material;
";
}

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
             <h4 class="card-title text-center">Reporte Costo vs Precio</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">'.
                '<thead >'.
                '<tr class="text-center" style="background:#2e4053 ;color:#ffffff;">'.                
                  '<th ></th>'.                  
                  '<th >PRODUCTO</th>'.
                  '<th >DIV</th>'.
                  '<th >COSTO U</th>'.
                  '<th >PRECIO U</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $index=0;
              $resp=mysqli_query($enlaceCon,$sql);
              while($row=mysqli_fetch_array($resp)){
                $index++;
                $html.='<tr>'.
                '<td class="text-center small">'.$index.'</td>'.
                '<td class="text-left small">'.$row['descripcion_material'].'</td>'.
                '<td class="text-left small">'.$row['cantidad_presentacion'].'</td>'.
                '<td class="text-left small">'.$row['costo_unitario'].'</td>'.
                '<td class="text-left small">'.$row['precio'].'</td>'.
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
