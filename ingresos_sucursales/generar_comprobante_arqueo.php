<?php
// ini_set('memory_limit','1G');
// set_time_limit(0);
// header("Pragma: public");
// header("Expires: 0");
// $fecha_c=date('dmY');
// $filename = "Comprobantes Generado".$fecha_c.".xls";
// header("Content-type: application/x-msdownload");
// header("Content-Disposition: attachment; filename=$filename");
// header("Pragma: no-cache");
// header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

echo "<br><br><br><br>";

require_once '../functions.php';
require_once '../conexion.php';
require_once '../conexion2.php';

$dbh_cabecera = new Conexion();
$dbh_detalle = new Conexion2();
session_start();
set_time_limit(0);
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=1;
// $globalArea=$_SESSION["globalArea"];
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalMes=$_SESSION['globalMes'];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
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

$fecha_comprobante=date("Y-m-d H:i:s");
if((int)$globalNombreGestion<(int)$anioActual){
  $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
}else{
  if((int)$mesActual==(int)$codMesActiva){
      $fechaHoraActual=date("Y-m-d H:i:s");
  }else{
    $fechaHoraActual=$globalNombreGestion."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
  } 
}
?>
<table class='table table-condensed table-sm table-bordered'>
<tr>
  <th class='bg-info text-white'>Index</th>
  <th class='bg-info text-white'>Cbte</th>
  <th class='bg-info text-white'>Fecha</th>
  <th class='bg-info text-white'>Suc</th>
</tr>
<?php
$fechai=$_GET['fechai'];
$fechaf=$_GET['fechaf'];
$sucursalgString=$_GET['cod_sucursal'];
$cod_tiposalida_efectivo=1001;
$tipoComprobante=1;//ingreso

$string_configuracion=obtenerValorConfiguracion_array('49,50,51,56,110,111,112,113,114,115,117,118');
$array_configuracion=explode(",",$string_configuracion);

// $cod_plancuenta_it=5031;//49 //gasto
// $cod_plancuenta_iva=2013;//debito fiscal iva 50
// $cod_plancuenta_it_haber=2014;//51 //pasivo
// $cod_plancuenta_atc=1023;//56 por defecto 
// $cod_plancuenta_ventas=4004; //110 VENTAS POR SUCURSALES
// $cod_plancuenta_costos=5004;//111 COSTO DE VENTAS
// $cod_plancuenta_costos_contra=1057;//112 INVENTARIO SUCURSALES LP
// $cod_plancuenta_dolar=1032;//113 banco moneda extranjera
// $cod_plancuenta_dolar_contra=5126;//114 otros ingresos comerciales
// $tc=6.96;//115 

$cod_plancuenta_it=$array_configuracion[0];
$cod_plancuenta_iva=$array_configuracion[1];
$cod_plancuenta_it_haber=$array_configuracion[2];
$cod_plancuenta_atc=$array_configuracion[3];

$cod_plancuenta_ventas=$array_configuracion[4];
$cod_plancuenta_costos=$array_configuracion[5];
$cod_plancuenta_costos_contra=$array_configuracion[6];
$cod_plancuenta_dolar=$array_configuracion[7];
$cod_plancuenta_dolar_contra=$array_configuracion[8];
$tc=$array_configuracion[9];
$cod_plancuenta_qr=$array_configuracion[10];
$cod_plancuenta_transfer=$array_configuracion[11];

$sql="SELECT s.cod_almacen,(select a.nombre_almacen from almacenes a where a.cod_almacen=s.cod_almacen)as nombre_almacen,s.fecha,(select a.cod_ciudad from almacenes a where a.cod_almacen=s.cod_almacen)as cod_ciudad,(select c.cod_plancuenta from ciudades c where c.cod_ciudad in (select a.cod_ciudad from almacenes a where a.cod_almacen=s.cod_almacen))as cod_plancuenta,(SELECT c.cod_area from ciudades c where c.cod_ciudad in (select a.cod_ciudad from almacenes a where a.cod_almacen=s.cod_almacen))as cod_area 
  from salida_almacenes s 
  where s.`cod_tiposalida`= $cod_tiposalida_efectivo  and s.`cod_almacen` in (select a.cod_almacen from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgString)) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fechai 00:00:00' and '$fechaf 23:59:59'  GROUP BY s.cod_almacen,s.fecha order by s.fecha,2";
  // echo $sql;
$contador_comprobantes=1;
require("../conexion_comercial.php");
$resp=mysqli_query($dbh,$sql);
while($row=mysqli_fetch_array($resp)){
  $fechaVenta=$row['fecha'];
  $cod_almacen_x=$row['cod_almacen'];
  $cod_ciudad=$row['cod_ciudad'];
  $cod_plancuenta_x=$row['cod_plancuenta'];
  $cod_area_cc=$row['cod_area'];
  // $cod_ciudad=obtener_codciudad_almacen_nuevosis($cod_almacen_x,1);
  $nombre_almacen=$row['nombre_almacen'];
  $sw_relacion=verificarRelacionComprobante($cod_ciudad,$fechaVenta);
  if($sw_relacion>0){?>
    <tr>
      <td align='left'><?=$contador_comprobantes?></td>
      <td align='left'>Ya fue Generado. Cmbt: <?=nombreComprobante($sw_relacion)?></td>
      <td align='right'><?=$fechaVenta?></td>
      <td align='left'><?=$nombre_almacen?></td>
    </tr>
  <?php }else{
    
    $array_fechaIngreso=explode('-', $fechaVenta);
    $globalGestion=$array_fechaIngreso[0];
    $cod_gestion=codigoGestion($globalGestion);
    $globalMes=$array_fechaIngreso[1];
    $globalMes=str_pad($globalMes, 2, "0", STR_PAD_LEFT);

    $nroCorrelativo=numeroCorrelativoComprobante($cod_gestion,$globalUnidad,$tipoComprobante,$globalMes);
    $glosa="VENTAS SUC. ".$nombre_almacen." DE FECHA ".$fechaVenta;
    $fechaHoraActual=$fechaVenta;
    
    $sw_comprobante=0;
    while ($sw_comprobante==0) {
        $codigo_comprobante=obtenerCodigoComprobante();
        if(verificarExistenciaComprobante($codigo_comprobante)==0){
            $sw_comprobante=1;
        }
    }
    $cod_uo_cc=1;//oficina central por defecto
    // $cod_area_cc=obtenerCodigoAreaciduad_comercial($cod_ciudad);
    $sqlInsert="INSERT INTO comprobantes (codigo,cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa, created_at, created_by) 
    VALUES ($codigo_comprobante,'1', '$globalUnidad', '$globalGestion', '1', '1', '$tipoComprobante', '$fechaHoraActual', '$nroCorrelativo', '$glosa', '$fecha_comprobante', '$globalUser')";
    // echo $sqlInsert;
    $stmtInsert = $dbh_cabecera->prepare($sqlInsert);
    $flagSuccessComprobante=$stmtInsert->execute();
    $ordenDetalle=0;
    if($flagSuccessComprobante){     
      $sqlrelacionarComprobante="INSERT into ingresos_sucursales_comprobantes(cod_ciudad,fecha,cod_comprobante) values($cod_ciudad,'$fechaVenta',$codigo_comprobante)";
      $stmt_relacion_ingreso = $dbh_detalle->prepare($sqlrelacionarComprobante);
      $stmt_relacion_ingreso->execute();
      ?>
      <tr>
        <td align='left'><?=$contador_comprobantes?></td>
        <td align='left'>I<?=str_pad($globalMes, 2, "0", STR_PAD_LEFT)."-".str_pad($nroCorrelativo, 5, "0", STR_PAD_LEFT);?></td>
        <td align='right'><?=$fechaVenta?></td>
        <td align='left'><?=$nombre_almacen?></td>
      </tr><?php

      //primera parte detalle comprobante
      $sql="SELECT s.cod_chofer,( SELECT (select cb.cod_plancuenta from cuentas_bancarias cb where cb.codigo=rd.cod_cuenta and cb.estado=1) as cuenta FROM registro_depositos rd where rd.cod_funcionario=s.cod_chofer and rd.fecha='$fechaVenta' and rd.cod_estadoreferencial=1 limit 1) as cod_plancuenta
      from `salida_almacenes` s where s.`cod_tiposalida`= 1001 and s.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a
      where a.`cod_ciudad`='$cod_ciudad' and cod_tipoalmacen=1) and CONCAT(s.fecha,' ',s.hora_salida) BETWEEN '$fechaVenta 00:00:00' and '$fechaVenta 23:59:59'   
      GROUP BY s.cod_chofer,s.fecha order by s.fecha,s.cod_chofer";
       //echo "<br><br>".$sql."</br></br>";
      
      $DBB=0;
      $HBB=0;
      $suma_total_efectivo=0;
      $suma_total_tarjetas=0;

      $suma_total_transferencias=0;
      $suma_total_qr=0;

      $suma_total_dolares=0;
      $suma_total_dolares_convertido=0;
      $suma_total_costo=0;
      $analitico_dolar_array=[];
      $monto_dolarbs_array=[];
      $monto_dolar_array=[];
      $index=0;
      $resp_detalle=mysqli_query($dbh,$sql);
      while($row_DETALLE1=mysqli_fetch_array($resp_detalle)){
        $cod_personal=$row_DETALLE1['cod_chofer'];
        $cod_plancuenta=$row_DETALLE1['cod_plancuenta'];

        // $montoefectivo=obtenerMonto_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
        // $montoTarjeta=obtenerMontoTarjeta_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
        // $montodolarstring=obtenerMontodolares_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
        // $montodolarArray=explode("###",$montodolarstring);
        // $monto_dolar=$montodolarArray[0];
        // $monto_dolar_bs=$montodolarArray[1];

        $srting_montos=obtenerMonto_ventas_nuevosis_neto($fechaVenta,$cod_ciudad,$cod_personal);
        $montosArray=explode("###",$srting_montos);
        $montoefectivo=$montosArray[0];
        $montoTarjeta=$montosArray[1];
        $montoTrasferencia=$montosArray[2];
        $montoQr=$montosArray[5];
        //dolar
        $analitico_dolar_array[$index]=obtener_cuenta_moneda_extranjera_dolar_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
        
        $monto_dolar=$montosArray[3];
        $monto_dolar_bs=$montosArray[4];
        $monto_dolar_array[$index]=$monto_dolar;
        $monto_dolarbs_array[$index]=$monto_dolar_bs;
        $montoAnulada=obtenerMontoAnuladas_ventas_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);        
        $DBB=$montoefectivo-$montoAnulada-$monto_dolar_bs;

        $HBB=0;
        $suma_total_tarjetas+=$montoTarjeta;
        
        $suma_total_transferencias+=$montoTrasferencia;
        $suma_total_qr+=$montoQr;

        $suma_total_efectivo+=$DBB;
        //costeo
        // $costo_venta=otenerCostoVenta_nuevosis($fechaVenta,$cod_ciudad,$cod_personal);
        $costo_venta=0;
        $suma_total_costo+=$costo_venta;
        $ordenDetalle++;
        if($cod_plancuenta==0 || $cod_plancuenta==null || $cod_plancuenta==""){
          $descripcion=$glosa;
          $cod_plancuenta=$cod_plancuenta_x;
        }else{
          $descripcion=$glosa." - ".nameCuenta($cod_plancuenta);
        }
        // $descripcion=$glosa." - ".nameCuenta($cod_plancuenta);
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
        $index++;
      }
      // Dolares
      if(count($analitico_dolar_array)>0){
        for ($x=0;$x<count($analitico_dolar_array); $x++) {
          $cod_cuenta_ext=$analitico_dolar_array[$x];
          $monto_dolar_bs_x=$monto_dolarbs_array[$x];
          $monto_dolar_x=$monto_dolar_array[$x];
          //echo $cod_cuenta_ext."-".$monto_dolar_bs_x."<br>";
          if($cod_cuenta_ext>0){
            // $tc=6.96;
            $suma_total_dolares+=$monto_dolar_bs_x;
            $DBB=$monto_dolar_x*$tc;
            $HBB=0;
            $suma_total_dolares_convertido+=$DBB;
            $cod_plancuenta_dolar=$cod_cuenta_ext;
            $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_dolar);
          
            $ordenDetalle++;
            $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_dolar,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
          }elseif($monto_dolar_bs_x>0){
            // echo "aqui";
            // $tc=6.96;
            $suma_total_dolares+=$monto_dolar_bs_x;
            $DBB=$monto_dolar_x*$tc;
            $HBB=0;
            $suma_total_dolares_convertido+=$DBB;
            // $cod_plancuenta_dolar=1032;            
            $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_dolar);
          
            $ordenDetalle++;
            $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_dolar,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
          }
        }
      }elseif($monto_dolar_bs>0){
        // echo "aqui";
        // $tc=6.96;
        $suma_total_dolares+=$monto_dolar_bs_x;
        $DBB=$monto_dolar_x*$tc;
        $HBB=0;
        $suma_total_dolares_convertido+=$DBB;
        $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_dolar);
      
        $ordenDetalle++;
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_dolar,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      }
      //total de tarjetas
      if($suma_total_tarjetas>0){
        $DBB=$suma_total_tarjetas;
        $HBB=0;
        // $cod_plancuenta_atc=1023;//por defecto
        $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_atc);
        $ordenDetalle++;
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_atc,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      }
      //total QR
      if($suma_total_qr>0){
        $DBB=$suma_total_qr;
        $HBB=0;
        $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_qr);
        $ordenDetalle++;
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_qr,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      }
       //total transferencia
      if($suma_total_transferencias>0){
        $DBB=$suma_total_transferencias;
        $HBB=0;
        $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_transfer);
        $ordenDetalle++;
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_transfer,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      }
      
      //impuesto a las transacciones 3% debito
      $total_ventas=$suma_total_efectivo+$suma_total_dolares+$suma_total_tarjetas+$suma_total_qr+$suma_total_transferencias;
      $impuesto_it=$total_ventas*3/100;
      $DBB=$impuesto_it;
      // $cod_plancuenta_it=5031;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_it);
      $ordenDetalle++;
       $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_it,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);

      //VENTAS POR SUCURSALES
      $ventas=$total_ventas*87/100;
      $DBB=0;
      $HBB=$ventas;
      
      // $cod_plancuenta_ventas=4004;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_ventas);
      $ordenDetalle++;
      $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_ventas,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);

      //DEBITO FISCAL IVA
      $debito=$total_ventas*13/100;
      $DBB=0;
      $HBB=$debito;
      // $cod_plancuenta_iva=2013;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_iva);
      $ordenDetalle++;
      $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_iva,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);

      //IT 3%
      $debito=$total_ventas*3/100;
      $DBB=0;
      $HBB=$debito;
      // $cod_plancuenta_it_haber=2014;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_it_haber);
      $ordenDetalle++;
      $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_it_haber,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      //COSTOS
      $DBB=$suma_total_costo;
      $HBB=0;
      // $cod_plancuenta_costos=5004;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_costos);
      $ordenDetalle++;
      $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_costos,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);

      //COSTOS contra cuenta
      $DBB=0;
      $HBB=$suma_total_costo;
      // $cod_plancuenta_costos_contra=1057;
      $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_costos_contra);
      $cuenta_auxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(4,$cod_area_cc,$cod_plancuenta_costos_contra);
      if($cuenta_auxiliar==0){
        $nombre_area=nameArea($cod_area_cc);
        $codEstado="1";
        $stmtInsertAux = $dbh_cabecera->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta,  cod_tipoauxiliar, cod_proveedorcliente) 
        VALUES ('$nombre_area', $codEstado,$cod_plancuenta_costos_contra, 4, $cod_area_cc)");
        $stmtInsertAux->execute();
        $cuenta_auxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(4,$cod_area_cc,$cod_plancuenta_costos_contra);
      }
      $ordenDetalle++;
      $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_costos_contra,$cuenta_auxiliar,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);

      // Dolares contra cuenta
      if($suma_total_dolares>0){
        $DBB=0;
        $HBB=$suma_total_dolares_convertido-$suma_total_dolares;
        //$cod_plancuenta_dolar_contra=4010;
        $descripcion=$glosa." - ".nameCuenta($cod_plancuenta_dolar_contra);
        $ordenDetalle++;
        $flagSuccessDet=insertarDetalleComprobante($codigo_comprobante,$cod_plancuenta_dolar_contra,0,$cod_uo_cc,$cod_area_cc,$DBB,$HBB,$descripcion,$ordenDetalle);
      }
    }
  }

  $contador_comprobante++;
} 
?>

</table>