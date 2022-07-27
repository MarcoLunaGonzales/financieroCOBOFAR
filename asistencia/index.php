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
                     <td width="25%"><center><i class="material-icons" style="color: #a9dfbf;font-size:60px;" >pending_actions</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#7dcea0;"><b>Jornada de Trabajo</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess">Asignación</label>
                             </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><button  class="btn btn-sm" onclick="boton_incremento_salarial_main(1)">Ingresar</button></center>
            </div>
         </div>
      </div> 

      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%;">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #fad7a0;font-size:60px;" >settings</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#f8c471;"><b>Procesar Asistencia</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                    <label class="control-label" for="inputSuccess">Sucursales</label>
                                </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><button  class="btn btn-sm" onclick="boton_incremento_salarial_main(3)">Ingresar</button></center>
            </div>
         </div>
      </div>

      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%;">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #fad7a0;font-size:60px;" >settings</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#f8c471;"><b>Procesar Asistencia</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                    <label class="control-label" for="inputSuccess">Of. Central</label>
                                </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><button  class="btn btn-sm" onclick="boton_incremento_salarial_main(3)">Ingresar</button></center>
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
                     <td width="25%"><center><i class="material-icons" style="color: #4f657c;font-size:60px;" >groups</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#4f657c;"><b>Cuadro de Asistencia</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess">Sucursales</label>
                             </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><a type="button" class="btn btn-sm" href="?opcion=asistenciaPersonalListaRRHH">Ingresar</a></center>
            </div>
         </div>
      </div> 

      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%; ">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #f5b7b1 ;font-size:60px;" >assignment_ind</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#f1948a ;"><b>Reporte de Marcación</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                               <label class="control-label" for="inputSuccess">Por Persona</label>
                             </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><a type="button" class="btn btn-sm" href="?opcion=rpt_asistencia_personal_from">Ingresar</a></center>
            </div>
         </div>
      </div> 
      <div class="col-md-4">
         <div class="card text-white mx-auto" style="background-color:white; width: 80%;">
            <div class="card-body">
               <table>
                  <tr>
                     <td width="25%"><center><i class="material-icons" style="color: #d2b4de;font-size:60px;" >assignment</i></center></td>
                     <td>
                        <div class="row">
                           <div class="col-sm-12">
                              <div class="form-group has-primary">
                                 <h5 class="card-title" style="color:#bb8fce;"><b>Reporte de Marcación</b></h5>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                    <label class="control-label" for="inputSuccess">Consolidado</label>
                                </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </table>
               <center><a  class="btn btn-sm" href="?opcion=rpt_asistencia_consolidado_from">Ingresar</a></center>
            </div>
         </div>
      </div>
   </div>

  </div>
</div>
