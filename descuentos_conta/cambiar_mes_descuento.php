<?php



require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$codigo_detalle=$_POST["codigo_detalle"];
$datos=$_POST["datos"];
$gestion=$_POST["gestion"];
$arra_datos=explode(',',$datos);
$contador_items= count($arra_datos);

$sql="DELETE from descuentos_conta_detalle_mes where cod_descuento_detalle=$codigo_detalle";
$stmtDelete = $dbh->prepare($sql);
$flagSuccess=$stmtDelete->execute();
for ($i=0; $i <$contador_items ; $i++) {
    $monto=$arra_datos[$i];
    $mes=$i+1;
    if($mes==13){
        $mes=1;
        $gestion=$gestion+1;
    }

    if($monto>0){
        $sql="INSERT INTO descuentos_conta_detalle_mes(cod_descuento_detalle,mes,gestion,monto,cod_estado,cod_comprobante_detalle)
        values ('$codigo_detalle','$mes','$gestion','$monto',1,0)";
        $stmt = $dbh->prepare($sql);
        $flagSuccess=$stmt->execute();
    }
    
}


if($flagSuccess){
    echo 1;
}else{
    echo 2;
}
?>
