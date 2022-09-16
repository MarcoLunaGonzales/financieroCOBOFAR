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

$sql="select ha.codigo,CONCAT(a.primer_nombre,' ',a.paterno) as nombres,h.descripcion,ha.cod_persona,ha.cod_horario, 
hd.ingreso_1,hd.salida_1,hd.ingreso_2,hd.salida_2,hd.ingreso_3,hd.salida_3,hd.ingreso_4,hd.salida_4,
(select descripcion from horarios_asignaciontipo where codigo=hd.cod_asignacion) as tipo
from horarios_persona ha join personal a on a.codigo=ha.cod_persona join horarios h on h.codigo=ha.cod_horario 
join horarios_detalle hd on hd.cod_horario=h.codigo where ha.estado=1 order by 2,h.descripcion,hd.cod_asignacion";
//echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('nombres', $nombreX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('cod_persona', $cod_personaX);
$stmt->bindColumn('cod_horario', $cod_horarioX);
$stmt->bindColumn('ingreso_1', $ingreso_1X);
$stmt->bindColumn('salida_1', $salida_1X);
$stmt->bindColumn('ingreso_2', $ingreso_2X);
$stmt->bindColumn('salida_2', $salida_2X);
$stmt->bindColumn('ingreso_3', $ingreso_3X);
$stmt->bindColumn('salida_3', $salida_3X);
$stmt->bindColumn('ingreso_4', $ingreso_4X);
$stmt->bindColumn('salida_4', $salida_4X);
$stmt->bindColumn('tipo', $tipoX);



?>
<script type="text/javascript">
  function mostrarFilaTablaHorario(codigo){   
  var mostrar=0; 
    $(".fila_"+codigo).each(function(){
        if($(this).hasClass("d-none")){
          $(this).removeClass("d-none");
          mostrar++;
        }else{
          $(this).addClass("d-none");
        }
    }); 

    if(mostrar>0){      
      if(!$("#icono_"+codigo).hasClass("text-danger")){
        $("#icono_"+codigo).removeClass("text-success");
        $("#icono_"+codigo).addClass("text-danger");
      }
      $("#icono_"+codigo).html("do_not_disturb_on");      
    }else{
      if(!$("#icono_"+codigo).hasClass("text-success")){
        $("#icono_"+codigo).removeClass("text-danger");
        $("#icono_"+codigo).addClass("text-success");
      }  
      $("#icono_"+codigo).html("add_circle"); 
    }   
  }
  function nuevoHorario(){
    $("#modalNuevoHorario").modal("show");
  }
  function asignarHorarioGestion(){    
    var modal_persona=$("#modal_persona").val();
    var modal_horario=$("#modal_horario").val();    
    if(modal_persona=="0"||modal_horario=="0"){
      
      Swal.fire("Informativo","Debe ingresar los datos del formulario!","info");
    }else{
        var parametros={"modal_horario":modal_horario,"modal_persona":modal_persona
        };
        $.ajax({
              type: "GET",
              dataType: 'html',
              url: "asistencia/asignacion_horarios_personas_save.php",
              data: parametros,
              success:  function (resp) {
                var r=resp.split("#####");
                if(r[1]==0){
                  window.location.href='?opcion=rpt_asignacion_horarios_personas';    
                }else{
                  Swal.fire("Error",r[2],"error");   
                }                
              }
        });  
      
    }
  }

  function verDetalleHorarioAsignado(){
    var parametros={"cod_horario":$("#modal_horario").val()};
        $.ajax({
              type: "GET",
              dataType: 'html',
              url: "asistencia/ajax_asignacion_horarios_detalle.php",
              data: parametros,
              success:  function (resp) {
                $("#detalle_horario").html(resp);             
              }
        });  
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
            <h4 class="card-title">Asignación de Horarios por Persona</h4> 
          </div>
          <div class="card-body">
            <input type="hidden" id="cod_horario" value="<?=$codigoHorario?>">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed small table-bordered" id="tablePaginatorHorarios"><!---->
                  <thead>
                     <tr class="bg-success" style="background: #00C2B9 !important;color:white;">
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center" colspan="2">CONTINUO</td>                      
                      <td class="text-center" colspan="2" style="background: #5C079B">TURNO MAÑANA</td>                      
                      <td class="text-center" colspan="2" style="background: #5C079B">TURNO TARDE</td>                      
                      <td class="text-center" colspan="2" style="background: #5C079B">TURNO NOCHE</td>                      
                      
                      
                      <td class="text-center"></td>
                    </tr>
                    <tr class="bg-success" style="background: #00C2B9 !important;color:white;">
                      <td class="text-center">#</td>
                      <td class="text-center">AREA</td>                     
                      <td class="text-center">HORARIO</td>
                      <td class="text-center">DÍA</td>
                      <td class="text-center">INGRESO</td>
                      <td class="text-center">SALIDA</td>                      
                      <td class="text-center" style="background: #5C079B">INGRESO</td>
                      <td class="text-center" style="background: #5C079B">SALIDA</td>                      
                      <td class="text-center" style="background: #5C079B">INGRESO</td>
                      <td class="text-center" style="background: #5C079B">SALIDA</td>                      
                      <td class="text-center" style="background: #5C079B">INGRESO</td>
                      <td class="text-center" style="background: #5C079B">SALIDA</td>
                      <td class="text-center">OPCION</td>
                    </tr>

                  </thead>
                  <tbody>
                    <?php $index=1;
                    $codSucursal="";
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {   
                      if($ingreso_1X==""){$ingreso_1X="-";}if($salida_1X==""){$salida_1X="-";}
                      if($ingreso_2X==""){$ingreso_2X="-";}if($salida_2X==""){$salida_2X="-";}
                      if($ingreso_3X==""){$ingreso_3X="-";}if($salida_3X==""){$salida_3X="-";}
                      if($ingreso_4X==""){$ingreso_4X="-";}if($salida_4X==""){$salida_4X="-";}                      
                      
                      if($codSucursal!=$cod_personaX){
                          //$claseFila="";
                         ?>
                        <tr>
                          <td class="text-left"><?=$index;?></td>
                          <td class="text-left" onclick="mostrarFilaTablaHorario(<?=$codigoX?>);return false;"><i style="font-size: 18px;" class="material-icons text-success" id="icono_<?=$codigoX?>">add_circle</i>  <?=$nombreX;?></td>
                          <td class="text-center"><?=$descripcionX?></td>
                          <td></td> 
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td></td>  
                          <td class="td-actions text-right">                            
                            <a href="asistencia/asignacion_horarios_personas_delete.php?codigo=<?=$codigoX?>&e=0" > 
                                  <i class="material-icons" title="Eliminar" style="color:red">delete</i>
                                </a>
                          </td>
                      </tr>
                       <?php
                        $codSucursal=$cod_personaX;
                        $index++;
                      }
                     ?>
                      <tr class="d-none fila_<?=$codigoX?>" style="background: #6EFCE6; color:#000;">
                          <td class="text-left"></td>
                          <td class="text-left small"><?=$nombreX;?></td>
                          <td class="text-center"></td>
                          <td class="text-left small"><?=$tipoX?></td>
                          <td class="text-center"><?=$ingreso_4X?></td>
                          <td class="text-center"><?=$salida_4X?></td> 
                          <td class="text-center"><?=$ingreso_1X?></td>
                          <td class="text-center"><?=$salida_1X?></td> 
                          <td class="text-center"><?=$ingreso_2X?></td>
                          <td class="text-center"><?=$salida_2X?></td> 
                          <td class="text-center"><?=$ingreso_3X?></td>
                          <td class="text-center"><?=$salida_3X?></td>  
                          <td class="td-actions text-right">
                           
                          </td>
                      </tr>
                    <?php  } ?>
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
          <a class="btn btn-danger text-white btn-round"  href="?opcion=asistenciaPersonal_main">Volver al Menú</a>                 

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
          <h5>Asignacion de Horario por Persona</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
        
          <label class="col-sm-2 col-form-label">Persona</label>
          <div class="col-sm-4">
            <div class="form-group">                  
              <select name="modal_persona" id="modal_persona" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true">
                <option  value="0" selected disabled>--SELECCIONE--</option>
                 <?php
                      $sql="SELECT codigo,CONCAT(primer_nombre,' ',paterno) AS nombre from personal where cod_estadoreferencial=1 and cod_estadopersonal=1;";
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
              <select name="modal_horario" id="modal_horario" class="selectpicker form-control form-control-sm"  data-style="btn btn-primary" data-live-search="true" onchange="verDetalleHorarioAsignado()"> 
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
        <div class="row" id="detalle_horario"></div>


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
