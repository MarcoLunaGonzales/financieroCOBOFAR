<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../conexion_sql.php'; 
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh_detalle = new Conexion();

$p=$_POST['p'];
$glbalUser=$p;
if($p==0){
  session_start();
  $globalUser=$_SESSION["globalUser"];
}

//datos de cabecera
$cantidadItems=$_POST['contador_items'];//total de intems
$glosa_ingreso=$_POST['glosa_ingreso'];//total de intems

$patron1="[\n|\r|\n\r]";
$glosa_ingreso = preg_replace($patron1, ", ", $glosa_ingreso);//quitamos salto de linea
$glosa_ingreso = str_replace('"', " ", $glosa_ingreso);//quitamos comillas dobles  
$glosa_ingreso = str_replace("'", " ", $glosa_ingreso);//quitamos comillas simples
$glosa_ingreso = str_replace('<', "(", $glosa_ingreso);//quitamos comillas dobles
$glosa_ingreso = str_replace('>', ")", $glosa_ingreso);//quitamos comillas dobles

$nro_correlativo=obtenerCorrelativoingresoAlmacen();
$cod_estado=1;

$sqlInsert="INSERT into ingresos_almacen(fecha,nro_correlativo,glosa,cod_estado,created_at,created_by) values(NOW(),$nro_correlativo,'$glosa_ingreso',$cod_estado,NOW(),$globalUser)";
$stmtInsert = $dbh->prepare($sqlInsert);
$stmtInsert->execute();
$cod_ingresoalmacen = $dbh->lastInsertId();
$string_dctos="";
for ($pro=1; $pro <= $cantidadItems ; $pro++){  
  $ingresos_activado_s=$_POST["ingresos_activado_s".$pro];//codigo estado de cuenta relacionado
  $dcto_ingreso_s=$_POST["dcto_ingreso_s".$pro];
  if($ingresos_activado_s>0){
    $string_dctos.=$dcto_ingreso_s.",";
  }
}

$string_dctos=trim($string_dctos,",");
$server=obtenerValorConfiguracion(104);
$bdname=obtenerValorConfiguracion(105);
$user=obtenerValorConfiguracion(106);
$pass=obtenerValorConfiguracion(107);
$dbh2=ConexionFarma_all($server,$bdname,$user,$pass);
$flagSuccess=false;
$sql_detalle="SELECT DCTO, IDPROVEEDOR, FECHA, GLO, TIPODOC, DOCUM, FECHA1, FECHA2, REFE, REFE1, RUC, MFACTURA,DESCTO1, DESCTO2, DESCTO3, DESCTO4
     FROM dbo.AMAESTRO
     WHERE (TIPO = 'A') and dcto in ($string_dctos) ORDER BY CAST(DOCUM AS INT)";
//echo $sql_detalle;
$stmtDet = $dbh2->prepare($sql_detalle);
//ejecutamos
$stmtDet->execute();
//bindColumn
$stmtDet->bindColumn('DCTO', $DCTO);
$stmtDet->bindColumn('IDPROVEEDOR', $IDPROVEEDOR);
$stmtDet->bindColumn('FECHA', $FECHA);
$stmtDet->bindColumn('GLO', $GLO);
$stmtDet->bindColumn('DOCUM', $DOCUM);
$stmtDet->bindColumn('FECHA1', $FECHA1);
$stmtDet->bindColumn('REFE', $REFE);
$stmtDet->bindColumn('REFE1', $REFE1);
$stmtDet->bindColumn('RUC', $RUC);
$stmtDet->bindColumn('MFACTURA', $MFACTURA);

$stmtDet->bindColumn('DESCTO1', $DESCTO1);
$stmtDet->bindColumn('DESCTO2', $DESCTO2);
$stmtDet->bindColumn('DESCTO3', $DESCTO3);
$stmtDet->bindColumn('DESCTO4', $DESCTO4);

while ($row = $stmtDet->fetch(PDO::FETCH_BOUND)) {

  // $total_venta=$MFACTURA-$DESCTO1-$DESCTO2-$DESCTO3-$DESCTO4;
  $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
  $total_venta=number_format($total_venta,2,'.','');

  // $FECHA=trim($FECHA,' 00:00:00.000');
  $FECHA1_array=explode(" ", $FECHA1);
  $FECHA1=$FECHA1_array[0];
  
  $IDPROVEEDOR_nuevo=codigoProveedorNuevo($IDPROVEEDOR);
  $sqlInsertDet="INSERT INTO ingresos_almacen_detalle(cod_ingresoalmacen, cod_proveedor,factura,fecha_factura,dcto_almacen,nit,autorizacion,codigo_control,monto_factura) 
  VALUES ($cod_ingresoalmacen,'$IDPROVEEDOR_nuevo','$DOCUM','$FECHA1','$DCTO','$RUC','$REFE1','$REFE',$total_venta)";
  //echo $sqlInsertDet;
  $stmtInsertDet = $dbh_detalle->prepare($sqlInsertDet);
  $flagSuccess=$stmtInsertDet->execute();
}   
if($flagSuccess){
     echo  "
   <script >$(document).ready(function() {
    swal({
        title: 'CORRECTO',
        text: 'Se procesó correctamente.!!! :D',
        type: 'success',
        
        confirmButtonClass: 'btn btn-success',
        
        confirmButtonText: 'Aceptar',

        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            window.close();
            return(true);
          } 
        });

});</script>";
}else{
     echo  "
   <script >$(document).ready(function() {
    swal({
        title: 'ERROR',
        text: 'Hubo un error en el proceso. Contactese con el administrador :(,
        type: 'error',
      
        confirmButtonClass: 'btn btn-success',
        
        confirmButtonText: 'Aceptar',
        
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            window.close();
            return(true);
          } 
        });

});</script>";
}



// showAlertSuccessError($flagSuccess,"../".$urlList);  


?>
