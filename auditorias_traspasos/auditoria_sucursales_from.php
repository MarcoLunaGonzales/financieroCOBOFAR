<?php
require_once 'conexion.php';
require_once 'styles.php';

$fechaActual=date('Y-m-d');
?>
<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="auditorias_traspasos/auditoria_sucursales_print.php" method="post"  target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Auditoria de Traspasos</h4>
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
            <label class="col-sm-2 col-form-label">Sucursal Origen</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" name="sucursal_origen[]" id="sucursal_origen" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                  <?php 
                    $queryUO1 = "SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
                      FROM areas_organizacion
                      where cod_estadoreferencial=1 and cod_unidad=2  or cod_area in (512,513) order by nombre_area";
                    $stmt = $dbh->query($queryUO1);
                    while ($row = $stmt->fetch()){ ?>
                      <option value="<?=$row['cod_area'];?>"><?=$row["nombre_area"];?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <label class="col-sm-1 col-form-label">Sucursal Destino</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" name="sucursal_destino[]" id="sucursal_destino" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                  <?php 
                    $queryUO1 = "SELECT cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
                      FROM areas_organizacion
                      where cod_estadoreferencial=1 and cod_unidad=2 or cod_area in (512,513) order by nombre_area";
                    $stmt = $dbh->query($queryUO1);
                    while ($row = $stmt->fetch()){ ?>
                      <option value="<?=$row['cod_area'];?>"><?=$row["nombre_area"];?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

       

          <div class="row">
            <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" name="tipo" id="tipo" data-style="select-with-transition" data-size="5" data-actions-box="true" required data-show-subtext="true" data-live-search="true">
                     <option value="1">Pendientes de Ingreso</option>
                     <option value="-1">Pendientes de Ingreso Detallado</option>
                     <option value="2">Tiempos en Traspasos Observados</option>
                     <option value="3">Tiempos en Traspasos TODO</option>
                </select>
              </div>
            </div>
          </div>
           <div class="row">
            <label class="col-sm-2 col-form-label">Tiempo en tránsito</label>
            <div class="col-sm-4">
              <div class="form-group">
                <select name='periodo_ingreso' class='selectpicker form-control' data-style='btn btn-primary'>";
                  <option value='1'>24 Hrs</option>
                  <option value='2'>48 Hrs</option>
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