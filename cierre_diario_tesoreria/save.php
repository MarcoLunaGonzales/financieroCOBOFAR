<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
session_start();
$tipo_cierre=$_POST['tipo_cierre'];
$comprobante=$_POST['comprobante'];
$cod_comprobante=$_POST['cod_comprobante'];
$token=$_POST['token'];
$nro_cheque=$_POST['nro_cheque'];
$emision=$_POST['emitidos_pago_s'];
$tipopago=$_POST["tipo_pago_s"];
$glosa=$_POST['glosa'];
$importe=$_POST['importe'];
$creado_por=$_SESSION['globalUser'];
$fecha=$_POST['fecha_emision'];
//datos para el envio
$dbh = new Conexion();
$flagSuccess=false;

$stmt = $dbh->prepare("SELECT IFNULL(max(c.codigo)+1,1)as codigo from cierre_tesoreria c");
$stmt->execute();
$codigoCierre=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoCierre=$row['codigo'];
}

if($tipopago!=1){
    $emision=0;
}

$sql="INSERT INTO cierre_tesoreria (codigo,cod_tipocierre,cod_comprobante,comprobante,cod_personal,fecha,token,nro_trasaccion_cheque,glosa,importe,estado,created_by,created_at,cod_cheque,cod_tipopago) 
VALUES('$codigoCierre','$tipo_cierre','$cod_comprobante','$comprobante','$creado_por','$fecha','$token','$nro_cheque','$glosa','$importe',1,'$creado_por',NOW(),'$emision','$tipopago')";
if($tipo_cierre==1){   
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute(); 
}else{
    if(obtenerSaldoCierreTesoreria(date("Y-m-d"))>=$importe){
       $stmt = $dbh->prepare($sql);
       $flagSuccess=$stmt->execute();  
    }  
}

if($flagSuccess==true&&$tipopago==1){     
     $cheque=$emision;
     $numero_cheque=$_POST['numero_cheque_s'];
     $nombre_ben=$_POST['beneficiario_s'];
     $sqlInsert3="INSERT INTO cheques_emitidos (cod_cheque,fecha,nombre_beneficiario,monto,cod_registrotesoreria,cod_estadoreferencial,numero) 
              VALUES ('".$cheque."','".$fecha."','".$nombre_ben."','".$importe."','".$codigoCierre."',1,'$numero_cheque')";
     $stmtInsert3 = $dbh->prepare($sqlInsert3);
     $stmtInsert3->execute();

     $sqlInsert4="UPDATE cheques SET nro_cheque=$numero_cheque where codigo=$cheque";
     $stmtInsert4 = $dbh->prepare($sqlInsert4);
     $stmtInsert4->execute();
}

showAlertSuccessError($flagSuccess,"../".$urlList); 
?>