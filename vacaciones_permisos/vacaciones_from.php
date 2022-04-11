<?php

require_once 'conexion.php';
require_once 'styles.php';

$cod_personal=$_GET['cod_personal'];
$gestion=$_GET['gestion'];
$saldo=$_GET['saldo'];

$dbh = new Conexion();
// $fechamin=$gestion.'-01-01';
// $fechamax=$gestion.'-12-31';
?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="vacaciones_permisos/vacaciones_save.php" method="post">
		  	<input type="hidden" name="gestion" value="<?=$ges?>">
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
						  	<?php
							  $stmt = $dbh->prepare("SELECT p.codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona 
							  	from personal p 
							  where p.codigo=$cod_personal");
							$stmt->execute();
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$codigoX=$row['codigo'];
								$nombrePersonaX=$row['nombrepersona'];
							?>
							<option value="<?=$codigoX;?>" selected><?=$nombrePersonaX;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
			    </div>

			    <label class="col-sm-2 col-form-label">Días Vacación</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <input  type='number' style="color: green;" class='form-control'  step="any" id='dias_vacacion'  name='dias_vacacion' min="5" value="<?=$saldo?>" max="<?=$saldo?>" required>
						</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Fecha Salida</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <INPUT  type='date' class='form-control'  id='fecha_inicio' size='5' name='fecha_inicio' required>
						</div>
				  </div>
				  <div class="col-sm-1">
						<div class="form-group">
						  
						</div>
				  </div>
				  <label class="col-sm-2 col-form-label">Fecha Ingreso</label>
				  <div class="col-sm-2">
						<div class="form-group">
						  <INPUT  type='date' class='form-control'  id='fecha_final' size='5' name='fecha_final' required>
						</div>
				  </div>
				  <div class="col-sm-1">
						<div class="form-group">
						  
						</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Glosa</label>
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