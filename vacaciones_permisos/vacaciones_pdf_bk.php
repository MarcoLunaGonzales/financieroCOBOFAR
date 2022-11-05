<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$cod_personal = $_GET["cp"];
$ing_planilla = $_GET["ip"];
$fecha_actual = $_GET["fa"];
$anios_antiguedad = $_GET["aa"];

// $datos = $_GET["datos"];

// $datos = serialize($datos);
if (isset($_GET['datos'])) {
    /* Deshacemos el trabajo hecho por 'serialize' */
    $datos = unserialize($_GET['datos']);
    // El contenido del error está en el índice 'error'
    // die($error['error']);
}

// var_dump($datos);
try{
    $stmtPersonal = $dbh->prepare("SELECT p.paterno,p.materno,p.primer_nombre,c.nombre as cargo,a.nombre as area,DATE_FORMAT(p.ing_planilla,'%d/%m/%Y')as ing_planilla_x,DATE_FORMAT(p.fecha_validacion_vacaciones,'%d/%m/%Y')as fecha_validacion_x,(select CONCAT_WS(' ',p2.primer_nombre,p2.paterno,p2.materno) from personal p2 where p2.codigo=p.cod_personal_validacion )as personal_validacion
    from personal p join cargos c on p.cod_cargo=c.codigo join areas a on p.cod_area=a.codigo
    WHERE p.codigo=$cod_personal");
    $stmtPersonal->execute();
    $result = $stmtPersonal->fetch();
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $primer_nombre = $result['primer_nombre'];
    $cargo = $result['cargo'];
    $area = $result['area'];
    $ing_planilla=$result['ing_planilla_x'];
    $fecha_validacion=$result['fecha_validacion_x'];
    $personal_validacion=$result['personal_validacion'];

$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFBalance.css" rel="stylesheet" />'.
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
            // '<img class="imagen-logo-izq" src="../assets/img/icono_sm_cobofar.jpg">'.
            '<div id="header_titulo_texto">Vacaciones del Personal</div>'.
            // '<h4><b>'.$primer_nombre.' '.$paterno.' '.$materno.'</b><br>'.$cargo.' <br>'.$area.'<br>Fecha de Ingreso:'.$ing_planilla.'</h4>';
            '<table class="table" align="center" style="width: 100%;border-collapse: collapse;">
                    <tr>'.
                        '<td class="td-border-none" width="25%"><img  src="../assets/img/icono_sm_cobofar.jpg" style="padding-left: 50px;padding-top: -25px;left: 0px;width:50px;height:50px;"></td>'.
                        '<td colspan="2" align="center" class="td-border-none">'.
                            '<b>'.$primer_nombre.' '.$paterno.' '.$materno.'</b><br>'.
                            $cargo.' <br>'.
                            $area.'<br>Fecha de Ingreso:'.$ing_planilla;
                            if($fecha_actual<>date('Y-m-d')){
                                $html.='<br>Fecha de Retiro:'.date('d/m/Y',strtotime($fecha_actual));    
                            }
                            $html.='<br>Fecha de Validación : '.$fecha_validacion.'<br> Personal Validación : '.$personal_validacion.
                        '</td >
                        <td class="td-border-none" width="25%"><center>Fecha Imp.: '.date('d/m/Y').'</center></td>'.
                    '</tr>

            </table>';
            $html.='</header>';

                    $html.='<table class="table"><thead>
                        <tr style="background:#45b39d;color:black;"><td></td><td align="center">F.INICIO</td><td align="center">F.FIN</td><td align="center">TOTAL DIAS</td><td align="center">TIPO</td><td align="center">SALDO</td></tr></thead><tbody>';
                    $saldo_total=0;
                    $contador_items=count($datos);
                    for ($i=0; $i <$contador_items; $i++) {
                        $datos_string=$datos[$i];
                        $array_datos=explode(",", $datos_string);
                        $nombre_gestion=$array_datos[0];
                        if($nombre_gestion<>-100){
                            $acumulado_gestion=$array_datos[1];    
                            $saldo_gestion=$acumulado_gestion;
                            //CANTIDAD DE FILAS 
                            $sqlcont="SELECT count(*)as contador
                                from personal_vacaciones where cod_personal=$cod_personal and cod_estadoreferencial=1 and gestion=$nombre_gestion";
                            $stmtcont = $dbh->prepare($sqlcont);
                            $stmtcont->execute();
                            $total_det = 1;
                            while ($resultcont = $stmtcont->fetch(PDO::FETCH_ASSOC)) {
                                $total_det=$resultcont['contador'];
                            }

                            $sql="SELECT DATE_FORMAT(pv.fecha_inicial,'%d/%m/%Y')as fecha_inicial,pv.hora_inicial,DATE_FORMAT(pv.fecha_final,'%d/%m/%Y')as fecha_final,pv.hora_final,pv.observaciones,pv.dias_vacacion,(select tvp.nombre from tipos_vacacion_personal tvp where tvp.codigo=pv.cod_tipovacacion)as tipo_vacacion
                                from personal_vacaciones pv  where pv.cod_personal=$cod_personal and pv.cod_estadoreferencial=1 and pv.gestion=$nombre_gestion";                        
                            $stmt = $dbh->prepare($sql);
                            $stmt->execute();
                            
                            $contador_det=0;
                            $html.='<tr style="padding:0px !important"><td align="center" rowspan="'.$total_det.'">GESTION<BR><b>'.$nombre_gestion.'</b><BR>ACUMULADO <b>'.$acumulado_gestion.'</b> DIAS</td>';
                            while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $fecha_inicial=$result['fecha_inicial'];
                                $hora_inicial=$result['hora_inicial'];
                                $fecha_final=$result['fecha_final'];
                                $hora_final=$result['hora_final'];
                                $tipo_vacacion=$result['tipo_vacacion'];
                                $dias_vacacion=$result['dias_vacacion'];
                                $saldo_gestion-=$dias_vacacion;
                                if($fecha_inicial=='00/00/0000'){
                                    $fecha_inicial="-";
                                }
                                if($fecha_final=='00/00/0000'){
                                    $fecha_final="-";
                                }
                                
                                if($contador_det>0){
                                    $html.='<tr>';
                                }
                                $contador_det++;
                                $html.='<td align="center">'.$fecha_inicial.'</td><td align="center">'.$fecha_final.'</td><td align="center">'.round($dias_vacacion).'</td><td>'.$tipo_vacacion.'</td><td align="center" ><b>'.$saldo_gestion.'</b></td></tr>';
                            }
                            if($contador_det==0){
                                $html.='<td></td><td></td><td></td><td></td><td align="center"><h2>'.$saldo_gestion.'</h2></td></tr>';
                            }
                            $saldo_total+=$saldo_gestion;
                        }else{
                            $acumulado_gestion=$array_datos[1]; 
                            
                            $doudecimas=$acumulado_gestion;
                        }
                    }
                    $html.='<tr style="background:#45b39d;color:black;"><td colspan="5" align="right">SALDO TOTAL AL '.date("d/m/Y").' </td><td align="center"><h2>'.$saldo_total.' días</h2></td></tr>';

                    

               $html.= '</tbody>'.            
            '</table>';

            $html.='<br><table width="100%"><tr ><td></td><td width="35%" align="right">DUODECIMAS DE VACACION : '.formatNumberDec($doudecimas).'<br> TOTAL : '.formatNumberDec($saldo_total+$doudecimas).' </td></tr></table>';
     

            $html.='<br><br><br><br><br><br><table width="100%">
                          <tr >'.
                          '<td><center><p>______________________________<BR>'.$primer_nombre.' '.$paterno.' '.$materno.'<br>TRABAJADOR</p></center></td>
                          <td width="50%"><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(26).'<BR>JEFE DE RECURSOS HUMANOS COBOFAR S.A.</p></center></td>
                          </tr>

                        </table>';
            $html.='</body>'.
      '</html>';
 // echo $html;
 descargarPDF("COBOFAR PERSONAL ",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
