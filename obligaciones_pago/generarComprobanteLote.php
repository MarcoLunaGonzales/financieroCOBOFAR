<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../conexion2.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$dbh_detalle = new Conexion2();


$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
$codigo=$_GET["cod"];
session_start();
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fecha_pago=date("Y-m-d H:i:s");

$total_monto_debe=0;
$total_monto_haber=0;

$flagSuccess=false;
$sw=verificar_relacion_comprobante_pagoproveedores($codigo);
if($sw==0){
    //creacion del comprobante de pago
    $codComprobante=obtenerCodigoComprobante();
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
    $tipoComprobante=obtenerValorConfiguracion(108);
    $nroCorrelativo=numeroCorrelativoComprobante($globalGestion,$globalUnidad,$tipoComprobante,$globalMes);
    $glosa="PAGOS  ";
    $userSolicitud=$globalUser;
    $unidadSol=$globalUnidad;
    $areaSol=$globalArea;

    $sqlInsert="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by, modified_at, modified_by) 
    VALUES ('$codComprobante', '1', '$globalUnidad', '$globalNombreGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fecha_pago', '$globalUser', '$fecha_pago', '$globalUser')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    
    $sqlDelete="";
    $sqlDelete="DELETE from comprobantes_detalle where cod_comprobante='$codComprobante'";
    $stmtDel = $dbh->prepare($sqlDelete);
    $flagSuccess=$stmtDel->execute();
        //fin de comprobante

    $sqlInsert4="UPDATE pagos_proveedores SET cod_comprobante=$codComprobante,cod_estadopago=5 where cod_pagolote=$codigo;
                 UPDATE pagos_lotes SET cod_comprobante=$codComprobante,cod_estadopagolote=5 where codigo=$codigo;";
    $stmtInsert4 = $dbh->prepare($sqlInsert4);
    $stmtInsert4->execute();
    $indexCompro=1;
    $datosPago = listaDetallePagosProveedoresLote($codigo);
    $obs_cabecera="PAGOS PROVEDORES";
    $monto_total_prontopago=0;
    while ($row = $datosPago->fetch(PDO::FETCH_ASSOC)) {
        $cod_plancuenta=$row['cod_plancuenta'];
        $tipo_estadocuenta=verificarTipoEstadoCuenta($cod_plancuenta);
        $proveedor=$row['cod_proveedor'];  
        $monto_pago=$row["monto"];
        $obs_cabecera=$row["obs_cabecera"];
        $pronto_pago=$row["pronto_pago"];
        if($pronto_pago==1){
            $descuento_proveedor=obtnerDescuentoProveedor($proveedor);
            $monto_total_prontopago+=$monto_pago*$descuento_proveedor/100;
        }
       // $codigo_detalle=$row["cod_solicitudrecursosdetalle"];
       $glosa_detalle=$row["observaciones"];
       $cod_solicitudrecursos=$row["cod_solicitudrecursos"];//se encuentra el estado de cuenta
       $cod_solicitudrecursosdetalle=$row["cod_solicitudrecursosdetalle"];//se encuentra el codigo de detalle comprobante 
       //comprobante detalle
       
       $cuentaAuxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(1,$proveedor,$cod_plancuenta);
        //$cuentaAuxiliar=0;
       //echo $cod_plancuenta."--<br>";
        // $cuenta=obtenerCuentaPasivaSolicitudesRecursos($cod_plancuenta);
        $numeroCuenta=trim(obtieneNumeroCuenta($cod_plancuenta));
        $inicioNumero=$numeroCuenta[0];
        $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        if($unidadarea[0]==0){
            $unidadDetalle=$unidadSol;
            $area=$areaSol;
        }else{
            $unidadDetalle=$unidadarea[0];
            $area=$unidadarea[1];
        }
        $glosaDetalle=$glosa." - ".$glosa_detalle;


        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        if($tipo_estadocuenta==2){//está en haber, invertir en debe  
            $debe=$monto_pago;
            $haber=0;
        }else{//para notas
            $debe=0;
            $haber=$monto_pago;
        }
        $total_monto_debe+=$debe;
        $total_monto_haber+=$haber;

        $sqlDet="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobanteDetalle','$codComprobante', '$cod_plancuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$glosaDetalle', '$indexCompro')";
        //echo $sqlDet."DDDDD";
        $stmtDet = $dbh_detalle->prepare($sqlDet);
        $stmtDet->execute();
        //fin comprobante detalle
        $sqlDetalleEstadoCuenta="INSERT INTO estados_cuenta (cod_comprobantedetalle, cod_plancuenta, monto, cod_proveedor, fecha,cod_comprobantedetalleorigen,cod_cuentaaux,glosa_auxiliar) 
        VALUES ('$codComprobanteDetalle', '$cod_plancuenta', '$monto_pago', '$proveedor', '$fecha_pago','$cod_solicitudrecursos','$cuentaAuxiliar','$glosaDetalle')";
        //echo "<br>".$sqlDetalleEstadoCuenta."ECECEC";
        $stmtDetalleEstadoCuenta = $dbh_detalle->prepare($sqlDetalleEstadoCuenta);
        $stmtDetalleEstadoCuenta->execute();
        $indexCompro++;
    }

    //prontopago

    if($monto_total_prontopago>0){
        $cuenta=obtenerValorConfiguracion(103);
        $cuentaAuxiliar=0;
        $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
        $inicioNumero=$numeroCuenta[0];
        //$unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
        // if($unidadarea[0]==0){
        //     $unidadDetalle=$unidadSol;
        //     $area=$areaSol;
        // }else{
        //     $unidadDetalle=$unidadarea[0];
        //     $area=$unidadarea[1];
        // }
        $unidadDetalle=obtenerValorConfiguracion(15);
        $area=obtenerValorConfiguracion(29);
        $debe=0;
        $haber=$monto_total_prontopago;
        $glosaDetalle=nameCuenta($cuenta);
        $indexCompro++;
        $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
        $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
        VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$obs_cabecera', '$indexCompro')";
        //echo $sqlDetalle."RRRR"; 
        $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
        $flagSuccessDetalle=$stmtDetalle->execute();
    }

    //contra cuenta BANCO
    $cuenta=obtenerValorConfiguracion(38);
    $cuentaAuxiliar=0;
    $numeroCuenta=trim(obtieneNumeroCuenta($cuenta));
    $inicioNumero=$numeroCuenta[0];
    $unidadarea=obtenerUnidadAreaCentrosdeCostos($inicioNumero);
    if($unidadarea[0]==0){
        $unidadDetalle=$unidadSol;
        $area=$areaSol;
    }else{
        $unidadDetalle=$unidadarea[0];
        $area=$unidadarea[1];
    }
    $debe=0;
    $haber=$total_monto_debe-$total_monto_haber-$monto_total_prontopago;

    $glosaDetalle=nameCuenta($cuenta);
    $indexCompro++;
    $codComprobanteDetalle=obtenerCodigoComprobanteDetalle();
    $sqlDetalle="INSERT INTO comprobantes_detalle (codigo,cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) 
    VALUES ('$codComprobanteDetalle','$codComprobante', '$cuenta', '$cuentaAuxiliar', '$unidadDetalle', '$area', '$debe', '$haber', '$obs_cabecera', '$indexCompro')";
    //echo $sqlDetalle."RRRR"; 
    $stmtDetalle = $dbh_detalle->prepare($sqlDetalle);
    $flagSuccessDetalle=$stmtDetalle->execute();

    //actualizamos la cabecera
    $datosCheque=obtenerdatosCheque($codigo);
    if($datosCheque!=""){
        $datosCheque_array=explode(",", $datosCheque);
        $codigo_cheque=$datosCheque_array[0];
        $numero_cheque=$datosCheque_array[1];
        $sqlUpdate="UPDATE comprobantes SET glosa='$obs_cabecera',numero_cheque=$numero_cheque,cod_emisioncheque=$codigo_cheque where codigo=$codComprobante";
        $stmtUpdate = $dbh_detalle->prepare($sqlUpdate);
        $stmtUpdate->execute();
    }else{
        $sqlUpdate="UPDATE comprobantes SET glosa='$obs_cabecera' where codigo=$codComprobante";
        $stmtUpdate = $dbh_detalle->prepare($sqlUpdate);
        $stmtUpdate->execute();    
    }
    $dbh="";
    $dbh_detalle="";
}
showAlertSuccessErrorComprobantePagos($flagSuccess,"../".$urlListPagoLotes);    

?>