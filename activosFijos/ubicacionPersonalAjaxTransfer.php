<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigo_UO=$_GET["codigo_UO"];
$db = new Conexion();

$stmt = $db->prepare("SELECT p.codigo,(CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre))as nombre from personal p, unidades_organizacionales uo 
where uo.codigo=p.cod_unidadorganizacional and uo.codigo=:codigo_UO and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 order by nombre");
$stmt->bindParam(':codigo_UO', $codigo_UO);
$stmt->execute();
$stmt2 = $db->prepare("SELECT p.codigo,(CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre))as nombre from personal p, unidades_organizacionales uo 
where uo.codigo=p.cod_unidadorganizacional and uo.codigo=:codigo_UO and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 order by nombre");
$stmt2->bindParam(':codigo_UO', $codigo_UO);
$stmt2->execute();
?>
<div class="row">
	<label class="col-sm-2 col-form-label">Responsable 1</label>
	<div class="col-sm-3">
	  <div class="form-group">
		<select id="cod_responsables_responsable" name="cod_responsables_responsable" class="selectpicker form-control form-control-sm" 
		data-style="btn btn-primary" data-size="5" data-show-subtext="true" data-live-search="true" required="true">
			<option value=""></option>
		    <?php 
	        while ($row = $stmt->fetch()){ 
	       ?>
	       <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
	        <?php 
	        } ?>
		</select>      
	  </div>
	</div><!--fin campo cod_responsables_responsable -->
	<label class="col-sm-2 col-form-label">Responsable 2</label>
	<div class="col-sm-3">
	  <div class="form-group">
		<select id="cod_responsables_responsable2" name="cod_responsables_responsable2" class="selectpicker form-control form-control-sm" 
		data-style="btn btn-primary" data-size="5" data-show-subtext="true" data-live-search="true">
			<option value=""></option>
		    <?php 
	        while ($row = $stmt2->fetch()){ 
	       ?>
	       <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
	        <?php 
	        } ?>
		</select>      
	  </div>
	</div><!--fin campo cod_responsables_responsable -->


</div>
