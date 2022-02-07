<?php
//include("functionsGeneral.php");

require_once 'conexion.php';

$globalUserX=$_SESSION['globalUser'];
//echo $globalUserX;
$globalPerfilX=$_SESSION['globalPerfil'];
$globalNameUserX=$_SESSION['globalNameUser'];
$globalNombreUnidadX=$_SESSION['globalNombreUnidad'];
$globalNombreAreaX=$_SESSION['globalNombreArea'];
// $obj=$_SESSION['globalMenuJson'];
$menuModulo=$_SESSION['modulo'];

$nombreModulo="";

switch ($menuModulo) {
  case 1:
   $nombreModulo="RRHH";
   // $estiloMenu="rojo";
   $estiloMenu="celestebebe";
   
  break;
  case 2:
  $nombreModulo="Activos Fijos";
   $estiloMenu="amarillo";
  break;
  case 3:
  $nombreModulo="Contabilidad";
   $estiloMenu="celeste";
  break;
  case 4:
  $nombreModulo="Presupuestos / Solicitudes";
   $estiloMenu="verde";
  break;
}


if($menuModulo==0){
?>
 <script>window.location.href="index.php";</script>
<?php
}
?>

<div class="sidebar" data-color="purple" data-background-color="<?=$estiloMenu?>" data-image="assets/img/scz.jpg">
  <div class="logo">
    <a href="" class="simple-text logo-mini">
      <img src="assets/img/icono_pastilla.png" width="30" />
    </a>
    <a href="index.php" class="simple-text logo-normal">
      COBOFAR
    </a>
  </div>
  <div class="sidebar-wrapper">
    <div class="user">
      <div class="photo">
        <img src="assets/img/faces/persona1.png" />
      </div>
      <div class="user-info">
        <a data-toggle="collapse" href="#collapseExample" class="username">
          <span>
            <?=$globalNameUserX;?>
            <!--b class="caret"></b-->
          </span>
        </a>
      </div>
    </div>
    <ul class="nav">
<?php


  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT codigo,nombre,url,icono,txtNuevaVentana from acceso_modulos_sistema_url where cod_submodulo=$menuModulo and padre=1
order by ordenar");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['codigo'];
    $actividadX=$row['nombre'];
    $paginaX=$row['url'];
    $iconoX=$row['icono'];
    $txtNuevaVentanaX=$row['txtNuevaVentana'];

  // $id=$objDet->id;
  // $actividad=$objDet->actividad;
  // $actividad=ucwords(strtolower($actividad));
  // $pagina=$objDet->pagina;
  // $icono=$objDet->icono;
  // $moduloWS=$objDet->modulo;
  //echo $id." ".$actividad." ".$pagina."<br>";
?>
    <li class="nav-item ">
      <a class="nav-link" data-toggle="collapse" href="#<?=$paginaX;?>">
        <i class="material-icons"><?=$iconoX;?></i>
        <p> <?=$actividadX;?>
          <b class="caret"></b>
        </p>
      </a>
      <div class="collapse" id="<?=$paginaX;?>">
        <ul class="nav"><!--hasta aqui el menu 1ra parte-->
  <?php 
  $sql="select DISTINCT a.codigo,a.nombre,a.url,a.icono,a.txtNuevaVentana 
from acceso_modulos_sistema_url a join acceso_modulos_sistema_perfiles_url b on a.codigo=b.cod_url
where a.cod_padre=$codigoX and b.cod_perfil in (select perfil from personal_datosadicionales where cod_personal=$globalUserX)
order by a.ordenar";
  // echo $sql;
  $stmt_submenu = $dbh->prepare($sql);
  $stmt_submenu->execute();
  while ($row_submenu = $stmt_submenu->fetch(PDO::FETCH_ASSOC)) {
    $codigoSubMenu=$row_submenu['codigo'];
    $nombreSubMenu=$row_submenu['nombre'];
    $paginaSubMenu=$row_submenu['url'];
    $iconoSubMenu=$row_submenu['icono'];
    $txtNuevaVentana=$row_submenu['txtNuevaVentana'];
    if($codigoSubMenu==105){

      ?>
      <li class="nav-item ">
        <a class="nav-link" href="<?=$paginaSubMenu;?>?cod_personal=<?=$globalUserX?>" <?=$txtNuevaVentana;?> >
          <span class="sidebar-mini"> <?=$iconoSubMenu;?> </span>
          <span class="sidebar-normal"> <?=$nombreSubMenu; ?> </span>
        </a>
      </li>
      <?php
    }else{
      ?>
      <li class="nav-item ">
        <a class="nav-link" href="<?=$paginaSubMenu;?>" <?=$txtNuevaVentana;?> >
          <span class="sidebar-mini"> <?=$iconoSubMenu;?> </span>
          <span class="sidebar-normal"> <?=$nombreSubMenu; ?> </span>
        </a>
      </li>
      <?php

    }
      
  } ?>

    <!--PARTE FINAL DE CADA MENU-->  
    </ul>
  </div>
</li>
<?php
  
}
?>

        </ul>
      </div>
    </div>
