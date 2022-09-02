<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion3.php';
// require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../assets/libraries/CifrasEnLetras.php';
$dbh = new Conexion3();
// set_time_limit(300)
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//


// $numero_cuenta_pagadora=obtenerValorConfiguracionPlanillas(35);
$nombre_cuenta_pagadora=obtenerValorConfiguracionPlanillas(36);

// $numero_cuenta_pagadora=obtenerValorConfiguracionPlanillas(35);
// $nombre_cuenta_pagadora=obtenerValorConfiguracionPlanillas(36);
try{
    //====================================

    $sql="SELECT f.*,trp.nombre as motrivoretiro,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as nombre_personal,(select nombre from tipos_estado_civil tec where tec.codigo=p.cod_estadocivil)as estado_civil,p.fecha_nacimiento,p.direccion,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,identificacion,(select pd.abreviatura from personal_departamentos pd where pd.codigo=cod_lugar_emision)as lugar_emision,lugar_emision_otro,(
        select  CONCAT_WS('-',m.abreviatura,g.nombre)as fecha from planillas p join gestiones g on p.cod_gestion=g.codigo join meses m on p.cod_mes=m.codigo where p.codigo=f.cod_planilla1)as fecha1,(
        select  CONCAT_WS('-',m.abreviatura,g.nombre)as fecha from planillas p join gestiones g on p.cod_gestion=g.codigo join meses m on p.cod_mes=m.codigo where p.codigo=f.cod_planilla2)as fecha2,(
        select  CONCAT_WS('-',m.abreviatura,g.nombre)as fecha from planillas p join gestiones g on p.cod_gestion=g.codigo join meses m on p.cod_mes=m.codigo where p.codigo=f.cod_planilla3)as fecha3,f.meses_aguinaldo,f.dias_aguinaldo,f.dias_vacaciones_pagar,f.anios_indemnizacion,f.meses_indemnizacion,f.dias_indemnizacion,f.duodecimas,p.cuenta_bancaria
    FROM finiquitos f join personal p on f.cod_personal=p.codigo join tipos_retiro_personal trp on trp.codigo=f.cod_tiporetiro
    where  f.codigo =$codigo";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    //edad de personal
    $fecha_nac=$result["fecha_nacimiento"];
    if($fecha_nac=='0000-00-00'){
        $edad_personal="";
    }else{
        $fechaComoEntero = strtotime($fecha_nac);
        $anio = date("Y", $fechaComoEntero);
        $anioActual=date('Y');
        $edad_personal=$anioActual-$anio;
    }
    //vacaciones
    $dias_vacacion=$result['dias_vacaciones_pagar'];
    $vacacion_total=$result['vacaciones_dias_monto']+$result['vacaciones_duodecimas_monto'];
    //aguinaldos
    $aguinaldo_total=$result['aguinaldo_anios_monto']+$result['aguinaldo_meses_monto']+$result['aguinaldo_dias_monto'];
    $meses_aguinaldo=$result['meses_aguinaldo'];
    $dias_aguinaldo=$result['dias_aguinaldo'];

    //cantidad de dias,meses y años trabajados
    $datos=obtenerTiempoDosFechas2($result['fecha_ingreso'],$result['fecha_retiro']);
    $anios_servicio=$datos[0];
    $meses_servicio=$datos[1];
    $dias_servicio=$datos[2]+1;

    $sueldo_3_atras=$result['sueldo_3_atras'];
    $sueldo_2_atras=$result['sueldo_2_atras'];
    $sueldo_1_atras=$result['sueldo_1_atras'];

    $suma_sueldos=$sueldo_3_atras+$sueldo_2_atras+$sueldo_1_atras;
    $sueldo_promedio=$result["sueldo_promedio"];
    

    $anios_indemnizacion=$result["anios_indemnizacion"];
    $meses_indemnizacion=$result["meses_indemnizacion"];
    $dias_indemnizacion=$result["dias_indemnizacion"];

    $indemnizacion_anios_monto=$result["indemnizacion_anios_monto"];
    $indemnizacion_meses_monto=$result["indemnizacion_meses_monto"];
    $indemnizacion_dias_monto=$result["indemnizacion_dias_monto"];
    $suma_indemnizacion=$indemnizacion_anios_monto+$indemnizacion_meses_monto+$indemnizacion_dias_monto;

    $total_beneficios_sociales=$aguinaldo_total+$vacacion_total+$suma_indemnizacion+$result['desahucio_monto'];

    $cod_planilla1=$result["cod_planilla1"];
    $cod_planilla2=$result["cod_planilla2"];
    $cod_planilla3=$result["cod_planilla3"];

    $numero_cuenta_pagadora=$result["cuenta_bancaria"];

    $fecha_finiquito=$result["fecha_pago"];

    

    

    $cod_personal=$result["cod_personal"];
    $datos_planilla=obtenerdatos_planilla2($cod_personal,$cod_planilla1);
    $renumeracion_mensual1=$datos_planilla[0];
    $bono_antiguedad1=$datos_planilla[1];
    $domingos_feriados1=$datos_planilla[2];
    $bono_refrigerio1=$datos_planilla[3];
    $falla_caja1=$datos_planilla[4];
    $movilidad1=$datos_planilla[5];

    $bferiados1=$datos_planilla[6];
    $breintegro1=$datos_planilla[7];
    $bextras1=$datos_planilla[8];    

    $datos_planilla=obtenerdatos_planilla2($cod_personal,$cod_planilla2);
    $renumeracion_mensual2=$datos_planilla[0];
    $bono_antiguedad2=$datos_planilla[1];
    $domingos_feriados2=$datos_planilla[2];
    $bono_refrigerio2=$datos_planilla[3];
    $falla_caja2=$datos_planilla[4];
    $movilidad2=$datos_planilla[5];
    $bferiados2=$datos_planilla[6];
    $breintegro2=$datos_planilla[7];
    $bextras2=$datos_planilla[8];    
    $datos_planilla=obtenerdatos_planilla2($cod_personal,$cod_planilla3);
    $renumeracion_mensual3=$datos_planilla[0];
    $bono_antiguedad3=$datos_planilla[1];
    $domingos_feriados3=$datos_planilla[2];
    $bono_refrigerio3=$datos_planilla[3];
    $falla_caja3=$datos_planilla[4];
    $movilidad3=$datos_planilla[5];
    
    $bferiados3=$datos_planilla[6];
    $breintegro3=$datos_planilla[7];
    $bextras3=$datos_planilla[8];    


    $entero=floor($result["total_a_pagar"]);
    $decimal=$result["total_a_pagar"]-$entero;
    $centavos=round($decimal*100);
    if($centavos<10){
    $centavos="0".$centavos;
    }




$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFFiniquito.css" rel="stylesheet" />'.
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
                '</header>'.
                '<table  border="1" align="center" style="width: 100%;">                
                    <tbody>
                        <tr>
                            <td colspan="2">'.
                                '<table style="width: 100%;"    >'.
                                    '<tbody>'.
                                        '<tr>'.
                                            '<td width="25%" ><img class="imagen_izq" src="../assets/img/bolivia.jpg"><br><p class="header_texto_inf" style="font-size: 9px;">'.obtenerValorConfiguracionEmpresa(1).'<BR></p></td>'.
                                            '<td ><p  class="header_titulo_texto">FINIQUITO</p></td>'.
                                            '<td width="25%" style="text-align: right;"><img width="200px" src="../assets/img/ministerio.jpg"></td>'.
                                        '</tr>'.
                                        // '<tr>'.
                                        //     '<td><p class="header_texto_inf" style="font-size: 9px;">'.obtenerValorConfiguracionEmpresa(1).'<BR>'.obtenerValorConfiguracionEmpresa(8).'</p></td>'.
                                        //     '<td width="40%"><p  class="header_titulo_texto">FINIQUITO</p></td>'.
                                        //     '<td><p class="header_texto_inf">'.obtenerValorConfiguracionEmpresa(2).'</p></td>'.
                                        // '</tr>'.
                                    '</tbody>'.
                                '</table>'.
                            '</td>
                        </tr>'.
                        '<tr>'.
                            '<td colspan="2">I.- DATOS GENERALES</td>'.
                        '</tr>'.
                        '<tr>'.
                            '<td colspan="2">'.
                                '<table border="1" class="table_hijo">
                                
                                    <tr>
                                        <td colspan="4">RAZÓN SOCIAL O NOMBRE DE LA EMPRESA</td>                                    
                                        <td colspan="7" align="center">'.strtoupper(obtenerValorConfiguracionEmpresa(3)).'</td>
                                        <td style="width: 3%">1</td>
                                        <td style="width: 3%">2</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">RAMA DE ACTIVIDAD ECONÓMICA</td>                                    
                                        <td></td>
                                        <td></td>
                                        <td colspan="2">DOMICILIO</td>
                                        <td colspan="5" align="center">'.obtenerValorConfiguracionEmpresa(6).'</td>                                    
                                    </tr>
                                    <tr>
                                        <td colspan="4">NOMBRE DEL TRABAJADOR</td>
                                        <td colspan="7" align="center">'.strtoupper($result["nombre_personal"]).'</td>
                                        <td>1</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td >ESTADO CIVIL</td>
                                        <td colspan="2" align="center">'.$result["estado_civil"].'</td>
                                        <td></td>
                                        <td colspan="1">EDAD</td>
                                        <td align="center">'.$edad_personal.'</td>
                                        <td align="center">años</td>
                                        <td colspan="1">DOMICILIO</td>
                                        <td align="center" colspan="5">'.$result["direccion"].'</td>                                
                                    </tr>
                                    <tr>
                                        <td colspan="3">PROFESION U OCUPACIÓN</td>
                                        <td colspan="8" align="center">'.$result["cargo"].'</td>
                                        <td></td>
                                        <td></td>                                                                    
                                    </tr>
                                    <tr>
                                        <td>CI</td>
                                        <td colspan="3" align="center">'.$result["identificacion"].' '.$result['lugar_emision'].' '.$result['lugar_emision_otro'].'</td>
                                        <td colspan="2">FECHA DE INGRESO</td>
                                        <td colspan="2" align="center">'.date('d/m/Y',strtotime($result["fecha_ingreso"])).'</td>
                                        <td colspan="2">FECHA RETIRO</td>
                                        <td colspan="3" align="center">'.date('d/m/Y',strtotime($result["fecha_retiro"])).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">MOTIVO DEL RETIRO</td>
                                        <td colspan="3" align="center">'.$result["motrivoretiro"].'</td>
                                        <td ></td>
                                        <td colspan="4">REMUNERACION MENSUAL Bs</td>
                                        <td colspan="3" align="center">'.formatNumberDec($sueldo_promedio).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" >TIEMPO DE SERVICIO</td>
                                        <td >'.$anios_servicio.'</td>
                                        <td >AÑOS</td>
                                        <td colspan="2">'.$meses_servicio.'</td>
                                        <td colspan="2">MESES</td>
                                        <td >'.$dias_servicio.'</td>
                                        <td >DIAS</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>                                    
                                    </tr>
                                </table>'.
                            '</td>'.
                        '</tr>'.
                        '<tr>'.
                            '<td colspan="2">II.- LIQUIDACIÓN DE LA REMUNERACIÓN PROMEDIO INDEMNIZABLE EN BASE A LOS 3 ÚLTIMOS MESES</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td>A) MESES</td>
                                        <td align="center" colspan="2">'.$result["fecha3"].'</td>
                                        <td align="center" colspan="2">'.$result["fecha2"].'</td>
                                        <td align="center" colspan="2">'.$result["fecha1"].'</td>
                                        <td align="center" colspan="2">TOTALES</td>                                    
                                    </tr>
                                    <tr>
                                        <td>B)REMUNERACIÓN MENSUAL</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($renumeracion_mensual3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($renumeracion_mensual2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($renumeracion_mensual1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($renumeracion_mensual1+$renumeracion_mensual2+$renumeracion_mensual3).'</td>
                                    </tr>
                                    <tr>
                                        <td>BONO DE ANTIGÜEDAD</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_antiguedad3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_antiguedad2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_antiguedad1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_antiguedad1+$bono_antiguedad2+$bono_antiguedad3).'</td>                                    
                                    </tr>
                                    <tr>
                                        <td>Turno rotatorio domingo o feriado y/o reemplazos</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($domingos_feriados3+$bferiados3+$breintegro3+$bextras3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($domingos_feriados2+$bferiados2+$breintegro2+$bextras2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($domingos_feriados1+$bferiados1+$breintegro1+$bextras1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($domingos_feriados1+$bferiados1+$breintegro1+$bextras1+$domingos_feriados2+$bferiados2+$breintegro2+$bextras2+$domingos_feriados3+$bferiados3+$breintegro3+$bextras3).'</td>
                                    </tr>
                                    <tr>
                                        <td>Bono Refrigerio</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_refrigerio3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_refrigerio2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_refrigerio1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($bono_refrigerio1+$bono_refrigerio2+$bono_refrigerio3).'</td>
                                    </tr>
                                    <tr>
                                        <td>Falla de Caja</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($falla_caja3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($falla_caja2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($falla_caja1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($falla_caja1+$falla_caja2+$falla_caja3).'</td>
                                    </tr>
                                    <tr>
                                        <td>Bono Movilidad</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($movilidad3).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($movilidad2).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($movilidad1).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($movilidad1+$movilidad2+$movilidad3).'</td>
                                    </tr>
                                
                                    <tr>
                                        <td>TOTAL</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="center">'.formatNumberDec($sueldo_3_atras).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="center">'.formatNumberDec($sueldo_2_atras).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="center">'.formatNumberDec($sueldo_1_atras).'</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="center">'.formatNumberDec($suma_sueldos).'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>III .- TOTAL REMUNERACIÓN PROMEDIO INDEMNIZABLE (A + B) DIVIDIDO ENTRE 3:</td>'.
                            '<td style="width: 20%" class="text-right">Bs '.formatNumberDec($sueldo_promedio).'</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td colspan="11">C) DESAHUCIO  TRES MESES (EN CASO DE RETIRO FORZOSO)</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($result["desahucio_monto"]).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">D) INDEMNIZACIÓN POR TIEMPO DE TRABAJO:</td>
                                        <td style="width: 3%">DE</td>
                                        <td class="text-right" >'.$anios_indemnizacion.'</td>
                                        <td colspan="2">AÑOS</td>
                                        <td style="width: 3%">Bs </td>
                                        <td colspan="2" align="right">'.formatNumberDec($indemnizacion_anios_monto).'</td>
                                        <td style="width: 3%"></td>
                                        <td align="right">'.formatNumberDec($indemnizacion_anios_monto).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td class="text-right">'.$meses_indemnizacion.'</td>
                                        <td colspan="2">MESES</td>
                                        <td style="width: 3%">Bs</td>
                                        <td colspan="2" align="right">'.formatNumberDec($indemnizacion_meses_monto).'</td>
                                        <td style="width: 3%"></td>
                                        <td align="right">'.formatNumberDec($indemnizacion_meses_monto).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td class="text-right" class="text-right">'.$dias_indemnizacion.'</td>
                                        <td colspan="2">DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td colspan="2" align="right"> '.formatNumberDec($indemnizacion_dias_monto).'</td>
                                        <td style="width: 3%"></td>
                                        <td align="right">'.formatNumberDec($indemnizacion_dias_monto).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">AGUINALDO DE NAVIDAD</td>
                                        <td style="width: 3%">DE</td>
                                        <td class="text-right">'.$meses_aguinaldo.'</td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2" class="text-right">'.$dias_aguinaldo.'</td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($aguinaldo_total).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">VACACIÓN</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2" class="text-right">'.($dias_vacacion+$result['duodecimas']).'</td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="right">'.formatNumberDec($vacacion_total).'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">PRIMA LEGAL (SI CORRESPONDE)</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td >OTROS</td>
                                        <td colspan="10"></td>
                                        
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td >GESTION</td>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>                                    
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>                                
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>IV .- TOTAL BENEFICIOS SOCIALES: C + D</td>'.
                            '<td style="width: 20%" class="text-right">Bs '.formatNumberDec($total_beneficios_sociales).'</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td style="width: 20%">E) DEDUCCIONES:</td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td align="center"></td>
                                        
                                    </tr>
                                    <tr>
                                        <td style="width: 20%">RC-IVA 13 %</td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td class="text-right"> '.formatNumberDec($result['deducciones_total']).'</td>
                                        
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        
                                    </tr>
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>V. IMPORTE LÍQUIDO A PAGAR C + D - E =</td>'.
                            '<td style="width: 20%" class="text-right">Bs '.formatNumberDec($result["total_a_pagar"]).'</td>'.
                        '</tr>'.
                        
                    '</tbody>
                </table>'.            
            

                '<hr style="page-break-after: always;
                    border: none;
                    margin: 0;
                    padding: 0;">'.
            
                '<table border="1" align="center" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td ><p align="justify" style="padding-left:20px;padding-right:20px;"> FORMA DE PAGO: &nbsp;&nbsp;EFECTIVO (&nbsp;&nbsp;&nbsp;) &nbsp;&nbsp;&nbsp; TRANSFERENCIA ( X ) N° de cuenta '.$numero_cuenta_pagadora.' '.$nombre_cuenta_pagadora.'<br>
                                IMPORTE DE LA SUMA CANCELADA:  &nbsp;&nbsp; '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos <br>';
                                $html.='</p></td>
                        </tr>
                        <tr>
                            <td ><p align="justify" style="padding-left:20px;padding-right:20px;"> YO  &nbsp;&nbsp;&nbsp; '.$result['nombre_personal'].' <br>

                            MAYOR DE EDAD, CON C.I. Nº '.$result['identificacion'].' '.$result['lugar_emision'].' DECLARO QUE EN LA FECHA RECIBO A MI ENTERA
                            SATISFACCIÓN, EL IMPORTE DE Bs &nbsp '.formatNumberDec($result["total_a_pagar"]).' &nbsp&nbsp POR CONCEPTO DE LA LIQUIDACIÓN DE MIS BENEFICIOS SOCIALES, DE CONFORMIDAD CON LA LEY GENERAL DEL TRABAJO, SU DECRETO REGLAMENTARIO Y DISPOSICIONES CONEXAS.<br><br><br>

                            LUGAR Y FECHA &nbsp; La Paz, '.date('d',strtotime($fecha_finiquito)).' de '.nombreMes(date('m',strtotime($fecha_finiquito))).' de '.date('Y',strtotime($fecha_finiquito)).'
                            <p>
                            <br><br><br><br><br><br>
                            <table style="width: 100%;">
                                <tr>
                                <td><p align="center">_______________________________<br>INTERESADO</p></td>
                                <td><p align="center">_______________________________<br>GERENTE GENERAL</p></td>
                                </tr>
                            </table><br><br><br><br><br>
                            <table style="width: 100%;">
                                <tr>
                                <td><p align="center">_______________________________<br>Vo. Bo. MINISTERIO DE TRABAJO</p></td>
                                <td> <p align="center"> _______________________________<br>SELLO</p></td>
                                </tr>
                            </table>
                            <br>
                            
                            </td>
                        </tr>
                        <tr>
                            <td ><p class="titulo_texto" align="center">INSTRUCCIONES<p>
                                <table align="center" style="width: 80%;">
                                <tr>
                                    <td style="width: 5%;"  VALIGN="TOP"><p> 1. </p></td>
                                    <td>
                                        <p align="justify" >
                                            En todos los casos en los cuales proceda el pago de beneficios sociales y que no estén comprendidos en el despido por las causales en el Art. 16 de la Ley General del Trabajo y el Art. 9 de su Reglamento, el Finiquito de contrato se suscribirá en el presente FORMULARIO
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td VALIGN="TOP" style="width: 5%;"><p> 2. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Los señores Directores, Jefes Departamentales e Inspectores Regionales, son los únicos funcionarios facultados para revisar y refrendar todo finiquito de contrato de trabajo, con cuya intervención alcanzará la correspondiente eficacia jurídica, en aplicación del Art. 22 de la Ley General del Trabajo.<br><br>
                                            La intervención de cualquier otro funcionario del Ministerio de Trabajo y Microempresa carecerá de toda validez legal.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td VALIGN="TOP" style="width: 5%;"><p> 3. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Las partes intervinientes en la suscripción del presente FINIQUITO, deberán acreditar su identidad personal con los documentos señalados por ley.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td  VALIGN="TOP" style="width: 5%;"><p> 4. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Este Formulario no constituye Ley entre partes por su carácter esencialmente revisable, por lo tanto las cifras en él contenidas no causan estado ni revisten el sello de cosa juzgada.
                                        </p>
                                    </td>
                                </tr>
                                </table>
                                <br>
                                <br>
                                <br>
                            </td>
                        </tr>
                    </tbody>
                </table>'.
        '</body>'.
      '</html>';       
      // echo    $html;
descargarPDFFiniquito("COBOFAR - FINIQUITO ".$result["nombre_personal"],$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
