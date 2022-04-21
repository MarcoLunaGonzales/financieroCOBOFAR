<?php

require("../conexion_comercial_oficial.php");

$cod_ciudad=$_POST["cod_ciudad"];
$cod_cuentaBancaria=$_POST["cod_cuentaBancaria"];
$cod_cuentaBancaria2=$_POST["cod_cuentaBancaria2"];



$sql="UPDATE ciudades set cod_plancuenta=$cod_cuentaBancaria,cod_plancuenta2=$cod_cuentaBancaria2 where cod_ciudad=$cod_ciudad";
$sql_inserta=mysqli_query($dbh,$sql);

if($sql_inserta){
	echo 1;
}else{
	echo 2;
}
 

?>
