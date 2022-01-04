<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


require_once '../layouts/bodylogin2.php';

set_time_limit(0);

$cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];

$mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);

$date = $gestion."-".$cod_mes."-01";
$dia_planilla_indemnizacion=date("t", strtotime($date));
$fecha_planilla_indemnizacion=date("t/m/Y", strtotime($date));

$fecha_planilla="01-".$cod_mes."-".$gestion;
$mes3=$cod_mes;
$mes2 = date("m",strtotime($fecha_planilla."- 1 month"));
$mes1 = date("m",strtotime($fecha_planilla."- 2 month"));


//nombre de unidad
$dbh = new Conexion();


//html del reporte
$html = '';
// $html.='<html>'.
//          '<head>'.
//              '<!-- CSS Files -->'.
//              '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
//              '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
//            '</head>';
// $html.='<body>'.
//         '<script type="text/php">'.
//       'if ( isset($pdf) ) {'. 
//         '$font = Font_Metrics::get_font("helvetica", "normal");'.
//         '$size = 9;'.
//         '$y = $pdf->get_height() - 24;'.
//         '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
//         '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
//       '}'.
//     '</script>';
// $html.=  '<header class="header">'.            
//             '<table width="100%">
//               <tr>
//               <td width="25%"><p>CORPORACION BOLIVIANA DE FARMACIAS S.A.<br>Av.Landaeta Nro. 836<br>La Paz - Bolivia<br>NIT:1022039027<br>N° PAT. 651-1-956</p></td>
//               <td><center><span style="font-size: 13px"><b>PREVISION INDEMNIZACIONES</b></span><BR>  CORRESPONDIENTE AL '.$dia_planilla_indemnizacion." de ".$mes." del ".$gestion.'<br><b>(Expresado En Bolivianos)</b></center></td>
//               <td width="25%"><center></center></td>
//               </tr>
//             </table>'.
//          '</header>';
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="50" height="40" src="../assets/img/favicon.png">
            </div>
             <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>-->
             <h4 class="card-title text-center">PREVISION INDEMNIZACIONES <?=$gestion?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
              <?php

          // $html.='<table class="table">'.
            $html.='<thead>'.
            '<tr class="table-title bold text-center">                 
                <td><small>N°</small></td> 
                <td><small>Area</small></td>                   
                <td><small>Area Filtro</small></td>
                <td><small>CI</small></td>
                <td><small>Ex.</small></td>
                <td><small>Paterno</small></td>
                <td><small>Materno</small></td>
                <td><small>Nombres</small></td>
                <td><small>Cargo</small></td>
                <td><small>Fecha Ingreso</small></td> 
                <td><small>FECHA PREVISION</small></td> 
                <td><small>'.strtoupper(nombreMes($mes1)).'</small></td>
                <td><small>'.strtoupper(nombreMes($mes2)).'</small></td>
                <td><small>'.strtoupper(nombreMes($mes3)).'</small></td>
                <td><small>Promedio</small></td>
                <td><small>Años Pendiente Indemn.</small></td>
                <td><small>Mes</small></td>
                <td><small>Día</small></td>
                <td><small>Años</small></td>
                <td><small>Meses</small></td>
                <td><small>Días</small></td>
                <td><small>Total Indemnizacion</small></td>
                <td><small>Previsión</small></td>
              </tr>';
            $html.='</thead><tbody>';
            $index=1;
     
            $sum_total_indemnizacion=0;
            $sum_total_prevision=0;

              $sql = "SELECT a.nombre as area,p.cod_unidadorganizacional,p.identificacion,(select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as lug_emision,p.paterno,p.materno,p.primer_nombre,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.ing_planilla,pid.sueldo_1,pid.sueldo_2,pid.sueldo_3,pid.promedio_ganado,pid.anios_antiguedad,pid.quinquenios_pagados,pid.anios_indemnizacion,pid.meses_indemnizacion,pid.dias_indemnizacion,pid.monto_anios,pid.monto_meses,pid.monto_dias,pid.total_indemnizacion,pid.prevision,p.turno
              from planillas_indemnizaciones_detalle pid join personal p on pid.cod_personal=p.codigo join areas a on pid.cod_area=a.codigo
              where pid.cod_planilla=$codPlanilla
              order by p.cod_unidadorganizacional,a.nombre,p.turno,p.paterno";
                // echo $sql;
              $stmtPersonal   = $dbh->prepare($sql);
              $stmtPersonal->execute(); 
              $stmtPersonal->bindColumn('area', $area);
              $stmtPersonal->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
              $stmtPersonal->bindColumn('identificacion', $identificacion);
              $stmtPersonal->bindColumn('lug_emision', $lug_emision);
              $stmtPersonal->bindColumn('paterno', $paterno);
              $stmtPersonal->bindColumn('materno', $materno);
              $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
              $stmtPersonal->bindColumn('cargo', $cargo);
              $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
              $stmtPersonal->bindColumn('sueldo_1', $sueldo_1);
              $stmtPersonal->bindColumn('sueldo_2', $sueldo_2);
              $stmtPersonal->bindColumn('sueldo_3', $sueldo_3);
              $stmtPersonal->bindColumn('promedio_ganado', $promedio_ganado);
              $stmtPersonal->bindColumn('anios_antiguedad', $anios_antiguedad);
              $stmtPersonal->bindColumn('quinquenios_pagados', $quinquenios_pagados);
              $stmtPersonal->bindColumn('anios_indemnizacion', $anios_indemnizacion);
              $stmtPersonal->bindColumn('meses_indemnizacion', $meses_indemnizacion);
              $stmtPersonal->bindColumn('dias_indemnizacion', $dias_indemnizacion);
              $stmtPersonal->bindColumn('monto_anios', $monto_anios);
              $stmtPersonal->bindColumn('monto_meses', $monto_meses);
              $stmtPersonal->bindColumn('monto_dias', $monto_dias);
              $stmtPersonal->bindColumn('total_indemnizacion', $total_indemnizacion);
              $stmtPersonal->bindColumn('prevision', $prevision);
              $stmtPersonal->bindColumn('turno', $turno);
              while ($row = $stmtPersonal->fetch()) 
              {  
                if($cod_unidadorganizacional<>1){
                  $area_filtro="SUCURUSAL";
                  if($turno==1){// mañana
                    $nombreTurno=" TM";
                  }elseif($turno==2){
                    $nombreTurno=" TT";
                  }
                }else{
                  $area_filtro="ADMINISTRATIVO";
                  $nombreTurno="";
                }

                $sum_total_prevision+=$prevision;
                $sum_total_indemnizacion+=$total_indemnizacion;

                $html.='<tr>                                                        
                  <td class="text-center small">'.$index.'</td>
                  <td class="text-left small">'.$area.' '.$nombreTurno.'</td>
                  <td class="text-left small">'.$area_filtro.'</td>
                  <td class="text-center small">'.$identificacion.'</td>
                  <td class="text-center small">'.$lug_emision.'</td>
                  <td class="text-left small">'.$paterno.'</td>
                  <td class="text-left small">'.$materno.'</td>
                  <td class="text-left small">'.$primer_nombre.'</td>
                  <td class="text-left small">'.$cargo.'</td>
                  <td class="small">'.strftime('%d/%m/%Y',strtotime($ing_planilla)).'</td>
                  <td class="small">'.$fecha_planilla_indemnizacion.'</td>
                  <td class="small" >'.formatNumberDec($sueldo_1).'</td>
                  <td class="small" >'.formatNumberDec($sueldo_2).'</td>
                  <td class="small" >'.formatNumberDec($sueldo_3).'</td>
                  <td class="small">'.formatNumberDec($promedio_ganado).'</td> 
                  <td class="small">'.$anios_indemnizacion.'</td> 
                  <td class="small">'.$meses_indemnizacion.'</td> 
                  <td class="small">'.$dias_indemnizacion.'</td> 
                  <td class="text-center small">'.formatNumberDec($monto_anios).'</td>
                  <td class="text-center small">'.formatNumberDec($monto_meses).'</td>
                  <td class="text-center small">'.formatNumberDec($monto_dias).'</td>
                  <td class="text-center small"><b>'.formatNumberDec($total_indemnizacion).'</b></td>
                  <td class="text-center small"><b>'.formatNumberDec($prevision).'</b></td>
                </tr>';
                $index+=1;
              }
            
      $html.='</tbody>';
      $html.='<tfoot><tr>
        <td colspan="21" class="text-right small">TOTAL BS.</td>
        <td class="small"><b>'.formatNumberDec($sum_total_indemnizacion).'</b></td>
        <td class="small"><b>'.formatNumberDec($sum_total_prevision).'</b></td>
      </tr>
      </tfoot>';
$html.='</table><br><br><br>';

echo $html;
// $html.='<table width="100%">
//    <tr >
//   <td width="25%"><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(26).'<BR>JEFE DE RECURSOS HUMANOS COBOFAR S.A.</p></center></td>
//   <td><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(24).'<BR>GERENTE GENERAL COBOFAR S.A.</p></center></td>
//   <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL COBOFAR S.A.</p></center></td>
//   </tr>
// </table>';
// $html.='</body>'.
//       '</html>';
$dbh=null;
$stmtPersonal=null;

  // echo $html;

//descargarPDFHorizontal("Planilla_aguinaldos_".$gestion,$html);
?>

            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>