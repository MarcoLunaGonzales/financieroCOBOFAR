<?php
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


try {
    $datos_conta = $_GET["datos_conta"];
    $datos_bd = $_GET["datos_bd"];
    $array_datos_conta=explode('@@@', $datos_conta);
    
    $nit=$array_datos_conta[0];
    $nfactura=$array_datos_conta[1];
    $auto=$array_datos_conta[2];
    $codigo=$array_datos_conta[3];
    $fecha=$array_datos_conta[4];

    $dcto_bd=$datos_bd;

    $sql="UPDATE ingreso_almacenes set nit_factura_proveedor='$nit',con_factura_proveedor='$codigo',nro_factura_proveedor='$nfactura',aut_factura_proveedor='$auto',f_factura_proveedor='$fecha' where cod_ingreso_almacen='$dcto_bd' ";

    //echo "<br><br>".$sql;
   
    require("../conexion_comercial_oficial.php");
    $resp=mysqli_query($dbh,$sql);
     mysqli_close($dbh);
    echo "PROCESADO... =)";
    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>