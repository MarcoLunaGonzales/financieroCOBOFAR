<?php
require_once 'conexion.php';
require_once 'styles.php';
$dbh = new Conexion();
$fecha_inicial=date('Y-m-01');
$fecha_fin=date('Y-m-t');
?>


<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="asistencia/reporte_asistencia_consolidado_print.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header card-header-text">
				<div class="card-text" style="background:#7d3c98;">
				  <h4 class="card-title">Reporte de Marcación Consolidado</h4>
				</div>
			  </div>
			  <div class="card-body ">

          <div class="row">
            <label class="col-sm-2 col-form-label">Oficina</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" 
                name="unidad_organizacional[]" id="unidad_organizacional" 
                data-style="select-with-transition" data-size="5" 
                data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true" onChange="ajaxRPTAF_oficina();">
                  <?php
                    $sql="SELECT codigo,nombre,abreviatura FROM unidades_organizacionales where cod_estado=1 order by nombre";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                    ?>
                    <option value="<?=$codigoX;?>"><?=$row['abreviatura'];?> - <?=$nombreX;?></option>
                    <?php 
                    }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <!--  fin de seleccion unidad organizacional-->
          <div class="row">
            <label class="col-sm-2 col-form-label">Area</label>
            <div class="col-sm-7">
            <div class="form-group">
              <div id="contenedor_areas_reporte">
                
              </div>
            </div>
            </div>
          </div>
          
          <div class="row">
            <label class="col-sm-2 col-form-label">Fecha Inicio</label>
            <div class="col-sm-3">
              <div class="form-group">
                <input type="date"  class="form-control" name="fecha_inicio" value="<?=$fecha_inicial?>">
              </div>
            </div>
            <label class="col-sm-2 col-form-label">Fecha Fin</label>
            <div class="col-sm-3">
              <div class="form-group">
                <input type="date"  class="form-control" name="fecha_fin" value="<?=$fecha_fin?>">
              </div>
            </div>
          </div>
          <!--  fin de seleccion unidad organizacional-->
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?> btn-sm">Generar</button>
          
          <a href="?opcion=asistenciaPersonal_main" class="btn btn-danger btn-sm">Volver</a>
			 </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>