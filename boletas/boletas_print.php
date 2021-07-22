<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once 'boletas_html.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES
try{
  $sql="SELECT Nro from planillas_sueldos where Nro=7
    order by Nro";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();  
  $html2="";
  $index=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $Nro=$row['Nro'];
    $html=generarHTMLBOLETA($Nro,$index);  
    $array_html2=explode('@@@@@@', $html);
    $html2.=$array_html2[0];
    if($html2!='ERROR'){
      
    }else{
      echo "hubo un error al generar la factura";
    }
    $index++;
  }
  descargarPDFBoleta("COBOFAR",$html2);
?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
