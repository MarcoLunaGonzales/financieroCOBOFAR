<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,(select t.nombre from tipos_pagoproveedor t where t.codigo=sr.cod_ebisalote)as tipo_pago,e.nombre as estado from pagos_lotes sr join estados_pago e on sr.cod_estadopagolote=e.codigo where cod_estadoreferencial=1 and sr.cod_estadopagolote=4 order by sr.codigo desc");
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
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-icon" >
                  <div class="card-icon" style="background:  #283747 ;">
                    <i class="material-icons">attach_money</i>
                  </div>
                  <h4 class="card-title"><b>Pagos Proveedores ADM</b></h4>
                </div>
                <div class="card-body">
                    <table class="table table-condesed small" id="tablePaginator">
                      <thead>
                        <tr style="background:#283747 ; color:#fff;">
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
                                // $codEstado=5; 
                                $btnEstado="btn-info";
                                // $estado="Pagado";
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
                                if($codEstado!=2){
                                  if($codEstado==4){ ?>
                                    <a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=1&admin=1" class="dropdown-item">
                                     <i class="material-icons text-danger">refresh</i> Devolver Pago
                                    </a>
                                    <a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=3&admin=1" class="dropdown-item">
                                      <i class="material-icons text-success">offline_pin</i> Aprobar
                                    </a><?php
                                  }      
                                }
                               ?>   
                              </div>
                            </div>             
                          </td>
                        </tr><?php
                        $index++;
                      } ?>
                      </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegisterLote_adminhistorico;?>')" class="btn btn-info">HISTORIAL</a>
              </div> 
            </div>
          </div>  
        </div>
    </div>


