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
$dbh = new Conexion();

 $stmt = $dbh->prepare("SELECT codigo,nombre,url,icono,txtNuevaVentana from acceso_modulos_sistema_url where cod_submodulo=$menuModulo and padre=1 order by ordenar");
  $stmt->execute();
  $i=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // $codigoX=$row['codigo'];
    // $actividadX=$row['nombre'];
    // $paginaX=$row['url'];
    // $iconoX=$row['icono'];
    // $txtNuevaVentanaX=$row['txtNuevaVentana'];
    $array_submenu['codigo']=$row['codigo'];
    $array_submenu['nombre']=$row['nombre'];
    $array_submenu['url']=$row['url'];
    $array_submenu['icono']=$row['icono'];
    $array_menu[$i]=$array_submenu;
    $i++;
}

$sql="SELECT DISTINCT a.codigo,a.nombre,a.url,a.icono,a.txtNuevaVentana,a.cod_padre
from acceso_modulos_sistema_url a join acceso_modulos_sistema_perfiles_url b on a.codigo=b.cod_url
where b.cod_perfil in ($globalPerfilX) AND a.cod_padre in (SELECT codigo from acceso_modulos_sistema_url where cod_submodulo=$menuModulo and padre=1 order by ordenar)
order by a.ordenar";
 // echo $sql;
$stmt_submenu = $dbh->prepare($sql);
$stmt_submenu->execute();
$array_submenu=[];
while ($row_submenu = $stmt_submenu->fetch(PDO::FETCH_ASSOC)) {
  $codigoSubMenu=$row_submenu['codigo'];
  $nombreSubMenu=$row_submenu['nombre'];
  $paginaSubMenu=$row_submenu['url'];
  $iconoSubMenu=$row_submenu['icono'];
  $txtNuevaVentana=$row_submenu['txtNuevaVentana'];
  $cod_padre=$row_submenu['cod_padre'];

  $array_submenu_det['codigo']=$codigoSubMenu;
  $array_submenu_det['nombre']=$nombreSubMenu;
  $array_submenu_det['url']=$paginaSubMenu;
  $array_submenu_det['icono']=$iconoSubMenu;
  $array_submenu_det['txtNuevaVentana']=$txtNuevaVentana;
  $array_submenu_det['cod_padre']=$cod_padre;

  $array_submenu[$codigoSubMenu]=$array_submenu_det;
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
  
  // $stmt = $dbh->prepare("SELECT codigo,nombre,url,icono,txtNuevaVentana from acceso_modulos_sistema_url where cod_submodulo=$menuModulo and padre=1 order by ordenar");
  // $stmt->execute();
  // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  //   $codigoX=$row['codigo'];
  //   $actividadX=$row['nombre'];
  //   $paginaX=$row['url'];
  //   $iconoX=$row['icono'];
  //   $txtNuevaVentanaX=$row['txtNuevaVentana'];

  foreach ($array_menu as $valuex) 
  {

    $codigoX=$valuex['codigo'];
    $actividadX=$valuex['nombre'];
    $paginaX=$valuex['url'];
    $iconoX=$valuex['icono'];
    $txtNuevaVentanaX="";
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
//   $sql="select DISTINCT a.codigo,a.nombre,a.url,a.icono,a.txtNuevaVentana 
// from acceso_modulos_sistema_url a join acceso_modulos_sistema_perfiles_url b on a.codigo=b.cod_url
// where a.cod_padre=$codigoX and b.cod_perfil in ($globalPerfilX)
// order by a.ordenar";
//   // echo $sql;
//   $stmt_submenu = $dbh->prepare($sql);
//   $stmt_submenu->execute();
//   while ($row_submenu = $stmt_submenu->fetch(PDO::FETCH_ASSOC)) {

  foreach ($array_submenu as $value) 
  {
    $codigoSubMenu=$value['codigo'];
    $nombreSubMenu=$value['nombre'];
    $paginaSubMenu=$value['url'];
    $iconoSubMenu=$value['icono'];
    $txtNuevaVentana=$value['txtNuevaVentana'];
    $cod_padreSubMenu=$value['cod_padre'];
    if($codigoX==$cod_padreSubMenu){
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
