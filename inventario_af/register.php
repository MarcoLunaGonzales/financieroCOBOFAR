<?php

require_once 'conexion.php';
require_once 'styles.php';

$codigo=$_GET['codigo'];
$dbh = new Conexion();
$fecha_actual=date('Y-m-d');

?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="?opcion=inventario_af_save" method="post"  enctype="multipart/form-data">
			<div class="card">
			  	<div class="card-header card-header-rose card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Mantenimiento de AF</h4>
					</div>				
			  </div>
			  <div class="card-body ">
			  	<input type="hidden" name="codigo" value="codigo" value="<?=$codigo?>">
			  	<div class="row">
					<label class="col-sm-2 col-form-label" style="color:black;">Nombre </label>
					<div class="col-sm-4">
						<div class="form-group">
						  <input  type='text' class='form-control'  id='nombre' name='nombre' placeholder="Ej: INVENTARIO SUCURSAL PRADO">
						</div>
					</div>
					<label class="col-sm-2 col-form-label" style="color:black;">Abreviatura </label>
					<div class="col-sm-4">
						<div class="form-group">
						  <input  type='text' class='form-control'  id='abreviatura' name='abreviatura' placeholder="Ej: INV-PRADO">
						</div>
					</div>
				</div>


				<div class="row">
					<label class="col-sm-2 col-form-label" style="color:black;">Responsable *</label>
					<div class="col-sm-4">
						<div class="form-group">
						<select class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true" name="cod_responsable" id="cod_responsable" required="true">
						  	<?php
						  		
						  		echo '<option value="">SELECCIONE UN ITEM</option>';
						  		
						  		$sqlArea="SELECT codigo,paterno,materno,primer_nombre from personal where cod_estadopersonal in (1,2) and cod_estadoreferencial=1 and cod_unidadorganizacional=1 
						  			order by paterno";
							  $stmtArea = $dbh->prepare($sqlArea);
							$stmtArea->execute();
							while ($rowResp = $stmtArea->fetch(PDO::FETCH_ASSOC)) {
								$codigoRes=$rowResp['codigo'];
								$nombreRespo=$rowResp['paterno']." ".$rowResp['materno']." ".$rowResp['primer_nombre'];
								$abreviaturaP=$rowResp['abreviatura'];
							?>
							<option  value="<?=$codigoRes;?>" data-subtext="<?=$abreviaturaP?>"><?=$nombreRespo;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
					</div>
					<label class="col-sm-2 col-form-label" style="color:black;">Area/Sucursal *</label>
					<div class="col-sm-4">
						<select class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true" name="cod_area" id="cod_area" required="true">
						  	<?php
						  	echo '<option value="">SELECCIONE UN ITEM</option>';
						  	$sqlArea="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 order by nombre";
							$stmtArea = $dbh->prepare($sqlArea);
							$stmtArea->execute();
							while ($rowArea = $stmtArea->fetch(PDO::FETCH_ASSOC)) {
								$codigoA=$rowArea['codigo'];
								$nombreA=$rowArea['nombre'];
								$abrevA=$rowArea['abreviatura'];
							?>
							<option value="<?=$codigoA;?>" data-subtext="<?=$abrevA?>"><?=$nombreA;?></option>	
							<?php
						  	}
						  	?>
						</select>
						
					</div>
				</div> 
				
				<div class="row" >
				  <label class="col-sm-2 col-form-label" style="color:black;">F. Inicio *</label>
				  <div class="col-sm-4">
					<div class="form-group">
					  <input  type='date' class='form-control'  id='fecha_inicio' name='fecha_inicio' value="<?=$fecha_actual?>" required>
					</div>
				  </div>

				  <label class="col-sm-2 col-form-label" style="color:black;">F. Fin *</label>
				  <div class="col-sm-4">
					<div class="form-group">
					  <input  type='date' class='form-control'  id='fecha_final' name='fecha_final' value="<?=$fecha_actual?>" required>
					</div>
				  </div>
				</div>
				<hr style=" height: 5px;color: #e4e6eb; background-color: #e4e6eb;">
				<!-- <div class="row"><label class="col-sm-8 col-form-label " style="color:blue;">OPCIONES DE INVENTARIO HABILITADAS</label></div> -->
				<div class="row" >
				  <label class="col-sm-2 col-form-label" style="color:#36508f;">Verificación</label>
					  <div class="col-sm-2">
						<div class="form-group">
						<div class="form-check">
                      		<label class="form-check-label">
                        		<input class="form-check-input" type="checkbox" id="bandera_verificacion" name="bandera_verificacion[]" value="1" checked="true">
                        		<span class="form-check-sign"><span class="check"></span></span>
                      		</label>
                		</div>
					</div>
				  </div>

				  <label class="col-sm-2 col-form-label" style="color:#36508f;">Edición</label>
				  <div class="col-sm-2">
					<div class="form-group">
					 	<div class="form-check">
                      		<label class="form-check-label">
                        		<input class="form-check-input" type="checkbox" id="bandera_edicion" name="bandera_edicion[]" value="1">
                        		<span class="form-check-sign"><span class="check"></span></span>
                      		</label>
                		</div>
					</div>
				  </div>
				  <label class="col-sm-2 col-form-label" style="color:#36508f;">Transferencia</label>
				  <div class="col-sm-2">
					<div class="form-group">
					 	<div class="form-check">
                      		<label class="form-check-label">
                        		<input class="form-check-input" type="checkbox" id="bandera_transferencia" name="bandera_transferencia[]" value="1">
                        		<span class="form-check-sign"><span class="check"></span></span>
                      		</label>
                		</div>
					</div>
				  </div>
				</div>

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="btn btn-success">Guardar</button>
					<a href="index.php?opcion=inventario_af_list" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	</div>
</div>

