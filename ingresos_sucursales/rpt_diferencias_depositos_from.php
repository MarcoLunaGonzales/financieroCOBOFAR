<?php
require_once 'conexion_comercial.php';
require_once 'styles.php';

$fechaActual=date('Y-m-d');
?>
<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="ingresos_sucursales/rpt_diferencias_depositos_print.php" method="post"  target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Diferencia de Depósitos</h4>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <label class="col-sm-2 col-form-label">Fecha Inicio</label>
            <div class="col-sm-4">
              <div class="form-group">
                <input class="form-control" type="date" value="<?=$fechaActual?>" name="fechainicio" id="fechainicio" required="true"/>
              </div>
            </div>
            <label class="col-sm-1 col-form-label">Fecha Fin</label>
            <div class="col-sm-4">
              <div class="form-group">
                <input class="form-control" type="date" value="<?=$fechaActual?>" name="fechafin" id="fechafin" required="true"/>
              </div>
            </div>
          </div>
           <div class="row">
            <label class="col-sm-2 col-form-label">Sucursal</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" name="sucursal[]" id="sucursal" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                  <?php 
                    $queryUO1 = "SELECT a.cod_ciudad,a.nombre_almacen from almacenes a 
                      where a.estado_pedidos=1 order by a.nombre_almacen";
                    $resp=mysqli_query($dbh,$queryUO1);
                  while($row=mysqli_fetch_array($resp)){  ?>
                      <option value="<?=$row['cod_ciudad'];?>"><?=$row["nombre_almacen"];?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label">Generar En Excel</label>
            <div class="col-sm-1">
              <div class="form-group">
                <div class="togglebutton">
                    <label>
                    <input type="checkbox" name="check_rs_cierres" id="check_rs_cierres" >
                    <span class="toggle"></span>
                    </label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonEdit;?>">Generar</button>
          <a href="index.php?opcion=main_cierrecaja_from" class="<?=$buttonCancel;?>">Volver</a>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>