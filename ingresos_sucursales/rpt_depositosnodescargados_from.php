<?php
require_once 'conexion.php';
require_once 'styles.php';

$fechaActual=date('Y-m-d');
?>
<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="ingresos_sucursales/rpt_depositosnodescargados_print.php" method="post"  target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Registro de Depósitos</h4>
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
            <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" data-style="btn btn-primary btn-sm" title="Seleccione una opcion" name="tipo_registro" id="tipo_registro"  required >
                  <option value="1">No Registrados</option>
                  <option value="2">Registrados</option>
                </select>
              </div>
            </div>

            <label class="col-sm-1 col-form-label">Sucursal</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" name="sucursal" id="sucursal" data-style="btn btn-primary btn-sm" required  data-live-search="true">
                  <option value="-100">TODO (solo tipo no Registrados)</option>
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
          <button type="submit" class="<?=$buttonEdit;?>">Generar</button>
          <a href="index.php?opcion=main_cierrecaja_from" class="<?=$buttonCancel;?>">Volver</a>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>