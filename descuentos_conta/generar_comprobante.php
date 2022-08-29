<?php

$dbh_detalle = new Conexion2();
$dbh_cabecera = new Conexion();
// $codigo=$_GET["codigo"];
session_start();
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fecha_actual=date("Y-m-d H:i:s");
$flagSuccess=false;
$sw=verificar_relacion_comprobante_descuentoper($codigo);
if($sw==0){
    $anioActual=date("Y");
    $mesActual=date("m");
    $diaActual=date("d");
    $codMesActiva=$_SESSION['globalMes']; 
    $month = $globalNombreGestion."-".$codMesActiva;
    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
    $diaUltimo = date('d', strtotime("{$aux} - 1 day"));
    $horasActual=date("H:i:s");
    if((int)$globalNombreGestion<(int)$anioActual){
      $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
    }else{
      if((int)$mesActual==(int)$codMesActiva){
          $fechaHoraActual=date("Y-m-d H:i:s");
      }else{
        $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
      } 
    }
    $tipoComprobante=3;
    $glosa=obtenerGlosaDescuentoPersonal($codigo);
    // $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;
    $sw_comprobante=0;
    while ($sw_comprobante==0) {
        $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,$tipoComprobante,$globalMes);
        $codComprobante=obtenerCodigoComprobante();    
        if(verificarExistenciaComprobante($codComprobante)==0){
            $sw_comprobante=1;
        }
    }
    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fecha_actual', '$globalUser')";
    $stmtInsert = $dbh_cabecera->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    if($flagSuccessComprobante){
        $sqlDelete="";
        $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
        $stmtDel = $dbh_cabecera->prepare($sqlDelete);
        $flagSuccess=$stmtDel->execute();
        $sqlInsert4="UPDATE descuentos_conta set cod_contabilizado=$codComprobante where codigo=$codigo;";
        $stmtInsert4 = $dbh_cabecera->prepare($sqlInsert4);
        $stmtInsert4->execute();
        $indexCompro=1;
        $datosIngresoDetalle = listaDetalleDescuentosPersonal($codigo);
        $monto_total_prontopago=0;
        while ($row = $datosIngresoDetalle->fetch(PDO::FETCH_ASSOC)) {
            $codigoDetalle=$row['codigo'];
            $cod_area=$row['cod_area'];
            $nombreArea=$row['nombreArea'];
            $fecha_d=$row['fecha'];
            $cod_personal=$row['cod_personal'];  
            $cod_cuentaDescuento=$row['cod_cuenta'];  
            $cod_contracuenta=$row['cod_contracuenta'];  
            $monto_sistema=$row['monto_sistema'];  
            $monto_depositado=$row['monto_depositado'];  
            $diferencia=$row['diferencia'];  
            $glosaDetalle=$row['glosa'];
            $tipo_contabilizacionDetalle=$row['tipo_contabilizacion'];
            $glosa_detalleSis="Ventas Suc. ".$nombreArea." de fecha ".$fecha_d;
            $glosa_detalleContra="Ajuste por diferencia deposito ". $glosa_detalleSis;
            $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(2,$cod_personal,$cod_cuentaDescuento);
            $nombrePersonal=namePersonalCompleto($cod_personal);
            // echo $cuentaAuxiliar."**<br>";
            if($cuentaAuxiliar==0){//creamos cuenta auxiliar
                $sqlDet="INSERT INTO cuentas_auxiliares (nombre,cod_cuenta,cod_estadoreferencial,cod_tipoauxiliar,cod_proveedorcliente) VALUES ('$nombrePersonal','$cod_cuentaDescuento','1','2','$cod_personal')";
                    // echo  $sqlDet; 
                 $stmtDetAux = $dbh_detalle->prepare($sqlDet);
                 $stmtDetAux->execute();
                $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(2,$cod_personal,$cod_cuentaDescuento);
            }
            $unidadDetalle=1;
            if($tipo_contabilizacionDetalle==1){//tiene banco incorporado
                $debe=$monto_depositado;
                $haber=0;
                $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
                $sqlDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_contracuenta', '0', '$unidadDetalle', '$cod_area', '$debe', '$haber', '$glosa_detalleSis', '$indexCompro')";
                $stmtDet = $dbh_detalle->prepare($sqlDet);
                $stmtDet->execute();
                $indexCompro++;
                $monto_cuentacorriente=$diferencia;
            }else{                
                $monto_cuentacorriente=$monto_sistema;
                $glosa_detalleSis=$glosaDetalle;
                $glosa_detalleContra=$glosaDetalle;
            }
            //cuenta corriente
            $debe=$monto_cuentacorriente;
            $haber=0;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_cuentaDescuento', '$cuentaAuxiliar', '$unidadDetalle', '$cod_area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
            $stmtDet = $dbh_detalle->prepare($sqlDet);
            $stmtDet->execute();
            $indexCompro++;
            //INGRESAMOS ESTADOS DE CUENTA
            $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
              VALUES ('$codComprobanteDetalle', '$cod_cuentaDescuento', '$debe', '$cod_personal', '$fecha_d','0','$cuentaAuxiliar','$glosaDetalle')";
            $stmtDetalleEstadoCuenta = $dbh_detalle->prepare($sqlDetalleEstadoCuenta);
            $stmtDetalleEstadoCuenta->execute();
            //actualizamos con que cod comprobante se guardó
            $sqlUpdateDesc="UPDATE descuentos_conta_detalle set cod_comprobantedetalle='$codComprobanteDetalle' where codigo=$codigoDetalle";
            $stmtUpdateDesc = $dbh_detalle->prepare($sqlUpdateDesc);
            $stmtUpdateDesc->execute();

            //contra cuenta
            $cuentaAuxiliarContracuenta=0;
            if($cod_contracuenta==5134){//cuenta INV. PROD. PARA DEVOLUCION
                $cuentaAuxiliarContracuenta=9796;//aux generico para la cuenta INV. PROD. PARA DEVOLUCION
            }

            $debe=0;
            $haber=$monto_sistema;
            $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
            $sqlDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_contracuenta', '$cuentaAuxiliarContracuenta', '$unidadDetalle', '$cod_area', '$debe', '$haber', '$glosa_detalleContra', '$indexCompro')";
            // echo $sqlDet;
            $stmtDet = $dbh_detalle->prepare($sqlDet);
            $stmtDet->execute();
            $indexCompro++;
        }
    }
    $dbh_cabecera="";
    $dbh_detalle="";
}

// showAlertSuccessErrorComprobanteIngresosAlm($flagSuccess,"../".$urlList);
   
?>