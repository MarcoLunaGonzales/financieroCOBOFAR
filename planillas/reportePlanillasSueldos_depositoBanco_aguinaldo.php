<?php
require_once '../conexion3.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
	$dbh = new Conexion3();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	// $cod_mes = $_GET["cod_mes"];//
	$tipo = $_GET["tipo"];//
	
	if($tipo==1){
		$sql_add="and pad.cuenta_bancaria>0";
		$string_foot="TOTAL PERSONAL CON CUENTA EN EL BANCO";
		$string_titulo="DEPÓSITO AL BANCO";
	}else{
		$sql_add="and pad.cuenta_bancaria=0";
		$string_foot="TOTAL PERSONAL SIN CUENTA";
		$string_titulo="PERSONAL SIN CUENTA EN EL BANCO";
	}

	// $mes=strtoupper(nombreMes($cod_mes));
	$gestion=nameGestion($cod_gestion);

	// $sqlArea="SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area
	// from personal_area_distribucion
	// where cod_estadoreferencial=1
	// GROUP BY cod_area order by cod_uo,nombre_area";
	// // echo $sqlArea;
	// $stmtArea = $dbh->prepare($sqlArea);
	// $stmtArea->execute();
	// $stmtArea->bindColumn('cod_area', $cod_area_x);
	// $stmtArea->bindColumn('nombre_area', $nombre_area_x);


	$swBonosOtro=false;
	$sqlBonos = "SELECT cod_bono,(select b.abreviatura from bonos b where b.codigo=cod_bono) as nombre_bono
        from bonos_personal_mes 
        where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)
        order by cod_bono ASC";
  // echo $sqlBonos;
	$stmtBonos = $dbh->prepare($sqlBonos);
	$stmtBonos->execute();                      
	$stmtBonos->bindColumn('cod_bono',$cod_bono);
	$stmtBonos->bindColumn('nombre_bono',$nombre_bono);
	while ($row = $stmtBonos->fetch()) 
	{ 
		$arrayBonos[] = $cod_bono;
		$swBonosOtro=true;
	}

	$sqlDescuento = "SELECT cod_descuento,(select d.abreviatura from descuentos d where d.codigo=cod_descuento) as nombre_descuentos
          from descuentos_personal_mes 
          where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)
          order by cod_descuento ASC";
  $stmtDescuento = $dbh->prepare($sqlDescuento);
  $stmtDescuento->execute();                      
  $stmtDescuento->bindColumn('cod_descuento',$cod_descuento);
  $stmtDescuento->bindColumn('nombre_descuentos',$nombre_descuentos);
  while ($row = $stmtDescuento->fetch()) 
  { 
    $arrayDescuentos[] = $cod_descuento;
    $swDescuentoOtro=true;
  }

//html del reporte
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF2.css" rel="stylesheet" />'.
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
$html.='<header class="header">'.            
    '<table width="100%">
      <tr>
      <td width="28%"><small><p>CORPORACION BOLIVIANA DE FARMACIAS<br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:1022039027</p></small></td>
      <td><center><span style="font-size: 13px"><b>'.$string_titulo.'</b></span><BR>Correspondientes a '.$gestion.'<br><b>EXPRESADA EN BOLIVIANOS</b></center></td>
      <td width="25%"><center></center></td>
      </tr>
    </table>'.
 '</header>';

$html.='<table class="table">
    <thead>
    	<tr class="table-title small bold text-center">                  
        <td width="2%">NRO</td> 
        <td width="15%">SUCURSAL</td>
        <td >PATERNO</td>
        <td >MATERNO</td>
        <td width="15%">NOMBRES</td>
        <td width="15%">CARGO</td>
        <td >CUENTA</td>
        <td >IMPORTE</td>
        <td>FIRMA</td>
    	</tr>                                  
    </thead>
    <tbody>';
			$index=1;
			$sum_total_basico=0;
			$sum_total_b_antiguedad=0;
			$sum_total_o_bonos=0;
			$sum_total_m_bonos=0;
			$sum_total_t_ganado=0;
			$sum_total_m_aportes=0;
			$sum_total_atrasos=0;
			$sum_total_anticipos=0;
			$sum_total_dotaciones=0;
			$sum_total_o_descuentos=0;
			$sum_total_m_descuentos=0;
			$sum_total_l_pagable=0;
			$sum_total_a_patronal=0;
			//$dias_trabajados_asistencia=30;//ver datos
				// $dias_trabajados_por_defecto = obtenerValorConfiguracionPlanillas(22); //por defecto
			$dias_trabajados_por_defecto=30;
			
				$sql = "SELECT ppm.cod_personal,ppm.total_aguinaldo,pad.primer_nombre,pad.paterno,pad.materno,
				(select c.nombre from cargos c where c.codigo=pad.cod_cargo)as cargo,pad.cuenta_bancaria,(select a.nombre from areas a where a.codigo=pad.cod_area) as areas
					from planillas_aguinaldos_detalle ppm,personal pad
					where ppm.cod_personal=pad.codigo and cod_planilla=$cod_planilla and pad.cod_estadoreferencial=1 and pad.cod_estadopersonal=1  $sql_add order by pad.cod_unidadorganizacional,areas,pad.paterno";
					// echo $sql."<br><br>";
				$stmtPersonal = $dbh->prepare($sql);
				$stmtPersonal->execute();	
				$stmtPersonal->bindColumn('cod_personalcargo', $cod_personalcargo);
				$stmtPersonal->bindColumn('primer_nombre', $nombrePersonal);
				$stmtPersonal->bindColumn('paterno', $paterno);
				$stmtPersonal->bindColumn('materno', $materno);
				$stmtPersonal->bindColumn('total_aguinaldo', $total_aguinaldo);
				$stmtPersonal->bindColumn('cuenta_bancaria', $cuenta_bancaria);
				$stmtPersonal->bindColumn('areas', $areas);
				$stmtPersonal->bindColumn('cargo', $cargo);
				while ($row = $stmtPersonal->fetch()) 
				{
		            $liquido_pagable_tp=$total_aguinaldo;
		            $sum_total_l_pagable+=$liquido_pagable_tp;
		        	$html.='<tr>
		              <td class="text-center small"><small>'.$index.'</small></td>
		              <td class="text-left small"><small>'.$areas.'</small></td>
		              <td class="text-left small"><small>'.$paterno.'</small></td>
		              <td class="text-left small"><small>'.$materno.'</small></td>
		              <td class="text-left small"><small>'.$nombrePersonal.'</small></td>
		              <td class="text-left small"><small>'.$cargo.'</small></td>
		              <td class="text-right small"><small>'.$cuenta_bancaria.'</small></td>
		              <td class="text-right small"><small>'.formatNumberDec($liquido_pagable_tp).'</small></td>
		              <td class="text-left small"><small></small></td>
		          	</tr>';
		            $index+=1;
		      	}
		                   
    $html.='</tbody>
    <tfoot>
    	<tr>                  
	        <th colspan="7" class="text-right">'.$string_foot.'</th>
	        <th colspan="2" class="text-right">'.formatNumberDec($sum_total_l_pagable).'</th>
    	</tr>
  	</tfoot>               
 	</table><br><br><br>';

 	$html.='<table width="100%">
 	<tr><td><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(23).'<br>JEFE DE SISTEMAS</p></center></td></tr>
 	</table>';
$html.='</body>'.
      '</html>';

      $dbh=null;
      $stmtBonos=null;
      $stmtDescuento=null;
      $stmtPersonal=null;
// echo $html;
descargarPDFBoleta("Planilla_".$mes."_".$gestion,$html);
?>

