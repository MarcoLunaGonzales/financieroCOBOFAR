<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
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
    $stmtPersonal = $dbh->prepare("SELECT p.paterno,p.materno,p.primer_nombre,c.nombre as cargo,a.nombre as area,DATE_FORMAT(ing_planilla,'%d/%m/%Y')as ing_planilla_x
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



$html.=  '<header class="header">'.
            // '<img class="imagen-logo-izq" src="../assets/img/icono_sm_cobofar.jpg">'.
            '<div id="header_titulo_texto">Vacaciones del Personal</div>'.
            '</header>'.

            '<table class="table"align="center" style="width: 100%;border-collapse: collapse;">'.
                '<tbody style=" font-family: Times New Roman;font-size: 11px;">';
                    $html.='<tr>'.
                        '<td class="td-border-none" width="25%"><img  src="../assets/img/icono_sm_cobofar.jpg" style="padding-left: 50px;padding-top: -25px;left: 0px;width:50px;height:50px;"></td>'.
                        '<td colspan="2" align="center" class="td-border-none">'.
                            '<h2><b>'.$primer_nombre.' '.$paterno.' '.$materno.'</b><br></h2>'.
                            $cargo.' <br>'.
                            $area.'<br>Fecha de Ingreso:'.$ing_planilla.
                        '</td >
                        <td class="td-border-none" width="25%"><center>Fecha Imp.: '.date('d/m/Y').'</center></td>'.
                    '</tr></tbody>
            </table>';

                    $html.='<table class="table"><tbody>
                    <tr style="background:#45b39d;color:black;"><td></td><td align="center">F.INICIO</td><td align="center">F.FIN</td><td align="center">TOTAL DIAS</td><td align="center">OBSERVACIONES</td><td align="center">SALDO</td></tr>';
                    $saldo_total=0;
                    $contador_items=count($datos);
                    for ($i=0; $i <$contador_items; $i++) { 
                        $datos_string=$datos[$i];
                        $array_datos=explode(",", $datos_string);
                        $nombre_gestion=$array_datos[0];
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

                        $sql="SELECT pv.fecha_inicial,pv.hora_inicial,pv.fecha_final,pv.hora_final,pv.observaciones,pv.dias_vacacion,(select tvp.nombre from tipos_vacacion_personal tvp where tvp.codigo=pv.cod_tipovacacion)as tipo_vacacion
                            from personal_vacaciones pv  where pv.cod_personal=$cod_personal and pv.cod_estadoreferencial=1 and pv.gestion=$nombre_gestion";                        
                        $stmt = $dbh->prepare($sql);
                        $stmt->execute();
                        
                        $contador_det=0;
                        $html.='<tr style="padding:0px !important"><td align="center" rowspan="'.$total_det.'">GESTION<BR><span style="font-size:18px">'.$nombre_gestion.'</span><BR>ACUMULADO <b>'.$acumulado_gestion.'</b> DIAS</td>';
                        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $fecha_inicial=$result['fecha_inicial'];
                            $hora_inicial=$result['hora_inicial'];
                            $fecha_final=$result['fecha_final'];
                            $hora_final=$result['hora_final'];
                            $tipo_vacacion=$result['tipo_vacacion'];
                            $dias_vacacion=$result['dias_vacacion'];
                            $saldo_gestion-=$dias_vacacion;
                            if($contador_det>0){
                                $html.='<tr>';
                            }
                            $contador_det++;
                            $html.='<td align="center">'.$fecha_inicial.'</td><td align="center">'.$fecha_final.'</td><td align="center">'.round($dias_vacacion).'</td><td>'.$tipo_vacacion.'</td><td align="center" style="font-size:15px"><b>'.$saldo_gestion.'</b></td></tr>';
                        }
                        if($contador_det==0){
                            $html.='<td></td><td></td><td></td><td></td><td align="center"><h2>'.$saldo_gestion.'</h2></td></tr>';
                        }
                        $saldo_total+=$saldo_gestion;
                    }
                    $html.='<tr style="background:#45b39d;color:black;"><td colspan="5" align="right">SALDO TOTAL AL '.date("d/m/Y").' </td><td align="center"><h2>'.$saldo_total.' días</h2></td></tr>';
               $html.= '</tbody>'.            
            '</table>';
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
