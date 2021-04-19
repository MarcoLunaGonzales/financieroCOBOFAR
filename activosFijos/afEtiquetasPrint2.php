<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$rubros=$_POST["rubros"];
$personal_x=$_POST["personal"];


$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);
$personalString=implode(",", $personal_x);

try{
    $sqlActivos="SELECT codigo,codigoactivo,activo,
(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as abr_uo,
(select a.abreviatura from areas a where a.codigo=cod_area) as abr_area,
(select concat_ws(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as nombre_responsable,(select c.numero from comprobantes  c where c.codigo=cod_comprobante ) as comprobante
from activosfijos 
where cod_estadoactivofijo = 1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) and cod_depreciaciones in ($rubrosString) and cod_responsables_responsable in ($personalString)";  

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
                        $nombreRubro = $resultRubro['nombreRubro']; //<p class="horizontalCode">'.$codigoActivoX.'</p>
                        //<p class="verticalText">'.$codigoActivoX.'</p>
                        $htmlFila.='<td align="center" width="50%"><img src="../assets/img/logo_4.png" width="100" height="65" style="padding-top:-0.3cm" /><br><hr><label style="font-weight:bolder;font-size:16px;">'.$codigoActivoX.'</label><br><label style="font-weight:bolder;font-size:10px;">ACTIVOS FIJOS</label><br><label style="font-weight:bolder;font-size:10px;">GESTION '.date("Y").'</label></td><td style="" align="right" width="50%">';
                         $fileName=obtenerQR_activosfijos($codigoX);
                        $htmlFila.= '<img src="'.$fileName.'"/>';
                        $htmlFila.='</td><td width="1%">&nbsp;</td>';
                        $cont++;
                         if($cont%2==0){//{
                          $html.='<table class="table" style="width:100% !important;">'.'<tbody><tr>';
                           $html.='<td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td><td width="20%"></td><td colspan="2" align="right"><label style="font-size:12px;font-weight:bold;">'.$nombreRubro.'</label></td></tr><tr>';
                           $html.=$htmlFila.'</tr><tr><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td><td width="20%"></td><td colspan="2" align="center"><label style="font-size:13px;font-weight:bold;">FARMACIAS BOLIVIA</label></td></tr>';
                           $htmlFila='';
                           $html.=''.'</tbody>'.'</table><div style="page-break-after:always;"></div>';  
                          break;
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
