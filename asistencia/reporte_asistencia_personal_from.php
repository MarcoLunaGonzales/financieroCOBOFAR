<?php
require_once 'conexion.php';
require_once 'styles.php';
$dbh = new Conexion();
$fecha_inicial=date('Y-m-01');
$fecha_fin=date('Y-m-t');
$add_per="";
if(isset($_GET['cp'])){
  $codigoPer=$_GET['cp'];
  $add_per=" and p.codigo in ($codigoPer)";
}
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="asistencia/reporte_asistencia_personal_print.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header card-header-text">
				<div class="card-text" style="background:#f1948a;">
				  <h4 class="card-title">Reporte de Marcación Por Persona</h4>
				</div>
			  </div>
			  <div class="card-body ">
          <div class="row">
            <label class="col-sm-2 col-form-label">Personal</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select name="cod_personal" id="cod_personal" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-live-search="true" required >
                    <?php
                      $sql="SELECT p.codigo,p.identificacion,p.primer_nombre,p.paterno,p.materno from personal p where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1  $add_per order by p.paterno";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigo=$rowg['codigo'];
                        $identificacion=$rowg['identificacion'];
                        $primer_nombre=$rowg['primer_nombre'];
                        $paterno=$rowg['paterno'];
                        $materno=$rowg['materno'];
                      ?>
                      <option  value="<?=$codigo;?>" data-subtext="<?=$identificacion?>"><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?></option>
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