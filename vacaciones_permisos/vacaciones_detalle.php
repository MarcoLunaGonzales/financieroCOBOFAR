<?php
require_once 'conexion.php';
require_once 'styles.php';



$cod_personal=$_GET['codigo'];

$ing_planilla=$_GET['ing_planilla'];
$fecha_actual=$_GET['fecha_actual'];
$anios_antiguedad=$_GET['anios_antiguedad'];


$date1 = new DateTime($ing_planilla);
$date2 = new DateTime($fecha_actual);
$diff = $date1->diff($date2);    
$diferencia_anios=$diff->y;

$nombre_personal=namePersonal($cod_personal);

$dbh = new Conexion();
$stmtEscalas = $dbh->prepare("SELECT anios_inicio,anios_final,dias_vacacion from escalas_vacaciones where cod_estadoreferencial=1");
$stmtEscalas->execute();
$stmtEscalas->bindColumn('anios_inicio', $anios_inicio);
$stmtEscalas->bindColumn('anios_final', $anios_final);
$stmtEscalas->bindColumn('dias_vacacion', $dias_vacacion);  
$i=0;
while ($rowEscalas = $stmtEscalas->fetch(PDO::FETCH_ASSOC))
{
  $array_escalas[$i]=$anios_inicio.",".$anios_final.",".$dias_vacacion;
  $i++;
} 
// echo "<br><br><br>";
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?= $colorCard; ?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title"><b>Información Vacaciones Personal</b></h4>
            <h4 class="card-title"><center><b>Nombre Completo : </b><?=$nombre_personal?></center></h4>
            <h4 class="card-title"><center><b>Fecha Ingreso : </b><?=date('d/m/Y',strtotime($ing_planilla))?> - <b> Antiguedad : </b><?=$diferencia_anios?> Años</center></h4>
            
          </div>
          <div class="card-body">
            <h4 style="color:#212f3d;"><b><i>Días de vacación disponible</i></b></h4>
            <div class="table-responsive">
              <table id="#" class="table table-bordered table-condensed table-striped  table-sm table-secondary">
                <thead>
                  <tr class='bg-dark text-white'>
                    <th class="text-center">Gestión</th>
                    <th class="text-center">Acumulados</th>
                    <th class="text-center">Utilizados</th>
                    <th class="text-center">Disponible</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acción</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                      $fechainicio=$ing_planilla;
                      $fechainicio_x=$fechainicio;
                      $total_dias_vacacion=0;
                      $total_dias_vacacion_uzadas=0;
                      $total_dias_vacacion_saldo=0;
                      $diferencia_mes_sobrante=0;
                      $diferencia_dias_sobrante=0;
                      while($fechainicio<=$fecha_actual){
                        $date1 = new DateTime($fechainicio_x);
                        $date2 = new DateTime($fechainicio);
                        $diff = $date1->diff($date2);    
                        $diferencia_anios=$diff->y;
                        for ($i=0; $i < count($array_escalas); $i++) { 
                           $datos=explode(',', $array_escalas[$i]);
                           $anios_inicio=$datos[0];
                           $anios_final=$datos[1];
                           $dias_vacacion=$datos[2];

                           if($anios_inicio<=$diferencia_anios and $diferencia_anios<$anios_final){
                            
                            // $gestion=date('Y', strtotime($fechainicio."- 1 year"));
                            $gestion=date('Y', strtotime($fechainicio));
                            $dias_utilizadas=obtenerDiasVacacionUzadas($cod_personal,$gestion);
                            $saldo=$dias_vacacion-$dias_utilizadas;
                            if($saldo<=0){
                              $estado='<span class="badge badge-danger ">No disponible';
                            }else{
                              $estado='<span class="badge badge-success">disponible';
                            }
                            ?>
                            <tr>
                              <td class="text-center"><?=date('d/m/Y', strtotime($fechainicio."- 1 year"));?> - <?=date('d/m/Y', strtotime($fechainicio));?>  <b>( <?=$gestion?>)</b></td>
                              <td class="text-center"><?=$dias_vacacion;?></td>
                              <td class="text-center"><?=$dias_utilizadas;?></td>
                              <td class="text-center"><?=$saldo;?></td>
                              <td class="text-center"><?=$estado;?></span></td>
                              <td class="td-actions">
                                <?php if($saldo>0){
                                  $datosModal=$cod_personal."/".$gestion."/".$saldo."/".$ing_planilla."/".$fecha_actual."/".$nombre_personal;
                                  ?>
                                  <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalAgregarC" onclick="agregaformVacaciones('<?=$datosModal;?>')">
                                    <i class="material-icons" title="Solicitar Vacaciones">add</i>
                                 </button>
                                  <?php
                                }
                                ?>
                              </td>
                            </tr>

                              <?php
                              $total_dias_vacacion += $dias_vacacion;
                              $total_dias_vacacion_uzadas+=$dias_utilizadas;
                              $total_dias_vacacion_saldo+=$saldo;
                              break;
                              
                           }
                        }

                        $fechainicio=date('Y-m-d',strtotime($fechainicio.'+1 year'));  
                      }

                      // if($fechainicio!=$ing_planilla){
                      //   $fechainicio=date('Y-m-d',strtotime($fechainicio.'-1 year'));
                      // }
                      // $date1 = new DateTime($fechainicio);
                      // $date2 = new DateTime($fecha_actual);
                      // $diff = $date1->diff($date2);    
                      // $diferencia_mes_sobrante=$diff->m;
                      // $diferencia_dias_sobrante=$diff->d;
                  ?>
                  <tr class='bg-dark text-white'>
                    <td class="text-center">TOTAL</td>
                    <td class="text-center"><?=$total_dias_vacacion?></td>
                    <td class="text-center"><?=$total_dias_vacacion_uzadas;?></td>
                    <td class="text-center"><?=$total_dias_vacacion_saldo;?></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer ml-auto mr-auto ">
            
            <a href="index.php?opcion=vacacionesPersonalLista" class="<?=$buttonCancel;?>"> <-- Volver </a>
          </div>
        </div>
       
      </div>
    </div>
  </div>
</div>



<!-- Modal agregar -->
<div class="modal fade" id="modalAgregarC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #212f3d;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background: #212f3d; color:white;"><b>Vacación Solicitada</b></h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personal_modal" id="codigo_personal_modal" value="0">
        <input type="hidden" name="ing_planilla" id="ing_planilla" value="0">
        <input type="hidden" name="fecha_actual" id="fecha_actual" value="0">
        <input type="hidden" name="saldo_modal" id="saldo_modal" value="0">
        <input type="hidden" name="gestion_modal" id="gestion_modal" value="0">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
                <input style="background: white;color:blue;text-align: center;font: 15px;" type="text" class='form-control' id="datos_cabecera" name="datos_cabecera" readonly="true" value="">
            </div>
          </div>

        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Total Días (*)</label>
          <div class="col-sm-3">
            <div class="form-group">
                <input  type='number' style="color: green;" class='form-control'  id='dias_vacacion'  name='dias_vacacion' min="5" value="<?=$saldo?>" max="<?=$saldo?>" required>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Inicio (*)</label>
          <div class="col-sm-3">
            <div class="form-group">
              <input  type='date' class='form-control'  id='fecha_inicio_modal'  name='fecha_inicio_modal' required>
            </div>
          </div>
          <label class="col-sm-3 col-form-label">Finalización (*)</label>
          <div class="col-sm-3">
            <div class="form-group">
              <input  type='date' class='form-control'  id='fecha_final_modal'  name='fecha_final_modal' required>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Comentarios</label>
          <div class="col-sm-9">
            <div class="form-group">
              <INPUT  type='text' class='form-control'  id='observaciones_modal' name='observaciones_modal' required>
            </div>
          </div>
        </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarPC" name="registrarPC" data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">  Cerrar </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarPC').click(function(){    
      codigo_personal_modal=document.getElementById("codigo_personal_modal").value;
      gestion_modal=document.getElementById("gestion_modal").value;

      ing_planilla=document.getElementById("ing_planilla").value;
      fecha_actual=document.getElementById("fecha_actual").value;
      saldo_modal=document.getElementById("saldo_modal").value;

      dias_vacacion=$('#dias_vacacion').val();
      fecha_inicio_modal=$('#fecha_inicio_modal').val();
      console.log(fecha_inicio_modal);
      fecha_final_modal=$('#fecha_final_modal').val();
      observaciones_modal=$('#observaciones_modal').val();
      if(gestion_modal==""){
        Swal.fire('ERROR!','Gestion No Encontrada. :(','error'); 
      }else{
         // alert(saldo_modal+"--"+dias_vacacion);
        if(dias_vacacion=="" || dias_vacacion==0 || dias_vacacion<5 ){//|| dias_vacacion>saldo_modal
          Swal.fire('ERROR!','Días Vacación no Permitido. :( ','error'); 
        }else{
          if(fecha_inicio_modal=="" || fecha_final_modal==""){
            Swal.fire('ERROR!','Fechas No Admitidas ','error'); 
          }else{
            RegistrarVacacionesPersonal(codigo_personal_modal,gestion_modal,dias_vacacion,fecha_inicio_modal,fecha_final_modal,observaciones_modal,ing_planilla,fecha_actual);    
          }
        }
      }
      //
    });
  });
</script>