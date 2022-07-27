<?php
set_time_limit(0);
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

require_once('../assets/importar_excel/php-excel-reader/excel_reader2.php');
require_once('../assets/importar_excel/SpreadsheetReader.php');
session_start();

$dbh = new Conexion();
$fechaActual=date("Y-m-d h:m:s");
$cod_libretabancariaregistro=obtenerCodigoRegistroLibreta();
$flagSuccess=false;
$globalUser=$_SESSION["globalUser"];




if (isset($_POST["codigo"])){
$codigoLibreta=$_POST["codigo"];
$observaciones=$_POST["observaciones"];
$tipo_formato=$_POST["tipo_formato"];
$tipo_cargado=$_POST["tipo_cargado"];
$cod_estadoreferencial="1";   
$message="";
$index=0;
$totalFilasCorrectas=0;
$filasErroneas=0;
$filasErroneasCampos=0;
$filasErroneasFechas=0;
$filaArchivo=0;
$listaFilasFechas=[];
$listaFilasCampos=[];

$urlOficial=$urlList2."&codigo=".$codigoLibreta;
if(isset($_POST["lista_padre"])){
  $urlOficial=$urlList;
}

if($tipo_cargado==2){
  /*$sqlDelete="DELETE FROM  libretas_bancariasdetalle where cod_libretabancaria=$codigoLibreta";
  $stmtDetalle = $dbh->prepare($sqlDelete);
  $stmtDetalle->execute();*/
}
$allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
$sqlInserts=[];  $lista_documento=[];
  if(in_array($_FILES["documentos_excel"]["type"],$allowedFileType)){

        $targetPath = 'subidas/'.$_FILES['documentos_excel']['name'];
        move_uploaded_file($_FILES['documentos_excel']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);       
        $sheetCount = count($Reader->sheets());
        for($i=0;$i<$sheetCount;$i++){         
        $Reader->ChangeSheet($i);
        $validacionFila=1;
           foreach ($Reader as $Row){ 
             if ($filaArchivo>0){
            	if($index==0){
            		// Prepare
                	$sqlRegistro="INSERT INTO libretas_bancariasregistro (codigo,fecha,cod_personal,observaciones,cod_estadoreferencial) 
                    	VALUES ($cod_libretabancariaregistro,'$fechaActual','$globalUser','$observaciones',1)";

                  $sqlInserts[$index]=$sqlRegistro;   
            	}
                $index++;

                //LIMPIAR VALORES SI NO EXISTE EL REGISTRO
                for ($filaXX=0; $filaXX <= 20; $filaXX++) { 
                    if(!isset($Row[$filaXX])){
                        $Row[$filaXX]="";
                    }
                }                


                //CAMPO DE FECHA Y HORA
                $fecha_hora = "";
                if(isset($Row[0])) {
                  $fechaFila=$Row[0]."";
                  $fe=explode("-", $fechaFila);
                  if(count($fe)==3){
                    if(strlen($fe[2])==2){
                      $fe[2]="20".$fe[2];
                      $fechafilaAux=$fe[2]."-".$fe[0]."-".$fe[1];
                    }else{
                      $fechafilaAux=$fe[2]."-".$fe[1]."-".$fe[0];
                    }
                    
                    if(verificarFecha(trim($fechafilaAux))==1){
                       $fecha_hora=$fechafilaAux;
                    }else{
                      $verSi=1;
                    }
                  }else{
                    $fe=explode("/", $fechaFila);
                    if(count($fe)==3){
                      if(strlen($fe[2])==2){
                        $fe[2]="20".$fe[2];
                        $fechafilaAux=$fe[2]."-".$fe[0]."-".$fe[1];
                      }else{
                        $fechafilaAux=$fe[2]."-".$fe[1]."-".$fe[0];
                      }
                      if(verificarFecha(trim($fechafilaAux))==1){
                        $fecha_hora=$fechafilaAux;
                      }else{
                        $verSi=1;
                      }
                    }else{
                      $verSi=1;
                      $fechaFila="";
                    }
                  } 
                }
                
                $hora = "";
                if(isset($Row[1])&&$tipo_formato!=2) { //PARA TODOS MENOS EL UNION
                  $hora=explode(":", $Row[1]);
                  if(count($hora)>2){
                    $horaFecha=$Row[1];
                  }else{
                    if(count($hora)>1){
                      $horaFecha=$hora[0].":".$hora[1].":00";  
                    }else{
                      $horaFecha=$hora[0].":00:00";
                    }                    
                  }
                	if(verificarHora($Row[1])==true){
                     $fecha_hora.=" ".$horaFecha;
                	}else{                     
                     $fecha_hora.=" ".$horaFecha;
                	}
                }

                switch ($tipo_formato) {
                  case 1: 
                      $nro_documento=$Row[9];
                      $descripcion=trim($Row[3]);
                      $informacion_complementaria=$Row[6];
                      $agencia=$Row[7];
                      $monto=$Row[4];
                      $nro_cheque=$Row[2];                                            
                      $canal=$Row[8];
                      $nro_referencia=$Row[9];
                      $cod_fila=$Row[10];
                      $saldo=$Row[5];
                  break;
                  case 2: 
                      $nro_documento=$Row[3];
                      $descripcion=trim($Row[2]);
                      $informacion_complementaria="";
                      $agencia=$Row[1];
                      $monto=$Row[4];
                      $nro_cheque="";                                            
                      $canal="";
                      $nro_referencia="";
                      $cod_fila="";
                      $saldo=$Row[5];
                  break;
                  case 3: 
                      $nro_documento=$Row[2];
                      $descripcion=trim($Row[9]);
                      $informacion_complementaria=trim($Row[7]);
                      $agencia=$Row[10];
                      $monto=$Row[19];
                      $nro_cheque=$Row[3];                                            
                      $canal="";
                      $nro_referencia="";
                      $cod_fila="";
                      $saldo=$Row[20];
                  break;
                  case 4: 
                      $nro_documento=$Row[5];
                      $descripcion=trim($Row[3]);
                      $informacion_complementaria=trim($Row[10]);
                      $agencia=$Row[2];
                      $monto=$Row[8];
                      $nro_cheque="";                                            
                      $canal="";
                      $nro_referencia=$Row[4];
                      $cod_fila="";
                      $saldo=$Row[9];
                  break;
                  case 5: 
                      $nro_documento=$Row[5];
                      $descripcion=trim($Row[3]);
                      $informacion_complementaria="";
                      $agencia=$Row[2];
                      $monto=$Row[6];
                      $nro_cheque="";                                            
                      $canal="";
                      $nro_referencia="";
                      $cod_fila="";
                      $saldo=$Row[7];
                  break;
                  case 6: 
                      $nro_documento=$Row[5];
                      $descripcion=trim($Row[4]);
                      $informacion_complementaria=trim($Row[9]);
                      $agencia=$Row[2];
                      $monto=$Row[7];
                      $nro_cheque="";                                            
                      $canal="";
                      $nro_referencia="";
                      $cod_fila="";
                      $saldo=$Row[8];
                  break;
                  
                }                

                              
                if (!empty($fecha_hora) || !empty($descripcion) || !empty($monto)) {
                	// Prepare
                  $verSi=0;
                  if(verificarFechaMaxDetalleLibreta($fecha_hora,$codigoLibreta)!=0&&$tipo_cargado==2){
                    $verSi=1;
                    //se encontraron fechas mayores a la fila
                  }
                  $verSi=0;
                  if($verSi==0){
                    if($descripcion=="" && ($monto==""||$monto==0)){

                    }else{
                      $lista_documento[$index]=$nro_documento;
                   $totalFilasCorrectas++; 
                	$sql="INSERT INTO libretas_bancariasdetalle (cod_libretabancaria,fecha_hora,nro_documento,descripcion,informacion_complementaria,agencia,monto,nro_cheque,cod_libretabancariaregistro,cod_estadoreferencial,canal,nro_referencia,codigo_fila,saldo) 
                    	VALUES ('$codigoLibreta','$fecha_hora','$nro_documento','$descripcion','$informacion_complementaria','$agencia','$monto','$nro_cheque','$cod_libretabancariaregistro','$cod_estadoreferencial','$canal','$nro_referencia','$cod_fila','$saldo')";
                    $sqlInserts[$index]=$sql;
                      
                    }
                  }else{
                    $listaFilasFechas[$filasErroneasFechas]=$index;
                    $filasErroneas++;
                    $filasErroneasFechas++;
                  }
                }else{
                  if($descripcion=="" && ($monto==""||$monto==0)){

                  }else{
                     $listaFilasCampos[$filasErroneasCampos]=$index;
                     $filasErroneasCampos++;
                     $filasErroneas++;
                  }  
                }
              } //fin de if  
                $filaArchivo++;
           }//fin foreach
        
         }//fin for

         //eliminarArchivo
         unlink($targetPath);
  }
  else
  { 
        $type = "error";
        $message = "El archivo enviado es invalido. Por favor vuelva a intentarlo";
  }
}
if($filasErroneas>0){
  $htmlInforme='';
  $htmlInforme='Errores sin formato: <b>'.$filasErroneasCampos.'</b> <a href="#colapseFormato" class="btn btn-default btn-sm" data-toggle="collapse">Ver más...</a>'.
  '<div id="colapseFormato" class="collapse small">'.
         'Filas:['.implode(",",$listaFilasCampos).']'.
       '</div>'.
  '<br>Errores de fecha: <b>'.$filasErroneasFechas.'</b><a href="#colapseFechas" class="btn btn-default btn-sm" data-toggle="collapse">Ver más...</a>'.
  '<div id="colapseFechas" class="collapse small">'.
         'Filas:['.implode(",",$listaFilasFechas).']'.
       '</div>'. 
  '<br><i class="material-icons text-danger">clear</i> Filas con errores: <b>'.$filasErroneas.'</b>'.    
  '<br><i class="material-icons text-success">check</i> Filas Correctas: <b>'.$totalFilasCorrectas.'</b>'.
  '<br>Total Filas: <b>'.$index.'</b>';
  showAlertSuccessErrorFilasLibreta("../".$urlOficial,$htmlInforme);  
}else{
  if($index>0){ // para registrar solo si hay filas en el archivo
    if(count($lista_documento) > count(array_unique($lista_documento))){
       $htmlInforme='';
       $htmlInforme='<b>Filas repetidas: El numero de Referencia / Documento se repite en algunas filas</b>';
       showAlertSuccessErrorFilasLibreta("../".$urlOficial,$htmlInforme);  
    }else{
      $sqlAcumulados=implode(";", $sqlInserts);
      $stmtAcumulados = $dbh->prepare($sqlAcumulados.";");
      $flagSuccess=$stmtAcumulados->execute();
      if($flagSuccess==true){
      	showAlertSuccessError(true,"../".$urlOficial);	
      }else{
	     showAlertSuccessError(false,"../".$urlOficial);
      }
    }
  }else{
    showAlertSuccessError(false,"../".$urlOficial);
  }
  
  
}

function verificarFecha($x) {
    if (date('Y-m-d', strtotime($x)) == $x) {
      return 1;
    }else{
      return 0;
     } 
}
function verificarHora($x) {
    if (date('H:m:s', strtotime($x)) == $x) {
      return true;
    } else {
      return false;
    }
}
?>