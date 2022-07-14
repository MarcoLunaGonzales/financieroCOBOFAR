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
     //echo $sql."<br><br><br>";
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
        //borramos logicamente descuento tipo aporte a sindicato
        $stmtDesDelete = $dbh->prepare("DELETE FROM descuentos_personal_mes WHERE cod_descuento=$cod_descuento_as and cod_gestion=$codGestionActiva and cod_mes=$codMes and cod_personal=$cod_personal"  );
        $stmtDesDelete->execute();
        $sqlBonosPactados="SELECT cod_bono,monto from bonos_personal_pactados where cod_personal=$cod_personal and cod_estadoreferencial=1 and tipo_bono_desc=1";
        // echo $sqlBonosPactados;
        $stmtPactados = $dbh->prepare($sqlBonosPactados);
        $stmtPactados->execute();
        $stmtPactados->bindColumn('cod_bono', $cod_bonopactado);
        $stmtPactados->bindColumn('monto', $montopactado);
        while ($rowPactados = $stmtPactados->fetch(PDO::FETCH_BOUND)) {
            $monto_bono=$montopactado;
            //bonos
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

            //****Solo descuento de tipo aporte al sindicato
            $aporte_sindicato=obtenerBonoDescuentoPactado($cod_personal,$cod_descuento_as,2);
            $stmtSindicato=$dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                VALUES ($cod_descuento_as,$cod_personal,$codGestionActiva,$codMes,$aporte_sindicato,$codEstado)");
            $flagSuccess=$stmtSindicato->execute();
        }
    }
    echo 1;
}
else{
    echo 0;
}

?>