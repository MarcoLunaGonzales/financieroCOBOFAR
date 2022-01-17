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

$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];

$responsable=$_GET['responsable'];
$tipoAlta=$_GET['tipoAlta'];
$areas=$_GET['areas'];

$glosa=$_GET['glosa'];
$codigo=$_GET['codigo'];

// $unidadOrgString=implode(",", $cod_uo);



$sql="SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, af.contabilizado,af.cod_comprobante,af.cod_depreciaciones, af.cod_tiposbienes,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,(select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable2)as nombre_responsable2
from activosfijos af
where af.cod_estadoactivofijo = 1 and af.tipo_af=2";  

if($cod_uo!=""){
  $sql.=" and af.cod_unidadorganizacional in ($cod_uo)";
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

$stmt->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmt->bindColumn('cod_tiposbienes', $cod_tiposbienes);

$stmt->bindColumn('nombre_unidad', $nombreUnidad);
$stmt->bindColumn('nombre_area', $nombreArea);
$stmt->bindColumn('proy_financiacion', $proy_financiacion);
$stmt->bindColumn('contabilizado', $contabilizado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);
?>
<table class="table table-condensed " id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th></th>
                      <th><small><b>Codigo</b></small></th>
                      <th><small><b>Of/Area</b></small></th>
                      <th><small><b>Fungible</b></small></th>
                      <th><small><b>F. Alta</b></small></th>
                      <th><small><b>Rubro/Bien</b></small></th>
                      <th><small><b>Respo1</b></small></th>
                      <th><small><b>Respo2</b></small></th>
                      <th><small><b>Acc/Eventos</b></small></th>   
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      $dep_nombre=trim(abrevDepreciacion($cod_depreciaciones)," - ");
                      //$tb_tipo=abrevTipoBienes($cod_tiposbienes);
                      $tb_tipo="";
                      ?>
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
                          <td class="text-center small"><small><?=$nombreUnidad;?>/<?=$nombreArea;?></small></td>
                          <td class="text-left small" ><small><?=$activo;?></small></td>
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
                                  <i class="material-icons text-success" ><?=$iconEdit;?></i>Editar F
                                </a>
                                <button rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons text-danger" ><?=$iconDelete;?></i>Borrar F
                                </button>
                                <a href='<?=$urlEditTransfer;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info" >transfer_within_a_station</i>Transferir F
                                </a> 
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalEditar" onclick="agregaformActivoFijo_baja('<?=$codigo;?>')">
                                  <i class="material-icons text-danger"  title="Editar">flight_land</i>Dar de Baja F
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
                                  <i class="material-icons text-warning"  style="color:black">extension</i>Accesorios F
                                </a>
                                <a href='<?=$urlafEventos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info"  style="color:black">event</i>Eventos F
                                </a>
                                <?php } ?>
                              </div>
                            </div>
                          </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>