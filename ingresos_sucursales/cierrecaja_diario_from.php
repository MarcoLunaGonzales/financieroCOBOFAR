<?php
require_once 'conexion.php';
require_once 'styles.php';

$fechaActual=date('Y-m-d');
?>
<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="ingresos_sucursales/cierrecaja_diario_print.php" method="post"  target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Ingresos Sucursales</h4>
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
                    $queryUO1 = "SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
                      FROM areas_organizacion
                      where cod_estadoreferencial=1 and cod_unidad=2 order by nombre_area";
                    $stmt = $dbh->query($queryUO1);
                    while ($row = $stmt->fetch()){ ?>
                      <option value="<?=$row['cod_area'];?>"><?=$row["nombre_area"];?></option>
                  <?php } ?>
                </select>
              </div>
            </div>                      
          </div>
        </div>
        <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
          <a href="index.php?opcion=main_cierrecaja_from" class="<?=$buttonCancel;?>">Volver</a>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>