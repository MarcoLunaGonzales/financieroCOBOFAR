<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo_af = $_GET["codigo"];//codigoactivofijo
try{
    $stmtAF = $dbh->prepare("SELECT (select p.nombre from proyectos_financiacionexterna p where p.codigo=cod_proy_financiacion) as nom_proy_financiacion
    from activosfijos
     WHERE codigo=:codigo");
    $stmtAF->bindParam(':codigo',$codigo_af);
    $stmtAF->execute();
    $result = $stmtAF->fetch();
    $nom_proy_financiacion = $result['nom_proy_financiacion'];    

    $stmt = $dbh->prepare("SELECT codigo,valorinicial,codigoactivo,tipoalta,DATE_FORMAT(fechalta ,'%d/%m/%Y')as fechalta,activo,otrodato,depreciacionacumulada,valorresidual,estadobien,(select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombre_depreciaciones,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable) as nombre_personal,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable2) as nombre_personal2,(select t.tipo_bien from tiposbienes t where t.codigo=cod_tiposbienes)as tipo_bien,(select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as nombre_uo2,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as abrev_uo2,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select c.numero from comprobantes  c where c.codigo=cod_comprobante ) as comprobante from activosfijos WHERE codigo=:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo_af);
    $stmt->execute();
    
    $result = $stmt->fetch();

    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];
    // $indiceufv = $result['indiceufv'];
    // $tipocambio = $result['tipocambio'];
    // $moneda = $result['moneda'];
    $valorinicial = $result['valorinicial'];
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];
    // $cod_depreciaciones = $result['cod_depreciaciones'];
    // $cod_tiposbienes = $result['cod_tiposbienes'];
    // $vidautilmeses = $result['vidautilmeses'];
    $estadobien = $result['estadobien'];
     $otrodato = $result['otrodato'];
    // $cod_ubicaciones = $result['cod_ubicaciones'];
    // $cod_empresa = $result['cod_empresa'];
    $activo = $result['activo'];
    // $cod_responsables_responsable = $result['cod_responsables_responsable'];
    // $cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
    // $created_at = $result['created_at'];
    // $created_by = $result['created_by'];
    // $modified_at = $result['modified_at'];
    $comprobante = $result['modified_by'];
    // $vidautilmeses_restante = $result['vidautilmeses_restante'];
    $nombre_personal = $result['nombre_personal'];
    $nombre_personal2 = $result['nombre_personal2'];
    $nombre_depreciaciones = $result['nombre_depreciaciones'];
    $tipo_bien = $result['tipo_bien'];
    $edificio = "";
    $oficina = "";
    // $nombre_uo = $result['nombre_uo'];
    $nombre_uo2 = $result['nombre_uo2'];
    $abrev_uo2 = $result['abrev_uo2'];
    $nombre_area = $result['nombre_area'];
    

    //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM activosfijosimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo_af);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];
    //==================================================================================================================

    //$gestion2 = $_POST["gestion"];
    $stmt2 = $dbh->prepare("SELECT * 
    from mesdepreciaciones m, mesdepreciaciones_detalle md
    WHERE m.codigo = md.cod_mesdepreciaciones 
    and md.cod_activosfijos = :codigo");
    // Ejecutamos
    //$stmt2->bindParam(':mes',$mes2);
    $stmt2->bindParam(':codigo',$codigo_af);

    $stmt2->execute();
    //resultado
    $stmt2->bindColumn('mes', $mes3);
    $stmt2->bindColumn('gestion', $gestion3);
    $stmt2->bindColumn('ufvinicio', $ufvinicio);
    $stmt2->bindColumn('ufvfinal', $ufvfinal);
    //$stmt2->bindColumn('estado', $estado);
    //$stmt2->bindColumn('codigo1', $codigo1);
    $stmt2->bindColumn('cod_mesdepreciaciones', $cod_mesdepreciaciones);
    $stmt2->bindColumn('cod_activosfijos', $cod_activosfijos);
    $stmt2->bindColumn('d2_valorresidual', $d2_valorresidual);
    $stmt2->bindColumn('d3_factoractualizacion', $d3_factoractualizacion);
    $stmt2->bindColumn('d4_valoractualizado', $d4_valoractualizado);
    $stmt2->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
    $stmt2->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
    $stmt2->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
    $stmt2->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
    $stmt2->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
    $stmt2->bindColumn('d10_valornetobs', $d10_valornetobs);
    $stmt2->bindColumn('d11_vidarestante', $d11_vidarestante);

    //asignaciones
    $query2 = "SELECT cod_unidadorganizacional,cod_area,fechaasignacion,estadobien_asig,cod_personal,cod_personal2,cod_estadoasignacionaf,cod_estadoasignacionaf,
    fecha_recepcion,observaciones_recepcion,fecha_devolucion,observaciones_devolucion
    from activofijos_asignaciones
    where cod_activosfijos =".$codigo_af." order by fechaasignacion desc";
        $statement2 = $dbh->query($query2);


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
            '<img class="imagen-logo-izq" src="../assets/img/icono_sm_cobofar.jpg" style="width: 70px; height:70px;">'.
            '<div id="header_titulo_texto">Ficha De Activo Fijo</div>'.

            '<br><br><br><br>'.
            '<table align="center" >'.
                '<tbody>';                

                    $row = $stmt2->fetch();
                        $d2_valorresidual_aux = $row["d2_valorresidual"];
                        $d10_valornetobs_aux = $row["d10_valornetobs"];

                        if($d10_valornetobs_aux==null){
                            $d10_valornetobs_aux=$valorinicial;
                        }
                        
                    $html.='<tr>'.
                        '<td class="text-left small" >'.
                            '<p>'.
                                '<b>Código Activo : </b>'.$codigoactivo.'<br>'.
                                '<b>Descripción : </b>'.$activo.'<br>'.
                                '<b>Oficina : </b>'.$nombre_uo2.' <br>'.
                                '<b>Area : </b>'.$nombre_area.' <br>'.
                                '<b>Rubro : </b>'.$nombre_depreciaciones.' <br>'.
                                '<b>Responsable 1: </b>'.$nombre_personal.' <br>'.
                                '<b>Responsable 2: </b>'.$nombre_personal2.' <br>'.
                                '<b>Tipo alta : </b>'.$tipoalta.'<br>'.
                                // '<b>Ubicación : </b>'.$edificio.'<br>'.
                                '<b>Estado Bien : </b>'.$estadobien.' <br>'.
                                '<b>Fecha alta : </b>'.$fechalta.'<br>'.
                                '<b>Tipo Bien : </b>'.$tipo_bien.'<br>'.
                                '<b>Valor Residual : </b> '.$d2_valorresidual_aux.'<br>'.
                                '<b>Valor Neto Bs : </b>'.$d10_valornetobs_aux.'<br>'.'<br>'.
                                '<b>Proyecto Financiación : </b>'.$nom_proy_financiacion.
                            '</p>'.
                        '</td>'.
                        '<td class="text-center small">'; if($imagen!="" || $imagen!=null){
                            $html.='<img src="imagenes/'.$imagen.'" style="width: 100px; height:100px;"><br>';
                            }
                            $fileName=obtenerQR_activosfijos_rpt($codigo_af);
                                                        
                                // $dir = 'qr_temp/';
                                // if(!file_exists($dir)){
                                //     mkdir ($dir);}
                                // $fileName = $dir.$codigoactivo.'.png';
                                // $tamanio = 2; //tamaño de imagen que se creará
                                // $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                // $frameSize = 1; //marco de qr                                
                                // $contenido = "Cod:".$codigoactivo."\nRubro:".$nombre_depreciaciones."\nDesc:".$activo."\nRespo.:".$abrev_uo2." - ".$nombre_personal."\n NC:".$comprobante;
                                // QRcode::png($contenido, $fileName, $level, $tamanio,$frameSize);
                                $html.='<img src="'.$fileName.'"/>';
                        $html.='</td>'.
                    '</tr>'.
                    '<hr>'.
                    '<tr>'. 
                        '<td>'.
                        '</td>'.
                        '<td>'.
                        '</td>'.
                        
                    '</tr>'.

                    '<hr>'.

                '</tbody>'.

            '</table>'.
            '<br><br><br><br>'.
            '<h4> ASIGNACIONES</h4>'.
            '<table class="table">'.
                '<thead>'.
                    '<tr>'.
                        '<th class="font-weight-bold"><small>Fecha Asig.</small></th>'.
                        '<th class="font-weight-bold"><small>Estado bien</small></th>'.
                        '<th class="font-weight-bold"><small>Personal</small></th>'.
                        '<th class="font-weight-bold"><small>Personal 2</small></th>'.
                        '<th class="font-weight-bold"><small>Oficina</small></th>'.
                        '<th class="font-weight-bold"><small>Area</small></th>'.
                        '<th class="font-weight-bold"><small>Estado Asignación</small></th>'.
                        '<th class="font-weight-bold"><small>F. Recepción</small></th>'.
                        '<th class="font-weight-bold"><small>Obs. Recepción</small></th>'.
                        '<th class="font-weight-bold"><small>F. Devolución</small></th>'.
                        '<th class="font-weight-bold"><small>Obs. Devolución</small></th>'.
                    '</tr>'.
                '</thead>'.
                '<tbody>';
                    while ($row = $statement2->fetch()) {
                       $html.='<tr>'.
                            '<td class="text-left"><small><small>'.$row["fechaasignacion"].'</small></small></td>'.
                            '<td class="text-left"><small><small>'.$row["estadobien_asig"].'</small></small></td>'.
                            '<td class="text-left"><small><small>'.namePersonal($row["cod_personal"]).'</small></small></td>'.
                            '<td class="text-left"><small><small>'.namePersonal($row["cod_personal2"]).'</small></small></td>'.
                            '<td class="text-left"><small><small>'.abrevUnidad($row["cod_unidadorganizacional"]).'</small></small></td>'.
                            '<td class="text-left"><small><small>'.abrevArea($row["cod_area"]).'</small></small></td>'.

                            '<td class="text-left"><small><small>'.nameTipoAsignacion($row["cod_estadoasignacionaf"]).'</small></small></td>'.
                            '<td class="text-left"><small><small>'.$row["fecha_recepcion"].'</small></small></td>'.
                            '<td class="text-left"><small><small>'.$row["observaciones_recepcion"].'</small></small></td>'.
                            '<td class="text-left"><small><small>'.$row["fecha_devolucion"].'</small></small></td>'.
                            '<td class="text-left"><small><small><small>'.$row["observaciones_devolucion"].'</small></small></small></td>'.
                     '</tr>';
                     } 
                $html.='</tbody>'.
            '</table>'.        
        '</body>'.
      '</html>';           
descargarPDF("COBOFAR  ".$codigoactivo,$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
