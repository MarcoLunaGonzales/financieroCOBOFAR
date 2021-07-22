<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion


echo "<br><br><br><br>";
try {
    $cod_responable1=$_POST["cod_responable1"];
    $cod_responable2=$_POST["cod_responable2"];
    $nuevo_cod_responable2=$_POST["nuevo_cod_responable2"];
    $nuevo_cod_responable1=$_POST["nuevo_cod_responable1"];
    $sql="";
    if($cod_responable2!=""){
        $sql.=" and cod_responsables_responsable2=$cod_responable2";  
    }
    $query = "SELECT codigo,estadobien,cod_unidadorganizacional,cod_area from activosfijos where cod_responsables_responsable=$cod_responable1 $sql and cod_estadoactivofijo=1 order by codigoactivo";

    // echo $query;
    $stmt = $dbh->query($query);
    $cod_ubicaciones="";
    $codEstadoAsignacionAF="1";
    $flagSuccess=false;
    while ($row = $stmt->fetch()){ 
        $codigo_af=$row["codigo"];
        $estadobien=$row["estadobien"];
        $cod_unidadorganizacional=$row["cod_unidadorganizacional"];
        $cod_area=$row["cod_area"];
        //actualizamos personal
        // $sql="UPDATE activosfijos set cod_responsables_responsable='$nuevo_cod_responable1', cod_responsables_responsable2='$nuevo_cod_responable2' where codigo = $codigo_af";
        // //echo $sql."<br>";
        // $stmtupdate = $dbh->prepare($sql);
        // $flagSuccess=$stmtupdate->execute();
        // if($flagSuccess){
            $sql="INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
            cod_ubicaciones,cod_personal, estadobien_asig, cod_unidadorganizacional, cod_area, cod_estadoasignacionaf,cod_personal2)
            values (:cod_activosfijos, now(),
            :cod_ubicaciones, :cod_personal, :estadobien_asig, :cod_unidadorganizacional, :cod_area, :cod_estadoasignacionaf,:cod_personal2)";
            $stmt2 = $dbh->prepare($sql);
            
            $stmt2->bindParam(':cod_activosfijos', $codigo_af);
            //$stmt2->bindParam(':fechaasignacion', $fechalta);
            $stmt2->bindParam(':cod_ubicaciones', $cod_ubicaciones);
            $stmt2->bindParam(':cod_personal', $nuevo_cod_responable1);
            $stmt2->bindParam(':cod_personal2', $nuevo_cod_responable2);
            $stmt2->bindParam(':estadobien_asig', $estadobien);
            $stmt2->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);
            $stmt2->bindParam(':cod_area', $cod_area);
            $stmt2->bindParam(':cod_estadoasignacionaf', $codEstadoAsignacionAF);
            //$stmt2->bindParam(':created_by', 1);
            //$stmt2->bindParam(':modified_by', 1);
            $flagSuccess=$stmt2->execute();
        // }
        
    }

    showAlertSuccessError($flagSuccess,$urlcambiar_respo);
    
} catch(PDOException $ex){
    //manejar error
    echo "Un error ocurrio".$ex->getMessage();
}
?>