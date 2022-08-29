<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["cod"];
try{  
  $stmt = $dbh->prepare("SELECT pl.nro_correlativo,DATE_FORMAT(pl.fecha,'%d/%m/%Y')as fecha,(select p.nombre from af_proveedores p where p.codigo=ppd.cod_proveedor) as proveedor,ppd.monto,ppd.observaciones from pagos_lotes pl join pagos_proveedores pp on pl.codigo=pp.cod_pagolote join pagos_proveedoresdetalle ppd on pp.codigo=ppd.cod_pagoproveedor
  where pl.codigo=$codigo");
  $stmt->execute();
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('fecha', $fecha);
  $stmt->bindColumn('proveedor', $proveedor);
  $stmt->bindColumn('monto', $monto);
  $stmt->bindColumn('observaciones', $observaciones);
  $monto_total=0;
  $string_facturas="";
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $datos_array=explode('F:', $observaciones);    
    $factura=" ";
    if(isset($datos_array[1])){
      $factura=$datos_array[1];
    }
    $string_facturas.=$factura." - ";
    $monto_total+=$monto;
  }
  $string_facturas=trim($string_facturas," - ");
  $nombre_proveedor=$proveedor;
  $nro_pago=$nro_correlativo;
  $fecha_actual=date('d/m/Y');  
  $fecha_cobro=$fecha;
  $copia_conta="Copia: Dpto. Contable";

  $entero=floor($monto_total);
  $decimal=$monto_total-$entero;
  $centavos=round($decimal*100);
  if($centavos<10){
    $centavos="0".$centavos;
  }




$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFFActura.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
          'if ( isset($pdf) ) {'. 
            '$font = Font_Metrics::get_font("helvetica", "normal");'.
            '$size = 9;'.
            '$y = $pdf->get_height() - 24;'.
            '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
            '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
          '}'.
        '</script>';
for ($i=0; $i <2 ; $i++) { 
        if($i==1){
          $copia_conta="Copia: Proveedor";
        }
        //<img height="50px"  src="../assets/img/logo_cobofar.png">
  $html.='<div  style="height: 49.4%"><br><br>';
  $html.=  '<table class="table">
                <tr>
                  <td rowspan="2" width="20%"></td>
                  <td align="center" class="table-title"><b>ORDEN DE PAGO</b></td>
                  <td rowspan="2" width="20%">N° : '.$nro_pago.'<br>Fecha : '.$fecha_actual.'</td>
                </tr>
                <tr>
                  <td align="center" class="table-title"><b>Paguese a : '.$nombre_proveedor.'</b></td>            
                </tr>          
            </table>'.  
            '<br>'.
            '<table class="table">
              <tr>
                <td  class="td-color-celeste"><b>Detalle De Facturas : </b></td>
              </tr> 
              <tr>
                <td class="text-left" style="padding: 5px;">'.$string_facturas.'</td>
              </tr>    
              <tr>
                <td class="text-right td-color-celeste" style="padding: 10px;"><b>Total  '.number_format($monto_total, 2, '.', ',').'</b></td>
              </tr>  
              <tr>
                <td class="text-center">
                  <table class="table">
                    <tr>
                      <td colspan="5" style="padding: 5px;">Observaciones : <br><br>Por el importe de : '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos<br><br> Fecha de Cobro : <b>'.$fecha_cobro.'</b></td>
                    </tr>
                    <tr>
                      <td width="20%"></td>
                      <td width="25%"></td>
                      <td width="25%"><p>&nbsp;<br>&nbsp;</p></td>
                      <td  rowspan="2" width="8%" style="padding: 5px;margin: 0px;border-right: hidden;">Firma :<br><br>Nombre :<br><br>C.I. :</td>
                      <td  rowspan="2" style="padding: 0px;margin: 0px;border-left: hidden;">_____________________<br><br>_____________________<br><br>_____________________</td>
                    </tr>
                    <tr>
                      <td class="text-center" style="padding: 0px;margin: 0px;border-top: hidden;"><small><small>'.$copia_conta.'</small></small></td>
                      <td class="text-center">CONTABILIDAD</td>
                      <td class="text-center">GER.GRAL</td>
                      
                    </tr>
                  </table>
                </td>
              </tr>                
            </table>';
  $html.='</div>';  
}

$html.='</body>'.
      '</html>';           
descargarPDF1("COBOFAR - ",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
