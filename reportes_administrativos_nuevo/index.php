<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="card">
			  	<div class="card-header card-success card-header-text" >
					<div class="card-text" style=" background: #c7cad3;">
				  		<h4 class="card-title" style="color:red;">Comprobantes</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">				  	
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlreporte_comprobantessinfacturascompras_from;?>" class="btn btn-success"> Crédito Fiscal VS Libro Compra</a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a class="btn btn-rose" href="<?=$urlreporte_comprobantesincompletos_from;?>">
				                    <span class="sidebar-normal">Comprobantes Incompletos</span>
				                </a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a class="btn btn-info" href="<?=$urlreporte_comprobantesinEC_from;?>">
				                    <span class="sidebar-normal">Comprobantes Sin Estados Cuenta</span>
				                </a>
							</div>
				  		</div>
					</div>
					

					<div class="row">				  	
				  		<div class="col-sm-3">
							<div class="form-group">								
								<a class="btn btn-primary" href="">				                
				                    <span class="sidebar-normal"> Cantidad Comprobantes</span>
				                </a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a class="btn btn-warning" href="comprobantes_modificados_from.php">
				                    <span class="sidebar-normal"> Comprobantes Modificados</span>
				                </a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div>

			<div class="card">
				<div class="card-header card-success card-header-text" >
					<div class="card-text" style=" background: #c7cad3;">
				  		<h4 class="card-title" style="color:red;">Impuestos</h4>
					</div>
			  	</div>
				<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a class="btn btn-danger" href="cuadro_ventas_from.php">
				                    <span class="sidebar-normal"> Cuadro Ventas Automaticas</span>
				                </a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a class="btn btn-success" href="cuadro_facturas_manuales_from.php">
				                    <span class="sidebar-normal"> Cuadro Facturas Manuales</span>
				                </a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div>
		
		</div>
	</div>
</div>