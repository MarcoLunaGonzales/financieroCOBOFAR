<?php

function generarHtmlBoletaRetroactivo($cod_planilla,$cod_gestion,$cod_personal){
	require_once __DIR__.'/../conexion.php';
	require_once '../assets/phpqrcode/qrlib.php';
	require_once __DIR__.'/../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();

	$sql_add="";
	if($cod_personal>0){
		$sql_add=" and p.codigo=$cod_personal";
	}
	set_time_limit(0);  
  $gestion=nameGestion($cod_gestion);
  $sql="SELECT p.codigo,prd.correlativo_planilla,p.paterno,p.materno,p.primer_nombre,prd.ing_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,prd.total_ganado,prd.ap_vejez,prd.riesgo_prof,prd.com_afp,prd.aporte_sol,prd.total_descuentos,prd.liquido_pagable,prd.haber_basico_nuevo
      from  personal p join planillas_retroactivos_detalle prd on p.codigo=prd.cod_personal join areas a on prd.cod_area=a.codigo
      where prd.cod_planilla=$cod_planilla $sql_add 
      order by correlativo_planilla";
    $stmt = $dbh->prepare($sql);
    //Ejecutamos
    $stmt->execute();
    // $result = $stmt->fetch();
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
    $codigo_generado="";
    // $index_planilla=1;
	while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$cod_personal=$result['codigo'];
		$numero_exa=alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion);
		//generando Clave unico 
		// $nuevo_numero=$cod_personal+$cod_planilla+$cod_gestion;
		// $cantidad_digitos=strlen($nuevo_numero);
		// $numero_adicional=$nuevo_numero+100+$cantidad_digitos;
		// $numero_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 		
		// echo hexdec($exa);//se convierte hexa a decimal
		$codigo_generado=$cod_personal.".".$cod_planilla.".".$cod_gestion.".".$numero_exa;
		//*** codigo
		$index_planilla=$result['correlativo_planilla'];
		$ap_vejez=$result['ap_vejez'];
		$riesgo_prof=$result['riesgo_prof'];
		$com_afp=$result['com_afp'];
		$aporte_sol=$result['aporte_sol'];
		$suma_ingresos=$result['total_ganado'];
		$suma_egresos=$result['total_descuentos'];
		$liquido_pagable=$result['liquido_pagable'];
		require 'boletas_retroactivo_html_aux.php';
		$html.='<br>';
	}
	$html.='</body>'.
	'</html>';
	$stmt=null;
	$dbh=null;
	return $html;
}
?>

