<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT codigo,fecha,nro_correlativo,glosa,cod_estado,cod_comprobante from ingresos_almacen order by nro_correlativo desc limit 50";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('nro_correlativo', $nro_correlativo);
$stmt->bindColumn('glosa', $glosa);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?=$iconCard;?></i>
            </div>
            <h4 class="card-title"><?=$moduleNamePlural?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center">Correlativo</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Glosa</th>
                      <th class="text-center">STA ING.</th>
                      <th class="text-center">STA CMPT</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      // echo $cod_comprobante."**";
                      if($cod_comprobante>0){
                        $cod_estadocomprobante=obtenerEstadoComprobante($cod_comprobante);
                        switch ($cod_estadocomprobante) {
                          case 1:
                            $estado_comprobante="Registrado";
                            $label_comprobante='<span class="badge badge-default">'; 
                            break;
                          case 2:
                            $estado_comprobante="Anulado";
                            $label_comprobante='<span class="badge badge-danger">'; 
                          break;
                          case 3:
                            $estado_comprobante="Aprobado";
                            $label_comprobante='<span class="badge badge-success">'; 
                          break;
                          default:
                            $estado_comprobante="NO ENCONTRADO";
                            $label_comprobante='<span class="badge badge-dark">'; 
                          break;
                        }  
                      }else{
                        $estado_comprobante="";
                        $label_comprobante='<span class="badge badge-dark">'; 
                      }
                      
                      if($cod_estado==1){
                        if($cod_comprobante>0){
                          $estado="Contabilizado";
                          $label='<span class="badge badge-info">'; 
                        }else{
                          $estado="Activo";  
                          $label='<span class="badge badge-success">'; 
                        }
                      }else{
                        $estado="Anulado";
                        $label='<span badge badge-danger>'; 
                      }
                      
                     ?>
                      <tr>
                          <td width="10%" class="text-center small"><?=$nro_correlativo;?></td>
                          <td width="10%" class="text-center small"><?=$fecha;?></td>
                          <td class="text-left small"><?=$glosa;?></td>
                          <td width="10%" class="text-left small"><?=$label.$estado?></span></td>
                          <td width="10%" class="text-left small"><?=$label_comprobante.$estado_comprobante?></span></td>
                          <td width="10%" class="td-actions text-right">
                            <?php
                              if($cod_comprobante>0 ){?>                                    
                                <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" target="_blank">
                                   <i class="material-icons" title="Imprimir Comprobante" style="color:red">print</i>
                               </a> 
                              <?php }elseif($cod_estado==1){ ?>
                                <a href="<?=$urlcontabilizar;?>?codigo=<?=$codigo;?>" target="_blank" > 
                                  <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                                </a>
                              <?php }
                            ?>
                          </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php
        if($globalAdmin==1){
        ?>
        <div class="card-footer fixed-bottom">
           <!-- <a class="<?=$buttonNormal;?>" target="_blank" onClick="location.href='<?=$urlfiltro;?>'">Registrar</a>  -->
          <a class="btn btn-success" target="_blank" onClick="redireccionarIngresosAlmacenAnt('<?=$globalUser?>')">Registrar</a>
          <a class="btn btn-warning" target="blank" onClick="pendientes_ingreso_almacen_ant()">Facturas Pendientes</a>
        </div>
        <?php
        }
        ?>
      </div>
    </div>  
  </div>
</div>


<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos...</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>
