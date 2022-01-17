<?php

require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$cod_uo=$_GET['cod_uo'];
$rubro=$_GET['rubro'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];

$responsable=$_GET['responsable'];
$tipoAlta=$_GET['tipoAlta'];
$areas=$_GET['areas'];

$glosa=$_GET['glosa'];
$codigo=$_GET['codigo'];

// $unidadOrgString=implode(",", $cod_uo);



$sql="SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable2)as nombre_responsable2
from activosfijos af, depreciaciones d, tiposbienes tb 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_estadoactivofijo = 1 and af.tipo_af=1";  

if($cod_uo!=""){
  $sql.=" and af.cod_unidadorganizacional in ($cod_uo)";
}
if($rubro!=""){
  $sql.=" and af.cod_tiposbienes in ($rubro)";  
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and af.fechalta BETWEEN '$fechaI' and '$fechaF'"; 
}
if($responsable!=""){
  $sql.=" and af.cod_responsables_responsable in ($responsable)";
}
if($tipoAlta!=""){
  $sql.=" and af.tipoalta in ('$tipoAlta')";
}
if($areas!=""){
  $sql.=" and af.cod_area in ($areas)";
}
if($glosa!=""){
  $sql.=" and af.activo like '%$glosa%'";
}
if($codigo!=""){
  $sql.=" and af.codigoactivo like '$codigo'";
}
// $sql.=" order by c.fecha desc, c.numero desc;";
//echo $sql; 



$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('codigoactivo', $codigoactivo);
$stmt->bindColumn('fechalta', $fechalta);
$stmt->bindColumn('activo', $activo);
$stmt->bindColumn('nombre_responsable', $nombre_responsable);
$stmt->bindColumn('nombre_responsable2', $nombre_responsable2);

$stmt->bindColumn('dep_nombre', $dep_nombre);
$stmt->bindColumn('tb_tipo', $tb_tipo);

$stmt->bindColumn('nombre_unidad', $nombreUnidad);
$stmt->bindColumn('nombre_area', $nombreArea);
$stmt->bindColumn('proy_financiacion', $proy_financiacion);
$stmt->bindColumn('contabilizado', $contabilizado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);
?>
<table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center"></th>
                      <th class="text-center">Codigo</th>
                      <th class="text-center">Of/Area</th>
                      <th class="text-center">Nombre Activo</th>
                      <th class="text-center">F. Alta</th>
                      <th class="text-center">Rubro/Bien</th>
                      <th class="text-center">Respo. 1</th>
                      <th class="text-center">Respo. 2</th>
                      <th class="text-center">Acc/Eventos</th>   
                      <th class="text-center"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                        <td  class="td-actions text-right">    
                            <a href='<?=$printDepreciacion1;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Ficha Activo Fijo" style="color:black">print</i>
                            </a>
                            <a href='<?=$printEtiqueta_af;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                              <i class="material-icons" title="Reimpresión Etiqueta" style="color:black">print</i>
                            </a>
                          </td>
                          <td class="text-center small"><small><?=$codigoactivo;?></small></td>
                          <td class="text-center small"><small><?=$nombreUnidad;?>-<?=$nombreArea;?></small></td>
                          <td class="text-left small" ><small><?=substr($activo, 0, 50);;?></small></td>
                          <td class="text-center small"><small><?=$fechalta;?></small></td>
                          <td class="text-left small"><small><?=$dep_nombre;?>/<?=$tb_tipo;?></small></td>
                          <td class="text-left small"><small><?=strtoupper($nombre_responsable)?></small></td>
                          <td class="text-left small"><small><?=strtoupper($nombre_responsable2)?></small></td>
                          <!-- <td class="text-left small"><?=$proy_financiacion;?></td> -->
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Primarios">
                                 <i class="material-icons" >list</i><small><small></small></small>
                              </button>
                              <div class="dropdown-menu" >
                                <?php if($globalAdmin==1){ ?>
                                <a href='<?=$urlEdit6;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-success" ><?=$iconEdit;?></i>Editar AF
                                </a>
                                <button rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons text-danger" ><?=$iconDelete;?></i>Borrar AF
                                </button>
                                <a href='<?=$urlEditTransfer;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info" >transfer_within_a_station</i>Transferir AF
                                </a> 
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalEditar" onclick="agregaformActivoFijo_baja('<?=$codigo;?>')">
                                  <i class="material-icons text-danger"  title="Editar">flight_land</i>Dar de Baja AF
                                </button><?php } ?>
                              </div>
                            </div>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Secundarios">
                                 <i class="material-icons" >list</i><small><small></small></small>
                              </button>
                              <div class="dropdown-menu" >
                              <?php if($globalAdmin==1){ ?>
                                <a href='<?=$urlafAccesorios;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-warning"  style="color:black">extension</i>Accesorios AF
                                </a>
                                <a href='<?=$urlafEventos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info"  style="color:black">event</i>Eventos AF
                                </a>
                                <a href='<?=$urlRevaluarAF;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-warning" style="color:black">trending_up</i>Reevaluar AF
                                </a><?php } ?>
                              </div>
                            </div>
                          </td>
                          <td class="text-center">
                            <?php
                            //si es mayor a cero, ya se genero el comprobante.
                              if($cod_comprobante>0){?>                                    
                                <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" target="_blank">
                                       <i class="material-icons" title="Imprimir Comporbante" style="color:red">print</i>
                                   </a> 
                              <?php }elseif($contabilizado==0){ ?>
                                <a href="<?=$urlprint_contabilizacion_cajachica;?>?cod_cajachica=<?=$cod_cajachica;?>" target="_blank" > 
                                  <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                                </a>
                              <?php }
                            ?>
                          </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>