<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../conexion_comercial_oficial.php';
require_once '../conexion_comercial2.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
session_start();
//$dbh = new Conexion();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$codMesActiva=$_SESSION['globalMes'];
$globalGestion=$_SESSION["globalGestion"];
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");

$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));

if((int)$globalNombreGestion<(int)$anioActual){
  $fechaActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
  $fechaActualModal=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion;
  $fechaActualVer=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaActual=date("Y-m-d");
      $fechaActualModal=date("d/m/Y");
      $fechaActualVer=$fechaActual;
  }else{
    $fechaActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;
      $fechaActualModal=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion; 
      $fechaActualVer=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo;    
  } 
}


$glosa_ingreso=$_POST['glosa_ingreso'];
$items=$_POST['contador_items'];

$codEmpresa=1;
$codAnio=$_SESSION['globalNombreGestion'];
$codMoneda=1;
$codEstadoComprobante=1;
$tipoComprobante=3;

//echo $fechaActual;
//$fechaActual=$fechaActualModal;


$cod_uo_solicitud=1;
$cod_area_solicitud=522;




$flagSuccess=false;
$tipoComprobante="";$codComprobante=0;
for ($i=1; $i <= (int)$items ; $i++) { 
  if($_POST["diferencia".$i]==0&&$_POST["cod_comprobante".$i]==""){ //crear solo si no hay diferencia
      $cuenta_salida=$_POST["cuenta_salida".$i]; 
      $cuenta_ingreso=$_POST["cuenta_ingreso".$i]; 
      $cuenta_salida_aux=$_POST["cuenta_salida_aux".$i]; 
      $cuenta_ingreso_aux=$_POST["cuenta_ingreso_aux".$i]; 

      $area_salida=$cod_area_solicitud; 
      $area_ingreso=$cod_area_solicitud; 
      
      if($_POST["area_salida".$i]>0){
        $area_salida=$_POST["area_salida".$i];
      }
      if($_POST["area_ingreso".$i]>0){
        $area_ingreso=$_POST["area_ingreso".$i];
      }


      $haber=$_POST["haber".$i]; 
      $debe=$_POST["debe".$i];   
      $cod_traspasos=$_POST["cod_traspasos".$i];    
      $nombreTraspaso=$_POST["nombre_traspasos".$i];    
      $concepto_contabilizacion=$glosa_ingreso." ".$nombreTraspaso;

      if($tipoComprobante!=$_POST["tipo_comprobante".$i]){
      }      

        $numeroComprobante =numeroCorrelativoComprobante($globalGestion,$cod_uo_solicitud,$tipoComprobante,$codMesActiva);
        $codComprobante=obtenerCodigoComprobante();
        $flagSuccess=insertarCabeceraComprobante($codComprobante,$codEmpresa,$cod_uo_solicitud,$codAnio,$codMoneda,$codEstadoComprobante,$tipoComprobante,$fechaActual,$numeroComprobante,$concepto_contabilizacion,$globalUser);        
        $tipoComprobante=$_POST["tipo_comprobante".$i];
      if($flagSuccess!=false){
          $sql="UPDATE salida_almacenes set costeo_cod_comprobante='$codComprobante' where cod_salida_almacenes in ($cod_traspasos);";
          mysqli_query($enlaceCon,$sql);    
          //mysqli_query($dbh,$sql);
      }
      //detalle del comprobante
      $insDet=insertarDetalleComprobante($codComprobante,$cuenta_ingreso,$cuenta_ingreso_aux,$cod_uo_solicitud,$area_ingreso,$debe,0,$concepto_contabilizacion,1);
      $insDet=insertarDetalleComprobante($codComprobante,$cuenta_salida,$cuenta_salida_aux,$cod_uo_solicitud,$area_salida,0,$haber,$concepto_contabilizacion,2);    
  }
}
mysqli_close($enlaceCon);
//mysqli_close($dbh);

showAlertSuccessError($flagSuccess,"../".$urlList);  



?>
