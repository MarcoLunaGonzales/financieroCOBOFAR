<?php

require_once 'conexion.php';
require_once 'functions.php';

$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();
try {
    
    $codigo=$_POST["codigo"];

    $nombre=$_POST["nombre"];
    $abreviatura=$_POST["abreviatura"];
    $cod_responsable=$_POST["cod_responsable"];
    $cod_area=$_POST["cod_area"];
    $cod_uo=1;
    $fecha_inicio=$_POST["fecha_inicio"];
    $fecha_final=$_POST["fecha_final"];

    $bandera_verificacion=0;
    if(isset($_POST['bandera_verificacion'])){
        $bandera_verificacion=1;//si se generará nuevo cufd
    }
    $bandera_edicion=0;
    if(isset($_POST['bandera_edicion'])){
        $bandera_edicion=1;//si se generará nuevo cufd
    }
    $bandera_transferir=0;
    if(isset($_POST['bandera_transferir'])){
        $bandera_transferir=1;//si se generará nuevo cufd
    }

    $created_at=date('Y-m-d H:m:s');
    $created_by=$globalUser;
    $modified_at=$created_at;
    $modified_by=$globalUser;
    
    $cod_estado = 1;

    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO inventarios_af(nombre,abreviatura,cod_responsable,cod_area,fecha_inicio,fecha_fin,bandera_edicion,bandera_transferir,bandera_verificacion,cod_estado,created_at,created_by) values
        ('$nombre','$abreviatura','$cod_responsable','$cod_area','$fecha_inicio','$fecha_final','$bandera_edicion','$bandera_transferir','$bandera_verificacion','$cod_estado','$created_at','$created_by')");

        $flagSuccess=$stmt->execute();

        showAlertSuccessError($flagSuccess,'?opcion=inventario_af_list');

        //$stmt->debugDumpParams();
    } else {
        //UPDATE
        $codigo = $_POST["codigo"];

        $sql="UPDATE activosfijos set codigoactivo=:codigoactivo,tipoalta=:tipoalta,fechalta=:fechalta,
        indiceufv=:indiceufv,tipocambio=:tipocambio,moneda=:moneda,valorinicial=:valorinicial,
        depreciacionacumulada=:depreciacionacumulada,valorresidual=:valorresidual,
        cod_depreciaciones=:cod_depreciaciones,cod_tiposbienes=:cod_tiposbienes,
        vidautilmeses=:vidautilmeses,estadobien=:estadobien,otrodato=:otrodato,cod_empresa=:cod_empresa,activo=:activo,
        vidautilmeses_restante=:vidautilmeses_restante,cod_af_proveedores=:cod_af_proveedores,
        numerofactura=:numerofactura, bandera_depreciar = :bandera_depreciar,cod_proy_financiacion=:cod_proy_financiacion,tipo_af=:cod_tiposactivos,modified_at=:modified_at,modified_by=:modified_by,fecha_iniciodepreciacion=:fechalta,cantidad_meses_depreciacion=:fechalta
         where codigo = :codigo";
        $stmt = $dbh->prepare($sql);
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlList6);

    }//si es insert o update
    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>