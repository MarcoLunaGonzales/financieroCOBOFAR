<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalNombreGestion"];
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT af.codigo,af.codigoactivo,af.otrodato,DATE_FORMAT(af.fechalta, '%d/%m/%Y')as fechalta, af.cod_depreciaciones, af.cod_tiposbienes,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable2)as nombre_responsable2
from activosfijos af 
where af.cod_estadoactivofijo = 1 and af.tipo_af=2 order by codigo desc limit 100";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('codigoactivo', $codigoactivo);
$stmt->bindColumn('fechalta', $fechalta);
$stmt->bindColumn('otrodato', $activo);
$stmt->bindColumn('nombre_responsable', $nombre_responsable);
$stmt->bindColumn('nombre_responsable2', $nombre_responsable2);
$stmt->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmt->bindColumn('cod_tiposbienes', $cod_tiposbienes);
$stmt->bindColumn('nombre_unidad', $nombreUnidad);
$stmt->bindColumn('nombre_area', $nombreArea);
$stmt->bindColumn('proy_financiacion', $proy_financiacion);
$stmt->bindColumn('contabilizado', $contabilizado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);





// busquena por Oficina
$stmtUO = $dbh->prepare("SELECT codigo, (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,c.cod_unidadorganizacional as codigo_uo
from activosfijos c where c.cod_estadoactivofijo=1 GROUP BY unidad order by unidad");
$stmtUO->execute();
$stmtUO->bindColumn('unidad', $nombreUnidad_x);
$stmtUO->bindColumn('codigo_uo', $codigo_uo);
// busquena por area
$stmtArea = $dbh->prepare("SELECT (select t.nombre from areas t where t.codigo=cod_area)as area,cod_area as codigoArea 
from  activosfijos where cod_estadoactivofijo=1 GROUP BY area ORDER BY area");
$stmtArea->execute();
$stmtArea->bindColumn('area', $nombre_area);
$stmtArea->bindColumn('codigoArea', $codigo_area);
// busquena por rubro
$stmtRubro = $dbh->prepare("SELECT (select t.nombre from depreciaciones t where t.codigo=cod_depreciaciones)as rubro,cod_depreciaciones as codigoRubro from  activosfijos where cod_estadoactivofijo=1 GROUP BY rubro ORDER BY rubro");
$stmtRubro->execute();
$stmtRubro->bindColumn('rubro', $nombre_rubro);
$stmtRubro->bindColumn('codigoRubro', $codigo_rubro);
// busquena por respnsable
$stmtPersonal = $dbh->prepare("SELECT (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable)as personal,cod_responsables_responsable from  activosfijos where cod_estadoactivofijo=1 GROUP BY personal ORDER BY personal");
$stmtPersonal->execute();
$stmtPersonal->bindColumn('personal', $nombre_personal);
$stmtPersonal->bindColumn('cod_responsables_responsable', $codigo_personal);

// busquena por tipoActivo
$stmtProyecto = $dbh->prepare("SELECT (select p.nombre from proyectos_financiacionexterna p where p.codigo=cod_proy_financiacion)as proyecto, cod_proy_financiacion from  activosfijos where cod_estadoactivofijo=1 GROUP BY cod_proy_financiacion ORDER BY proyecto");
$stmtProyecto->execute();
$stmtProyecto->bindColumn('proyecto', $nombre_proyecto);
$stmtProyecto->bindColumn('cod_proy_financiacion', $codigo_proy);
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
            <h4 class="card-title">Lista de Fungibles</h4>
            <h4 align="right">
              <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                <i class="material-icons" title="Buscador Avanzado">search</i>
              </button>                      
            </h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div  id="data_activosFijos">
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
              </div>
            </div>
          </div>
          <div class="card-footer fixed-bottom">
              <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
          <button class="btn btn-danger" onClick="location.href='index.php?opcion=activosfijosLista'">Volver</button>
        </div>
        </div>
      </div>
    </div>  
  </div>
</div>



<!-- Modal busqueda de activos fijos-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador Avanzado Activos Fijos</h4>
      </div>
      <div class="modal-body ">

        <div class="row">
          <div class="form-group col-sm-3">
            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple title="OFICINAS"> 
              <!-- <option value=""></option> -->
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombreUnidad_x;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-3">            
            <select name="areas[]" id="areas" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple title="AREAS"> 
              <!-- <option value=""></option> -->
              <?php while ($rowTC = $stmtArea->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_area;?>"> <?=$nombre_area;?></option>
              <?php }?>
            </select>
          </div>     
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio" value="2021-01-01" title="Fecha Inicio">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin" value="<?=$globalGestion?>-12-31"  title="Fecha Fin">
          </div>
                   
        </div> 

        <div class="row">
          <div class="form-group col-sm-4">
            <select  name="responsable[]" id="responsable" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple title="RESPONSABLE"> 
              <!-- <option value=""></option> -->
              <?php while ($rowPersonal = $stmtPersonal->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_personal;?>"> <?=$nombre_personal;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-4">
            <select name="tipoAlta[]" id="tipoAlta" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple title="TIPO ALTA"> 
              <!-- <option value=""></option> -->
              <option value="NUEVO">NUEVO</option>
              <option value="USADO">USADO</option>
            </select>
          </div>          
                 
        </div> 
        <div class="row">
          <label class="col-sm-3 text-center" style="color:#0040FF;">Descripción</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda" title="Descripción" >
          </div>           
        </div> 
        <div class="row">
          <label class="col-sm-3 text-center" style="color:#0040FF;">Código Fungible</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="codigoBusqueda" id="codigoBusqueda" title="Código Fungible" >
          </div>           
        </div> 

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarActivoFijo_fungible" name="botonBuscarActivoFijo_fungible" onclick="botonBuscarActivoFijo_fungible()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
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

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Dar de Baja Fungible </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_activo_b" id="cod_activo_b" value="0">
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Observaciones</label>
          <div class="col-sm-8">
            <div class="form-group">
              <textarea class="form-control"  name="obs_baja" id="obs_baja"></textarea>
            </div>
          </div>
        </div>
        <div  id="div_contenedor_fecha_fin_e">          
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPC"  data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Cerrar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#EditarPC').click(function(){
      cod_activo_b=document.getElementById("cod_activo_b").value;
      obs_baja=$('#obs_baja').val();
      save_obs_AF_baja(cod_activo_b,obs_baja,2);
    });
  });
</script>
