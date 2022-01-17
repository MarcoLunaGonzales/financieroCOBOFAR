<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {

echo "<br><br><br>";
    
    $globalUser=$_SESSION["globalUser"];
    $created_at=date('Y-m-d h:m:s');
    $created_by=$globalUser;



    $codigoactivo=$_POST["codigoactivo"];
    //echo $codigoactivo;
    $cod_unidadorganizacional=$_POST["cod_uo"];
    $cod_area = $_POST['cod_area'];
    $cod_responsable = $_POST["cod_responsables_responsable"];
    $cod_responsable2 = $_POST["cod_responsables_responsable2"];
    //obtenemos datos del activo anterior...
    $stmtPREVIO = $dbh->prepare("SELECT * FROM activofijos_asignaciones where cod_activosfijos=:codigo  order by codigo desc limit 1");
    //Ejecutamos;
    $stmtPREVIO->bindParam(':codigo',$codigoactivo);
    $stmtPREVIO->execute();
    $resultPREVIO = $stmtPREVIO->fetch();
    $cod_ubicaciones = $resultPREVIO['cod_ubicaciones'];
    $estadobien_asig = $resultPREVIO['estadobien_asig'];
    //fecha actual

    $fechaasignacion=date("Y-m-d H:i:s");
    $cod_estadoasignacionaf = 1;
        $stmt = $dbh->prepare("INSERT INTO activofijos_asignaciones(cod_activosfijos,fechaasignacion,
            cod_ubicaciones,cod_unidadorganizacional,cod_area,cod_personal, estadobien_asig,cod_estadoasignacionaf,cod_personal2,created_at,created_by)
            values (:codigoactivo, :fechaasignacion,
            :cod_ubicaciones, :cod_unidadorganizacional,:cod_area,:cod_personal, :estadobien_asig,:cod_estadoasignacionaf,:cod_personal2,:created_at,:created_by)");
        $stmt->bindParam(':codigoactivo', $codigoactivo);
        $stmt->bindParam(':fechaasignacion', $fechaasignacion);
        $stmt->bindParam(':cod_ubicaciones', $cod_ubicaciones);
        $stmt->bindParam(':cod_unidadorganizacional', $cod_unidadorganizacional);//no sirve
        $stmt->bindParam(':cod_area', $cod_area);//no sirve
        $stmt->bindParam(':cod_personal', $cod_responsable);//no sirve
        $stmt->bindParam(':estadobien_asig', $estadobien_asig);
        $stmt->bindParam(':cod_estadoasignacionaf', $cod_estadoasignacionaf);
        $stmt->bindParam(':cod_personal2', $cod_responsable2);

        $stmt->bindParam(':created_by', $created_by);
        $stmt->bindParam(':created_at', $created_at);
        //$stmt->bindParam(':created_at', $fechaasignacion);
        $flagSuccess=$stmt->execute();
        showAlertSuccessError($flagSuccess,$urlList6);
    } catch(PDOException $ex){
        //manejar error
        echo "Un error ocurrio".$ex->getMessage();
    }
?>