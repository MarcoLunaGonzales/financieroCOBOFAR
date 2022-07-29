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

$sql="SELECT h.codigo,a.nombre,h.cod_area,h.cod_asignacion,ha.descripcion,h.fecha_inicio,h.fecha_fin,
h.ingreso_1,h.salida_1,h.ingreso_2,h.salida_2,h.ingreso_3,h.salida_3,h.ingreso_4,h.salida_4
,h.activo,(SELECT CONCAT(primer_nombre) from personal where codigo=h.created_by) as creador 
FROM horarios_areas h join areas a on a.codigo=h.cod_area 
join horarios_asignaciontipo ha on ha.codigo=h.cod_asignacion
where h.cod_estadoreferencial=1
order by a.nombre,h.cod_asignacion,h.activo;";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('nombre', $nombreX);
$stmt->bindColumn('cod_area', $cod_areaX);
$stmt->bindColumn('cod_asignacion', $cod_asignacionX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('fecha_inicio', $fecha_inicioX);
$stmt->bindColumn('fecha_fin', $fecha_finX);
$stmt->bindColumn('ingreso_1', $ingreso_1X);
$stmt->bindColumn('salida_1', $salida_1X);
$stmt->bindColumn('ingreso_2', $ingreso_2X);
$stmt->bindColumn('salida_2', $salida_2X);
$stmt->bindColumn('ingreso_3', $ingreso_3X);
$stmt->bindColumn('salida_3', $salida_3X);
$stmt->bindColumn('ingreso_4', $ingreso_4X);
$stmt->bindColumn('salida_4', $salida_4X);
$stmt->bindColumn('activo', $activoX);
$stmt->bindColumn('creador', $creadorX);

?>
<script type="text/javascript">
  function nuevoHorario(){
    $("#modalNuevoHorario").modal("show");
  }
  function asignarHorarioGestion(){
    var modal_area=$("#modal_area").val();
    var modal_tipoasignacion=$("#modal_tipoasignacion").val();
    var modal_tipohorario=$("#modal_tipohorario").val();    
    var modal_fecha_inicio=$("#modal_fecha_inicio").val();
    var modal_fecha_fin=$("#modal_fecha_fin").val();

    var errorHora=0;
    var existeTurnos=0;
    $("#modal_tipohorario option:selected").each(function(){
      existeTurnos++;
      if($("#ingreso_"+$(this).attr('value')).val()==""||$("#salida_"+$(this).attr('value')).val()==""||($("#ingreso_"+$(this).attr('value')).val()>=$("#salida_"+$(this).attr('value')).val()&&$(this).attr('value')!=3)){
        errorHora++;  
        $("#ingreso_"+$(this).attr('value')).attr("style","color:red"); 
        $("#salida_"+$(this).attr('value')).attr("style","color:red");      
      }else{
         $("#ingreso_"+$(this).attr('value')).attr("style","color:#000"); 
         $("#salida_"+$(this).attr('value')).attr("style","color:#000"); 
      }      
    });
    if(existeTurnos==0){
      if($("#ingreso_4").val()==""||$("#salida_4").val()==""||$("#ingreso_4").val()>=$("#salida_4").val()){
        errorHora++;
        $("#ingreso_4").attr("style","color:red"); 
        $("#salida_4").attr("style","color:red");
      }else{
        $("#ingreso_4").attr("style","color:#000"); 
        $("#salida_4").attr("style","color:#000"); 
      }
      
    }

    if(modal_fecha_inicio==""||modal_area=="0"||modal_tipoasignacion=="0"||modal_fecha_fin==""){
      //||modal_tipohorario.length==0 horario continuo
      Swal.fire("Informativo","Debe ingresar los datos del formulario!","info");
    }else{
      if(errorHora>0){
        Swal.fire("Informativo","Verifique el rango de Horas","info");
      }else{
        var parametros={"area":modal_area,"tipo_asignacion":modal_tipoasignacion,"tipo_horario":modal_tipohorario,"fecha_inicio":modal_fecha_inicio,"fecha_fin":modal_fecha_fin,
          "ingreso_1":$("#ingreso_1").val(),"salida_1":$("#salida_1").val(),
          "ingreso_2":$("#ingreso_2").val(),"salida_2":$("#salida_2").val(),
          "ingreso_3":$("#ingreso_3").val(),"salida_3":$("#salida_3").val(),
          "ingreso_4":$("#ingreso_4").val(),"salida_4":$("#salida_4").val()
        };
        $.ajax({
              type: "GET",
              dataType: 'html',
              url: "asistencia/asignacion_horarios_save.php",
              data: parametros,
              success:  function (resp) {
                var r=resp.split("#####");
                if(r[1]==0){
                  window.location.href='?opcion=rpt_asignacion_horarios_from';    
                }else{
                  Swal.fire("Error",r[2],"error");   
                }                
              }
        });  
      }
            
    }
   //$("#modalNuevoHorario").modal("show"); 
  }

  function mostrarHorarioTurno(){

    $("#modal_tipohorario option").each(function(){      
      $(".clase_"+$(this).attr('value')).each(function(){
        if(!$(this).hasClass("d-none")){
          $(this).addClass("d-none");
        }
      });
    });
    var seleccionados=0;
    $("#modal_tipohorario option:selected").each(function(){
      seleccionados++;      
      $(".clase_"+$(this).attr('value')).each(function(){
        if($(this).hasClass("d-none")){
          $(this).removeClass("d-none");
        }
      })
    });

    if(seleccionados>0){
      $(".clase_4").each(function(){
        if(!$(this).hasClass("d-none")){
          $(this).addClass("d-none");
        }
      })
    }else{
      $(".clase_4").each(function(){
        if($(this).hasClass("d-none")){
          $(this).removeClass("d-none");
        }
      })
    }
  }
</script>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-info card-header-icon">
            <div class="card-icon" style="background:#69C401 !important;color:white;">
              <i class="material-icons">more_time</i>
            </div>
            <h4 class="card-title">Asignación de Horarios</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed small table-bordered" id="tablePaginatorHead">
                  <thead>
                    <tr class="bg-success" style="background: #AD15D3 !important;color:white;">
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center" colspan="2">CONTINUO</td>                      
                      <td class="text-center" colspan="2" style="background: #3BB913;">TURNO MAÑANA</td>                      
                      <td class="text-center" colspan="2" style="background: #3BB913;">TURNO TARDE</td>                      
                      <td class="text-center" colspan="2" style="background: #3BB913;">TURNO NOCHE</td>                      
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                      <td class="text-center"></td>
                    </tr>
                    <tr class="bg-success" style="background: #AD15D3 !important;color:white;">
                      <td class="text-center">AREA</td>
                      <td class="text-center">ASIGNACIÓN</td>
                      <td class="text-center">FECHA INICIO</td>
                      <td class="text-center">FECHA FIN</td>                      
                      <td class="text-center">INGRESO</td>
                      <td class="text-center">SALIDA</td>                      
                      <td class="text-center" style="background: #3BB913;">INGRESO</td>
                      <td class="text-center" style="background: #3BB913;">SALIDA</td>                      
                      <td class="text-center" style="background: #3BB913;">INGRESO</td>
                      <td class="text-center" style="background: #3BB913;">SALIDA</td>                      
                      <td class="text-center" style="background: #3BB913;">INGRESO</td>
                      <td class="text-center" style="background: #3BB913;">SALIDA</td>                                            
                      <td class="text-center">ACTIVO</td>
                      <td class="text-center">CREADO POR</td>
                      <td class="text-center">OPCION</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      $estadoActivo='<a href="asistencia/asignacion_horarios_activar.php?codigo='.$codigoX.'&e='.$activoX.'"><i class="material-icons" style="color:#A9A9A9;font-size:35px;">toggle_off</i></a>';
                      if($activoX==1){
                        $estadoActivo='<a href="asistencia/asignacion_horarios_activar.php?codigo='.$codigoX.'&e='.$activoX.'"><i class="material-icons" style="color:#B12BC1;font-size:35px;">toggle_on</i></a>';
                      } 
                      if($ingreso_1X==""){$ingreso_1X="-";}if($salida_1X==""){$salida_1X="-";}
                      if($ingreso_2X==""){$ingreso_2X="-";}if($salida_2X==""){$salida_2X="-";}
                      if($ingreso_3X==""){$ingreso_3X="-";}if($salida_3X==""){$salida_3X="-";}
                      if($ingreso_4X==""){$ingreso_4X="-";}if($salida_4X==""){$salida_4X="-";}                        
                     ?>
                      <tr>
                          <td class="text-left"><?=$nombreX;?></td>
                          <td class="text-left"><?=$descripcionX;?></td>
                          <td class="text-center"><?=$fecha_inicioX;?></td>
                          <td class="text-center"><?=$fecha_finX?></span></td>
                          <td class="text-center"><?=$ingreso_4X?></span></td>
                          <td class="text-center"><?=$salida_4X?></span></td> 
                          <td class="text-center"><?=$ingreso_1X?></span></td>
                          <td class="text-center"><?=$salida_1X?></span></td> 
                          <td class="text-center"><?=$ingreso_2X?></span></td>
                          <td class="text-center"><?=$salida_2X?></span></td> 
                          <td class="text-center"><?=$ingreso_3X?></span></td>
                          <td class="text-center"><?=$salida_3X?></span></td>  
                          <td class="text-center"><?=$estadoActivo;?></td>
                          <td class="text-center"><?=$creadorX;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($activoX==0){
                            ?> <a href="asistencia/asignacion_horarios_delete.php?codigo=<?=$codigoX?>&e=0" > 
                                  <i class="material-icons" title="Eliminar" style="color:red">delete</i>
                                </a><?php
                            }
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
          <a class="btn btn-info text-white btn-round btn-fab" style="background:#69C401 !important;color:white;" href="#" onClick="nuevoHorario();return false;"><i class="material-icons">add</i></a>
          <a class="btn btn-default text-white btn-round" href="?opcion=rpt_asignacion_horarios_des">Deshabilitados</a>
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
      <div class="card-header card-header-info card-header-text">
        <div class="card-text" style="background:#69C401 !important;color:white;">
          <h5>Asignacion de Horario</h5> 
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <label class="col-sm-1 col-form-label">Area</label>
          <div class="col-sm-3">
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
          <label class="col-sm-2 col-form-label">Tipo Asignación</label>
          <div class="col-sm-2">
            <div class="form-group">                  
              <select name="modal_tipoasignacion" id="modal_tipoasignacion" class="selectpicker form-control form-control-sm" data-style="btn btn-primary">
                <option  value="0" selected disabled>--SELECCIONE--</option>
                 <?php
                      $sql="SELECT a.codigo,a.descripcion from horarios_asignaciontipo a";
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
          <label class="col-sm-2 col-form-label">Turno(s)</label>
          <div class="col-sm-2">
            <div class="form-group">                  
              <select name="modal_tipohorario[]" id="modal_tipohorario" class="selectpicker form-control form-control-sm" multiple data-style="btn btn-primary" onchange="mostrarHorarioTurno();return false;" data-title="HORARIO CONTINUO">                
                 <?php
                      $sql="SELECT a.codigo,a.descripcion from horarios_tipo a where a.codigo<>4";
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
        <center>
        <div class="row col-sm-10">
          <table class="table table-condensed table-bordered">
            <tr class="bg-warning" style="background:#69C401 !important;color:white;">
              <td colspan="2" class="d-none clase_1">TURNO MAÑANA</td>
              <td colspan="2" class="d-none clase_2">TURNO TARDE</td>
              <td colspan="2" class="d-none clase_3">TURNO NOCHE</td>
              <td colspan="2" style="background:#AD15D3 !important;color:white;" class="clase_4">HORARIO CONTINUO</td>
            </tr>
            <tr class="bg-warning" style="background:#69C401 !important;color:white;">
              <td class="d-none clase_1">INGRESO</td>
              <td class="d-none clase_1">SALIDA</td>
              <td class="d-none clase_2">INGRESO</td>
              <td class="d-none clase_2">SALIDA</td>
              <td class="d-none clase_3">INGRESO</td>
              <td class="d-none clase_3">SALIDA</td>
              <td style="background:#AD15D3 !important;color:white;" class="clase_4">INGRESO</td>
              <td style="background:#AD15D3 !important;color:white;" class="clase_4">SALIDA</td>
            </tr>
            <tr>
              <td class="d-none clase_1"><input class="form-control" type="time" name="ingreso_1" id="ingreso_1" required="true" value="08:30"/></td>
              <td class="d-none clase_1"><input class="form-control" type="time" name="salida_1" id="salida_1" required="true" value="08:30"/></td>
              <td class="d-none clase_2"><input class="form-control" type="time" name="ingreso_2" id="ingreso_2" required="true" value="08:30"/></td>
              <td class="d-none clase_2"><input class="form-control" type="time" name="salida_2" id="salida_2" required="true" value="08:30"/></td>
              <td class="d-none clase_3"><input class="form-control" type="time" name="ingreso_3" id="ingreso_3" required="true" value="08:30"/></td>
              <td class="d-none clase_3"><input class="form-control" type="time" name="salida_3" id="salida_3" required="true" value="08:30"/></td>
              <td class="clase_4"><input class="form-control" type="time" name="ingreso_4" id="ingreso_4" required="true" value="08:30"/></td>
              <td class="clase_4"><input class="form-control" type="time" name="salida_4" id="salida_4" required="true" value="08:30"/></td>
            </tr>
          </table>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Fecha Inicio</label>
          <div class="col-sm-3">
          <div class="form-group">
            <input class="form-control" type="date" name="modal_fecha_inicio" id="modal_fecha_inicio" required="true"/>
          </div>
          </div>
          <label class="col-sm-2 col-form-label">Fecha Fin</label>
          <div class="col-sm-3">
          <div class="form-group">
            <input class="form-control" type="date" name="modal_fecha_fin" id="modal_fecha_fin" required="true"/>
          </div>
          </div>
        </div>
          </center>



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
