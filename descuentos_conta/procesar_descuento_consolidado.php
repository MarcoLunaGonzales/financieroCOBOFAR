<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];
$cod_personal=$_POST["cod_personal"];
$estado=$_POST["estado"];

if($estado==3){//validar
    $sql="INSERT INTO descuentos_conta_consolidado(mes,gestion,cod_estado,cod_comprobante,created_at,created_by)
    values ('$mes','$gestion','$estado',0,NOW(),'$cod_personal')";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}elseif($estado==4){//autorizado
    //cabecera consolidado
    $sql="UPDATE descuentos_conta_consolidado set cod_estado='$estado',cod_personal_autorizado='$cod_personal' where mes='$mes' and gestion='$gestion'";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
    //detalle de descuento mes
}
if($flagSuccess){
    $sql="UPDATE descuentos_conta_detalle_mes set cod_estado='$estado' where mes='$mes' and gestion='$gestion'";
    $stmtDetalleMes = $dbh->prepare($sql);
    $flagSuccess=$stmtDetalleMes->execute();    
}


if($flagSuccess){
    echo 1;
}else{
    echo 2;
}
?>
