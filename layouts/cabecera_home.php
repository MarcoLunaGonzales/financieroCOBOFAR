<?php 

  $urlListGestionTrabajo="index.php?opcion=listGestionTrabajo";
  $urllistUnidadOrganizacional="index.php?opcion=listUnidadOrganizacional";
  $urmesCurso="index.php?opcion=mesCurso";
  $urmesCurso2="index.php?opcion=mesCurso2"; 
?>
<div class="panel">
<!-- Navbar -->
      <nav class="navbar navbar-expand-sm navbar-transparent navbar-absolute fixed-top">
        <div class="container-fluid" style="background: #212f3d" >
          <div class="navbar-wrapper">
            <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-sm btn-just-icon btn-white btn-fab btn-round">
                <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
              </button>
            </div>
     
          </div>
            <?php 
              $globalNombreGestion=$_SESSION['globalNombreGestion'];
              $globalMes=$_SESSION['globalMes'];
              $globalNombreUnidad=$_SESSION['globalNombreUnidad'];
              $globalNombreArea=$_SESSION['globalNombreArea'];
              $fechaSistema=date("d/m/Y");
              $horaSistema=date("H:i");
            ?>
            <h6 style="color:#FFFFFF;">Gesti&oacute;n Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Gestión de Trabajo" style="color:#FF0000;" href='<?=$urlListGestionTrabajo?>' >[<?=$globalNombreGestion;?>]</a></h4>
            &nbsp;&nbsp;&nbsp;
            <h6 style="color:#FFFFFF;">Mes Trabajo: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Mes de Trabajo" style="color:#FF0000; " href='<?=$urmesCurso2?>' >[<?=$globalMes;?>]</a></h4>&nbsp;&nbsp;&nbsp;
            <h6 style="color:#FFFFFF;">Unidad: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Cambiar Oficina de Trabajo" style="color:#FF0000; " href='<?=$urllistUnidadOrganizacional?>' >[ <?=$globalNombreUnidad;?> ]</a></h4> &nbsp;&nbsp; <h6 style="color:#FFFFFF;">Area: </h6>&nbsp;<h4 class="text-danger font-weight-bold"><a title="Aceptar Solicitud" style="color:#FF0000; " href='#' >[ <?=$globalNombreArea;?> ]</a></h4>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          
          <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

//verificar si hay bonos indefinidos
bonosIndefinidos();

//enviar alertas a correos
//enviarNotificacionesSistema(1);

$fechaActual=date("Y-m-d");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_estadoreferencial from monedas");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_estadoreferencial', $codEstadoRef);
$stmt->bindColumn('codigo', $codigoMon);
$stmt->bindColumn('abreviatura', $abreviaturaMon);
$stmt->bindColumn('nombre', $nombreMon);
$html="";
$contMonedas=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    if($codigoMon!=1){
      $valorTipo=obtenerValorTipoCambio($codigoMon,$fechaActual);
      if($valorTipo==0){
        $html.='<a class="dropdown-item" href="?opcion=tipoDeCambio">No hay valores en '.$nombreMon.'</a>';
        $contMonedas++;
       }
     }
 }

 if($contMonedas==0){
  $html='<label class="dropdown-item">No hay Notificaciones</label>';
 $numeroNot='';  
 }else{
 $numeroNot='<span class="notification">'.$contMonedas.'</span>'; 
 }
 
            if(!isset($_GET['q'])){
 ?>

              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons" style="color:#FFFFFF;">notifications</i>
                  <?=$numeroNot?>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                  <?=$html?>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="#pablo" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons" style="color:#FFFFFF;">person</i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="logout.php">Salir</a>
                </div>
              </li>
              <?php
               } ?>
            </ul>
          </div>
        </div>
      </nav>
<!-- End Navbar -->