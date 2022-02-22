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
              <td width="25%"><p>CORPORACION BOLIVIANA DE FARMACIAS S.A.<br>NIT:1022039027<br>Av.Landaeta Nro. 836<br>La Paz - Bolivia<br>N° Patronal C.N.S. 01 - 652 - 00289</p></td>
              <td><center><span style="font-size: 13px"><b>PLANILLA CORRESPONDIENTE AL MES DE '.$mes.' '.$gestion.'<br><b>EXPRESADO EN BOLIVIANOS<BR>S.S. LARGO PLAZO<BR>TELF.: 2 - 413051</b></center></td>
              <td width="25%"><center></center></td>
              </tr>
            </table>'.
         '</header>';

$html.='

<table class="table" >
    <thead>
        <tr class="table-title bold text-center">                 
        <td><small>N°</small></td> 
        <td><small>CI</small></td>
        <td><small>Lugar de Emision</small></td>
        <td><small>PATERNO</small></td>
        <td><small>MATERNO</small></td>
        <td><small>NOMBRE</small></td>
        <td><small>NACION<br>ALIDAD</small></td>
        <td><small>FECHA NAC.</small></td>
        <td><small>DIAS TRAB</small></td>
        <td><small>CARGO</small></td>
        <td><small>FECHA ING R.A. INASES 129/2016</small></td>
        <td><small>HABER BASICO DIAS TRAB</small></td>
        <td><small>OTROS INGRESOS</small></td>
        <td><small>BONO ANT</small></td>
        <td><small>TOTAL GANADO</small></td>
        <td><small>DESCTO AFPS 12.71%</small></td>
        <td><small>OTROS DESCTS.</small></td>
        <td><small>TOTAL DESCTO</small></td>
        <td><small>LIQUIDO PAG</small></td>
        <td><small>FIRMA</small></td>
      </tr>                                  
    </thead>
    <tbody>';
      
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
          
          $html.='<tr>
            <td class="small"><small>'.$index.'</small></td> 
            <td class="small"><small>'.$identificacion.'</small></td>
            <td class="small"><small>'.$lugar_emision.'</small></td>
            <td class="small"><small>'.$paterno.'</small></td>
            <td class="small"><small>'.$materno.'</small></td>
            <td class="small"><small>'.$primer_nombre.'</small></td>
            <td class="small"><small>BOLIVIANA</small></td>
            <td class="text-center small"><small>'.strftime("%d/%m/%Y",strtotime($fecha_nacimiento)).'</small></td>
            <td class="text-center small"><small>'.$dias_trabajados.'</small></td>
            <td class="small"><small>'.$cargo.'</small></td>
            <td class="text-center small"><small>'.strftime("%d/%m/%Y",strtotime($ing_planilla)).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($haber_basico).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($bonos_otros).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($bono_antiguedad).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($total_ganado).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($aporte_caja).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($descuentos_otros).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($total_descuentos).'</small></td>
            <td class="text-right small"><small>'.formatNumberDec($liquido_pagable).'</small></td>
            <td class="small"><small></small></td>
            </tr>';
              $index+=1;
          }
             $html.='
              </tbody>
            </table>';
//echo $html;
descargarPDFHorizontal("Planilla_CNS_".$mes.'_'.$gestion,$html);

