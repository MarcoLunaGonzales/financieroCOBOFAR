<?php
require_once 'conexion.php';
require_once 'styles.php';


// $globalAdmin = $_SESSION["globalAdmin"];
$dbh = new Conexion();

if(isset($_GET['q'])){
  $cod_personal_q=$_GET['q'];
  $admin=$_GET['a'];//indicador de admin
  $cod_area=$_GET['s'];//cod area

  $q=$_GET['q'];
  $a=$_GET['a'];//indicador de admin
  $s=$_GET['s'];//cod area

}else{
  $cod_personal_q=$_SESSION['globalUser'];
  $cod_area=obtenerAreasAdmin_permisos($cod_personal_q);
  if($cod_area<>""){
    $admin=1;//indicador de admin
    $cod_area=$cod_area;//cod area
  }else{
    $admin=0;//indicador de admin
    $cod_area=$_SESSION['globalArea'];//cod area
  }


  $q=0;
  $a=0;//indicador de admin
  $s=0;//cod area
}

$sql="SELECT pp.codigo,pp.cod_personal,pp.cod_tipopermiso,tpp.nombre as nombre_tipopermiso,pp.fecha_inicial,pp.hora_inicial,pp.fecha_final,pp.hora_final,pp.observaciones,pp.cod_estado,epp.nombre as nombre_estado,pp.fecha_evento,pp.dias_permiso,pp.minutos_permiso,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal)as nombre_personal,pp.created_at,a.abreviatura as area
from personal_permisos pp join estados_permisos_personal epp on pp.cod_estado=epp.codigo join tipos_permisos_personal tpp on pp.cod_tipopermiso=tpp.codigo join areas a on pp.cod_area=a.codigo
 where pp.cod_estado=3 and cod_area in ($cod_area) order by pp.created_at";
 // echo "<br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
// $stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('cod_tipopermiso', $cod_tipopermiso);
$stmt->bindColumn('nombre_tipopermiso', $nombre_tipopermiso);
$stmt->bindColumn('fecha_inicial', $fecha_inicial);
$stmt->bindColumn('hora_inicial', $hora_inicial);
$stmt->bindColumn('fecha_final', $fecha_final);
$stmt->bindColumn('hora_final', $hora_final);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);
$stmt->bindColumn('fecha_evento', $fecha_evento); 
$stmt->bindColumn('dias_permiso', $dias_permiso); 
$stmt->bindColumn('minutos_permiso', $minutos_permiso); 
$stmt->bindColumn('nombre_personal', $nombre_personal); 
$stmt->bindColumn('created_at', $created_at); 
$stmt->bindColumn('area', $area);
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-rose card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><b>Autorización de Permisos</b></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator100" class="table table-condensed table-bordered">
                <thead>
                  <tr  class='bg-dark text-white'>
                    <th class="text-left" width="2%">#</th>
                    <th class="text-center" width="5%">Area/Suc.</th>
                    <th class="text-center" width="15%">Personal</th>
                    <th class="text-center" width="15%">Tipo Permiso</th>
                    <th class="text-center" width="5%">Fecha<br>Solicitud</th>
                    <th class="text-center" width="5%">Salida</th>
                    <th class="text-center" width="5%">Retorno</th>
                    <th class="text-center" width="2%">Total Días</th>
                    <th class="text-center" width="2%">Total Min</th>

                    <th class="text-center">Observaciones</th>
                    <th class="text-center"width="10%">Estado</th>
                    <th class="text-center" width="5%">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $index = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      $sw=0;
                      $btn_delete='';
                      $btn_ws='';
                      $estilo="";
                      $label="<span>";
                      $titulo_icono="";
                      switch ($cod_estado) {
                        // case 2://anulado
                        //   $btn_delete='d-none';
                        //   $btn_ws='d-none';
                        //   $estilo="style='background:#e08982;'";
                        //   $label="<span class='badge badge-danger'>";
                        //   break;
                        // case 1://Registrado
                        //   $label="<span class='badge badge-default'>";
                        //   $sw=3;//esta en registrado, enviar a autorizacion
                        //   $titulo_icono="Enviar a Autorización";
                        //   break;
                        case 3://en revision
                          $label="<span class='badge badge-warning'>";
                          $sw=4;//esta en revision, enviar a autorizado
                          $titulo_icono="Autorizar Solicitud de permiso";
                          break;
                      }
                      $datos_devolucion=$codigo."###".$q."###".$a."###".$s;
                      ?>
                    <tr <?=$estilo?> >
                      <td class="text-center"><?=$index;?></td>
                      <td class="text-left"><?=$area?></td>
                      <td class="text-left"><?=$nombre_personal?></td>
                      
                      <td class="text-left"><?=$nombre_tipopermiso?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($created_at));?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($fecha_inicial));?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($fecha_final));?></td>
                      <td class="text-center"><?=$dias_permiso;?></td>
                      <td class="text-center"><?=$minutos_permiso;?></td>
                      <td class="text-center"><?=$observaciones;?></td>
                      <td class="text-center"><?=$label.$nombre_estado;?></span></td>
                      <td class="td-actions">
                        <?php
                        if(isset($_GET['q'])){?>
                          <a href='index.php?opcion=permisosPersonalSaveSW&codigo=<?=$codigo?>&sw=<?=$sw?>&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>&t=1'  rel="tooltip" class="btn btn-info btn-sm <?=$btn_ws?>">
                            <i class="material-icons" title="<?=$titulo_icono?>">check_circle</i>
                          </a>
                        <?php }else{?>
                          <a href='index.php?opcion=permisosPersonalSaveSW&codigo=<?=$codigo?>&sw=<?=$sw?>&t=1'  rel="tooltip" class="btn btn-info btn-sm <?=$btn_ws?>">
                            <i class="material-icons" title="<?=$titulo_icono?>">check_circle</i>
                          </a>
                        <?php }
                        ?>
                          <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud_permiso" onclick="modalDevolverSolicitud_permiso('<?=$datos_devolucion;?>')">
                            <i class="material-icons" title="Rechazar Solicitud de Permiso">dangerous</i>
                          </button>
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
        </div>
          <div class="card-footer fixed-bottom">
            <?php 
            if(isset($_GET['q'])){?>
              <button class="btn btn-danger" onClick="location.href='index.php?opcion=permisosPersonalLista&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>'">Volver</button>
            <?php }else{?>
              <button class="btn btn-danger" onClick="location.href='index.php?opcion=permisosPersonalLista'">Volver</button>
            <?php }
            ?>
          </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalDevolverSolicitud_permiso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c6be7;color:white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><b>Rechazar Solicitud de Permiso</b></h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="codigo" id="codigo" value="0">
        <input type="hidden" name="q" id="q" value="0">
        <input type="hidden" name="a" id="a" value="0">
        <input type="hidden" name="s" id="s" value="0">
        
        <div class="row">
          <label class="col-sm-12 col-form-label" style="color:red;"><small>Escribe cuales son tus observaciones:</small></label>
        </div>
        <div class="row">
          <div class="col-sm-12" style="background-color: #bac5db">
            <div class="form-group" >              
              <textarea type="text" name="observaciones" id="observaciones" class="form-control" required="true"></textarea>
            </div>
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="rechazarSolicitud" name="rechazarSolicitud" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- para la factura manual -->
<script type="text/javascript">
  $(document).ready(function(){    
    $('#rechazarSolicitud').click(function(){      
      var codigo=document.getElementById("codigo").value;
      var q=document.getElementById("q").value;
      var a=document.getElementById("a").value;
      var s=document.getElementById("s").value;
      var observaciones=$('#observaciones').val();
      
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{                
          registrarRechazoSolicitud_permiso(codigo,q,a,s,observaciones);
      }      
    }); 
  });
</script>
