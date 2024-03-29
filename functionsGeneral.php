<?php
require_once 'conexion.php';

date_default_timezone_set('America/La_Paz');

function showAlertSuccessError($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message','$url');
      </script>";
   }
}
function showAlertSuccessErrorLibretaDetalle($url){
      echo "<script>
         alerts.showSwal('error-borrar-libreta-detalle','$url');
      </script>";
}
function showAlertSuccessErrorPagosCapacitacion($bandera, $url){   
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message-capacitacion','$url');
      </script>";
   }
}

function showAlertSuccessErrorFilasLibreta($url,$mensaje){   
      echo "<script>
         alerts.showSwal('error-message-filas-libreta','$url####$mensaje');
      </script>";
}

function showAlertNewSolicitudRecursos($url){
      echo "<script>
         alerts.showSwal('success-solicitud','$url');
      </script>";
}

function showAlertSuccessError2($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message2','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message2','$url');
      </script>";
   }
}
function showAlertSuccessError3($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message3','$url');
      </script>";
   }
}
function showAlertSuccessError4($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-message4','$url');
      </script>";
   }
}

function showAlertSuccessErrorCajachica($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-messageCajaChica','$url');
      </script>";
   }
}
function showAlertSuccessErrorDepreciaciones($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-messageDepreciaciones','$url');
      </script>";
   }
}
function showAlertSuccessErrorDepreciaciones2($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-messageDepreciaciones2','$url');
      </script>";
   }
}
function showAlertSuccessErrorFacturas($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-messageFacturas','$url');
      </script>";
   }
}
function showAlertSuccessErrorComprobantePagos($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-comprobante-duplicado-pago','$url');
      </script>";
   }
}

function showAlertSuccessErrorComprobanteIngresosAlm($bandera, $url){
   if($bandera==true){
      echo "<script>
         alerts.showSwal('success-message','$url');
      </script>";
   }
   if ($bandera==false){
      echo "<script>
         alerts.showSwal('error-comprobante-duplicado-ingreso-alm','$url');
      </script>";
   }
}



function clean_string($string)
{
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    $string = str_replace(
        array('.',',',';','  '),
        array('','','',' '),
        $string
    );
    return $string;
}

function string_sanitize($s) { 
  $result = preg_replace("/[^a-zA-Z0-9]+/", "", $s); 
  return $result; 
} 

function formatNumberInt($valor) { 
   $float_redondeado=number_format($valor, 0); 
   return $float_redondeado; 
}

function formatNumberDec($valor) { 
   $float_redondeado=number_format($valor, 2); 
   return $float_redondeado; 
}


function obtieneValorConfig($codigo){
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion='$codigo'");
  $stmt->execute();
  $valor="";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['valor_configuracion'];
  }  
  return $valor;
}
function cantidadF($arreglo){
    $cont=0;
    foreach ($arreglo as $elemento) {
      $cont++;
    }
    return $cont;
   }


  function alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion){
    //generando Clave unico 
    $nuevo_numero=$cod_personal+$cod_planilla+$cod_mes+$cod_gestion;
    $cantidad_digitos=strlen($nuevo_numero);
    $numero_adicional=$nuevo_numero+100+$cantidad_digitos;
    $numero_adicional_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 
    return $numero_adicional_exa;
}
?>



