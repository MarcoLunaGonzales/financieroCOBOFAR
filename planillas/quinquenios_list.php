<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT f.codigo,f.cod_personal,f.fecha_ingreso,f.fecha_retiro,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) as nombre_personal,
(Select t.nombre from tipos_retiro_personal t where t.codigo=cod_tiporetiro) as motivo_retiro,f.anios_indemnizacion,fecha_pago,fecha_solicitud,anios_pagados
from finiquitos f join personal p on f.cod_personal=p.codigo
where f.cod_estadoreferencial=1 and f.tipo_beneficio=2 limit 20");//f.tipo_beneficio=2 corresponde a quinquenio
  //ejecutamos
  $stmt->execute();
  //bindColumn
  $stmt->bindColumn('codigo', $codigo);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('nombre_personal', $nombre_personal);
  $stmt->bindColumn('fecha_ingreso', $fecha_ingreso);
  $stmt->bindColumn('fecha_retiro', $fecha_retiro);
  $stmt->bindColumn('motivo_retiro', $motivo_retiro);
  $stmt->bindColumn('anios_indemnizacion', $anios_indemnizacion);
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Quinquenios</h4>
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>                    
                      <th>#</th>
                      <th>Cód. Personal</th>      
                      <th>Nombre Personal</th>      
                      <th>Fecha Ingreso</th>                      
                      <th>Fecha Retiro</th>                      
                      <th>Motivo Retiro</th> 
                      <th></th>
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;
                  // $datos="";
                  // $cont= array();
                  $datosX="";
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    ?>
                    <tr>                    
                      
                      <td><?=$index?></td>
                      <td><?=$cod_personal;?></td>                      
                      <td><?=strtoupper($nombre_personal);?></td>
                      <td><?=$fecha_ingreso;?></td>
                      <td><?=$fecha_retiro;?></td>
                      <td><?=$motivo_retiro;?></td>
                      <td class="td-actions text-right">
                        <?php
                          if($globalAdmin==1){
                        ?>
                          <a href='planillas/quinquenios_print.php?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                            <i class="material-icons" title="Imprimir Quinquenio">print</i>
                          </a>                          
                          <a href='?opcion=quinquenios_form&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                            <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                          </a>
                          <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteFiniquito;?>&codigo=<?=$codigo;?>')">
                            <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                          </button>
                          <?php
                            }
                          ?>
                        
                        </td>

                    </tr>
                  <?php $index++; } ?>
                </tbody>                                      
              </table>
          </div>
          <?php

              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='?opcion=quinquenios_form&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
           
        </div>
      </div>
    </div>
  </div>


