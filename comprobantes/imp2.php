<?php
set_time_limit(0);

require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
$monedaBimon=1;
session_start();

if(!isset($_GET['comp'])){
    header("location:list.php");
}else{
    $codigo=$_GET['comp'];
    $moneda=$_GET['mon'];
   
}

$url="location: http://10.10.1.19/financieroCOBOFARv1/comprobantes/imp2.php?comp=".$codigo."&mon=".$moneda;
  header($url); 
?>
