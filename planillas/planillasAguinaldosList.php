<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalCodUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION["globalNombreUnidad"];

$dbh = new Conexion();

  $stmtAdmnin = $dbh->prepare("SELECT codigo,cod_gestion,cod_estadoplanilla,
  (select g.nombre from gestiones g where g.codigo=cod_gestion) as gestion,
  (select ep.nombre from estados_planilla ep where ep.codigo=cod_estadoplanilla) as nombre_estadoplanilla
  from planillas_aguinaldos 
  ");
  $stmtAdmnin->execute();
  $stmtAdmnin->bindColumn('codigo', $codigo_planilla);
  $stmtAdmnin->bindColumn('gestion', $gestion);
  $stmtAdmnin->bindColumn('cod_gestion', $cod_gestion);
  $stmtAdmnin->bindColumn('cod_estadoplanilla', $cod_estadoplanilla);
  $stmtAdmnin->bindColumn('nombre_estadoplanilla', $nombre_estadoplanilla);

  $stmtAdmninUO = $dbh->prepare("SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1
  GROUP BY cod_uo");
  $stmtAdmninUO->execute();
  $stmtAdmninUO->bindColumn('cod_uo', $cod_uo_x);
  $stmtAdmninUO->bindColumn('nombre_uo', $nombre_uo_x);
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="col-md-12">     
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title">Planilla De Aguinaldos</h4>       
          </div>
          <div class="card-body ">
              <table class="table" id="tablePaginator">
                <thead>
                    <tr>                    
                      <th>Gestión</th>
                      <th>Estado</th>
                      <th></th> 
                      <th></th>
                    </tr>
                </thead>
                <tbody>
                  <?php $index=1;                  
                  $datosX="";
                  while ($row = $stmtAdmnin->fetch(PDO::FETCH_BOUND)) {
                    $label_uo_aux='';
                    $datosX =$codigo_planilla."-";
                    if($cod_estadoplanilla==1){
                      $label='<span class="badge badge-danger">';
                    }
                    if($cod_estadoplanilla==2){
                      $label='<span class="badge badge-warning">';
                      $nombre_estadoplanilla='';
                      $stmtAdmninUOAux = $dbh->prepare("SELECT cod_uo,abreviatura from configuraciones_planilla_sueldo
                      GROUP BY abreviatura");
                      $stmtAdmninUOAux->execute();
                      $stmtAdmninUOAux->bindColumn('cod_uo', $cod_uo_aux);
                      $stmtAdmninUOAux->bindColumn('abreviatura', $nombre_uo_aux);
                      while ($row = $stmtAdmninUOAux->fetch(PDO::FETCH_BOUND)) {
                        $stmtAdmninUOAux2 = $dbh->prepare("SELECT cod_uo
                           from planillas_aguinaldos_uo_cerrados where cod_planilla=$codigo_planilla and cod_uo=$cod_uo_aux");
                        $stmtAdmninUOAux2->execute();
                        $resultAdmninUOAux2=$stmtAdmninUOAux2->fetch();
                        $cod_uo_aux2=$resultAdmninUOAux2['cod_uo'];                    
                        if($cod_uo_aux==$cod_uo_aux2){
                          $label_uo_aux.='<span class="badge badge-success">'.$nombre_uo_aux.'</span>';
                        }else{
                          $label_uo_aux.='<span class="badge badge-warning">'.$nombre_uo_aux.'</span>';
                        }
                      }
                    }
                    if($cod_estadoplanilla==3){                      
                      $label='<span class="badge badge-success">';
                    }                  
                    ?>
                    <tr>                    
                      <td><?=$gestion?></td>
                      <td><?=$label.$nombre_estadoplanilla."</span>";?><?=$label_uo_aux?></td>
                      <td class="td-actions text-right">
                        <?php
                        if($cod_estadoplanilla==1){    ?>
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalProcesar" onclick="agregaformPre('<?=$datosX;?>')">
                          <i class="material-icons" title="Procesar Planilla Aguinaldos">perm_data_setting</i>
                        </button>                      
                        <?php }                                                                          
                        if($cod_estadoplanilla==2){    ?>
                        <button type="button" class="btn" style="background-color:#3b83bd;color:#ffffff;" data-toggle="modal" data-target="#modalreProcesar" onclick="agregaformRP('<?=$datosX;?>')">
                          <i class="material-icons" title="Reprocesar Planilla Aguinaldos">autorenew</i>                   
                        </button>                                                                            
                      
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalCerrar" onclick="agregaformCP('<?=$datosX;?>')">
                          <i class="material-icons" title="Cerrar Planilla Aguinaldos">assignment_returned</i>
                        </button>
                        <?php }?>
                      </td>
                      <td class="td-actions text-right">
                        <?php                      
                        if($cod_estadoplanilla==2 || $cod_estadoplanilla==3){    ?>                                                                                                                                 
                        <div class="dropdown">
                          <button class="btn btn-primary dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla Aguinaldos">remove_red_eye</i>                        
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>OFICINA</small></li>
                            <?php
                            while ($row = $stmtAdmninUO->fetch(PDO::FETCH_BOUND)) {
                              if($cod_uo_x>0){?>                                                                
                                  <li role="presentation"><a role="item" href="<?=$urlPlanillaAguinaldoPersonalReporte;?>?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&codigo_uo=<?=$cod_uo_x;?>" target="_blank"><small><?=$nombre_uo_x;?></small></a></li>
                                <?php 
                              }
                            }
                          ?>                              
                          </ul>
                        </div>

                        <div class="dropdown">
                          <button class="btn btn-danger dropdown-toggle" type="button" id="reporte_sueldos" data-toggle="dropdown" aria-extended="true">
                            <i class="material-icons" title="Ver Planilla Aguinaldos">remove_red_eye</i>                        
                            <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu" aria-labelledby="reporte_sueldos">
                            <li role="presentation" class="dropdown-header"><small>Reportes</small></li>
                            <li role="presentation"><a role="item" href='planillas/planillasAguinaldosPDF.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>' target="_blank"><small>Planilla General</small></a></li>
                            <li role="presentation"><a role="item" href='boletas/boletas_aguinaldos_from.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>' target="_blank"><small>Boletas</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/reportePlanillasSueldos_depositoBanco_aguinaldo.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=1" target="_blank"><small>CON CUENTA</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/reportePlanillasSueldos_depositoBanco_aguinaldo.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>&tipo=2" target="_blank"><small>SIN CUENTA</small></a></li>
                            <li role="presentation"><a role="item" href="planillas/planillasAguinaldos_OVT.php?codigo_planilla=<?=$codigo_planilla;?>&cod_gestion=<?=$cod_gestion;?>" target="_blank"><small>Planilla OVT</small></a></li>
                          </ul>
                        </div>
                        <?php }?>
                      </td>
            
                    </tr>
                  <?php $index++; } 

                  $dbh=null;
                  $stmtAdmnin=null;
                  $stmtAdmninUO=null;
                  $stmtAdmninUOAux=null;
                  $stmtAdmninUOAux2=null;
                  ?>
                </tbody>                                      
              </table>
          </div>
          <div class="card-footer fixed-bottom">
            <a href='<?=$urlGenerarPlanillaAguinaldosPrevia;?>' rel="tooltip" class="btn btn-success">
              Registrar Planilla Del Año En Curso
            </a>                                            
          </div>  
        </div>
      </div>
    </div>
  </div>
  <!--modal procesar-->
  <div class="modal fade" id="modalProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planilla" id="codigo_planilla" value="0">        
          Esta acción Procesará La planilla de Aguinaldos del año en curso. ¿Deseas Continuar?
          <div id="cargaP" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarProceso">Aceptar</button>
          <button type="button" class="btn btn-danger" id="CancelarProceso" data-dismiss="modal" > <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Reprocesar-->
  <div class="modal fade" id="modalreProcesar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaRP" id="codigo_planillaRP" value="0">        
          Esta acción ReProcesará La planilla De Aguinaldos Del Año En Curso. ¿Deseas Continuar?
          <div id="cargaR" style="display:none">
            <h3><b>Por favor espere...</b></h3>
          </div>
        </div>    
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarReProceso" >Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="CancelarReProceso"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <!--modal Cerrra-->
  <div class="modal fade" id="modalCerrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="codigo_planillaCP" id="codigo_planillaCP" value="0">        
          Esta acción Cerrará La planilla De Aguinaldos Del año En Curso. ¿Deseas Continuar?
        </div>       
        <div class="modal-footer">
          <button type="button" class="btn btn-success" id="AceptarCerrar" data-dismiss="modal">Aceptar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#AceptarProceso').click(function(){      
        var cod_planilla=document.getElementById("codigo_planilla").value;      
        ProcesarPlanillaAguinaldos(cod_planilla);
      });
      $('#AceptarReProceso').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaRP").value;      
        ReprocesarPlanillaAguinaldos(cod_planilla);
      });
      $('#AceptarCerrar').click(function(){      
        cod_planilla=document.getElementById("codigo_planillaCP").value;      

        CerrarPlanillaAguinaldos(cod_planilla);
      });
      
    });
  </script>
  