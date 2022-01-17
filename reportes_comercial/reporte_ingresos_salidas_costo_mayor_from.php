<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;
$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";
?>
<div class="content">
	<div class="container-fluid">
		<!-- <div style="overflow-y:scroll; ">-->
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Reporte Ingresos/Salidas Costo > Precio</h4>
          </div>
          <form class="" action="reporte_ingresos_salidas_costo_mayor.php" target="_blank" method="POST">
          <div class="card-body">
              <div class="row">
	                <label class="col-sm-2 col-form-label">Desde</label>
	                <div class="col-sm-4">
	                	<div class="form-group">
	                			<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde"  value="<?=$fechaDesde?>">
	                  </div>
	                </div>
	                <label class="col-sm-1 col-form-label">Hasta</label>
	                <div class="col-sm-4">
	                	<div class="form-group">
	                		<div id="div_contenedor_fechaH">				                			
	                			<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta"  value="<?=$fechaHasta?>">
	                		</div>
	                  </div>
	                </div>
	            </div>
          </div><!--card body--> 
          <div class="card-footer">
          	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
  					</div>
         </form> 
        </div>	  
      </div>         
        <!-- </div>	 -->
	</div>
        
</div>

