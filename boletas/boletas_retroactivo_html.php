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
		$numero_exa=alghoBolPersonal($cod_personal,$cod_planilla,0,$cod_gestion);
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


function generarHtmlBoletaSueldosMes($cod_planilla,$cod_gestion,$cod_mes,$cod_personal){
	require_once __DIR__.'/../conexion.php';
	require_once '../assets/phpqrcode/qrlib.php';
	require_once __DIR__.'/../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$sql_add="";
	if($cod_personal>0){
		$sql_add=" and p.codigo in ($cod_personal)";
	}
	set_time_limit(0);  
  $porcentaje_aport_afp=obtenerValorConfiguracionPlanillas(12);
	$porcentaje_aport_sol=obtenerValorConfiguracionPlanillas(15);
	set_time_limit(0);
  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);

  $sql="SELECT p.codigo, p.primer_nombre as nombres,CONCAT(p.paterno,' ', p.materno) as apellidos,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,pm.haber_basico_pactado,pm.haber_basico as haber_basico2,pm.total_ganado,pm.correlativo_planilla,pm.liquido_pagable,
    pm.dias_trabajados,pm.bono_antiguedad,pm.afp_1,pm.afp_2,pp.seguro_de_salud,pp.riesgo_profesional,pp.rc_iva,pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.anticipo,
    (select bm.monto from bonos_personal_mes bm where bm.cod_bono=11 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bnoches,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=12 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bdomingos,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=13 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bferiados,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=14 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bmovilidad,(select sum(bm.monto) from bonos_personal_mes bm where bm.cod_bono in (15,16) and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as brefrig,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=17 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as breintegro,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=18 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bventas,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=19 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bfallo,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=20 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bextras,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=1 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dprestamos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=2 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dinventarios,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=3 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dvencidos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=4 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as datrasos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=5 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dfaltante,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=6 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dotros,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=100 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as daportesind,(select pk.obs_reintegro from personal_kardex_mes pk where pk.cod_personal=p.codigo and pk.cod_gestion=$cod_gestion and pk.cod_mes=$cod_mes and pk.cod_estadoreferencial=1)as obsreintegro,p.ing_planilla
    FROM personal p
    join planillas_personal_mes pm on pm.cod_personalcargo=p.codigo
      join planillas_personal_mes_patronal pp on pp.cod_planilla=pm.cod_planilla and pp.cod_personal_cargo=pm.cod_personalcargo
    join areas a on pm.cod_area=a.codigo

    where pm.cod_planilla=$cod_planilla $sql_add 
    order by pm.correlativo_planilla";


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
	//generando Clave unico 
	// $nuevo_numero=$cod_personal+$cod_planilla+$cod_mes+$cod_gestion;
	// $cantidad_digitos=strlen($nuevo_numero);
	// $numero_adicional=$nuevo_numero+100+$cantidad_digitos;
	// $numero_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 
	$numero_exa=alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion);
	// echo $exa."_";
	// echo hexdec($exa);//se convierte hexa a decimal
	$codigo_generado=$cod_personal.".".$cod_planilla.".".$cod_mes.".".$cod_gestion.".".$numero_exa;
	// $cod_personal=$result['codigo'];

	$haber_basico_dias=$result['haber_basico2'];
	$bono_antiguedad=$result['bono_antiguedad'];
	$com_ventas=$result['bventas'];
	$fallo_caja=$result['bfallo'];
	$hrs_noche=$result['bnoches'];
	$hras_domingo=$result['bdomingos'];
	$hrs_feriado=$result['bferiados'];
	$hras_extraordianrias=$result['bextras'];
	$reintegro=$result['breintegro'];
	$movilidad=$result['bmovilidad'];
	$refrigerio=$result['brefrig'];
	$obs_reintegro=$result['obsreintegro'];

	$ing_planilla=$result['ing_planilla'];

	$Ap_Vejez=$result['seguro_de_salud'];
	$Riesgo_Prof=$result['riesgo_profesional'];
	$totalGanado=$result['total_ganado'];
	$ComAFP=$totalGanado*$porcentaje_aport_afp/100;
	$aposol=$totalGanado*$porcentaje_aport_sol/100;

	$aposol13=$result['a_solidario_13000'];
	$aposol25=$result['a_solidario_25000'];
	$aposol35=$result['a_solidario_35000'];

	$RC_IVA=$result['rc_iva'];
	$Anticipos=$result['anticipo'];
	$Prestamos=$result['dprestamos'];
	$Inventario=$result['dinventarios'];
	$Vencidos=$result['dvencidos'];
	$Atrasos=$result['datrasos'];
	$Faltantes_Caja=$result['dfaltante'];
	$Otros_Descuentos=$result['dotros'];
	$Aporte_Sindical=$result['daportesind'];

	$index_planilla=$result['correlativo_planilla'];

	$suma_ingresos=$haber_basico_dias+$bono_antiguedad+$com_ventas+$fallo_caja+$hrs_noche+$hras_domingo+$hrs_feriado+$hras_extraordianrias+$reintegro+$movilidad+$refrigerio;
	$suma_egresos=$Ap_Vejez+$Riesgo_Prof+$ComAFP+$aposol+$aposol13+$aposol25+$aposol35+$RC_IVA+$Anticipos+$Prestamos+$Inventario+$Vencidos+$Atrasos+$Faltantes_Caja+$Otros_Descuentos+$Aporte_Sindical;

	$liquido_pagable=$suma_ingresos-$suma_egresos;
	// $liquido_pagable=$result['liquido_pagable'];
	if($cod_personal==-1000){
		require 'boletas_html_aux.php';
		$html.='<hr>';
		require 'boletas_html_aux.php';	
	}else{
		require 'boletas_html_aux.php';
		$html.='<br>';
		// require 'boletas_html_aux.php';	
	}
	// $index_planilla++;
}

	$html.='</body>'.
	'</html>';
	$stmt=null;
	$dbh=null;
	return $html;
}

?>

