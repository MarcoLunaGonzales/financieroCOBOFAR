<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET["codigo"];

try{
    $sqlActivos="SELECT codigo,codigoactivo,activo,
(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as abr_uo,
(select a.abreviatura from areas a where a.codigo=cod_area) as abr_area,
(select concat_ws(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as nombre_responsable,(select c.numero from comprobantes  c where c.codigo=cod_comprobante ) as comprobante
from activosfijos 
where codigo in ($codigo) ";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigoX);
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('abr_uo', $abr_uoX);
$stmtActivos->bindColumn('abr_area', $abr_areaX);
$stmtActivos->bindColumn('nombre_responsable', $nombre_responsableX);
$stmtActivos->bindColumn('comprobante', $comprobanteX);
    


$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF3Etiqueta.css" rel="stylesheet" />'.
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
  
                    $cont=0;$htmlFila='';
                    while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                        
                      $stmtRubro = $dbh->prepare("SELECT (select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombreRubro
                        from activosfijos where codigo=$codigoX");
                      $stmtRubro->execute();
                      $resultRubro = $stmtRubro->fetch();
                      $nombreRubro = $resultRubro['nombreRubro'];
                      $htmlFila.='<td align="center" width="50%"><img src="../assets/img/logo_4.png" width="100" height="65" style="padding-top:-0.3cm" /><br><hr><label style="font-weight:bolder;font-size:16px;">'.$codigoActivoX.'</label><br><label style="font-weight:bolder;font-size:10px;">ACTIVOS FIJOS</label><br><label style="font-weight:bolder;font-size:10px;">GESTION '.date("Y").'</label></td><td style="" align="right" width="50%">';
                      $fileName=obtenerQR_activosfijos($codigoX);
                      $htmlFila.= '<img src="'.$fileName.'"/>';
                      $htmlFila.='</td><td width="1%">&nbsp;</td>';
                      $cont++;
                      if($cont%2==0){
                        $html.='<table class="table" style="width:100% !important;">'.'<tbody><tr>';
                        $html.='<td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td><td width="20%"></td><td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td></tr><tr>';
                        $html.=$htmlFila.'</tr><tr><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td><td width="20%"></td><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td></tr>';
                        $htmlFila='';
                        $html.=''.'</tbody>'.'</table><div style="page-break-after:always;"></div>';  
                        // break;
                      }
                      //imprimir 2 veces, ya que no acepta uno solo
                      $stmtRubro = $dbh->prepare("SELECT (select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombreRubro
                        from activosfijos where codigo=$codigoX");
                      $stmtRubro->execute();
                      $resultRubro = $stmtRubro->fetch();
                      $nombreRubro = $resultRubro['nombreRubro'];
                      $htmlFila.='<td align="center" width="50%"><img src="../assets/img/logo_4.png" width="100" height="65" style="padding-top:-0.3cm" /><br><hr><label style="font-weight:bolder;font-size:16px;">'.$codigoActivoX.'</label><br><label style="font-weight:bolder;font-size:10px;">ACTIVOS FIJOS</label><br><label style="font-weight:bolder;font-size:10px;">GESTION '.date("Y").'</label></td><td style="" align="right" width="50%">';
                      $fileName=obtenerQR_activosfijos($codigoX);
                      $htmlFila.= '<img src="'.$fileName.'"/>';
                      $htmlFila.='</td><td width="1%">&nbsp;</td>';
                      $cont++;
                      if($cont%2==0){
                        $html.='<table class="table" style="width:100% !important;">'.'<tbody><tr>';
                        $html.='<td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td><td width="20%"></td><td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td></tr><tr>';
                      $html.=$htmlFila.'</tr><tr><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td><td width="20%"></td><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td></tr>';
                      $htmlFila='';
                      $html.=''.'</tbody>'.'</table><div style="page-break-after:always;"></div>';  
                        // break;
                      }
                    }
        $html.='</body>'.
      '</html>';           
descargarPDFEtiqueta("COBOFAR-ETIQUETAS-AF",$html);
//echo $html;
?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
