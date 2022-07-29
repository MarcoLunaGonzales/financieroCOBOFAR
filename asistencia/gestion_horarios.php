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

$sql="SELECT h.codigo,t.descripcion as tipo,h.descripcion,h.hora_ingreso,h.hora_salida from horarios h join horarios_tipo t on t.codigo=h.tipo where h.estado=1 order by t.codigo;";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('tipo', $tipoX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('hora_ingreso', $hora_ingresoX);
$stmt->bindColumn('hora_salida', $hora_salidaX);

?>
<script type="text/javascript">
  function nuevoHorario(){
    $("#modalNuevoHorario").modal("show");
  }
  function guardarHorarioGestion(){
    var descripcion=$("#modal_descripcion").val();
    var tipo=$("#modal_tipohorario").val();
    var modal_ingreso=$("#modal_ingreso").val();
    var modal_salida=$("#modal_salida").val();

    if(descripcion==""||tipo=="0"||modal_ingreso==""||modal_salida==""){
      Swal.fire("Informativo","Debe ingresar los datos del formulario!","info");
    }else{
      var parametros={"tipo":tipo,"descripcion":descripcion,"ingreso":modal_ingreso,"salida":modal_salida};
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
                      <th class="text-center">TIPO HORARIO</th>
                      <th class="text-center">DESCRIPCIÓN</th>
                      <th class="text-center">HORA INGRESO</th>
                      <th class="text-center">HORA SALIDA</th>
                      <th class="text-center">OPCIONES</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {              
                     ?>
                      <tr>
                          <td width="15%" class="text-left small"><?=$tipoX;?></td>
                          <td class="text-left small"><?=$descripcionX;?></td>
                          <td width="15%" class="text-center small"><?=$hora_ingresoX;?></td>
                          <td width="15%" class="text-center small"><?=$hora_salidaX?></span></td>  
                          <td width="10%" class="td-actions text-right">
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
          <label class="col-sm-2 col-form-label">Tipo</label>
          <div class="col-sm-7">
            <div class="form-group">                  
              <select name="modal_tipohorario" id="modal_tipohorario" class="selectpicker form-control form-control-sm" data-style="btn btn-primary">
                <option  value="0" selected disabled>--SELECCIONE--</option>
                 <?php
                      $sql="SELECT a.codigo,a.descripcion from horarios_tipo a";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigo=$rowg['codigo'];
                        $nombre=$rowg['descripcion'];
                      ?>
                      <option  value="<?=$codigo;?>"><?=$nombre;?></option>
                      <?php 
                      }
                    ?>              
             </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Ingreso</label>
          <div class="col-sm-4">
          <div class="form-group">
            <input class="form-control" type="time" name="modal_ingreso" id="modal_ingreso" required="true" value="08:30"/>
          </div>
          </div>
          <label class="col-sm-2 col-form-label">Salida</label>
          <div class="col-sm-4">
          <div class="form-group">
            <input class="form-control" type="time" name="modal_salida" id="modal_salida" required="true" value="17:30"/>
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
