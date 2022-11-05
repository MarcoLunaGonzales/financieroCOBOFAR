<?php
require_once 'conexion.php';
// require_once 'configModule.php';
require_once 'styles.php';


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT i.codigo,i.nombre,i.abreviatura,CONCAT_WS(' ',p.primer_nombre,p.paterno)as responsable,(select uo.nombre from unidades_organizacionales uo where uo.codigo=i.cod_uo) as unidad,a.nombre as area,i.fecha_inicio,i.fecha_fin,i.bandera_edicion,i.bandera_transferir,i.bandera_verificacion,i.cod_estado,eaf.nombre as estado
from inventarios_af i join personal p on i.cod_responsable=p.codigo join areas a on i.cod_area=a.codigo join estados_inventarios_af eaf on i.cod_estado=eaf.codigo
order by i.codigo desc limit 100");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('responsable', $responsable);
$stmt->bindColumn('unidad', $unidad);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('fecha_inicio', $fecha_inicio);
$stmt->bindColumn('fecha_fin', $fecha_fin);
$stmt->bindColumn('bandera_edicion', $bandera_edicion);
$stmt->bindColumn('bandera_transferir', $bandera_transferir);
$stmt->bindColumn('bandera_verificacion', $bandera_verificacion);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('estado', $estado);

?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div style="overflow-y:scroll;">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-info card-header-icon">
              <div class="card-icon">
                <i class="material-icons">support</i>
              </div>
              <h4 class="card-title"><b>Mantenimiento de AF</b></h4>
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group" align="right">
                    <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" >
                      <i class="material-icons" title="Buscador Avanzado">search</i>
                    </button>
                  </div>
                </div>
              </div>
            <div class="card-body" id="data_pago_proveedores">
                <table class="table table-condensed table-bordered small " id="tablePaginator">
                  <thead>
                    <tr class="bg-dark text-white">
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Responsable</th>
                      <th>Fecha I.</th>
                      <th>Fecha F.</th>
                      <!-- <th>Estado</th> -->
                      <th>Visualización</th>
                      <th>Edición</th>
                      <th>Transferencia</th>
                      <th>Estado</th>
                      <th class="text-right" width="25%">Actions</th>
                    </tr>
                  </thead>
                  <tbody><?php
                  $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      $icono_bandera_v="<i class='material-icons text-danger'>cancel</i>";
                      $icono_bandera_e="<i class='material-icons text-danger'>cancel</i>";
                      $icono_bandera_t="<i class='material-icons text-danger'>cancel</i>";
                      if($bandera_verificacion==1){
                        $icono_bandera_v="<i class='material-icons text-warning'>done_outline</i>";
                      }
                      if($bandera_edicion==1){
                        $icono_bandera_e="<i class='material-icons text-warning'>done_outline</i>";
                      }
                      if($bandera_transferir==1){
                        $icono_bandera_t="<i class='material-icons text-warning'>done_outline</i>";
                      }
                      switch ($cod_estado) {
                        case 1://REGISTRADO 
                          $estiloEstado="badge badge-default";
                          break;
                        case 2://ANULADO 
                          $estiloEstado="badge badge-danger";
                          break;
                        case 3://SINCRONIZADO
                          $estiloEstado="badge badge-success";
                          break;
                        case 4://EN REVISION
                          $estiloEstado="badge badge-warning";
                          break;
                        case 5://FINALIZACION
                          $estiloEstado="badge badge-warning";
                          break;
                      }
                      ?>
                    <tr>
                      <td><?=$codigo?></td>
                      <td><?=$nombre?> (<?=$abreviatura?>)</td>
                      <td><?=$responsable?></td>
                      <td><?=strftime('%d/%m/%Y',strtotime($fecha_inicio));?></td>
                      <td><?=strftime('%d/%m/%Y',strtotime($fecha_fin));?></td>
                      <td><?=$icono_bandera_v?></td>
                      <td><?=$icono_bandera_e?></td>
                      <td><?=$icono_bandera_t?></td>
                      <td><span class="<?=$estiloEstado?>"><?=$estado;?></span></td>
                      <td class="td-actions text-right">
                          <a href="#" target="_blank" class="btn btn-rose">
                             <i class="material-icons">payment</i>
                          </a>
                          <a href="#" target="_blank" class="btn btn-warning">
                            <i class="material-icons">print</i>
                          </a>
                          <a href="#"  class="btn btn-success">
                            <i class="material-icons">attach_money</i>
                          </a>  
                                      
                      </td>
                    </tr>
                  <?php
                      $index++;
                    }
                  ?>
                  </tbody>
                </table>
            </div>
          </div>
          <div class="card-footer fixed-bottom">
            <button class="btn btn-info btn-round" onClick="location.href='index.php?opcion=inventario_af_register&codigo=0'"><i class="material-icons">add</i>NUEVO</button>
          </div>      
        </div>
      </div>  
    </div>
  </div>
</div>


<div class="modal fade" id="modalBuscador_pagoproveedores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="color:#547ba4;font-size:25px;">Buscador Pago Proveedores</h4>
        <center><img src="assets/img/robot.gif" width="50"></center>
         <br>
      </div>
      <div class="modal-body ">
        <div class="row">
          <label class="form-group col-sm-2 text-center"><b>Nro. Pago</b></label>
          <div class="form-group col-sm-2">
            <input class="form-control input-sm" type="text" name="nro_pagoproveedor" id="nro_pagoproveedor"  >
          </div>
          <label class="form-group col-sm-2 text-center"><b>Fechas</b></label>
           <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin">
          </div>
        </div>
        <br><br>
        <div class="row">

            <label class="col-sm-1 text-center form-group"><b>Personal</b></label>
          <div class="form-group col-sm-4">
            <select  name="personal_busqueda[]" id="personal_busqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>
              <?php 
              $stmt_per = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) as nombre
                from pagos_lotes pl join personal p on pl.created_by=p.codigo
                where pl.cod_estadoreferencial=1 GROUP BY pl.created_by order by p.primer_nombre");
              $stmt_per->execute();
              $stmt_per->bindColumn('codigo', $codigo_per);
              $stmt_per->bindColumn('nombre', $nombre_per);
              while ($rowper = $stmt_per->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_per;?>"><?=$nombre_per;?></option>
              <?php }?>
            </select>
          </div>

          <label class="col-sm-1 text-center form-group"><b>Glosa</b></label>
          <div class="form-group col-sm-6">
            <input class="form-control input-sm" type="text" name="razon_social_b" id="razon_social_b"  >
          </div>      
        </div> 


           
         
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-rose" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscar_pagoproveedores()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>


