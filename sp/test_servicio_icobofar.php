<?php
require_once '../conexion.php';
require_once '../functions.php';

// $direccion=obtenerValorConfiguracion(42);//direccion des servicio web
$sIde = "ifinanciero";
$sKey = "ce94a8dabdf0b112eafa27a5aa475751";  

//verificar login
// $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
//         "accion"=>"VerificarUser", 
//         "u"=>"ismael.sullcamani", //
//         "p"=>"109494"); //1565
// $parametros=json_encode($parametros);
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL,"http://localhost:8090/icobofar/wsfarm/ws_login.php"); //PRUEBA
// // curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
// curl_setopt($ch, CURLOPT_POST, TRUE);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $remote_server_output = curl_exec ($ch);
// curl_close ($ch);
// header('Content-type: application/json');   
// print_r($remote_server_output); 

//verificar datos activo fijo
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
        "accion"=>"verificar_fichaactivo", 
        "k"=>"32", //
        "qr"=>"02-22-1001"); //1565
$parametros=json_encode($parametros);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://localhost:8090/icobofar/wsfarm/ws_operaciones.php"); //PRUEBA
// curl_setopt($ch, CURLOPT_URL,$direccion."capacitacion/ws-inscribiralumno.php");
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$remote_server_output = curl_exec ($ch);
curl_close ($ch);
header('Content-type: application/json');   
print_r($remote_server_output); 



?>