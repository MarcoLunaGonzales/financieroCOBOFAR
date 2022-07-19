<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';
require_once '../conexion_comercial2.php'; 

$dbh = new Conexion();
$desde=date("Y-m"); // strtotime('-1 month')
$desde.="-01";
$hasta= date("Y-m-t", strtotime($desde));

?>
<script type="text/javascript">
	function verificacionSegundaEtapaSucursales(){
		window.open("verificacion.php?desde="+$("#fecha_desde").val()+"&hasta="+$("#fecha_hasta").val());
	}
	function finalizarEtapaCosteoMes(){
		window.open("finalizarEtapa.php?desde="+$("#fecha_desde").val()+"&hasta="+$("#fecha_hasta").val());
	}
	// window.location.href="verificacion.php?desde=";
</script>
<div class="content">
	<form id="form1" class="form-horizontal" action="../costeo_general/rpt_costeo_pendientes_print.php" method="GET"  target="_blank">
	<div class="container-fluid">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title">Proceso Costeo Sucursales</h4>
            </div>
            <div class="card-body">
	            <div class="row">	                  	
					<label class="col-sm-2 col-form-label">Desde</label>
					<div class="col-sm-4">
						<div class="form-group">
							<div id="div_contenedor_fechaI">				                			
								<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde"  value="<?=$desde?>" min="2022-07-01">	
							</div>		                                
						 </div>
					</div>
					<label class="col-sm-2 col-form-label">Hasta</label>
					<div class="col-sm-4">
						<div class="form-group">
							<div id="div_contenedor_fechaH">				                			
								<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$hasta?>" min="2022-07-01">
							</div>
						</div>
					</div>				 
	           	</div><!--div fechas row-->
	            <!-- <div class="row">
		            <label class="col-sm-2 col-form-label">Generar En Excel</label>
		            <div class="col-sm-1">
		              <div class="form-group">
		                <div class="togglebutton">
		                    <label>
		                    <input type="checkbox" name="check_rs_cierres" id="check_rs_cierres" checked>
		                    <span class="toggle"></span>
		                    </label>
		                </div>
		              </div>
		            </div>
		        </div> -->


            <div class="card-footer">
            	<button type="submit" class="<?=$buttonNormal;?>">Costear Sucursales</button>
            	<a href="#" onclick="verificacionSegundaEtapaSucursales();return false;" class="btn btn-warning">Segunda Etapa de Costeo</a>
            	<a href="#" onclick="finalizarEtapaCosteoMes();return false;" class="btn btn-danger">Finalizar Costeo</a>
						</div>						

	        </div>
          </div>	  
    </div>         
	</div>
	</form>
</div>

