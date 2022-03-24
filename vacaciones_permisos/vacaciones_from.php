<?php

require_once 'conexion.php';
require_once 'styles.php';


$dbh = new Conexion();
$fecha_rptdefault=date("Y-m-d");
$hora_rptinidefault=date('H:m');
?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="vacaciones_permisos/vacaciones_save.php" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar Vacaciones</h4>
					</div>				
			  </div>
			  <div class="card-body ">
			  <div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
					<div class="col-sm-4">
			        	<div class="form-group">
				       <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="<?=$comboColor;?>" required="true">
						  	<option disabled selected value="">Persona</option>
						  	<?php
							  $stmt = $dbh->prepare("SELECT p.codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona 
							  	from personal p 
							  where p.cod_estadoreferencial=1 and p.cod_estadopersonal=1");
							$stmt->execute();
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$codigoX=$row['codigo'];
								$nombrePersonaX=$row['nombrepersona'];
							?>
							<option value="<?=$codigoX;?>"><?=$nombrePersonaX;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
			    </div>

			    <label class="col-sm-2 col-form-label">Días Vacación</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <input  type='number' style="color: green;" class='form-control'  step="any" id='dias_vacacion' min='0.5'  name='dias_vacacion' required>
						</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Fecha Salida</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <INPUT  type='date' class='form-control' value='<?=$fecha_rptdefault?>' id='fecha_inicio' size='5' name='fecha_inicio' required>
						</div>
				  </div>
				  <div class="col-sm-1">
						<div class="form-group">
						  
						</div>
				  </div>
				  <label class="col-sm-2 col-form-label">Fecha Ingreso</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <INPUT  type='date' class='form-control' value='<?=$fecha_rptdefault?>' id='fecha_final' size='5' name='fecha_final' required>
						</div>
				  </div>
				  <div class="col-sm-1">
						<div class="form-group">
						  
						</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Motivo</label>
				  <div class="col-sm-8">
						<div class="form-group">
						  <INPUT  type='text' class='form-control'  id='observaciones' name='observaciones' required>
						</div>
				  </div>
				</div>

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="btn btn-success">Guardar</button>
				<a href="index.php?opcion=vacacionesPersonalLista" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>