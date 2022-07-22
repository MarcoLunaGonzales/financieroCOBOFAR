<?php

require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once 'configModule.php';
$dbh = new Conexion();
session_start();
$codGestionActiva=$_SESSION['globalGestion'];
$globalNombreGestion=$_SESSION['globalNombreGestion'];
if (isset($_POST["cod_mes"])) {
    $codMes=$_POST["cod_mes"];
    // $fechai=$globalNombreGestion.'-'.$codMes.'-01';
    // $L = new DateTime($fechai); 
    // $fechaf= $L->format('Y-m-t');
        //descuetos
    $sql="SELECT d.codigo from descuentos d where d.tipo_descuento in (2,3) and d.cod_estadoreferencial=1";
    // echo $sql;
    $stmtDescuentos = $dbh->prepare($sql);
    $stmtDescuentos->execute();
    $array_descuentos=[];$i=0;
    while ($rowdescuentos = $stmtDescuentos->fetch(PDO::FETCH_ASSOC)) {
        $array_descuentos[$i]=$rowdescuentos['codigo'];
        $i++;
    }
    $contadorDescuentos=count($array_descuentos);

    $codEstado=1;
    $cod_descuento_as=100; //aporte al sindicato
    $dias_trabajados_mes = obtenerValorConfiguracionPlanillas(22); //por defecto
    
    //borramos logicamente
    $stmte = $dbh->prepare("DELETE from bonos_personal_mes WHERE cod_gestion=$codGestionActiva and cod_mes=$codMes");
    $flagSuccess=$stmte->execute();
    // $cantidadDomingos=domingosMes($fechai,$fechaf);
    $sql="SELECT pk.cod_personal,pk.cod_gestion,pk.cod_mes,pk.faltas,pk.faltas_sin_descuento,pk.dias_vacacion,pk.dias_trabajados,pk.domingos_trabajados_normal,pk.feriado_normal,pk.noche_normal,pk.domingo_reemplazo,pk.feriado_reemplazo,pk.ordianrio_reemplazo,pk.hxdomingo_extras,pk.hxferiado_extras,pk.hxdnnormal_extras,pk.reintegro,p.haber_basico
    from personal_kardex_mes pk join personal p on pk.cod_personal=p.codigo 
    where pk.cod_mes=$codMes and pk.cod_gestion=$codGestionActiva and pk.cod_estadoreferencial=1";//and p.codigo=1
     // echo $sql."<br><br><br>";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('cod_personal', $cod_personal);
    $stmt->bindColumn('cod_gestion', $cod_gestion);
    $stmt->bindColumn('cod_mes', $cod_mes);
    $stmt->bindColumn('faltas', $faltas);
    $stmt->bindColumn('faltas_sin_descuento', $faltas_sin_descuento);
    $stmt->bindColumn('dias_vacacion', $dias_vacacion);
    $stmt->bindColumn('dias_trabajados', $dias_trabajados);
    $stmt->bindColumn('domingos_trabajados_normal', $domingos_trabajados_normal);
    $stmt->bindColumn('feriado_normal', $feriado_normal);
    $stmt->bindColumn('noche_normal', $noche_normal);//ok
    $stmt->bindColumn('domingo_reemplazo', $domingo_reemplazo);
    $stmt->bindColumn('feriado_reemplazo', $feriado_reemplazo);
    $stmt->bindColumn('ordianrio_reemplazo', $ordianrio_reemplazo);
    $stmt->bindColumn('hxdomingo_extras', $hxdomingo_extras);
    $stmt->bindColumn('hxferiado_extras', $hxferiado_extras);
    $stmt->bindColumn('hxdnnormal_extras', $hxdnnormal_extras);
    $stmt->bindColumn('reintegro', $reintegro);
    $stmt->bindColumn('haber_basico', $haber_basico);
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
        //borramos logicamente descuento tipo no visible en plantilla
        $sql="DELETE FROM descuentos_personal_mes WHERE cod_gestion=$codGestionActiva and cod_mes=$codMes and cod_personal=$cod_personal and cod_descuento in (select d.codigo from descuentos d where d.tipo_descuento in (2,3))";//tipo descuento 1 vista plantilla, se carga desde la plantilla 
        // echo $sql;
        $stmtDesDelete = $dbh->prepare($sql);
        $flagSuccessDelete= $stmtDesDelete->execute();
        $sqlBonosPactados="SELECT cod_bono,monto from bonos_personal_pactados where cod_personal=$cod_personal and cod_estadoreferencial=1 and tipo_bono_desc=1";
        // echo $sqlBonosPactados;
        $stmtPactados = $dbh->prepare($sqlBonosPactados);
        $stmtPactados->execute();
        $stmtPactados->bindColumn('cod_bono', $cod_bonopactado);
        $stmtPactados->bindColumn('monto', $montopactado);
        while ($rowPactados = $stmtPactados->fetch(PDO::FETCH_BOUND)) {
            $monto_bono=$montopactado;
            //PROCESO DE BONOS
            switch ($cod_bonopactado) {
                case 11://NOCHES PACTADOS
                    //$hras_nocturas=($haber_basico/30/8)*2;
                    $monto_bono=$noche_normal*$montopactado;
                break;
                case 12://DOMINGOS PACTADOS
                    $monto_bono=($montopactado*($domingos_trabajados_normal+$domingo_reemplazo))+(($montopactado/12)*$hxdomingo_extras);
                break;
                case 13://FERIADOS PACTADOS
                    $monto_bono=($montopactado*($feriado_normal+$feriado_reemplazo))+(($montopactado/12)*$hxferiado_extras);
                break;
                case 14://movilidad
                    $monto_bono=$montopactado*($dias_trabajados-$faltas-$faltas_sin_descuento-$dias_vacacion)/$dias_trabajados_mes;//bono movilidad
                break;
                case 15://REFRIGERIO DE LUNES A SABADO PACTADOS
                    $monto_bono=$montopactado*($dias_trabajados-$faltas-$faltas_sin_descuento-$dias_vacacion+$ordianrio_reemplazo);//REFRIGERIO DE LUNES A SABADO PACTADOS
                break;
                case 16://REFRIGERIO DOMINGOS Y FERIADOS PACTADOS 
                    $monto_bono=$montopactado*($domingos_trabajados_normal+$feriado_normal+$domingo_reemplazo+$feriado_reemplazo);
                break;
                case 17://REINTEGRO
                    $monto_bono=$reintegro;
                break;
                case 20://Dias ordinarios HORAS EXTRAS
                    $monto_x_dia=$haber_basico/30;
                    $monto_bono=($monto_x_dia*$ordianrio_reemplazo)+(($monto_x_dia/8)*$hxdnnormal_extras);
                break;
                //14 MOVILIDAD
                //18 COMISION DE VENTAS
                // case 19://FALLO DE CAJA
                // case 20://HORAS EXTRAS
            }
            $sqlinsert="INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto, indefinido,cod_estadoreferencial) 
                            VALUES ($cod_bonopactado,$cod_personal,$codGestionActiva,$codMes,$monto_bono,0,$codEstado)";
            //echo $sqlinsert;
            $stmtInsert = $dbh->prepare($sqlinsert);
            $flagSuccess=$stmtInsert->execute();
        }
        // PROCESO DE DESCUENTOS **  los de tipo_descuento 1 ya fueron cargados al cargar la plantilla
        // echo "<br>";
        // var_dump($array_descuentos);
        for ($j=0; $j < $contadorDescuentos; $j++) { 
            $codigoDescuento=$array_descuentos[$j];
            if($codigoDescuento==$cod_descuento_as){// Aporte al sindicato
                $montoDescuento=obtenerBonoDescuentoPactado($cod_personal,$cod_descuento_as,2);
            }else{
                $montoDescuento=obtenerDescuentoPersonalMes($cod_personal,$codMes,$globalNombreGestion,$codigoDescuento);
            }
            $sqlInsert="INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
            VALUES ($codigoDescuento,$cod_personal,$codGestionActiva,$codMes,'$montoDescuento',$codEstado)";
            // echo $sqlInsert."<br>";
            $stmtSindicato=$dbh->prepare($sqlInsert);
            $flagSuccess=$stmtSindicato->execute();
        }

        // Aporte al sindicato
        // $aporte_sindicato=obtenerBonoDescuentoPactado($cod_personal,$cod_descuento_as,2);
        // $stmtSindicato=$dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
        //     VALUES ($cod_descuento_as,$cod_personal,$codGestionActiva,$codMes,$aporte_sindicato,$codEstado)");
        // $flagSuccess=$stmtSindicato->execute();
        //descuentos desde modulo de descuentos
        // $sql="SELECT ddm.monto,(select t.cod_descuento from tipos_descuentos_conta t where t.codigo=dd.cod_tipodescuento) as cod_descuento
        // from descuentos_conta d  join descuentos_conta_detalle dd on d.codigo=dd.cod_descuento join descuentos_conta_detalle_mes  ddm on ddm.cod_descuento_detalle=dd.codigo
        // where d.cod_estado=3 and ddm.mes=$codMes and ddm.gestion=$globalNombreGestion and dd.cod_personal=$cod_personal";

        // $sql="SELECT sum(ddm.monto)as monto,t.cod_descuento as cod_descuento
        // from descuentos_conta d  join descuentos_conta_detalle dd on d.codigo=dd.cod_descuento join tipos_descuentos_conta t on t.codigo=dd.cod_tipodescuento join descuentos_conta_detalle_mes  ddm on ddm.cod_descuento_detalle=dd.codigo
        // where d.cod_estado=3 and ddm.mes=$codMes and ddm.gestion=$globalNombreGestion and dd.cod_personal=$cod_personal
        // GROUP BY t.cod_descuento";
        //  // echo $sql;
        // $stmtDescuento = $dbh->prepare($sql);
        // $stmtDescuento->execute();
        // while ($rowdescuento = $stmtDescuento->fetch(PDO::FETCH_ASSOC)) {
        //      $montoDescuento=$rowdescuento['monto'];
        //      $cod_descuento=$rowdescuento['cod_descuento'];
        //     // Aporte al sindicato
        //     // $aporte_sindicato=obtenerBonoDescuentoPactado($cod_personal,$cod_descuento_as,2);
        //     $stmtSindicato=$dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
        //         VALUES ($cod_descuento,$cod_personal,$codGestionActiva,$codMes,$montoDescuento,$codEstado)");
        //     $flagSuccess=$stmtSindicato->execute();
        // }
    }
    echo 1;
}
else{
    echo 0;
}

?>