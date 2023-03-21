<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
$codigo_af = $_GET["codigo"];//codigoactivofijo

try{
    $stmt = $dbh->prepare("SELECT codigo from activosfijos WHERE codigoactivo=:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo_af);
    $stmt->execute();
    
    $result = $stmt->fetch();
    $codigo_af=$result['codigo'];
    
    $stmt = $dbh->prepare("SELECT valorinicial,codigoactivo,tipoalta,DATE_FORMAT(fechalta ,'%d/%m/%Y')as fechalta,activo,otrodato,depreciacionacumulada,valorresidual,estadobien,(select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombre_depreciaciones,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable) as nombre_personal,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable2) as nombre_personal2,(select t.tipo_bien from tiposbienes t where t.codigo=cod_tiposbienes)as tipo_bien,(select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as nombre_uo2,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area
        from activosfijos 
        WHERE codigo=$codigo_af");
    //Ejecutamos;
    $stmt->execute();
    $result = $stmt->fetch();
    // $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];    
    $valorinicial = $result['valorinicial'];
    // $depreciacionacumulada = $result['depreciacionacumulada'];
    // $valorresidual = $result['valorresidual'];
    $estadobien = $result['estadobien'];
    $otrodato = $result['otrodato'];    
    $activo = $result['activo'];
    $nombre_personal = $result['nombre_personal'];
    $nombre_personal2 = $result['nombre_personal2'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $nombre_uo2 = $result['nombre_uo2'];
    $nombre_area = $result['nombre_area'];
    //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT imagen FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo_af);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    $imagen = $resultIM['imagen'];
    //==================================================================================================================
    //valor neto
    $stmt2 = $dbh->prepare("SELECT d10_valornetobs
    from mesdepreciaciones m, mesdepreciaciones_detalle md
    WHERE m.codigo = md.cod_mesdepreciaciones 
    and md.cod_activosfijos = $codigo_af and m.estado=1 order by m.codigo desc limit 1");
    $stmt2->execute();
    //==================================================================================================================
    //asignaciones
    $query2 = "SELECT asg.cod_unidadorganizacional,asg.cod_area,asg.fechaasignacion,asg.estadobien_asig,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,(select CONCAT_WS(' ',p2.paterno,p2.materno,p2.primer_nombre) from personal p2 where  p2.codigo=asg.cod_personal2) as personal2,asg.cod_estadoasignacionaf,asg.fecha_recepcion,asg.observaciones_recepcion,asg.fecha_devolucion,asg.observaciones_devolucion
    from activofijos_asignaciones asg join personal p on asg.cod_personal=p.codigo
    where asg.cod_activosfijos = $codigo_af 
    order by asg.fechaasignacion desc limit 20";
    $statement2 = $dbh->query($query2);

    //depreciaciones
    $sql="SELECT m.gestion,m.mes,(select ms.nombre from meses ms where ms.codigo=m.mes)as namemes,md.d2_valorresidual,md.d5_incrementoporcentual,md.d4_valoractualizado,md.d6_depreciacionacumuladaanterior,md.d7_incrementodepreciacionacumulada,md.d8_depreciacionperiodo,md.d9_depreciacionacumuladaactual,md.d10_valornetobs,md.d11_vidarestante
    from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
    WHERE  m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
     and af.codigo=$codigo_af ORDER BY 1,2 limit 20";
    $stmtDepreciaciones = $dbh->prepare($sql);
    $stmtDepreciaciones->execute();
    $stmtDepreciaciones->bindColumn('gestion', $gestionDepre);
    $stmtDepreciaciones->bindColumn('namemes', $mesDepre);
    $stmtDepreciaciones->bindColumn('d2_valorresidual', $d2_valorresidual);
    $stmtDepreciaciones->bindColumn('d4_valoractualizado', $d4_valoractualizado);
    $stmtDepreciaciones->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
    $stmtDepreciaciones->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
    $stmtDepreciaciones->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
    $stmtDepreciaciones->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
    $stmtDepreciaciones->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
    $stmtDepreciaciones->bindColumn('d10_valornetobs', $d10_valornetobs);
    $stmtDepreciaciones->bindColumn('d11_vidarestante', $d11_vidarestante);

$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
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
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/icono_sm_cobofar.jpg" style="width: 50px; height:50px;">'.
            '<div id="header_titulo_texto">Ficha De Activo Fijo</div>'.
            '<br><br>'.
            '<table class="table">'.
                '<tbody>';                

                    $rowDepre1 = $stmt2->fetch();
                    $d10_valornetobs_aux = $rowDepre1["d10_valornetobs"];
                    if($d10_valornetobs_aux==null){
                        $d10_valornetobs_aux=$valorinicial;
                    }
                    $html.='<tr>'.
                        '<td class="text-center" align="center" style="padding-top:20px;border-left:0;border-top:0;" >'; if($imagen!="" || $imagen!=null){
                            $html.='<img src="imagenes/'.$imagen.'" style="width: 100px; height:100px;border-radius:280%;"><br>';
                        }
                        $html.='<p style="font-size:15px;"><b>'.$codigoactivo.'</b></p></td>'.
                        '<td class="text-left small" rowspan="2" style="padding-left:20px;border: 0;" >'.
                            '<p>'.
                                '<b>Descripción : </b>'.$activo.'<br>'.
                                '<b>Otro Dato : </b>'.$otrodato.'<br>'.
                                '<b>Oficina : </b>'.$nombre_uo2.' <br>'.
                                '<b>Area : </b>'.$nombre_area.' <br><br>'.
                                '<b>Rubro : </b>'.$nombre_depreciaciones.' <br>'.
                                '<b>Tipo Bien : </b>'.$tipo_bien.'<br>'.
                                '<b>Tipo alta : </b>'.$tipoalta.'<br>'.
                                '<b>Estado Bien : </b>'.$estadobien.' <br>'.
                                '<b>Responsable 1: </b>'.$nombre_personal.' <br>'.
                                '<b>Responsable 2: </b>'.$nombre_personal2.' <br><br>'.
                                '<b>Fecha alta : </b>'.$fechalta.'<br>'.
                                '<b>Valor Inicial : </b> '.formatNumberDec($valorinicial).'<br>'.
                                '<b>Valor Neto Bs : </b>'.formatNumberDec($d10_valornetobs_aux).'<br>'.
                            '</p>'.
                        '</td>'.
                    '</tr>'.
                    '<tr><td class="text-center" align="center" style="border-left:0;border-top:0;border-bottom:0;">';
                        $fileName=obtenerQR_activosfijos_rpt($codigo_af);
                        $html.='<img src="'.$fileName.'"/>';
                    $html.='</td></tr>'.
                '</tbody>'.
            '</table>'.
            
            '<h4> DEPRECIACIONES</h4>'.
            '<table class="table">'.
                '<tr>'.
                    '<td class="text-center"><b><small>Gestion</small></b></td>'.
                    '<td class="text-center"><b><small>Mes</small></b></td>'.
                    '<td class="text-center"><b><small>Valor<br>Anterior</small></b></td>'.
                    '<td class="text-center"><b><small>Actualización</small></b></td>'.
                    '<td class="text-center"><b><small>Valor<br>Actualizado</small></b></td>'.
                    '<td class="text-center"><b><small>Depreciación<br>Acumulada Anterior</small></b></td>'.
                    '<td class="text-center"><b><small>Actualización<br>Depreciación Acumulada</small></b></td>'.
                    '<td class="text-center"><b><small>Depreciación Periodo</small></b></td>'.
                    '<td class="text-center"><b><small>Depreciacion Acumulada</small></b></td>'.
                    '<td class="text-center"><b><small>Valor Neto</small></b></td>'.
                    '<td class="text-center"><b><small>Vida útil Restante</small></b></td>'.
                '</tr>';
                while ($rowDepre = $stmtDepreciaciones->fetch()) {
                    $html.='<tr>'.
                        '<td class="text-left"><small><small>'.$gestionDepre.'</small></small></td>'.
                        '<td class="text-left"><small><small>'.$mesDepre.'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d2_valorresidual).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d5_incrementoporcentual).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d4_valoractualizado).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d6_depreciacionacumuladaanterior).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d7_incrementodepreciacionacumulada).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d8_depreciacionperiodo).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d9_depreciacionacumuladaactual).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.formatNumberDec($d10_valornetobs).'</small></small></td>'.
                        '<td class="text-center"><small><small>'.$d11_vidarestante.'</small></small></td>'.
                    '</tr>';
                } 
        $html.='</table>'.
        '<br>'.
            '<h4> ASIGNACIONES</h4>'.
            '<table class="table">'.
                '<tr>'.
                    '<td class="text-center"><b><small>Fecha Asig.</small></b></td>'.
                    '<td class="text-center"><b><small>Of./Area</small></b></td>'.
                    '<td class="text-center"><b><small>Estado bien</small></b></td>'.
                    '<td class="text-center"><b><small>Responsables</small></b></td>'.
                    '<td class="text-center"><b><small>Estado Asignación</small></b></td>'.
                    '<td class="text-center"><b><small>Recepción</small></b></td>'.
                    '<td class="text-center"><b><small>Devolución</small></b></td>'.
                '</tr>';
                while ($row = $statement2->fetch()) {
                   $html.='<tr>'.
                        '<td class="text-left"><small><small>'.$row["fechaasignacion"].'</small></small></td>'.
                        '<td class="text-left"><small><small>'.abrevUnidad($row["cod_unidadorganizacional"]).'/'.nameArea($row["cod_area"]).'</small></small></td>'.
                        '<td class="text-left"><small><small>'.$row["estadobien_asig"].'</small></small></td>'.
                        '<td class="text-left"><small><small>'.$row["personal"].'<br>'.$row["personal2"].'</small></small></td>'.
                        '<td class="text-left"><small><small>'.nameTipoAsignacion($row["cod_estadoasignacionaf"]).'</small></small></td>'.
                        '<td class="text-left"><small><small>Fecha : '.$row["fecha_recepcion"].'<br>Obs : '.$row["observaciones_recepcion"].'</small></small></td>'.
                        '<td class="text-left"><small><small>Fecha : '.$row["fecha_devolucion"].'<br>Obs : '.$row["observaciones_devolucion"].'</small></small></td>'.
                    '</tr>';
                }        
            $html.='</table>'.
        '</body>'.
      '</html>';           
descargarPDF("COBOFAR  ".$codigoactivo,$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
