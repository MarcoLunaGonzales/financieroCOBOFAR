<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$sIde = "bolfincobo";
$sKey = "rrf656nb2396k6g6x44434h56jzx5g6";

// $direccion=obtenerValorConfiguracion(56);//direccion del servicio web ifinanciero
$direccion="localhost:8090/financieroCOBOFAR/wsifin/";

// $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
//     "accion"=>"listPlanillasRetroactivos", 
//     "codPersonal"=>32);
// $parametros=json_encode($parametros);
// // abrimos la sesiรณn cURL
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL,$direccion."ws_list_planillas.php");
// // indicamos el tipo de peticiรณn: POST
// curl_setopt($ch, CURLOPT_POST, TRUE);
// // definimos cada uno de los parรกmetros
// curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
// // recibimos la respuesta y la guardamos en una variable
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $remote_server_output = curl_exec ($ch);
// curl_close ($ch);

// $respuesta=json_decode($remote_server_output);
// // imprimir en formato JSON
// header('Content-type: application/json');   
// print_r($remote_server_output);  

//*******pdf base 64

$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, 
    "accion"=>"ObtenerBoletaRetroactivo", 
    "codPersonal"=>32,"codPlanilla"=>4,"codGestion"=>3585);
$parametros=json_encode($parametros);
// abrimos la sesiรณn cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$direccion."ws_obtener_boletas.php");
// indicamos el tipo de peticion: POST
curl_setopt($ch, CURLOPT_POST, TRUE);
// definimos cada uno de los parรกmetros
curl_setopt($ch, CURLOPT_POSTFIELDS, $parametros);
// recibimos la respuesta y la guardamos en una variable
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$remote_server_output = curl_exec ($ch);
curl_close ($ch);

$respuesta=json_decode($remote_server_output);
// imprimir en formato JSON
header('Content-type: application/json');   
print_r($remote_server_output); 










?>