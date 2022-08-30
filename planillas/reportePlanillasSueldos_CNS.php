<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  $cod_mes = $_GET["cod_mes"];//
  $tipo = $_GET["tipo"];//

  if($tipo==1){//CNS
    $sqlTipo=" and p.cod_cajasalud=1";
  }elseif($tipo==2){//CPS
    $sqlTipo=" and p.cod_cajasalud=2";
  }

  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);

$fecha_x=$gestion.'-'.$cod_mes.'-01';
$fecha_cns=date("Y-m-t",strtotime($fecha_x."+ 1 month")); 
$datos_fecha=explode("-", $fecha_cns);
//sacamos el ultimo día habil
$dia_ultimo=$datos_fecha[2];
$dia_semana=date("N",strtotime($fecha_cns)); 
if ($dia_semana == 7) {
    $dia_ultimo=$dia_ultimo-2;
}
if ($dia_semana == 6) {
    $dia_ultimo=$dia_ultimo-1;
}


$porcentaje_aport_afp=obtenerValorConfiguracionPlanillas(12);
$porcentaje_aport_sol=obtenerValorConfiguracionPlanillas(15);
//html del reporte
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF_planillas.css" rel="stylesheet" />'.
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
              <td width="50%"><span style="font-size: 15px;">CORPORACION BOLIVIANA DE FARMACIAS S.A.</span><br>NIT:1022039027<br>Av.Landaeta Nro. 836<br>La Paz - Bolivia<br>N° Patronal C.N.S. 01 - 652 - 00289</td>
              <td><span style="font-size: 15px">PLANILLA CORRESPONDIENTE AL MES DE '.$mes.' '.$gestion.'</span><br>EXPRESADO EN BOLIVIANOS<BR>S.S. LARGO PLAZO<BR>TELF.: 2 - 413051</td>
              </tr>
            </table>'.
         '</header>';

$html.='

<table class="table" >
    <thead>
        <tr class="table-title bold text-center" style="font-size: 8px !important;">
        <td>N°</td> 
        <td>CI</td>
        <td>Lugar de Emision</td>
        <td>PATERNO</td>
        <td>MATERNO</td>
        <td>NOMBRE</td>
        <td>NACION<br>ALIDAD</td>
        <td>FECHA NAC.</td>
        <td>DIAS TRAB</td>
        <td>CARGO</td>
        <td>FECHA ING R.A. INASES 129/2016</td>
        <td>HABER BASICO DIAS TRAB</td>
        <td>OTROS INGRESOS</td>
        <td>BONO ANT</td>
        <td>TOTAL GANADO</td>
        <td>DESCTO AFPS 12.71%</td>
        <td>OTROS DESCTS.</td>
        <td>TOTAL DESCTO</td>
        <td>LIQUIDO PAG</td>
      </tr>                                  
    </thead>
    <tbody>';
      $total_haber_basico=0;
      $total_bonos_otros=0;
      $total_bono_antiguedad=0;
      $total_total_ganado=0;
      $total_aporte_caja=0;
      $total_descuentos_otros=0;
      $total_total_descuentos=0;
      $total_liquido_pagable=0;

      $index=1;
      $sql = "SELECT a.nombre,p.identificacion,( select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision)as lugar_emision,p.fecha_nacimiento,p.paterno,p.materno,p.primer_nombre,
      (select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,p.ing_planilla,ppm.dias_trabajados,ppm.haber_basico,ppm.haber_basico_pactado,ppm.bono_antiguedad,ppm.total_ganado,ppm.afp_1,ppm.afp_2,pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.anticipo,pp.rc_iva,ppm.liquido_pagable,pp.riesgo_profesional,ppm.bonos_otros,ppm.descuentos_otros,monto_descuentos
      from personal p
      join planillas_personal_mes ppm on ppm.cod_personalcargo=p.codigo
      join planillas_personal_mes_patronal pp on pp.cod_planilla=ppm.cod_planilla and pp.cod_personal_cargo=ppm.cod_personalcargo
      join areas a on p.cod_area=a.codigo
      where  ppm.cod_planilla=$cod_planilla  $sqlTipo
      order by p.cod_unidadorganizacional,a.nombre,p.paterno";
        // echo $sql."<br><br>";
        $stmtPersonal = $dbh->prepare($sql);
        $stmtPersonal->execute(); 
        $stmtPersonal->bindColumn('nombre', $nombre);
        $stmtPersonal->bindColumn('identificacion', $identificacion);
        $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
        $stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
        $stmtPersonal->bindColumn('paterno', $paterno);
        $stmtPersonal->bindColumn('materno', $materno);
        $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
        $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
        $stmtPersonal->bindColumn('cargo', $cargo);
        $stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
        $stmtPersonal->bindColumn('haber_basico_pactado', $haber_basico_pactado);
        $stmtPersonal->bindColumn('haber_basico', $haber_basico);
        $stmtPersonal->bindColumn('bono_antiguedad', $bono_antiguedad);
        $stmtPersonal->bindColumn('total_ganado', $total_ganado);
        $stmtPersonal->bindColumn('anticipo', $anticipo);
        $stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
        $stmtPersonal->bindColumn('afp_1', $afp_1);
        $stmtPersonal->bindColumn('afp_2', $afp_2);
        $stmtPersonal->bindColumn('a_solidario_13000', $a_solidario_13000);
        $stmtPersonal->bindColumn('a_solidario_25000', $a_solidario_25000);
        $stmtPersonal->bindColumn('a_solidario_35000', $a_solidario_35000);
        $stmtPersonal->bindColumn('bonos_otros', $bonos_otros);
        $stmtPersonal->bindColumn('descuentos_otros', $descuentos_otros);
        $stmtPersonal->bindColumn('monto_descuentos', $monto_descuentos);
        while ($row = $stmtPersonal->fetch()) 
        {  
          // $aporte_caja=$afp_1+$afp_2;
          $aporte_caja=$afp_1+$afp_2+$a_solidario_13000+$a_solidario_25000+$a_solidario_35000;
          $total_descuentos=$monto_descuentos;
          $ComAFP=$total_ganado*$porcentaje_aport_afp/100;
          $aposol=$total_ganado*$porcentaje_aport_sol/100;
          $total_haber_basico+=$haber_basico;
          $total_bonos_otros+=$bonos_otros;
          $total_bono_antiguedad+=$bono_antiguedad;
          $total_total_ganado+=$total_ganado;
          $total_aporte_caja+=$aporte_caja;
          $total_descuentos_otros+=$descuentos_otros;
          $total_total_descuentos+=$total_descuentos;
          $total_liquido_pagable+=$liquido_pagable;
          
          $html.='<tr style="font-size: 8px !important;">
            <td>'.$index.'</td> 
            <td>'.$identificacion.'</td>
            <td>'.$lugar_emision.'</td>
            <td>'.$paterno.'</td>
            <td>'.$materno.'</td>
            <td>'.$primer_nombre.'</td>
            <td>BOLIVIANA</td>
            <td class="text-center">'.strftime("%d/%m/%Y",strtotime($fecha_nacimiento)).'</td>
            <td class="text-center">'.$dias_trabajados.'</td>
            <td>'.$cargo.'</td>
            <td class="text-center">'.strftime("%d/%m/%Y",strtotime($ing_planilla)).'</td>
            <td class="text-right">'.formatNumberDec($haber_basico).'</td>
            <td class="text-right">'.formatNumberDec($bonos_otros).'</td>
            <td class="text-right">'.formatNumberDec($bono_antiguedad).'</td>
            <td class="text-right">'.formatNumberDec($total_ganado).'</td>
            <td class="text-right">'.formatNumberDec($aporte_caja).'</td>
            <td class="text-right">'.formatNumberDec($descuentos_otros).'</td>
            <td class="text-right">'.formatNumberDec($total_descuentos).'</td>
            <td class="text-right">'.formatNumberDec($liquido_pagable).'</td>
            </tr>';
              $index+=1;
          }


          $html.='<tr style="font-size: 8px !important;">
            <td colspan="11" class="text-center"><b>TOTAL</b></td> 
            
            <td class="text-right"><b>'.formatNumberDec($total_haber_basico).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_bonos_otros).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_bono_antiguedad).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_total_ganado).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_aporte_caja).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_descuentos_otros).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_total_descuentos).'</b></td>
            <td class="text-right"><b>'.formatNumberDec($total_liquido_pagable).'</b></td>
            </tr>';

             $html.='
              </tbody>
            </table><br><br><br><br><br><br><br><br><br><br><br><br>';


$html.='<table width="100%">
  <tr >
  <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL COBOFAR S.A.</p></center></td>
  <td><center><p>SELLO</p></center></td>
  <td width="25%"><center><p>LA PAZ, '.$dia_ultimo.' DE '.strtoupper(nombreMes($datos_fecha[1])).' DE '.$datos_fecha[0].'</p></center></td>
  </tr>
</table>';

//echo $html;
descargarPDFHorizontal("Planilla_CNS_".$mes.'_'.$gestion,$html);

