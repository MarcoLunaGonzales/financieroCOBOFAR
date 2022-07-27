<meta charset="utf-8">
<?php
require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION["globalUnidad"];

$string_configuracion=obtenerValorConfiguracion(116);
$array_personal_respo_audi=explode(",", $string_configuracion);
$sw_personal_audi=false;
for ($i=0; $i <count($array_personal_respo_audi) ; $i++) { 
    if($globalUser==$array_personal_respo_audi[$i]){
        $sw_personal_audi=true;
    }
}

// $fecha_actualización=date("d/m/Y h:i:s", strtotime(obtenerFechaActualizacionComercial()));

$fecha_actualización=date('d/m/Y');

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  
                </div>
                <div class="card-body">
                  <h4 class="text-center" style="color:#3b6675;"><b>INGRESOS SUCURSALES</b></h4>
                  <h4 class="text-center" style="color:red;">Fecha Actualización : <?=$fecha_actualización?></h4>
                  <!-- <a href="index.php?opcion=principal_CajaChica_historico" target="_blank" class="btn btn-primary btn-sm float-right" style="background-color:#707B7C;">Ir Al Histórico</a> -->
                  <div class="row div-center text-center">

                     <div class="card text-white mx-auto" style="background-color: #3b6675; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >assignment_late</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=rpt_depositosnodescargados" >
                                <h5 class="card-title" style="color:#ffffff;"><b>REGISTRO DE DEPOSITOS</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Reportes</p>
                              </a>  
                           </div>
                        </div>
                     </div>
                     <div class="card text-white mx-auto" style="background-color: #38797e; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >archive</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=rpt_diferencias_depositos_from" >
                                <h5 class="card-title" style="color:#ffffff;"><b>DIFERENCIAS EN DEPOSITOS</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Reportes</p>
                              </a>  
                           </div>
                        </div>
                     </div>
                     <div class="card text-white mx-auto" style="background-color:#73c6b6; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >assignment_turned_in</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=cierrecaja_from" >
                                <h5 class="card-title" style="color:#ffffff;"><b>GENERACIÓN DE COMPROBANTES</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Ingresos</p>
                              </a>  
                           </div>
                        </div>
                     </div>

                     <div class="card text-white mx-auto" style="background-color: #38a598; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >archive</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=rpt_bajadepositos_from" >
                                <h5 class="card-title" style="color:#ffffff;"><b>PUNTOS DE VENTA</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Reportes</p>
                              </a>  
                           </div>
                        </div>
                     </div>
                     

                     <div class="card text-white mx-auto" style="background-color: #38797e; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >description</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=rpt_facturasgeneradas_comercial_from" >
                                <h5 class="card-title" style="color:#ffffff;"><b>FACTURAS GENERADAS</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Reportes</p>
                              </a>  
                           </div>
                        </div>
                     </div>


                     <?php if($sw_personal_audi){
                        ?>
   
                     <div class="card text-white mx-auto" style="background-color: #3b6675; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >pending_actions</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=auditoria_sucursales_from" >
                                <h5 class="card-title" style="color:#ffffff;"><b>AUDITORÍA</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Traspasos</p>
                              </a>  
                           </div>
                        </div>
                     </div>
                 <?php }?>
                     <div class="card text-white mx-auto" style="background-color: #1f618d; width: 18rem;">
                        <div class="row">
                           <div class="col-md-4">
                              <center><i class="material-icons" style="color: #fff;font-size:60px;" >settings</i></center>
                           </div>
                           <div class="col-sm-8">
                              <a href="?opcion=rpt_configuraciones_comercial" >
                                <h5 class="card-title" style="color:#ffffff;"><b>CONFIGURACIÓN</b></h5>
                                <p class="card-text text-small" style="color:#ffffff">Cuentas de Sucursales</p>
                              </a>  
                           </div>
                        </div>
                     </div>

                     

                   </div>
                </div>
              </div>
                    
            </div>
          </div>  
        </div>
    </div>
