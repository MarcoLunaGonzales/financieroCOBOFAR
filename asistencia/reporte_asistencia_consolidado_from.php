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
            <label class="col-sm-2 col-form-label">Sucursal</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select name="cod_sucursal[]" id="cod_sucursal" class="selectpicker form-control form-control-sm" required data-style="select-with-transition" data-size="5" data-actions-box="true" multiple data-live-search="true">
                    <?php
                      $sql="SELECT a.codigo,a.nombre from areas a where a.cod_estado=1 and a.centro_costos=1 order by a.nombre";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigo=$rowg['codigo'];
                        $nombre=$rowg['nombre'];
                      ?>
                      <option  value="<?=$codigo;?>"><?=$nombre;?></option>
                      <?php 
                      }
                    ?>
                </select>
              </div>
            </div>
          </div><!--fin campo gestion -->
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