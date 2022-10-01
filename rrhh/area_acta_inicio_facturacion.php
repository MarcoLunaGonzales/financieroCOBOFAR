<?php //ESTADO FINALIZADO


require("../conexion_comercial2.php");
//require_once '../assets/libraries/CifrasEnLetras.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

//RECIBIMOS LAS VARIABLES

if(isset($_GET["cod_sucursal"])){
  $codigo = $_GET["cod_sucursal"];
}else{
  $codigo = 0;
}


$cod_area="";
$direccion="";
$nombre_ciudad="";
$cod_almacen="";

$sql="SELECT c.cod_area,c.direccion,c.nombre_ciudad,a.cod_almacen
from ciudades c join almacenes a on c.cod_ciudad=a.cod_ciudad and cod_tipoalmacen=1
where c.cod_area=$codigo";
// echo $sql;
$resp = mysqli_query($enlaceCon,$sql);
while ($dat = mysqli_fetch_array($resp)) {
  $cod_area=$dat['cod_area'];
  $direccion=$dat['direccion'];
  $nombre_ciudad=$dat['nombre_ciudad'];
  $cod_almacen=$dat['cod_almacen']; 
}

$fecha_venta="";
$fecha_venta1="";
$sql="select DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') as fecha_2,fecha from salida_almacenes where cod_almacen=$cod_almacen and cod_tiposalida=1001 and salida_anulada=0 limit 1";
$resp = mysqli_query($enlaceCon,$sql);
while ($dat = mysqli_fetch_array($resp)) {
  $fecha_venta=$dat['fecha_2'];
  $fecha_venta1=$dat['fecha'];
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

  //
  $html.=  '<table class="table">
                <tr>
                  <td rowspan="3"><center><img height="50px"  src="../assets/img/logo_cobofar.png"></center></td>
                  <td width="20%" align="left">Versión</td>
                  <td width="20%"><b>1</b></td>
                </tr>
                <tr>
                  <td align="left">Código</td>
                  <td>'.$codigo.'</td>
                </tr>          
                <tr>
                  <td align="left">Fecha de Aprobación</td>
                  <td>'.date('d',strtotime($fecha_venta1)).' de '.nombreMes(date('m',strtotime($fecha_venta1))).' de '.date('Y',strtotime($fecha_venta1)).'</td>
                </tr>
                <tr>
                <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                  <td>Elaborado por : <b>DPTO. SISTEMAS</b></td>
                  <td colspan="2">Aporbado por : <b> GERENCIA GENERAL</b></td>
                </tr>
            </table>'.  
            '<br>';
$html.=  '<table class="table">
              <tr>
                <td width="30%">Nit : 1022039027</td>
                <td align="left"><b>ACTA DE INICIO DE FACTURACIÓN</b></td>
              </tr>
          </table>'.
          '<br>';
$html.=  '<table class="table">
              <tr>
                <td colspan="2">DATOS</td>
              </tr>
              <tr>
                <td>
                  <p><BR><BR>
                    FECHA INICIO FACTURACION : <BR><BR>
                    LUGAR:  <BR><BR>
                    NOMBRE : <BR><BR>
                  </p>
                </td>
                <td>
                  <p><BR><BR><b>
                  '.$fecha_venta.'<BR><BR>
                  '.strtoupper($direccion).'<BR><BR>
                  '.strtoupper($nombre_ciudad).'</b><BR><BR>
                  </p>
                </td>
              </tr>
          </table>'.
          '<br>';
$html.=  '<table class="table">
              <tr>
                <td colspan="2" class="text-center"><b>LEGALIZACIÓN</b></td>
              </tr>
              <tr>
                <td class="text-center" ><p><BR><BR>
                    <BR><BR>
                    <BR><BR>
                    <BR><BR>
                    _______________________<BR>
                    Ing. David Huarina<BR>
                    DEPTO. SISTEMAS
                </p></td>
                <td class="text-center" ><p><BR><BR>
                    <BR><BR>
                    <BR><BR>
                    <BR><BR>
                    _______________________<BR>
                    Ing. Ismael Sullcamani<BR>
                    DEPTO. SISTEMAS
                </p></td>
              </tr>
          </table>'.
          '<br>';
            
            
    
  $html.='</div>';  


$html.='</body>'.
      '</html>';           
      // echo $html;
descargarPDF1("COBOFAR - ",$html);


?>
