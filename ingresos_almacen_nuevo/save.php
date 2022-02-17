<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../conexion2.php';
require_once '../conexion_comercial_oficial.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh_cabecera = new Conexion();
$dbh_detalle = new Conexion2();

$p=$_POST['p'];
$globalUser=$p;
if($p==0){
  session_start();
  if(isset($_SESSION["globalUser"]))
  {
    $globalUser=$_SESSION["globalUser"];  
  }else{
    $globalUser=0;
  } 
}
//datos de cabecera
$cantidadItems=$_POST['contador_items'];//total de intems
$glosa_ingreso=$_POST['glosa_ingreso'];//total de intems

$flagSuccess=false;
$cod_ingresoalmacen=0;
while ($cod_ingresoalmacen==0) {
  $nro_correlativo=obtenerCorrelativoingresoAlmacen();
  $cod_estado=1;
  $sqlInsert="INSERT into ingresos_almacen(fecha,nro_correlativo,glosa,cod_estado,created_at,created_by) values(NOW(),$nro_correlativo,'$glosa_ingreso',$cod_estado,NOW(),'$globalUser')";
  $stmtInsert = $dbh_cabecera->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();
  $cod_ingresoalmacen = $dbh_cabecera->lastInsertId();
}

if($cod_ingresoalmacen>0){
  $string_dctos="";
  for ($pro=1; $pro <= $cantidadItems ; $pro++){  
    $ingresos_activado_s=$_POST["ingresos_activado_s".$pro];//codigo estado de cuenta relacionado
    $dcto_ingreso_s=$_POST["dcto_ingreso_s".$pro];
    if($ingresos_activado_s>0){
      $string_dctos.=$dcto_ingreso_s.",";
    }
  }

  $string_dctos=trim($string_dctos,",");
  $sql="SELECT ia.cod_ingreso_almacen,ia.nro_correlativo,ia.cod_proveedor,ia.observaciones,ia.nro_factura_proveedor,ia.created_by,ia.created_date,ia.f_factura_proveedor,ia.con_factura_proveedor,ia.aut_factura_proveedor,ia.monto_factura_proveedor_desc,ia.nit_factura_proveedor,ia.monto_factura_proveedor
                    from ingreso_almacenes ia
                    where ia.cod_tipoingreso=1004 and ia.cod_ingreso_almacen in ($string_dctos) ORDER BY nro_factura_proveedor";
  // echo $sql; 
  $resp=mysqli_query($dbh,$sql);
  while($row=mysqli_fetch_array($resp)){  
    $cod_ingreso_almacen=$row['cod_ingreso_almacen'];
    $cod_proveedor=$row['cod_proveedor'];
    $nro_correlativo=$row['nro_correlativo'];
    $nro_factura_proveedor=$row['nro_factura_proveedor'];
    $f_factura_proveedor=$row['f_factura_proveedor'];
    $con_factura_proveedor=$row['con_factura_proveedor'];
    $aut_factura_proveedor=$row['aut_factura_proveedor'];
    $nit_factura_proveedor=$row['nit_factura_proveedor'];
    $monto_factura_proveedor_desc=$row['monto_factura_proveedor_desc'];
    $monto_factura_proveedor=$row['monto_factura_proveedor'];
    
    $monto_desc=$monto_factura_proveedor-$monto_factura_proveedor_desc;

    // if($monto_factura_proveedor == $monto_factura_proveedor_desc){
    //   $monto_desc=0;
    // }



    // if($monto_desc == $monto_factura_proveedor_desc){
    //   $monto_factura_proveedor_desc=0;
    // }
    // $monto_desc=$monto_factura_proveedor-$monto_factura_proveedor_desc;

    // // $total_venta=$MFACTURA-$DESCTO1-$DESCTO2-$DESCTO3-$DESCTO4;
    // $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
    // $total_venta=number_format($total_venta,2,'.','');
    // // $FECHA=trim($FECHA,' 00:00:00.000');
    // $FECHA1_array=explode(" ", $FECHA1);
    // $FECHA1=$FECHA1_array[0];

    $IDPROVEEDOR_nuevo=codigoProveedorNuevo($cod_proveedor);
    $sqlInsertDet="INSERT INTO ingresos_almacen_detalle(cod_ingresoalmacen, cod_proveedor,factura,fecha_factura,dcto_almacen,nit,autorizacion,codigo_control,monto_factura,desc_total) 
    VALUES ($cod_ingresoalmacen,'$IDPROVEEDOR_nuevo','$nro_factura_proveedor','$f_factura_proveedor','$cod_ingreso_almacen','$nit_factura_proveedor','$aut_factura_proveedor','$con_factura_proveedor',$monto_factura_proveedor_desc,$monto_desc)";
    //echo $sqlInsertDet;
    $stmtInsertDet = $dbh_detalle->prepare($sqlInsertDet);
    $flagSuccess=$stmtInsertDet->execute();
  }
}

showAlertSuccessError($flagSuccess,"../".$urlList);  






?>
