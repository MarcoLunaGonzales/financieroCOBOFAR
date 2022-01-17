<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require 'assets/phpqrcode/qrlib.php';


$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$codigo_af=$codigo;
$globalAdmin=$_SESSION["globalAdmin"];
//asignaciones
$query2 = "SELECT af.codigo,af.codigoactivo,af.cod_depreciaciones,af.cod_tiposbienes,af.cod_responsables_responsable,af.cod_responsables_responsable2,af.cod_unidadorganizacional,af.cod_area,af.otrodato from activosfijos af where af.codigo=$codigo_af";
//echo "<br><br><br>".$query2;
$statement2 = $dbh->query($query2);
//unidad
$queryUO = "SELECT * from unidades_organizacionales order by 2";
$statementUO = $dbh->query($queryUO);
//unidad
$queryAREA = "SELECT * from areas order by 2";
$statementArea = $dbh->query($queryAREA);
$responsable='';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveTransfer;?>" method="post"  enctype="multipart/form-data">
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons"><?=$iconCard;?></i>
                    </div>
                    <h4 class="card-title">Transferencia De Activos Fijos</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                <th>CodAF</th>
                                <th>Nombre</th>
                                <th>QR</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                                <th>Responsable1</th>
                                <th>Responsable2</th>
                                <th>Oficina</th>
                              </tr>
                          </thead>
                          <tbody>
                          <?php $index=1;
                              while ($row = $statement2->fetch()) { 
                                $cod_activosfijos=$row["codigo"];
                                $CodigoAlterno=$row["codigoactivo"];
                                $datos_afs=obtenerdatos_af_asignacion($cod_activosfijos);

                                if($datos_afs!=""){
                                  $array_datosa=explode("###",$datos_afs);
                                  $fechaasignacion=$array_datosa[0];
                                  $estadobien_asig=$array_datosa[1];
                                  
                                }else{
                                  $fechaasignacion="";
                                  $estadobien_asig="<span style='padding:1;'' class='badge badge-danger'>Recepcion pendiente</span>";
                                }

                                $nombre_personal=namePersonal($row["cod_responsables_responsable"]);
                                $nombre_personal2=namePersonal($row["cod_responsables_responsable2"]);
                                $nombre_uo=abrevUnidad($row["cod_unidadorganizacional"]);
                                $nombre_area=abrevArea($row["cod_area"]);
                                $nombreActivo=$row["otrodato"];
                                
                                  
                                }?>
                             <tr>
                                <td><?=$CodigoAlterno;?></td>
                                <td><small><?=$nombreActivo;?></small></td>
                                <td>
                                  <?php
                                  $fileName=obtenerQR_activosfijos_rpt($codigo_af);
                                  echo '<img src="'.$fileName.'"/>';
                                  ?>
                                </td>
                                
                                <td><?=$fechaasignacion;?></td>
                                <td><?=$estadobien_asig;?></td>
                                <td><?=$nombre_personal;?></td>
                                <td><?=$nombre_personal2;?></td>
                                <td><?=$nombre_uo;?>/<?=$nombre_area;?></td>
                              </tr>
                          </tbody>
                      </table>
                    </div>
                  </div><!--card body-->
                </div> 

                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title">Transferir A:</h4>
                    </div>
                  </div>
                  <div class="card-body ">
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Código Activo</label>
                      <div class="col-sm-4">
                          <div class="form-group">
                              <input type="hidden" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigo_af;?>"/>
                              <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoalternoAF" id="codigoalternoAF" required="true"  value="<?=$CodigoAlterno;?>"/>
                          </div>
                      </div>
                    </div>
    
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Oficina</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_uo" name="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxPersonalUbicacionTrasfer(this);" data-show-subtext="true" data-live-search="true" required="true">
                            <option value=""></option>
                            <?php while ($row = $statementUO->fetch()){ ?>
                              <option  value="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                            <?php } ?> 
                            </select>
                        </div>
                      </div>
                    </div><!--fin campo unidad-->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Area</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <div id="div_contenedor_area">
                          </div>
                        </div>
                      </div>
                    </div><!--fin campo area -->
                    
                    <div id="div_personal_UO">
                          
                    </div>
                  
                </div>
                <div class="card-footer fixed-bottom">
                    <button type="submit" class="<?=$buttonNormal;?>">guardar</button>
                    <a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>"> <-- Volver </a>
                </div>
              </form>
            </div>

        </div>  
    </div>
</div>

