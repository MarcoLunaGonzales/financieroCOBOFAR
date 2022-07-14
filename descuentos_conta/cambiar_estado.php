<?php

if (isset($_GET["codigo"])) {//aprobacion o autorizacion
    // echo "<br><br><br><br><br>aqui";

    require_once 'conexion.php';
    require_once 'conexion2.php';

    require_once 'functionsGeneral.php';
    require_once 'functions.php';
    $sw_url=true;
    $codigo=$_GET["codigo"];
    $cod_estado=$_GET["sw"];
    if(isset($_GET["t"])){//desde list admin
        $sql="UPDATE descuentos_conta set cod_estado='$cod_estado',cod_personal_autorizado=$q where codigo=$codigo";
    }else{
        $sql="UPDATE descuentos_conta set cod_estado='$cod_estado' where codigo=$codigo";
        require_once 'generar_comprobante.php';
    }

}elseif(isset($_POST["codigo"])){//rechazo 
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';
    $sw_url=false;
    $codigo=$_POST["codigo"];
    $observaciones=$_POST["observaciones"];
    if($a==-1000){//rrhh
        $sql="UPDATE personal_permisos set cod_estado='6',observaciones_rechazo='$observaciones',cod_personal_aprobado='$q' where codigo=$codigo";
    }else{
        $sql="UPDATE personal_permisos set cod_estado='6',observaciones_rechazo='$observaciones',cod_personal_autorizado='$q' where codigo=$codigo";    
    }
}
$dbh = new Conexion();
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

if($sw_url){
    if(isset($_GET["t"])){
        showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalListaADM');
     }else{
        showAlertSuccessError($flagSuccess,'index.php?opcion=descuentosContaList');
    }
}else{
    if($flagSuccess){
        echo 1;
    }else{
        echo 2;
    }
}


?>
