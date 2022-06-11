<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';


$globalAdmin="";
$globalUnidad="";
$globalArea="";
$globalUser="";

$dbh = new Conexion();
$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";
?>

<div class="content">
	<div class="container-fluid">
				 	
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte de Comprobantes</h4>
                </div>
                <form class="" action="reportes/reporteComprobantes_print.php" target="_blank" method="POST">
                <div class="card-body">
              	<div class="row">
	                <label class="col-sm-1 col-form-label">Personal</label>
				          <div class="form-group col-sm-4">
				            <select  name="personal_busqueda[]" id="personal_busqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>
				              <?php 
				              $stmt_per = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as nombre
												from comprobantes pl join personal p on pl.created_by=p.codigo
												GROUP BY pl.created_by order by p.primer_nombre");
				              $stmt_per->execute();
				              $stmt_per->bindColumn('codigo', $codigo_per);
				              $stmt_per->bindColumn('nombre', $nombre_per);
				              while ($rowper = $stmt_per->fetch(PDO::FETCH_BOUND)) { ?>
				                <option value="<?=$codigo_per;?>"><?=$nombre_per;?></option>
				              <?php }?>
				            </select>
				          </div>
				          <label class="col-sm-2 col-form-label">Tipo Comprobante</label>
				          <div class="form-group col-sm-4">
				            <select  name="cod_tipocomprobante[]" id="cod_tipocomprobante" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>
				              <?php 
				              $stmt_per = $dbh->prepare("select c.cod_tipocomprobante,tc.nombre 
												from comprobantes c join tipos_comprobante tc on c.cod_tipocomprobante=tc.codigo
												GROUP BY c.cod_tipocomprobante order by tc.nombre");
				              $stmt_per->execute();
				              $stmt_per->bindColumn('cod_tipocomprobante', $cod_tipocomprobante);
				              $stmt_per->bindColumn('nombre', $nombre_comp);
				              while ($rowper = $stmt_per->fetch(PDO::FETCH_BOUND)) { ?>
				                <option value="<?=$cod_tipocomprobante;?>"><?=$nombre_comp;?></option>
				              <?php }?>
				            </select>
				          </div>

              	</div><!--div row-->
	              <div class="row">	                  	
						<label class="col-sm-1 col-form-label">Desde</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" value="<?=$fechaDesde?>">	
								</div>		                                
							 </div>
						</div>
						<label class="col-sm-2 col-form-label">Hasta</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$fechaHasta?>">
								</div>
							</div>
						</div>	
	            </div><!--div fechas row-->	                
	            </div><!--div fechas row-->
                <div class="card-footer">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
			  				</div>
               </form> 
              </div>	  
            </div>
	</div>
        
</div>

