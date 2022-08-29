<?php
echo "<br><br><br><Br>";
echo "aqwui";
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
$dbh = new Conexion();
// $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion


error_reporting(E_ALL);
ini_set('display_errors',1);


    $tipo_beneficio=2;//tipo beneficio 1 finiquito//2 quinquenio
    $codigo = $_POST["codigo"];//cod quinquenio

    $fecha_pago = $_POST["fecha_pago"];//cod quinquenio
    $fecha_solicitud = $_POST["fecha_solicitud"];//cod quinquenio

    
    // $anios_trabajados_pagados = $_POST["anios_trabajados_pagados"];
    // $cod_tiporetiro = $_POST["cod_tiporetiro"];
    $datos_personal = explode('##', $_POST["cod_personal"]);
    $cod_personal =$datos_personal[0]; 
    $codigo_contrato="";
    $anios_trabajados_pagados = 0;


    
    // $anio_actual= date('Y');
    //$fecha_retiro = $_POST["fecha_retiro"];
    // echo $cod_personal."-".$codigo."-".$anios_trabajados_pagados;

    $cod_estadoreferencial =   1;    
    $created_by = $_SESSION['globalUser'];//$_POST["created_by"];
    $modified_by = $_SESSION['globalUser'];//$_POST["modified_by"];    
    //tipo_retiro
    // $stmttipoRetiro = $dbh->prepare("SELECT fecha_iniciocontrato,fecha_fincontrato,cod_tipocontrato From personal_contratos where codigo=$codigo_contrato");
    // $stmttipoRetiro->execute();
    // $resultRetiro =  $stmttipoRetiro->fetch();
    // $motivo_retiro = $cod_tiporetiro;
    // $fecha_retiro=$resultRetiro['fecha_fincontrato'];
    // $ing_contr_x = $resultRetiro['fecha_iniciocontrato'];
    // $cod_tipocontratox = $resultRetiro['cod_tipocontrato'];

    
    // $ing_contr_x = $resultRetiro['fecha_iniciocontrato'];
    // $cod_tipocontratox = $resultRetiro['cod_tipocontrato'];

    $motivo_retiro = 0;

    $sql="SELECT p.ing_contr
      FROM personal p
      WHERE p.codigo=$cod_personal";
       // echo "<br><br><br>".$sql;
    $stmtTipoContrato = $dbh->prepare($sql);
    $stmtTipoContrato->execute();
    $resultTipoContrato = $stmtTipoContrato->fetch();
    // $fecha_retiro_aux = "";
    $ing_contr_x = $resultTipoContrato['ing_contr'];
    // $fecha_retiro = date("Y-m-d",strtotime($ing_contr_x."+ 5 years - 1 days"));
    // $fecha_retiro_x=date('Y-m-01',strtotime($fecha_retiro));

    $fecha_retiro = $fecha_pago;
    $fecha_retiro_x=date('Y-m-01',strtotime($fecha_retiro));
   
    //fecha ingreso
    $anio_ingreso = date("Y", strtotime($ing_contr_x));
    $mes_ingreso = date("m", strtotime($ing_contr_x));
    $dia_ingreso = date("d", strtotime($ing_contr_x));

    //fecha retiro
    $anio_retiro = date("Y", strtotime($fecha_retiro));
    // $mes_retiro = date("m", strtotime($fecha_retiro));
    // $dia_retiro = date("d", strtotime($fecha_retiro));
    
    // echo "<br><br><br>".$fecha_retiro_x;
    if(date('d',strtotime($fecha_retiro))==date('t',strtotime($fecha_retiro))){
        $anio_retiro_1= date("Y",strtotime($fecha_retiro_x));
        $anio_retiro_2= date("Y",strtotime($fecha_retiro_x."- 1 month"));
        $anio_retiro_3= date("Y",strtotime($fecha_retiro_x."- 2 month"));
        // 
        $mes_retiro_1= date("m",strtotime($fecha_retiro_x));
        $mes_retiro_2= date("m",strtotime($fecha_retiro_x."- 1 month"));
        $mes_retiro_3= date("m",strtotime($fecha_retiro_x."- 2 month"));
    }else{
        $anio_retiro_1= date("Y",strtotime($fecha_retiro_x."- 1 month"));
        $anio_retiro_2= date("Y",strtotime($fecha_retiro_x."- 2 month"));
        $anio_retiro_3= date("Y",strtotime($fecha_retiro_x."- 3 month"));
        // 
        $mes_retiro_1= date("m",strtotime($fecha_retiro_x."- 1 month"));
        $mes_retiro_2= date("m",strtotime($fecha_retiro_x."- 2 month"));
        $mes_retiro_3= date("m",strtotime($fecha_retiro_x."- 3 month"));
    }
    //aun no hay datos de planillas

     // echo "<br><br><br><br>".$fecha_retiro."*".$mes_retiro_3."-".$mes_retiro_2."-".$mes_retiro_1;

    $cod_planilla_3_atras=obtener_id_planilla(codigoGestion($anio_retiro_3),($mes_retiro_3));
    $cod_planilla_2_atras=obtener_id_planilla(codigoGestion($anio_retiro_2),($mes_retiro_2));
    $cod_planilla_1_atras=obtener_id_planilla(codigoGestion($anio_retiro_1),($mes_retiro_1));

    
    $anios_aux=$anio_ingreso+$anios_trabajados_pagados;
    $ing_contr = $anios_aux.'-'.$mes_ingreso.'-'.$dia_ingreso;
    // echo $ing_contr."-".$ing_contr_x;
    $sueldo_3_atras=0;
    $sueldo_2_atras=0;
    $sueldo_1_atras=0;
    if($cod_planilla_1_atras>0){
        $sueldo_1_atras=obtenerSueldomes($cod_personal,$cod_planilla_1_atras);
    }
    if($cod_planilla_2_atras>0){
        $sueldo_2_atras=obtenerSueldomes($cod_personal,$cod_planilla_2_atras);    
    }
    if($cod_planilla_3_atras>0){
        $sueldo_3_atras=obtenerSueldomes($cod_personal,$cod_planilla_3_atras);   
    }



    $sueldo_promedio=($sueldo_3_atras+$sueldo_2_atras+$sueldo_1_atras)/3;
    //desahucio 3 meses
    
    $desahucio_3_meses=0;//buscar valor

    //indemnizacion ****
    $fecha_retiro_xy=date("Y-m-d",strtotime($fecha_retiro."+ 1 day")); 

    $date1 = new DateTime($ing_contr_x);
    $date2 = new DateTime($fecha_retiro_xy);
    $diff = $date1->diff($date2);
    
    // $anios_antiguedad=$diff->y;
    $anios_antiguedad=5;
    $meses_indemnizacion=0;
    $dias_indemnizacion=0;
    // $quinquenios_pagados=obtenerQuinquenioPagadoPersonal($cod_personal);
    
    $anios_indemnizacion=$anios_antiguedad;
    $indemnizacion_anios_monto=$sueldo_promedio*$anios_indemnizacion;
    // $indemnizacion_meses_monto=($sueldo_promedio/12)*$meses_indemnizacion;
    // $indemnizacion_dias_monto=($sueldo_promedio/360)*$dias_indemnizacion;
    $indemnizacion_meses_monto=0;
    $indemnizacion_dias_monto=0;
    $suma_indemnizacion=$indemnizacion_anios_monto+$indemnizacion_meses_monto+$indemnizacion_dias_monto;

    //calculo de Aguinaldo ***
    // $fecha_retiro_xy=date("Y-m-d",strtotime($fecha_retiro."+ 1 day")); 
    // $dateTimeAsg1 = date_create($anio_retiro.'-01-01'); 
    // $dateTimeAsg2 = date_create($fecha_retiro_xy); 
    // $differenceAsg = date_diff($dateTimeAsg1, $dateTimeAsg2);
    // $aguinaldo_meses = $differenceAsg->m;
    // $aguinaldo_dias = $differenceAsg->d;
    $aguinaldo_meses=0;
    $aguinaldo_dias=0;
    // echo "<br>".$anio_retiro.'-01-01'."<br>";
    // echo $fecha_retiro_xy."<br>";


    // $aguinaldo_meses = $mes_retiro_1;
    // $aguinaldo_dias=$dia_retiro;
    $aguinaldo_anios_monto=0;//AGUINALDO NO PAGADOS
    $aguinaldo_meses_monto=$sueldo_promedio/12*$aguinaldo_meses;
    $aguinaldo_dias_monto=($sueldo_promedio/12/30)*$aguinaldo_dias;

    $suma_aguinaldo=$aguinaldo_meses_monto+$aguinaldo_dias_monto;


    //vacaciones
    $vacaciones_pagar = 0;
    $duodecimas = 0;
    $otros_pagar = 0;

    $vacaciones_dias=0;
    $vacaciones_doudecimas=0;
    $vacaciones_dias_monto=$sueldo_promedio/30*$vacaciones_dias;
    $vacaciones_duodecimas_monto=$sueldo_promedio/30*$vacaciones_doudecimas;
    $suma_vacaciones=$vacaciones_dias_monto+$vacaciones_duodecimas_monto;
    //desahucio
    $desahucio=0;//buscar datos
    $desahucio_monto=$sueldo_promedio*$desahucio;
    //otros
    $servicios_adicionales=0;//buscar datos
    $subsidios_meses=0;//buscar datos
    $finiquitos_a_cuenta=0;//buscar datos

    // $suma_otros=$servicios_adicionales+$subsidios_meses+$finiquitos_a_cuenta;
    $suma_otros=0;
    //deducciones
    $porcentaje_deducciones_por_vacaciones=obtenerValorConfiguracion(14);
    $deducciones_total=($suma_vacaciones*$porcentaje_deducciones_por_vacaciones/100);
    //total
    $total_a_pagar=$desahucio_3_meses+$suma_indemnizacion+$suma_aguinaldo+$suma_vacaciones+$desahucio_monto+$suma_otros-$deducciones_total;
    
    

    $observaciones=date('d/m/Y',strtotime($ing_contr_x))." al ".date("d/m/Y",strtotime($ing_contr_x."+ 5 years - 1 days"));
    // echo "vacaciones_duodecimas_monto:".$vacaciones_duodecimas_monto."<br>";
    // echo "suma_aguinaldo:".$suma_aguinaldo."<br>";
    // echo "suma_vacaciones:".$suma_vacaciones."<br>";
    // echo "deducciones_total:".$deducciones_total."<br>";
    // echo "total_a_pagar:".$total_a_pagar."<br>";
    
    if ($_POST["codigo"] == 0){
        $stmt = $dbh->prepare("INSERT INTO finiquitos(cod_personal,fecha_ingreso,fecha_retiro,cod_tiporetiro,sueldo_promedio,sueldo_3_atras,sueldo_2_atras,sueldo_1_atras,indemnizacion_anios_monto,indemnizacion_meses_monto,indemnizacion_dias_monto,aguinaldo_anios_monto,aguinaldo_meses_monto,aguinaldo_dias_monto,vacaciones_dias_monto,vacaciones_duodecimas_monto,desahucio_monto,servicios_adicionales,subsidios_meses,finiquitos_a_cuenta,deducciones_total,total_a_pagar,observaciones,cod_estadoreferencial,created_by,modified_by,cod_contrato,anios_pagados,dias_vacaciones_pagar,duodecimas,otros_pagar,cod_planilla1,cod_planilla2,cod_planilla3,meses_aguinaldo,dias_aguinaldo,anios_indemnizacion,meses_indemnizacion,dias_indemnizacion,tipo_beneficio,fecha_solicitud,fecha_pago) 
        values (:cod_personal,:ing_contr,:fecha_retiro,:motivo_retiro,:sueldo_promedio,:sueldo_3_atras,:sueldo_2_atras,:sueldo_1_atras,:indemnizacion_anios_monto,:indemnizacion_meses_monto,:indemnizacion_dias_monto,:aguinaldo_anios_monto,:aguinaldo_meses_monto,:aguinaldo_dias_monto,:vacaciones_dias_monto,:vacaciones_duodecimas_monto,:desahucio_monto,:servicios_adicionales,:subsidios_meses,:finiquitos_a_cuenta,:deducciones_total,:total_a_pagar,:observaciones,:cod_estadoreferencial,:created_by,:modified_by,:codigo_contrato, :anios_pagados, :dias_vacaciones_pagar, :duodecimas,:otros_pagar,:cod_planilla1,:cod_planilla2,:cod_planilla3,:meses_aguinaldo,:dias_aguinaldo,:anios_indemnizacion,:meses_indemnizacion,:dias_indemnizacion,:tipo_beneficio,:fecha_solicitud,:fecha_pago)");
        //Bind
        $stmt->bindParam(':cod_personal', $cod_personal);
        $stmt->bindParam(':ing_contr',$ing_contr);
        $stmt->bindParam(':fecha_retiro',$fecha_retiro);
        $stmt->bindParam(':motivo_retiro',$motivo_retiro);
        $stmt->bindParam(':sueldo_promedio',$sueldo_promedio);
        $stmt->bindParam(':sueldo_3_atras',$sueldo_3_atras);
        $stmt->bindParam(':sueldo_2_atras',$sueldo_2_atras);
        $stmt->bindParam(':sueldo_1_atras',$sueldo_1_atras);
        $stmt->bindParam(':indemnizacion_anios_monto',$indemnizacion_anios_monto);
        $stmt->bindParam(':indemnizacion_meses_monto',$indemnizacion_meses_monto);
        $stmt->bindParam(':indemnizacion_dias_monto',$indemnizacion_dias_monto);
        $stmt->bindParam(':aguinaldo_anios_monto',$aguinaldo_anios_monto);
        $stmt->bindParam(':aguinaldo_meses_monto',$aguinaldo_meses_monto);
        $stmt->bindParam(':aguinaldo_dias_monto',$aguinaldo_dias_monto);
        $stmt->bindParam(':vacaciones_dias_monto',$vacaciones_dias_monto);
        $stmt->bindParam(':vacaciones_duodecimas_monto',$vacaciones_duodecimas_monto);
        $stmt->bindParam(':desahucio_monto',$desahucio_monto);
        $stmt->bindParam(':servicios_adicionales',$servicios_adicionales);
        $stmt->bindParam(':subsidios_meses',$subsidios_meses);
        $stmt->bindParam(':finiquitos_a_cuenta',$finiquitos_a_cuenta);
        $stmt->bindParam(':deducciones_total',$deducciones_total);
        $stmt->bindParam(':total_a_pagar',$total_a_pagar);
        $stmt->bindParam(':observaciones',$observaciones);
        $stmt->bindParam(':cod_estadoreferencial',$cod_estadoreferencial);
        $stmt->bindParam(':created_by',$created_by);
        $stmt->bindParam(':modified_by',$modified_by);
        $stmt->bindParam(':codigo_contrato',$codigo_contrato);

        $stmt->bindParam(':anios_pagados',$anios_trabajados_pagados);
        $stmt->bindParam(':dias_vacaciones_pagar',$vacaciones_pagar);
        $stmt->bindParam(':duodecimas',$duodecimas);
        $stmt->bindParam(':otros_pagar',$otros_pagar);


        $stmt->bindParam(':cod_planilla1',$cod_planilla_1_atras);
        $stmt->bindParam(':cod_planilla2',$cod_planilla_2_atras);
        $stmt->bindParam(':cod_planilla3',$cod_planilla_3_atras);

        $stmt->bindParam(':meses_aguinaldo',$aguinaldo_meses);
        $stmt->bindParam(':dias_aguinaldo',$aguinaldo_dias);

        $stmt->bindParam(':anios_indemnizacion',$anios_indemnizacion);
        $stmt->bindParam(':meses_indemnizacion',$meses_indemnizacion);
        $stmt->bindParam(':dias_indemnizacion',$dias_indemnizacion);
        $stmt->bindParam(':tipo_beneficio',$tipo_beneficio);

        $stmt->bindParam(':fecha_solicitud',$fecha_solicitud);
        $stmt->bindParam(':fecha_pago',$fecha_pago);


        $flagSuccess=$stmt->execute();
        //sacamos el codigo de finiquito;
        // $stmtCodFiniquito = $dbh->prepare("SELECT codigo from finiquitos where cod_contrato=$codigo_contrato");
        // $stmtCodFiniquito->execute();
        // $resultFiniquitoCod =  $stmtCodFiniquito->fetch();
        // $cod_finiquito_x = $resultFiniquitoCod['codigo'];
        // $stmtUpdateContrato = $dbh->prepare("UPDATE personal_contratos set cod_finiquito=$cod_finiquito_x where codigo=$codigo_contrato");
        // $stmtUpdateContrato->execute();
        
        showAlertSuccessError($flagSuccess,'?opcion=quinquenios_list');

        //$stmt->debugDumpParams();
    } else {//update
        $stmt = $dbh->prepare("UPDATE finiquitos set cod_personal=$cod_personal,fecha_ingreso='$ing_contr',fecha_retiro='$fecha_retiro',cod_tiporetiro='$motivo_retiro',sueldo_promedio='$sueldo_promedio',sueldo_3_atras='$sueldo_3_atras',sueldo_2_atras='$sueldo_2_atras',sueldo_1_atras='$sueldo_1_atras',indemnizacion_anios_monto='$indemnizacion_anios_monto',indemnizacion_meses_monto='$indemnizacion_meses_monto',indemnizacion_dias_monto='$indemnizacion_dias_monto',aguinaldo_anios_monto='$aguinaldo_anios_monto',aguinaldo_meses_monto='$aguinaldo_meses_monto',aguinaldo_dias_monto='$aguinaldo_dias_monto',vacaciones_dias_monto='$vacaciones_dias_monto',vacaciones_duodecimas_monto='$vacaciones_duodecimas_monto',desahucio_monto='$desahucio_monto',servicios_adicionales='$servicios_adicionales',subsidios_meses='$subsidios_meses',finiquitos_a_cuenta='$finiquitos_a_cuenta',deducciones_total='$deducciones_total',total_a_pagar='$total_a_pagar',observaciones='$observaciones',modified_by=$modified_by,anios_pagados='$anios_trabajados_pagados',dias_vacaciones_pagar='$vacaciones_pagar',duodecimas='$duodecimas',otros_pagar='$otros_pagar',cod_planilla1='$cod_planilla_1_atras',cod_planilla2='$cod_planilla_2_atras',cod_planilla3='$cod_planilla_3_atras',meses_aguinaldo='$aguinaldo_meses',dias_aguinaldo='$aguinaldo_dias',anios_indemnizacion='$anios_indemnizacion',meses_indemnizacion='$meses_indemnizacion',dias_indemnizacion='$dias_indemnizacion',tipo_beneficio='$tipo_beneficio',fecha_solicitud='$fecha_solicitud',fecha_pago='$fecha_pago'
         where codigo = $codigo");
        $flagSuccess=$stmt->execute();
        
        showAlertSuccessError($flagSuccess,'?opcion=quinquenios_list');

    }//si es insert o update
    
  
?>