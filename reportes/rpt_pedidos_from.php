<?php
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));

$fechaDesde="01/".$m."/".$y;
$fechaHasta=$d."/".$m."/".$y;
$dbh = new Conexion();
?>

<div class="content">
	<div class="container-fluid">
		<div style="overflow-y: scroll;">
			
	            <div class="col-md-12">
	              	<div class="card">
		                <div class="card-header <?=$colorCard;?> card-header-icon">
		                  <div class="card-icon">
		                    <i class="material-icons"><?=$iconCard;?></i>
		                  </div>
		                  <h4 class="card-title">Reporte Pedidos Proveedores</h4>
		                </div>
		                <form class="" action="rpt_pedidos_print.php" target="_blank" method="POST">
			                <div class="card-body">
			                	<div class="row">
				                  	<div class="col-sm-6">
				                  		<div class="row">
							                 <label class="col-sm-4 col-form-label">Proveedor</label>
							                 <div class="col-sm-8">
							                	<div class="form-group">				                		
					                                <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)"> -->
					                                <select class="selectpicker form-control form-control-sm" name="proveedor[]" id="proveedor" required multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true">				  	   
							  	                        <?php
							  	                        $stmt = $dbh->prepare("SELECT codigo,idproveedor_almacen,nombre from af_proveedores where cod_estado=1 and idproveedor_almacen>0 order by nombre");
								                         $stmt->execute();
								                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								                          	$codigoX=$row['codigo'];
								                          	$nombreX=$row['nombre'];
								                          	$idproveedor_almacenX=$row['idproveedor_almacen'];
								                          ?>
								                       <option value="<?=$idproveedor_almacenX;?>"><?=$nombreX?></option>	
								                         <?php
							  	                         }
							  	                         ?>
							                        </select>
							                     </div>
							                  </div>
							            </div>
				      	             </div>
				                </div><!--div row-->
			                  <div class="row">
			                  	<div class="col-sm-6">
			                  		<div class="row">
						                 <label class="col-sm-4 col-form-label">Desde</label>
						                 <div class="col-sm-8">
						                	<div class="form-group">
						                		<div id="div_contenedor_fechaI">
							                		<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" value="<?=$fechaDesde?>">
							                	</div>
				                                
						                     </div>
						                  </div>
						             </div>
			      	             </div>
			                  	<div class="col-sm-6">
			                  		<div class="row">
						                 <label class="col-sm-4 col-form-label">Hasta</label>
						                 <div class="col-sm-8">
						                	<div class="form-group">
				                               <div id="div_contenedor_fechaH">
						                			<input type="date" class="form-control " autocomplete="off" name="fecha_hasta" id="fecha_hasta"  value="<?=$fechaHasta?>">
						                		</div>
						                    </div>
						                  </div>
						              </div>
							      </div>
			                  </div><!--div row-->
			                </div><!--card body-->
			                <div class="card-footer fixed-bottom">
			                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
							   <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>
						    </div>
		               	</form> 
	              	</div>	  
	            </div>
	        </div>	
	
          
    </div>
</div>

