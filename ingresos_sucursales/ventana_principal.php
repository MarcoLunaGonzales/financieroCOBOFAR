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

$fecha_actualización=date("d/m/Y h:i:s", strtotime(obtenerFechaActualizacionComercial()));

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                </div>
                <div class="card-body">
                  <h4 class="text-center" style="color:blue;">INGRESOS SUCURSALES</h4>
                  <h4 class="text-center" style="color:red;">Última Actualización : <?=$fecha_actualización?><br>Se recomienda trabajar hasta un día antes :)</h4>
                  <!-- <a href="index.php?opcion=principal_CajaChica_historico" target="_blank" class="btn btn-primary btn-sm float-right" style="background-color:#707B7C;">Ir Al Histórico</a> -->
                  <div class="row div-center text-center">

                     <div class="card text-white mx-auto" style="background-color:#d35400; width: 18rem;">
                       <a href="?opcion=cierrecaja_from" >
                          <div class="card-body ">
                             <h5 class="card-title" style="color:#ffffff;">GENERAR COMPROBANTE</h5>
                             <p class="card-text text-small" style="color:#ffffff">***<br>Ingresos Sucursales</p>
                             <i class="material-icons" style="color:#37474f">home_work</i>
                          </div>
                       </a>
                     </div>
                     <div class="card text-white mx-auto" style="background-color: #d98880; width: 18rem;">
                       <a href="?opcion=rpt_bajadepositos_from" >
                          <div class="card-body ">
                             <h5 class="card-title" style="color:#ffffff;">BAJA DE DEPOSITOS</h5>
                             <p class="card-text text-small" style="color:#ffffff">***<br>Reportes</p>
                             <i class="material-icons" style="color:#37474f">home_work</i>
                          </div>
                       </a>
                     </div>

                    <div class="card text-white mx-auto" style="background-color:#CD5C5C; width: 18rem;">
                       <a href="?opcion=rpt_facturasgeneradas_comercial_from" >
                          <div class="card-body ">
                             <h5 class="card-title" style="color:#ffffff;">FACTURAS</h5>
                             <p class="card-text text-small" style="color:#ffffff">***<br>Reportes</p>
                             <i class="material-icons" style="color:#37474f">home_work</i>
                          </div>
                       </a>
                     </div>
                     <?php if($sw_personal_audi){
                        ?>
                     <div class="card text-white mx-auto" style="background-color:#727289; width: 18rem;">
                       <a href="?opcion=auditoria_sucursales_from" >
                          <div class="card-body ">
                             <h5 class="card-title" style="color:#ffffff;">AUDITORÍA</h5>
                             <p class="card-text text-small" style="color:#ffffff">***<br>Traspaso Entre Sucursales</p>
                             <i class="material-icons" style="color:#37474f">pending_actions</i>
                          </div>
                       </a>
                     </div>
                 <?php }?>

                    <div class="card text-white mx-auto" style="background-color:#1f618d; width: 18rem;">
                       <a href="?opcion=rpt_configuraciones_comercial" >
                          <div class="card-body ">
                             <h5 class="card-title" style="color:#ffffff;">CONFIGURACIONES</h5>
                             <p class="card-text text-small" style="color:#ffffff">***<br>Cuentas de Sucursales</p>
                             <i class="material-icons" style="color:#37474f">settings</i>
                          </div>
                       </a>
                     </div>
                   </div>
                </div>
              </div>
                    
            </div>
          </div>  
        </div>
    </div>
