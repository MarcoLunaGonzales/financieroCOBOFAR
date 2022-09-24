<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
// require_once '../conexion2.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

ini_set('display_errors',1);
session_start();
$globalUser=$_SESSION["globalUser"];
$codigo=$_POST["codigo"];
$glosaCabecera=$_POST["glosa"];//fecha de descuento
$fecha=$_POST["fecha_cabecera"];

$gestion=date('Y',strtotime($fecha));
$mes=date('m',strtotime($fecha));

$dbh = new Conexion();
// $dbh_detalle = new Conexion2();
// $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

$flagSuccess=false;
if($codigo==0){
    //cabecera
    $codigo=obtenerCodigoDescuentoConta();
    $sql="INSERT INTO descuentos_conta(codigo,fecha,glosa,cod_estado,cod_contabilizado,created_at,created_by) 
        values ('$codigo','$fecha','$glosaCabecera','1','0',NOW(),'$globalUser')";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}else{
    $sql="UPDATE descuentos_conta set fecha='$fecha',glosa='$glosaCabecera',modified_at=NOW(),modified_by='$globalUser' where codigo= '$codigo'";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}
if($flagSuccess){
    //borrar 

    $sqldelteMes="DELETE from descuentos_conta_detalle_mes where cod_descuento_detalle in (select codigo from descuentos_conta_detalle where cod_descuento='$codigo');";
    $stmtDeleteMes = $dbh->prepare($sqldelteMes);
    $flagSuccess=$stmtDeleteMes->execute();

    $sqldelte="DELETE from descuentos_conta_detalle where cod_descuento='$codigo';";
    $stmtDelete = $dbh->prepare($sqldelte);
    $flagSuccess=$stmtDelete->execute();

    // detalle de descuentos
    $cantidad_filas = $_POST["cantidad_filas"];
    for ($i=1;$i<=$cantidad_filas;$i++){                
        if(isset($_POST["cod_sucursal".$i])){
            $cod_sucursal=$_POST["cod_sucursal".$i];
            $fecha=$_POST["fecha".$i];
            $cod_personal=$_POST["cod_personal".$i];  
            $cod_tipodescuento=$_POST["cod_tipodescuento".$i];
            $cod_contracuenta=$_POST["cod_contracuenta".$i];
            $monto_sistema=$_POST["monto_sistema".$i]; 
            $monto_deposito=$_POST["monto_deposito".$i]; 
            $monto_diferencia=$_POST["monto_diferencia".$i]; 
            $glosa=$_POST["glosa_detalle".$i];
            $codigoDetalle=obtenerCodigoDescuentoDetalle();
            $sql="INSERT INTO descuentos_conta_detalle(codigo,cod_descuento,cod_area,fecha,cod_personal,cod_tipodescuento,cod_contracuenta,monto_sistema,monto_depositado,diferencia,glosa)
            values ('$codigoDetalle','$codigo','$cod_sucursal','$fecha','$cod_personal','$cod_tipodescuento','$cod_contracuenta','$monto_sistema','$monto_deposito','$monto_diferencia','$glosa')";
            $stmt = $dbh->prepare($sql);                
            $flagSuccess=$stmt->execute();
            
            $sql="INSERT INTO descuentos_conta_detalle_mes(cod_descuento_detalle,mes,gestion,monto,cod_comprobante_detalle,cod_estado)
            values ('$codigoDetalle','$mes','$gestion','$monto_diferencia',0,1)";
            $stmtDesMes = $dbh->prepare($sql);                
            $flagSuccess=$stmtDesMes->execute();
        }
        
    }    
    //subir archivos al servidor
    if(isset($_POST["cantidad_archivosadjuntos"])){
        $nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
        for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
            if(isset($_POST['codigo_archivo'.$ar])){
                if($_FILES['documentos_cabecera'.$ar]["name"]){
                  $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
                  $filename = str_replace("%","",$filename);//quitamos el % del nombre;
                  $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
                  $directorio = '../assets/archivos-respaldo/archivos_solicitudes_facturacion/SOLFAC-'.$cod_facturacion; //Declaramos una  variable con la ruta donde guardaremos los archivoss
                  //Validamos si la ruta de destino existe, en caso de no existir la creamos
                  if(!file_exists($directorio)){
                    mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
                  }
                  $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos                      
                  //Movemos y validamos que el archivos se haya cargado correctamente
                  //El primer campo es el origen y el segundo el destino
                  // echo $filename."--";
                  if(move_uploaded_file($source, $target_path)) { 
                    echo "Archivo guargado.";
                    $tipo=$_POST['codigo_archivo'.$ar];
                    $descripcion=$_POST['nombre_archivo'.$ar];
                    // $codArchivoAdjunto=obtenerCodigoUltimoTabla('archivos_adjuntos_solicitud_facturacion');
                    // $tipoPadre=2708;
                    // $sqlInsert="INSERT INTO archivos_adjuntos_solicitud_facturacion(codigo,cod_tipoarchivo,descripcion,direccion_archivo,cod_solicitud_facturacion) 
                    // VALUES ($codArchivoAdjunto,'$tipo','$descripcion','$target_path','$cod_facturacion')";
                    // $stmtInsert = $dbh->prepare($sqlInsert);
                    // $flagArchivo=$stmtInsert->execute();    
                  }else {    
                      echo "Error al guardar archivo.";
                  } 
                }
            }
        }
    }
}
showAlertSuccessError($flagSuccess,"../index.php?opcion=descuentosContaList");  

?>