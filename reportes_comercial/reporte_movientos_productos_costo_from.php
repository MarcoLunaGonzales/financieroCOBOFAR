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
            <h4 class="card-title">Reporte Movimientos de Productos</h4>
          </div>
          <form class="" action="reporte_movientos_productos_costo_print.php" target="_blank" method="POST">
          <div class="card-body">
              <div class="row">
              	<label class="col-sm-2 col-form-label">Sucursal</label>
	                <div class="col-sm-4">
	                	<div class="form-group">
	                		<select name="sucursal" id="sucursal" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                          <option value=""></option>
                          <?php 
                          $queryUO1 = "SELECT cod_almacen,nombre_almacen from almacenes where estado_pedidos=1 order by nombre_almacen";
                                $resp=mysqli_query($dbh,$queryUO1);
                                while($row=mysqli_fetch_array($resp)){  ?>
                              <option value="<?=$row['cod_almacen'];?>"><?=$row["nombre_almacen"];?></option>
                          <?php } ?>
                      </select>
	                  </div>
	                </div>

	                <label class="col-sm-1 col-form-label">Proveedor</label>
                  <div class="col-sm-4">
                      <div class="form-group">
                          <select name="proveedor" id="proveedor" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                              <option value=""></option>
                              <?php 
                              $queryUO1 = "SELECT cod_proveedor,nombre_proveedor from proveedores where estado_activo=1 and cod_proveedor>0 order by nombre_proveedor";
                                    $resp=mysqli_query($dbh,$queryUO1);
                                    while($row=mysqli_fetch_array($resp)){  ?>
                                  <option value="<?=$row['cod_proveedor'];?>"><?=$row["nombre_proveedor"];?></option>
                              <?php } ?>
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

