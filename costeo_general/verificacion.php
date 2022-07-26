<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';
require_once '../conexion_comercial2.php'; 

$dbh = new Conexion();


$desde=$_GET['desde'];
$hasta=$_GET['hasta'];
?>

<div class="content">
	<form id="form1" class="form-horizontal" action="../costeo_general/verificacion_print.php" method="GET">
	<div class="container-fluid">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title">Proceso Costeo Sucursales - Segunda Fase</h4>
            </div>
            <div class="card-body">
            	<input type="hidden" name="fecha_desde" id="fecha_desde" value="<?=$desde?>">
            	<input type="hidden" name="fecha_hasta" id="fecha_hasta" value="<?=$hasta?>">
            	<?php 

$fechaInicio=$desde." 00:00:00";
//$fechaFin=date("Y-m-d",strtotime($hasta))." 23:59:59";
$fechaFin=$hasta." 23:59:59";

$sql="SELECT DISTINCT ai.cod_almacen,ai.nombre_almacen
FROM ingreso_detalle_almacenes id 
join ingreso_almacenes i on i.cod_ingreso_almacen=id.cod_ingreso_almacen
join material_apoyo m on m.codigo_material=id.cod_material
join salida_almacenes s on s.cod_salida_almacenes=i.cod_salida_almacen
join salida_detalle_almacenes sd on sd.cod_salida_almacen=s.cod_salida_almacenes AND sd.cod_material=id.cod_material
JOIN costoscobofar.costo_transaccion cti on cti.cod_documento=id.cod_ingreso_almacen and cti.cod_material=id.cod_material and cti.cod_tipodocumento=1
JOIN costoscobofar.costo_transaccion cts on cts.cod_documento=sd.cod_salida_almacen and cts.cod_material=sd.cod_material and cts.cod_tipodocumento=0
join almacenes ai on ai.cod_almacen=i.cod_almacen
join almacenes asa on asa.cod_almacen=s.cod_almacen
where s.fecha>='$fechaInicio' and s.fecha<='$fechaFin' and s.salida_anulada=0 and i.ingreso_anulado=0
and ai.cod_tipoalmacen=1 and asa.cod_tipoalmacen=1 
and round(cti.costo_unitario,2)<>round(cts.costo_unitario,2) AND s.costeo_cod_comprobante is null
GROUP BY ai.cod_almacen;"; //and (s.cod_almacen<>1000 and s.cod_almacen<>1078) //and i.cod_almacen!=1000
		        ?>

		        <div class="table-responsive">
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed table-striped " style="width:100%">
                <thead>                              
                  <tr>
                    <th width="2%"><small><b>-</b></small></th>
                    <th width="15%"><small><b>Detalle</b></small></th>
                    <th width="10%"><small><b>Sucursal</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                  $resp=mysqli_query($enlaceCon,$sql);
                  while($row=mysqli_fetch_array($resp)){ 
                    $cod_almacen=$row['cod_almacen'];
                    $nombre_almacen=$row['nombre_almacen']; 
                    $detalle_traspaso="La sucursal tiene ingresos que no cuadran con el traspaso origen!";                   
                      $index++;
                      ?>
                    <tr>
                      <td class="text-center small"><?=$index;?></td>
                      <td class="text-center small"><b><?=$detalle_traspaso;?></b></td>
                      <td class="text-left small"><?=$nombre_almacen;?></td>
                    </tr>
                    <?php   
                    // }
                  }?>
                </tbody>
              </table>            
            </div>
            </div>
            <div class="card-footer">
            	<button type="submit" class="<?=$buttonNormal;?>">Procesar Segunda Etapa</button>
            	<a href="rpt_costeo_pendientes_from.php" class="btn btn-danger">Volver al Formulario</a>
						</div>					
	        
          </div>	  
    </div>         
	</div>
	</form>
</div>

