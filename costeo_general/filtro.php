<?php
require_once '../conexion.php';
require_once '../conexion_comercial_oficial.php'; 
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';



$globalAdmin="";
$globalUnidad="";
$globalArea="";
$globalUser="";



$desde=date("Y-m", strtotime('-1 month'));
$desde.="-01";
$hasta= date("Y-m-t", strtotime($desde));


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
                  <h4 class="card-title">Filtro Traspasos Sucursales</h4>
                </div>
                
                <div class="card-body">
                	<input type="hidden" name="p" id="p" value="<?=$p?>">
	                <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Desde</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde"  value="<?=$desde?>">	
								</div>		                                
							 </div>
						</div>
						<label class="col-sm-2 col-form-label">Hasta</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$hasta?>">
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
										<label class="col-sm-2 col-form-label">Sucursal</label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select name="id_sucursal" id="id_sucursal" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                <option value=""></option>
                                <?php 
                                $queryUO1 = "SELECT cod_ciudad,descripcion from ciudades where cod_impuestos>0 or cod_ciudad = -1 or cod_ciudad=-3 order by descripcion";
                                $resp=mysqli_query($dbh,$queryUO1);
                                while($row=mysqli_fetch_array($resp)){  ?>
                                    <option value="<?=$row['cod_ciudad'];?>"><?=$row["descripcion"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>		            
	                </div><!--div fechas row-->
                <div class="card-footer">
                	<button onclick="seleccionar_traspasos_sucursales_nuevo();" class="<?=$buttonNormal;?>">Ver Traspasos</button>
								 <!--  <a href="../reportes_ventas/" class="<?=$buttonCancel;?>"> <-- Volver </a> -->
				</div>
              </div>	  
            </div>         
        
	</div>
        
</div>

