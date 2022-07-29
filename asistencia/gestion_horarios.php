<?php
require_once 'conexion.php';
//require_once 'asistencia/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT h.codigo,h.descripcion,h.fecha_inicio,h.fecha_fin,h.activo,h.cod_estadoreferencial,(SELECT CONCAT(primer_nombre) from personal where codigo=h.created_by) as creador from horarios h where h.cod_estadoreferencial=1 order by h.created_at desc;";

$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('creador', $creadorX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('fecha_inicio', $fecha_inicioX);
$stmt->bindColumn('fecha_fin', $fecha_finX);

?>
<script type="text/javascript">
  function nuevoHorario(){
    $("#modalNuevoHorario").modal("show");
  }
  function guardarHorarioGestion(){
    var descripcion=$("#modal_descripcion").val();    
    var modal_inicio=$("#modal_inicio").val();
    var modal_fin=$("#modal_fin").val();

    if(descripcion==""||modal_inicio==""||modal_fin==""){
      Swal.fire("Informativo","Debe ingresar los datos del formulario!","info");
    }else{
      var parametros={"descripcion":descripcion,"inicio":modal_inicio,"salida":modal_fin};
      $.ajax({
            type: "GET",
            dataType: 'html',
            url: "asistencia/gestion_horarios_save.php",
            data: parametros,
            success:  function (resp) {
              window.location.href='?opcion=rpt_gestion_horarios_from';    
            }
      });  
    }
   //$("#modalNuevoHorario").modal("show"); 
  }
</script>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-rose card-header-icon">
            <div class="card-icon">
              <i class="material-icons">timer</i>
            </div>
            <h4 class="card-title">Gestión de Horarios</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">                
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>
                      <th class="text-center">DESCRIPCIÓN</th>
                      <th class="text-center">FECHA INICIO</th>
                      <th class="text-center">FECHA FIN</th>
                      <th class="text-center">CREADO POR</th>
                      <th class="text-center">OPCIONES</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {              
                     ?>
                      <tr>
                          <td width="5%" class="text-left small"><?=$index;?></td>
                          <td class="text-left small"><?=$descripcionX;?></td>
                          <td width="15%" class="text-center small"><?=date("d/m/Y",strtotime($fecha_inicioX));?></td>
                          <td width="15%" class="text-center small"><?=date("d/m/Y",strtotime($fecha_finX))?></span></td>  
                          <td width="15%" class="text-left small"><?=$creadorX;?></td>
                          <td class="td-actions text-right">
                            <a href="index.php?opcion=rpt_asignacion_horarios_from&codigo=<?=$codigoX?>" class="btn btn-sm btn-fab btn-warning"> 
                                  <i class="material-icons" title="Detalles">playlist_add</i>
                            </a>
                            <a href="asistencia/gestion_horarios_delete.php?codigo=<?=$codigoX?>&e=0" target="_blank" > 
                                  <i class="material-icons" title="Eliminar" style="color:red">delete</i>
                                </a>
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
          <a class="btn btn-rose text-white btn-round btn-fab" href="#" onClick="nuevoHorario();return false;"><i class="material-icons">add</i></a>
          <a class="btn btn-default text-white btn-round" href="?opcion=rpt_gestion_horarios_des">Deshabilitados</a>
          <a class="btn btn-default text-white btn-round" style="background:#7CC6A8 !important;" href="?opcion=asistenciaPersonal_main">Volver al Menú</a>

        </div>
        <?php
        }
        ?>
      </div>
    </div>  
  </div>
</div>



<div class="modal fade" id="modalNuevoHorario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-rose card-header-text">
        <div class="card-text">
          <h5>Nuevo Horario</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <label class="col-sm-2 col-form-label">Descripción</label>
          <div class="col-sm-7">
          <div class="form-group">
            <input class="form-control" type="text" name="modal_descripcion" id="modal_descripcion" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Ej: OFICINA CENTRAL, LUN-VIE"/>
          </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label">Ingreso</label>
          <div class="col-sm-4">
          <div class="form-group">
            <input class="form-control" type="date" name="modal_inicio" id="modal_inicio" required="true" value="08:30"/>
          </div>
          </div>
          <label class="col-sm-2 col-form-label">Salida</label>
          <div class="col-sm-4">
          <div class="form-group">
            <input class="form-control" type="date" name="modal_fin" id="modal_fin" required="true" value="17:30"/>
          </div>
          </div>
        </div>


        <div class="form-group float-right">
            <button type="button" class="btn btn-warning btn-round" onclick="guardarHorarioGestion();return false;">REGISTRAR HORARIO</button>
        </div>         
      </div>
    </div>
  </div>
</div>
<!-- edit -->


<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos...</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>
