<?php
require_once 'conexion.php';
//require_once 'asistencia/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$codigoHorario=$_GET["codigo"];
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="select a.nombre,h.descripcion,ha.cod_area,ha.cod_horario from horarios_area ha join areas a on a.codigo=ha.cod_area join horarios h on h.codigo=ha.cod_horario order by a.nombre,h.descripcion";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('nombre', $nombreX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('cod_area', $cod_areaX);
$stmt->bindColumn('cod_horario', $cod_horarioX);




?>
<script type="text/javascript">
  function nuevoHorario(){
    $("#modalNuevoHorario").modal("show");
  }
  function asignarHorarioGestion(){    
    var modal_area=$("#modal_area").val();
    var modal_horario=$("#modal_horario").val();    
    if(modal_area=="0"||modal_horario=="0"){
      
      Swal.fire("Informativo","Debe ingresar los datos del formulario!","info");
    }else{
        var parametros={"modal_horario":modal_horario,"modal_area":modal_area
        };
        $.ajax({
              type: "GET",
              dataType: 'html',
              url: "asistencia/asignacion_horarios_areas_save.php",
              data: parametros,
              success:  function (resp) {
                var r=resp.split("#####");
                if(r[1]==0){
                  window.location.href='?opcion=rpt_asignacion_horarios_areas';    
                }else{
                  Swal.fire("Error",r[2],"error");   
                }                
              }
        });  
      
    }
  }

</script>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-info card-header-icon">
            <div class="card-icon" style="background:#F3BC02 !important;color:white;">
              <i class="material-icons">more_time</i>
            </div>
            <h4 class="card-title">Aignación de Horarios</h4> 
          </div>
          <div class="card-body">
            <input type="hidden" id="cod_horario" value="<?=$codigoHorario?>">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed small table-bordered" id="tablePaginatorHead">
                  <thead>
                    <tr class="bg-success" style="background: #00C2B9 !important;color:white;">
                      <td class="text-center">#</td>
                      <td class="text-center">AREA</td>                     
                      <td class="text-center">HORARIO</td>
                      <td class="text-center">OPCION</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                         
                     ?>
                      <tr>
                          <td class="text-left"><?=$index;?></td>
                          <td class="text-left"><?=$nombreX;?></td>
                          <td class="text-center"><?=$descripcionX?></span></td>
                          <td class="td-actions text-right">
                            <a href="#" > 
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
          <a class="btn btn-info text-white btn-round btn-fab" style="background:#F3BC02 !important;color:white;" href="#" onClick="nuevoHorario();return false;"><i class="material-icons">add</i></a>
          <a class="btn btn-default text-white btn-round" style="background:#7CC6A8 !important;" href="?opcion=rpt_gestion_horarios_from">Volver al Menú</a>

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
      <div class="card-header card-header-info card-header-text">
        <div class="card-text" style="background:#F3BC02 !important;color:white;">
          <h5>Asignacion de Horario</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
        
          <label class="col-sm-2 col-form-label">Area</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="modal_area" id="modal_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true">
                <option  value="0" selected disabled>--SELECCIONE--</option>
                 <?php
                      $sql="SELECT codigo,nombre FROM areas WHERE cod_estado=1 and centro_costos=1 order by nombre;";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigo=$rowg['codigo'];
                        $nombre=$rowg['nombre'];
                      ?>
                      <option  value="<?=$codigo;?>"><?=$nombre;?></option>
                      <?php 
                      }
                    ?>              
             </select>
            </div>
          </div>
          <label class="col-sm-2 col-form-label">Horario</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="modal_horario" id="modal_horario" class="selectpicker form-control form-control-sm"  data-style="btn btn-primary" data-live-search="true"> 
              <option  value="0" selected disabled>--SELECCIONE--</option>               
                 <?php
                      $sql="SELECT codigo,descripcion,fecha_inicio,fecha_fin FROM horarios where activo=1 AND cod_estadoreferencial=1;";
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
        <br>



        <div class="form-group float-right">
            <button type="button" class="btn btn-warning btn-round" onclick="asignarHorarioGestion();return false;">ASIGNAR HORARIO</button>
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
