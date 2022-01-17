<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,(select t.nombre from tipos_pagoproveedor t where t.codigo=sr.cod_ebisalote)as tipo_pago,e.nombre as estado from pagos_lotes sr join estados_pago e on sr.cod_estadopagolote=e.codigo where cod_estadoreferencial=1 order by sr.codigo desc limit 50");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_estadopagolote', $codEstado);
$stmt->bindColumn('nro_correlativo', $nro_correlativo);
$stmt->bindColumn('tipo_pago', $tipo_pago);//cod_tipo pago
$stmt->bindColumn('created_at', $created_at);//fecha creacion
$stmt->bindColumn('created_by', $created_by);//cod responsable

$array_personal_autorizado=explode(",",obtenerValorConfiguracion(109));
$string_personal=false;
for ($i=0; $i <count($array_personal_autorizado) ; $i++) { 
  $cod_personal=$array_personal_autorizado[$i];
  if($globalUser==$cod_personal){
   $string_personal=true;
  }
} 



?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    <div style="overflow-y:scroll;">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">attach_money</i>
              </div>
              <h4 class="card-title"><b>Pago Proveedores</b></h4>
            </div>
            <div class="row">
                <div class="col-sm-12">
                  <div class="form-group" align="right">
                    <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador_pagoproveedores">
                      <i class="material-icons" title="Buscador Avanzado">search</i>
                    </button>                               
                  </div>
                </div>
              </div>
            <div class="card-body" id="data_pago_proveedores">
                <table class="table table-condesed small" id="tablePaginator">
                  <thead>
                    <tr style="background:#21618C; color:#fff;">
                      <th>Nro</th>
                      <th>Nombre</th>
                      <th>Fecha Pago</th>
                      <th>Tipo Pago</th>
                      <th>OBS</th>
                      <!-- <th>Estado</th> -->
                      <th class="text-right" width="25%">Actions</th>
                    </tr>
                  </thead>
                  <tbody><?php
                  $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      $sta_comprobante=obtenerEstadoComprobante($codComprobante);
                      $nombre_responsable= namePersonalCompleto($created_by);
                      $detalle_responsable="Creado Por : ".$nombre_responsable." En Fecha: ".$created_at;
                      switch ($codEstado) {
                        case 1:
                          $btnEstado="btn-default";
                          break;
                        case 2:
                          $btnEstado="btn-danger";
                          break;
                        case 3:
                          $btnEstado="btn-success";
                          break;
                        case 4:
                          $btnEstado="btn-warning";
                          break;
                        case 5:
                          if($codComprobante>0 && $sta_comprobante!=2){
                            $btnEstado="btn-info";
                          }else{
                            $codEstado=3; 
                            $btnEstado="btn-success";
                            $estado="Aprobado";
                          }
                          break;
                      }
                      ?>
                    <tr>
                      <td><?=$nro_correlativo?></td>
                      <td><?=$nombre?></td>
                      <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                      <td><?=$tipo_pago;?></td>
                      <td><?=$observaciones?></td>
                      <!-- <td class="text-muted"><?=$estado?></td> -->
                      <td class="td-actions text-right">
                        <?php 
                        if($codComprobante>0 and $sta_comprobante!=2){//print comprobante
                          ?>
                          <div class="btn-group dropdown">
                            <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE DE PAGOS" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             <i class="material-icons"><?=$iconImp;?></i>
                            </button>
                                <div class="dropdown-menu">
                                  <?php
                                    $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                   $stmtMoneda->execute();
                                   while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                     $codigoX=$row['codigo'];
                                     $nombreX=$row['nombre'];
                                     $abrevX=$row['abreviatura'];
                                        ?>
                                         <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                             <i class="material-icons">list_alt</i> <?=$abrevX?>
                                         </a> 
                                       <?php
                                     }
                                     ?>
                                </div>
                              </div>   
                          <?php  
                        }
                        ?>
                        <div class="btn-group dropdown">
                          <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?=$detalle_responsable?>">
                            <i class="material-icons">list</i> <?=$estado;?>
                          </button>
                          <div class="dropdown-menu">
                            <a href="<?=$urlVerPago?>?cod=<?=$codigo?>&sw_estado=<?=$codEstado?>" target="_blank" class="dropdown-item">
                               <i class="material-icons text-rose">payment</i>Ver Pago
                            </a>
                            <a href="<?=$urlOrdenPagoPrint?>?cod=<?=$codigo?>" target="_blank" class="dropdown-item">
                              <i class="material-icons text-warning">print</i>Imprimir Orden Pago
                            </a>
                            <?php
                            if($codEstado!=2 and $string_personal){
                              if($codComprobante==0 || $sta_comprobante==2){ ?>
                                <!-- <a title="Editar Pago Proveedores"  href='<?=$urlEditPagoLote;?>?cod=<?=$codigo;?>' class="dropdown-item">
                                  <i class="material-icons text-info"><?=$iconEdit;?></i>Editar Pago
                                </a>  -->
                                <a href="#" onclick="alerts.showSwal('warning-message-crear-comprobante','<?=$urlGenerarComprobanteLote?>?cod=<?=$codigo?>')" class="dropdown-item">
                                  <i class="material-icons text-success">attach_money</i> Generar Comprobante Pago
                                </a><?php
                              }
                            }
                           ?>   
                          </div>
                        </div>             
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
            <a href="#" onclick="javascript:window.open('<?=$urlRegisterLote;?>')" class="btn btn-primary"><i class="material-icons">add</i> Nuevo Pago</a>
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


