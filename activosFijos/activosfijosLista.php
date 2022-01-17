<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT af.codigo,af.codigoactivo,af.activo,DATE_FORMAT(af.fechalta, '%d/%m/%Y')as fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,(select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable2)as nombre_responsable2
from activosfijos af, depreciaciones d, tiposbienes tb 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_estadoactivofijo = 1 and tipo_af=1 order by codigo desc limit 100";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
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
            <h4 class="card-title"><?=$moduleNamePlural6?></h4>
            <h4 align="right">

              <button rel="tooltip" title="Fungibles" class="btn btn-info btn-round btn-fab btn-sm" onClick="location.href='<?=$urlList_fungibles;?>'">
                  <i class="material-icons" >list</i>
                </button>                  
              <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                <i class="material-icons" title="Buscador Avanzado">search</i>
              </button>                      
            </h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
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
                                <!-- <button rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlDardeBaja;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons text-danger" >flight_land</i>Dar de Baja AF
                                </button>  -->
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
              </div>
            </div>
          </div>
        </div>
        <?php
        if($globalAdmin==1){
        ?>
				<div class="card-footer fixed-bottom">
              <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
          <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_activosfijos;?>&codigo=0'">Registrar</button>
        </div>
        <?php
        }
        ?>
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
            <label class="col-sm-3 text-center" style="color:#0040FF;">Oficina</label> 
            <label class="col-sm-3 text-center" style="color:#0040FF;">Area</label>
            <label class="col-sm-6 text-center" style="color:#0040FF;">Fechas</label>                  
            
        </div> 
        <div class="row">
          <div class="form-group col-sm-3">
            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombreUnidad_x;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-3">            
            <select name="areas[]" id="areas" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowTC = $stmtArea->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_area;?>"> <?=$nombre_area;?></option>
              <?php }?>
            </select>
          </div>     
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio" value="<?=$globalGestion?>-01-01" min="<?=$globalGestion?>-01-01" max="<?=$globalGestion?>-12-31">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin" value="<?=$globalGestion?>-12-31" min="<?=$globalGestion?>-01-01" max="<?=$globalGestion?>-12-31"  >
          </div>
                   
        </div> 
        
        <div class="row">
            <label class="col-sm-4 text-center" style="color:#0040FF;">Respontable</label> 
            <label class="col-sm-4 text-center" style="color:#0040FF;">Tipo Alta</label>                  
            <label class="col-sm-4 text-center" style="color:#0040FF;">Rubro</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-4">
            <select  name="responsable[]" id="responsable" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowPersonal = $stmtPersonal->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_personal;?>"> <?=$nombre_personal;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-4">
            <select name="tipoAlta[]" id="tipoAlta" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <option value="NUEVO">NUEVO</option>
              <option value="USADO">USADO</option>
            </select>
          </div>          
          <div class="form-group col-sm-4">            
            <select name="rubro[]" id="rubro" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowTC = $stmtRubro->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_rubro;?>"> <?=$nombre_rubro;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        <div class="row">
          <label class="col-sm-3 text-center" style="color:#0040FF;">Descripción</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda"  >
          </div>           
        </div> 
        <div class="row">
          <label class="col-sm-3 text-center" style="color:#0040FF;">Código Activo</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="codigoBusqueda" id="codigoBusqueda"  >
          </div>           
        </div> 

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarActivoFijo" name="botonBuscarActivoFijo" onclick="botonBuscarActivoFijo()">Buscar</button>
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
        <h4 class="modal-title" id="myModalLabel">Dar de Baja Activo Fijo </h4>
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
      save_obs_AF_baja(cod_activo_b,obs_baja,1);
    });
  });
</script>
