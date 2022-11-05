<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$fechaActual=date("Y-m-d");
?>
<script type="text/javascript">
	function imprimirReportePantalla(){
		window.open('<?=$urlReportePrint?>?desde='+$("#desde").val()+'&hasta='+$("#hasta").val()+'&nopdf=1','asd_open_repo');		
	}
</script>
<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
			<form action="<?=$urlReportePrint?>" method="GET" target="_blank">
			<div class="card">
			  <div class="card-header card-header-info card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Cierre <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				
				<div class="row">
				  <label class="col-sm-2 col-form-label">Desde</label>
				  <div class="col-sm-3">
					<div class="form-group">
					  <input class="form-control" type="date" required name="desde" id="desde" value="<?=$fechaActual?>">
					</div>
				  </div>
				  <label class="col-sm-2 col-form-label">Hasta</label>
				  <div class="col-sm-4">
					<div class="form-group">
					  <input class="form-control" type="date" required name="hasta" id="hasta" value="<?=$fechaActual?>">
					</div>
				  </div>
				</div>
				<hr>
			  </div>
			  <div class="card-footer fixed-bottom ml-auto mr-auto">
				<button class="btn btn-rose"> <i class='material-icons'>print</i> IMPRIMIR REPORTE</button>
				<a href="#" class="btn btn-success" onclick="imprimirReportePantalla();"> <i class='material-icons'>dashboard</i> VER REPORTE</a>
			  </div>
			</div>
			</form>
		</div>
	
	</div>
</div>