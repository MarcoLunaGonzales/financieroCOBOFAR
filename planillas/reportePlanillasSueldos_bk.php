<?php
require_once '../conexion3.php';
require_once '../functions.php';

session_start();
set_time_limit(0);
//INICIAR valores de las sumas
$total1=0;$total2=0;$total3=0;$total4=0;$total5=0;$total6=0;
$total_bonos_otros=0;$total_afp1=0; $total_afp2=0;
$total_rc_iva=0;$total_otros_descuentos=0;
$total_anticipos=0;

$cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
//nombre de unidad
$dbh = new Conexion3();
$mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);

// $dias_trabajados_asistencia=30;//ver datos

$dias_trabajados_mes=30; 
//$dias_trabajados_mes = obtenerValorConfiguracionPlanillas(22); //por defecto
$hrsTrabajadas=8;


//html del reporte
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
            '<table width="100%">
              <tr>
              <td width="25%"><p>CORPORACION BOLIVIANA DE FARMACIAS<br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:10022039027<br>N° Empleador Ministerio de Trabajo 1146072502</p></td>
              <td><center><span style="font-size: 13px"><b>PLANILLA DE SUELDOS Y SALARIOS</b></span><BR>Correspondientes al mes de '.$mes.' '.$gestion.'<br><b>EXPRESADA EN BOLIVIANOS</b></center></td>
              <td width="25%"><center>N° PAT. 651-1-956</center></td>
              </tr>
            </table>'.
         '</header>';

          $html.='<table class="table">'.
            '<thead>'.
            '<tr class="table-title small bold text-center">'.
              '<td width="1%">Nro</td>'.
              '<td width="5%">CI EXT</td>'.
              '<td>Apellidos y Nombres</td>'.
              '<td width="2%">Sexo</td>'.
              '<td width="2%">Nac ión</td>'.
              '<td width="4%">Fech Nac</td>'.
              '<td width="11%">Cargo</td>'.
              '<td width="4%">Fech Ing</td>'.
              '<td width="2%">Hrs Trab</td>'.
              '<td width="2%">Días Trab</td>'.
              '<td width="4%">Haber Basico</td>'.
              '<td width="4%">Haber Basico DTrab</td>'.
              '<td width="4%">Bono Ant</td>'.
              '<td width="4%">Otr Bonos</td>'.
              '<td width="4%">Tot Gan</td>'.
              '<td width="3%">AFP F</td>'.
              '<td width="3%">AFP P</td>'.
              '<td width="3%">Antic</td>'.
              '<td width="3%">RC-IVA</td>'.              
              '<td width="4%">Otr Desc</td>'.
              '<td width="4%">Tot Desc</td>'.
              '<td width="5%">Liq Pag</td>'; 
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            // $a_solidario_13000=0;
            // $a_solidario_25000=0;
            // $a_solidario_35000=0;
            $rc_iva=0;
            $atrasos=0;
            $anticipo=0;
            
            $data = obtenerPlanillaSueldosRevision($codPlanilla);
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              $cod_personal_cargo=$row['codigo'];
              $dias_trabajados_planilla=$row['dias_trabajados'];
              $monto_bonos_otros=obtenerPlanillaSueldoRevisionBonos($cod_personal_cargo,$cod_gestion,$cod_mes,$dias_trabajados_planilla,$dias_trabajados_mes);
              
              $porcentaje=$row['porcentaje'];

              // $a_solidario_13000=$row['a_solidario_13000'];
              // $a_solidario_25000=$row['a_solidario_25000'];
              $rc_iva=$row['rc_iva'];
              $atrasos=$row['atrasos'];
              $anticipo=$row['anticipo'];

              $fechaNac=$row['fecha_nacimiento'];
              $emision=$row['emision'];
              $sexo=$row['genero'];
              $nacion=$row['nacionalidad'];
              
              $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos
                      from descuentos_personal_mes 
                      where  cod_personal=$cod_personal_cargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
              $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
              $stmtDescuentosOtros->execute();
              $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
              $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];
              if($codArea!=$row['cod_area']){
                $html.='<tr>'.
                      '<td colspan="22"><center>Departamento / Sucursal: <b>'.$row['area'].'</b></center></td>';                                  
                    $html.='</tr>'; 
                $codArea=$row['cod_area'];      
              }
              $html.='<tr>'.
                '<td><small>'.$index.'</small></td>'.
                '<td><small>'.$row['ci'].' '.$emision.'</small></td>'.
                '<td><small>'.$row['apellidos']." ".$row['nombres'].'</small></td>'.
                '<td><small>'.$sexo.'</small></td>'.
                '<td><small>'.$nacion.'</small></td>'.
                '<td><small>'.strftime('%d/%m/%Y',strtotime($fechaNac)).'</small></td>'.
                '<td class="text-small"><small>'.$row['cargo'].'</small></td>'.
                '<td class="text-small"><small>'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</small></td>'.
                '<td class="text-right"><small>'.$hrsTrabajadas.'</small></td>'.
                '<td class="text-right"><small>'.$dias_trabajados_planilla.'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['haber_basico']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['haber_basico']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['bono_antiguedad']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($monto_bonos_otros*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['total_ganado']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['afp_1']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['afp_2']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                // '<td class="text-right">'.number_format(($a_solidario_13000+$a_solidario_25000)*$porcentaje/100, 2, '.', ',').'</td>'.
                '<td class="text-right"><small>'.number_format($anticipo*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($rc_iva*$porcentaje/100, 2, '.', ',').'</small></td>'.
                
                '<td class="text-right"><small>'.number_format($sumaDescuentos_otros*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['monto_descuentos']*$porcentaje/100, 2, '.', ',').'</small></td>'.
                '<td class="text-right"><small>'.number_format($row['liquido_pagable']*$porcentaje/100, 2, '.', ',').'</small></td>';
              $html.='</tr>';
              //suma de totales
              $total1+=$row['haber_basico']*$porcentaje/100;                  
              $total3+=$row['bono_antiguedad']*$porcentaje/100; 
              $total_bonos_otros +=($monto_bonos_otros)*$porcentaje/100;
              $total4+=$row['total_ganado']*$porcentaje/100;
              $total_afp1+=$row['afp_1']*$porcentaje/100; 
              $total_afp2+=$row['afp_2']*$porcentaje/100; 
              // $total_aporteSolidario+=($a_solidario_13000+$a_solidario_25000+$a_solidario_35000)*$porcentaje/100; 
              $total_rc_iva+=$rc_iva*$porcentaje/100;              
              $total_otros_descuentos+=($sumaDescuentos_otros+$atrasos)*$porcentaje/100;
              $total_anticipos+=$anticipo*$porcentaje/100;
              $total5+=$row['monto_descuentos']*$porcentaje/100; 
              $total6+=$row['liquido_pagable']*$porcentaje/100; 
              $index++;
            }
            
      $html.='</tbody>';
$html.='<tfoot><tr class="table-title small bold text-center">'.
              '<td colspan="21"></td>'.
              '<td>'.number_format($total6).'</td>'; 
            $html.='</tr></tfoot>';

$html.=    '</table>';        
$html.='</body>'.
      '</html>';           
// echo $html;
descargarPDFHorizontal("Planilla_".$mes."_".$gestion,$html);
?>
