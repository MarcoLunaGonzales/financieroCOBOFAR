<?php
require_once '../layouts/bodylogin2.php';

require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require("../conexion_comercial.php");
?>
<div class="content">
	<div class="container-fluid">
		<!-- <div style="overflow-y:scroll; ">-->
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Reporte Movimientos Costo - Sucursal Detallado</h4>
          </div>
          <form class="" action="rptInv_movimientos_costo_sucursalesIngresos.php" target="_blank" method="POST">
          <div class="card-body">
              <div class="row">
              	<label class="col-sm-2 col-form-label">Sucursales</label>
	                <div class="col-sm-4">
	                	<div class="form-group">
	                		<select name="sucursal[]" id="sucursal" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true" multiple data-actions-box="true">
                          <?php 
                          $queryUO1 = "SELECT cod_almacen,nombre_almacen from almacenes where estado_pedidos=1 or cod_almacen=1000 order by nombre_almacen";
                                $resp=mysqli_query($dbh,$queryUO1);
                                while($row=mysqli_fetch_array($resp)){  ?>
                              <option value="<?=$row['cod_almacen'];?>"><?=$row["nombre_almacen"];?></option>
                          <?php } ?>
                      </select>
	                  </div>
	                </div>

              </div>
              <div class="row">
                <label class="col-sm-2 col-form-label">Sucursales Cerradas</label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <select name="tipo_cerrada" id="tipo_cerrada" class="selectpicker form-control form-control-sm" data-style="btn btn-warning"  data-show-subtext="true" required="true">
                          <option value="1">SI</option>
                          <option value="0" selected>NO</option>
                      </select>
                    </div>
                  </div>

              </div>
             	<div class="row">
                <label class="col-sm-2 col-form-label">Fecha Inicio</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <input class="form-control" type="date" name="fechainicio" id="fechainicio" required="true"/>
                  </div>
                </div>
                <label class="col-sm-1 col-form-label">Fecha Fin</label>
                <div class="col-sm-4">
                  <div class="form-group">
                    <input class="form-control" type="date" name="fechafin" id="fechafin" required="true"/>
                  </div>
                </div>
              </div>
               

          </div><!--card body--> 
          <div class="card-footer">
          	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
  					</div>
         </form> 
        </div>	  
      </div>         
        <!-- </div>	 -->
	</div>
</div>

