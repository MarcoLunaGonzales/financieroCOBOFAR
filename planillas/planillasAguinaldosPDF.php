<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
session_start();
set_time_limit(0);

// $cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
//nombre de unidad
$dbh = new Conexion();
// $mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);
  $stmtArea = $dbh->prepare("SELECT cod_area,cod_uo,(SELECT a.nombre from areas a where a.codigo=cod_area) as nombre_area
   from personal_area_distribucion
  where cod_estadoreferencial=1
  GROUP BY cod_area order by cod_uo,nombre_area");
  $stmtArea->execute();
  $stmtArea->bindColumn('cod_area', $cod_area_x);
  $stmtArea->bindColumn('cod_uo', $cod_uo_x);
  $stmtArea->bindColumn('nombre_area', $nombre_area_x);
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
              <td width="25%"><p>CORPORACION BOLIVIANA DE FARMACIAS S.A.<br>Av.Landaeta Nro. 836<br>La Paz - Bolivia<br>NIT:1022039027<br>N° PAT. 651-1-956</p></td>
              <td><center><span style="font-size: 13px"><b>PLANILLA DE PAGO DE AGUINALDO DE NAVIDAD</b></span><BR>  CORRESPONDIENTE A LA GESTION '.$gestion.'<br><b>(Expresado En Bolivianos)</b></center></td>
              <td width="25%"><center></center></td>
              </tr>
            </table>'.
         '</header>';
          $html.='<table class="table">'.
            '<thead>'.
            '<tr class="table-title bold text-center">                 
                      <td><small>N°</small></td> 
                      <td><small>Area</small></td>                   
                      <td><small>CI</small></td>
                      <td><small>Ex.</small></td>
                      <td><small>Fecha Nac.</small></td>
                      <td><small>Paterno</small></td>
                      <td><small>Materno</small></td>
                      <td><small>Nombres</small></td>                    
                      <td><small>Fecha Ingreso</small></td> 
                      <td><small>Cargo</small></td>
                      <td><small>Septiembre</small></td>
                      <td><small>Octubre</small></td>
                      <td><small>Noviembre</small></td>
                      <td><small>Sumatoria</small></td>
                      <td><small>Promedio Tot Gan</small></td>
                      <td><small>Meses Trabajados</small></td>
                      <td><small>Total Ganado</small></td>
                      <td width="8%" height="6%"><small>Firma</small></td>
                    </tr>';
            $html.='</thead><tbody>';
           
            $index=1;
            $sum_total_sueldo1=0;
            $sum_total_sueldo2=0;
            $sum_total_sueldo3=0;
            $sum_total_promedio_tp=0;
            $sum_total_aguinaldo_tp=0;
            $sum_ssumatoria_ganado=0;

            while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) 
            {
              $sql = "SELECT ppm.cod_personal,ppm.sueldo_1,ppm.sueldo_2,ppm.sueldo_3,ppm.meses_trabajados,ppm.dias_trabajados,pad.porcentaje, ppm.total_aguinaldo, p.primer_nombre as personal, p.paterno,p.materno, p.identificacion as doc_id, (select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as lug_emision,p.ing_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,ppm.dias_360,p.fecha_nacimiento,p.turno,ppm.sumatoria_ganado,ppm.promedio_ganado
              from planillas_aguinaldos_detalle ppm join personal_area_distribucion pad on ppm.cod_personal=pad.cod_personal and pad.cod_estadoreferencial=1 join personal p on ppm.cod_personal=p.codigo
              where cod_planilla=$codPlanilla and pad.cod_uo in ($cod_uo_x) and pad.cod_area=$cod_area_x order by p.turno,p.paterno";
                // echo $sql;
              $stmtPersonal   = $dbh->prepare($sql);
              $stmtPersonal->execute(); 
              $stmtPersonal->bindColumn('cod_personal', $cod_personalcargo);
              $stmtPersonal->bindColumn('sueldo_1', $sueldo_1);
              $stmtPersonal->bindColumn('sueldo_2', $sueldo_2);
              $stmtPersonal->bindColumn('sueldo_3', $sueldo_3);
              $stmtPersonal->bindColumn('meses_trabajados', $meses_trabajados);
              $stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
              $stmtPersonal->bindColumn('total_aguinaldo', $total_aguinaldo);
              $stmtPersonal->bindColumn('porcentaje', $porcentaje);
              $stmtPersonal->bindColumn('personal', $personal);
              $stmtPersonal->bindColumn('paterno', $paterno);
              $stmtPersonal->bindColumn('materno', $materno);
              $stmtPersonal->bindColumn('doc_id', $doc_id);
              $stmtPersonal->bindColumn('lug_emision', $lug_emision);
              $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
              $stmtPersonal->bindColumn('cargo', $cargo);
              $stmtPersonal->bindColumn('dias_360', $dias_360);
              $stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
              $stmtPersonal->bindColumn('turno', $turno);

              $stmtPersonal->bindColumn('sumatoria_ganado', $sumatoria_ganado);
              $stmtPersonal->bindColumn('promedio_ganado', $promedio_ganado);
              while ($row = $stmtPersonal->fetch()) 
              {  
                if($cod_uo_x<>1){
                  if($turno==1){// mañana
                    $nombreTurno=" TM";
                  }elseif($turno==2){
                    $nombreTurno=" TT";
                  }
                }else{
                  $nombreTurno="";
                }

                //dividiendo montos a su porcentaje respectivo
                $sueldo_1_tp=$sueldo_1*$porcentaje/100;
                $sueldo_2_tp=$sueldo_2*$porcentaje/100;
                $sueldo_3_tp=$sueldo_3*$porcentaje/100;
                // $sumatoria_ganado=$sueldo_1_tp+$sueldo_2_tp+$sueldo_3_tp;
                // $promedio_ganado=$sumatoria_ganado/3;
                // $dias_sueldo=$promedio_ganado/360*$dias_trabajados;
                // $meses_sueldo=$promedio_ganado/12*$meses_trabajados;
                $total_aguinaldo_tp=$total_aguinaldo*$porcentaje/100;
                $sum_total_sueldo1+=$sueldo_1_tp;
                $sum_total_sueldo2+=$sueldo_2_tp;
                $sum_total_sueldo3+=$sueldo_3_tp;
                $sum_ssumatoria_ganado+=$sumatoria_ganado;
                $sum_total_promedio_tp+=$promedio_ganado;
                $sum_total_aguinaldo_tp+=$total_aguinaldo_tp;
                $html.='<tr>                                                        
                  <td class="text-center small">'.$index.'</td>
                  <td class="text-left small">'.$nombre_area_x.' '.$nombreTurno.'</td>                    
                  <td class="text-center small">'.$doc_id.'</td>
                  <td class="text-center small">'.$lug_emision.'</td>
                  <td class="small">'.strftime('%d/%m/%Y',strtotime($fecha_nacimiento)).'</td>
                  <td class="text-left small">'.$paterno.'</td>
                  <td class="text-left small">'.$materno.'</td>
                  <td class="text-left small">'.$personal.'</td>
                  <td class="small">'.strftime('%d/%m/%Y',strtotime($ing_planilla)).'</td>
                  <td class="text-left small">'.$cargo.'</td>
                  <td class="small" >'.formatNumberDec($sueldo_1_tp).'</td>
                  <td class="small" >'.formatNumberDec($sueldo_2_tp).'</td>
                  <td class="small" >'.formatNumberDec($sueldo_3_tp).'</td>
                  <td class="small">'.formatNumberDec($sumatoria_ganado).'</td> 
                  <td class="small">'.formatNumberDec($promedio_ganado).'</td> 
                  <td class="text-center small">'.formatNumberDec($dias_360).'</td>
                  <td class="text-center small"><b>'.formatNumberDec($total_aguinaldo_tp).'</b></td>
                  <td width="8%" height="6%"></td>
                </tr>';
                $index+=1;
              }
            }
      $html.='</tbody>';
      $html.='<tfoot><tr>
        <td colspan="16" class="text-right small">TOTAL BS.</td>
        <td class="small"><b>'.formatNumberDec($sum_total_aguinaldo_tp).'</b></td>
        <td width="8%" height="6%"></td>
      </tr>
      </tfoot>';
$html.='</table><br><br><br>';
$html.='<table width="100%">
   <tr >
  <td width="25%"><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(26).'<BR>JEFE DE RECURSOS HUMANOS COBOFAR S.A.</p></center></td>
  <td><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(24).'<BR>GERENTE GENERAL COBOFAR S.A.</p></center></td>
  <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL COBOFAR S.A.</p></center></td>
  </tr>
</table>';
$html.='</body>'.
      '</html>';
$dbh=null;
$stmtBonos=null;
$stmtDescuento=null;
$stmtBonosOtrs=null;
$stmtDescuentos=null;

  //echo $html;

descargarPDFHorizontal("Planilla_aguinaldos_".$gestion,$html);
?>
