<meta charset="utf-8">
<?php
require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION["globalUnidad"];

?>
<div class="content">
  <div class="container-fluid">

   <h2 style="color:#2c3e50;"><b>Control de Asistencia<hr style="background:#3b6675;height:10px;" align="left" width="490px"></b></h2>
   <div class="row">
      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%; ">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #3b7a56;font-size:60px;" >pending_actions</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#3b7a56;"><b>Gestión de Horarios</b></h5>
                              </div>
                           </div>
                        </div>
                    <!--     <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess" style="color:#808b96"></label>
                             </div>
                           </div>
                        </div> -->
                     </td>
                  </tr>
               </table>
               <center>
                  <center>
                     <div class="btn-group">
                        <a  class="btn btn-sm" href="?opcion=rpt_gestion_horarios_from">HORARIOS</a>
                        <a  class="btn btn-sm btn-success" style="background:#3b7a56 !important;" href="?opcion=rpt_asignacion_horarios_areas">ASIGNACIÓN</a>
                     </div>
                     <a  class="btn btn-sm" href="?opcion=rpt_opt_horarios">REPORTE</a>
                  </center>
               </center>
            </div>
         </div>
      </div> 
      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%;">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #dc7633 ;font-size:60px;" >settings</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#dc7633;"><b>Procesar Asistencia</b></h5>
                              </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><a class="btn btn-sm" href="asistencia/procesar_asistencia_personal.php" >Sucursales</a><br>
                  <?php
                  if($globalUser==32){?>


                     <a class="btn btn-sm" href="asistencia/procesar_asistencia_personal_ofcen.php" style="background: #dc7633 !important;">Of. Central</a></center>
                  <?php }
                  ?>
               
            </div>
         </div>
      </div>

      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%; ">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color:#c0392b ;font-size:60px;" >assignment_ind</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#c0392b ;"><b>Reporte de Marcación</b></h5>
                              </div>
                           </div>
                        </div>
                  <!--       <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess" style="color:#808b96">Por Persona</label>
                             </div>
                           </div>
                        </div> -->
                     </td>
                  </tr>
               </table>
               <center><a type="button" class="btn btn-sm" href="?opcion=rpt_asistencia_personal_from">Por Persona</a></center>
               <center><a  class="btn btn-sm" href="?opcion=rpt_asistencia_consolidado_from" style="background:#c0392b !important;">Por Area/Sucursal</a></center>
            </div>
         </div>
      </div>  

       
   </div>

   <div class="row">
      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%;">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color:#1a5276;font-size:60px;" >groups</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color#1a5276;"><b>Reporte Asistencia</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                        <!--    <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess" style="color:#808b96">Sucursales</label>
                             </div>
                           </div>
                        </div> -->
                     </td>
                  </tr>
               </table>
                  <center>
                  <div class="btn-group">
                     <a type="button" class="btn btn-sm" href="?opcion=asistenciaPersonalListaRRHH">Cuadro <BR>Asistencia</a>
                     <!-- <a type="button" class="btn btn-sm" href="?opcion=asistenciaPersonalListaRRHH" style="background:#1a5276 !important;" >Reemplazos<BR>a Suc.</a> -->
                  </div>   
                  </center>
                  <!-- <center>
                     <a type="button" class="btn btn-sm" href="?opcion=asistenciaPersonalListaRRHH">Reemplazos<BR>a Otras Suc.</a>
                  </center> -->
               
            </div>
         </div>
      </div>
          
   </div>

  </div>
</div>
