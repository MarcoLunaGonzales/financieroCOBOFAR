<?php



$periodo=0;
foreach ($array_periodo as $periodo) {
    
    $fecha_hasta=date('Y-m-d',strtotime($desde.'+'.$periodo.' day'));

    $sqlFechaEstadoCuentadet="and e.fecha BETWEEN '$desde 00:00:00' and '$fecha_hasta 23:59:59'";

    $sql="SELECT e.fecha,e.cod_cuentaaux,sum(e.monto)as monto_ec,ca.nombre,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber
        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuentadet and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by e.fecha"; //ca.nombre, 
    // echo $sql;
    $stmtUO = $dbh->prepare($sql);
    $stmtUO->execute();
    $index=1;
    while ($row = $stmtUO->fetch()) {
        $monto_ecX=$row['monto_ec'];
        $cod_cuentaauxX=$row['cod_cuentaaux'];
        $nombreX=$row['nombre'];
        $tipoDebeHaberX=$row['tipoDebeHaber'];   
        //PAGADO
        $sql="SELECT sum(e.monto)as monto_ec
        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen>0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) and e.cod_cuentaaux in ($cod_cuentaauxX) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY e.cod_cuentaaux  order by e.fecha";
        $stmt_d = $dbh->prepare($sql);
        $stmt_d->execute();
        $monto_ecD=0;
        while ($row_d = $stmt_d->fetch()) {
            $monto_ecD=$row_d['monto_ec'];
        }
        if($tipoDebeHaberX==2){//proveedor
            // $totalCredito=$totalCredito+$monto_ecX;
            // $totalDebito=$totalDebito+$monto_ecD;
            // $saldo_X=$monto_ecX-$monto_ecD;
            // $html.='<tr class="bg-white" >
            //     <td class="text-center small">'.$index.'</td>
            //     <td class="text-left small">'.$nombreX.'</td>
            //     <td class="text-right small">'.formatNumberDec($saldo_X).'</td>
            // </tr>'; 
        }else{//cliente
            $totalCredito=$totalCredito+$monto_ecD;
            $totalDebito=$totalDebito+$monto_ecX;
            $saldo_X=$monto_ecX-$monto_ecD;

            $html.='<td class="text-right small">'.formatNumberDec($saldo_X).'</td>';
        }
        $index++;
    }


}
$html.='<td class="text-right small">'.formatNumberDec($saldo_X).'</td>';

?>