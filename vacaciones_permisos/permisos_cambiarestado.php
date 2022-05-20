<?php

if (isset($_GET["codigo"])) {//aprobacion o autorizacion
    // echo "<br><br><br><br><br>aqui";
    if(isset($_GET["q"])){
        $q=$_GET["q"];
        $a=$_GET["a"];
        $s=$_GET["s"];
    }else{
        $q=$_SESSION['globalUser'];
    }

    require_once 'conexion.php';
    require_once 'functionsGeneral.php';
    $sw_url=true;
    $codigo=$_GET["codigo"];
    $sw=$_GET["sw"];
    if(isset($_GET["t"])){//desde list admin
        $sql="UPDATE personal_permisos set cod_estado='$sw',cod_personal_autorizado=$q where codigo=$codigo";
    }else{
        $sql="UPDATE personal_permisos set cod_estado='$sw' where codigo=$codigo";    
    }

}elseif(isset($_POST["codigo"])){//rechazo 
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';
    $sw_url=false;
    $codigo=$_POST["codigo"];
    $q=$_POST["q"];
    $a=$_POST["a"];
    $s=$_POST["s"];
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
        if(isset($_GET["q"])){
            showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalListaADM&q='.$q.'&a='.$a.'&s='.$s);  
        }else{
            showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalListaADM');
        }
     }else{
        if(isset($_GET["q"])){
            showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalLista&q='.$q.'&a='.$a.'&s='.$s);  
        }else{
            showAlertSuccessError($flagSuccess,'index.php?opcion=permisosPersonalLista');
        }    
    }
}else{
    if($flagSuccess){
        echo 1;
    }else{
        echo 2;
    }
}


?>
