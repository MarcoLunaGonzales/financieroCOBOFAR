<?php
require_once '../conexion.php';
require_once '../conexion_sql.php'; 
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';


$server=obtenerValorConfiguracion(104);
$bdname=obtenerValorConfiguracion(105);
$user=obtenerValorConfiguracion(106);
$pass=obtenerValorConfiguracion(107);
$dbh2=ConexionFarma_all($server,$bdname,$user,$pass);

$globalAdmin="";
$globalUnidad="";
$globalArea="";
$globalUser="";

$dbh = new Conexion();
$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d");
$fechaDesde=$y."-".$m."-01";
$fechaHasta=$y."-".$m."-".$d;
if(isset($_GET['p'])){
	$p=$_GET['p'];
}else{
	$p=0;
}
?>

<div class="content">
	<div class="container-fluid">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Filtro Ingresos Almacén</h4>
                </div>
                
                <div class="card-body">
                	<input type="hidden" name="p" id="p" value="<?=$p?>">
	                <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Desde</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde"  value="<?=$fechaDesde?>">	
								</div>		                                
							 </div>
						</div>
						<label class="col-sm-2 col-form-label">Hasta</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$fechaHasta?>">
								</div>
							</div>
						</div>				 
	                </div><!--div fechas row-->
		              <div class="row">	                  	
										<label class="col-sm-2 col-form-label">Glosa</label>
										<div class="col-sm-8">
											<div class="form-group">
												<div id="div_contenedor_fechaH">				                			
													<input type="text" class="form-control" name="glosa" id="glosa">
												</div>
											</div>
										</div>				            
	                </div><!--div fechas row-->
	                <div class="row">	                  	
										<label class="col-sm-2 col-form-label">Proveedor</label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select name="id_proveedor" id="id_proveedor" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                <option value=""></option>
                                <?php 
                                $queryUO1 = "SELECT     IDPROVEEDOR, DES
                                FROM         PROVEEDORES
                                ORDER BY DES";
                                $statementUO1 = $dbh2->query($queryUO1);
                                while ($row = $statementUO1->fetch()){ ?>
                                    <option value="<?=$row['IDPROVEEDOR'];?>"><?=$row["DES"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>		            
	                </div><!--div fechas row-->
                <div class="card-footer">
                	<button onclick="seleccionar_ingresos_almacen();" class="<?=$buttonNormal;?>">Ver Ingresos</button>
								 <!--  <a href="../reportes_ventas/" class="<?=$buttonCancel;?>"> <-- Volver </a> -->
							  </div>
              </div>	  
            </div>         
        
	</div>
        
</div>

