<?php //ESTADO FINALIZADO


if (isset($_GET["check_rs_cierres"])) {
  $check_rs_cierres=$_GET["check_rs_cierres"]; 
  if($check_rs_cierres){
    ?>
    <meta charset="utf-8">
    <?php
    $sw_excel=0;
    header("Pragma: public");
    header("Expires: 0");
    $filename = "facturas_almacen_pendientes.xls";
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  }else{
    $sw_excel=1;
  }
}else{
  $sw_excel=1;
}

require_once '../styles.php';

if($sw_excel==1){
  require_once '../layouts/bodylogin2.php';
}


require_once '../conexion_sql.php'; 
require_once '../functions.php';
require_once '../functionsGeneral.php';


$fechaDesde=$_GET['fecha_desde'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$desde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];
$fechahasta=$_GET['fecha_hasta'];
$fechahastaTitulo= explode("-",$fechahasta);
$hasta=$fechahastaTitulo[2].'/'.$fechahastaTitulo[1].'/'.$fechahastaTitulo[0];
$fechaTitulo="De ".$desde." a ".$hasta;

?>
<table class="table table-bordered table-condensed" style="width:100%">
  <thead>                              
    <tr>
      <th colspan="9"><small><b>FACTURAS ALMACEN PENDIENTES</b></small></th>
    </tr>                                  
    <tr>
      <th><small><b>-</b></small></th>
      <th width="5%"><small><b>Dcto ALM</b></small></th>                                
      <th s><small><b>Proveedor</b></small></th>
      <th width="4%"><small><b>Factura</b></small></th>
      <th width="10%"><small><b>F. Factura</b></small></th>
      <th width="5%"><small><b>NIT</b></small></th>
      <th width="5%"><small><b>AUTORIZA</b></small></th>                                  
      <th><small><b>CODIGO</b></small></th>
      <th><small><b>MONTO</b></small></th>                         
    </tr>                                  
  </thead>
  <tbody>
    <?php
    $index=0;
     $server=obtenerValorConfiguracion(104);
     $bdname=obtenerValorConfiguracion(105);
     $user=obtenerValorConfiguracion(106);
     $pass=obtenerValorConfiguracion(107);
     $dbh2=ConexionFarma_all($server,$bdname,$user,$pass);

     $sql="SELECT a.DCTO, a.IDPROVEEDOR, p.DES, a.FECHA, a.GLO, a.TIPODOC, a.DOCUM, a.FECHA1, a.FECHA2, a.REFE, a.REFE1, a.RUC, a.MFACTURA, a.DESCTO1, a.DESCTO2, a.DESCTO3, a.DESCTO4
      FROM dbo.AMAESTRO AS a INNER JOIN dbo.PROVEEDORES AS p ON a.IDPROVEEDOR = p.IDPROVEEDOR
       WHERE (a.TIPO = 'A') AND  (a.FECHA1 BETWEEN '$desde 00:00:00' AND '$hasta 23:59:59') AND (a.STA = 'A') ORDER BY a.IDPROVEEDOR,CAST(a.DOCUM AS INT)";  
    //echo $sql;
    $stmt = $dbh2->prepare($sql);     
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $DCTO=$row['DCTO'];
      $IDPROVEEDOR=$row['DES'];
      $GLO=$row['GLO'];
      $DOCUM=$row['DOCUM'];
      $array_FECHA1=explode(" ", $row['FECHA1']); 
      $FECHA1=$array_FECHA1[0];
      $REFE=$row['REFE'];
      $REFE1=$row['REFE1'];
      $RUC=$row['RUC'];
      $MFACTURA=$row['MFACTURA'];
      $DESCTO1=$row['DESCTO1'];
      $DESCTO2=$row['DESCTO2'];
      $DESCTO3=$row['DESCTO3'];
      $DESCTO4=$row['DESCTO4'];
      
      $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
      $total_venta=number_format($total_venta,2,'.','');
      $datos_ingreso=$DCTO."/".$DOCUM."/".$FECHA1."/".$RUC."/".$REFE1."/".$REFE."/".$total_venta."/".$fechaDesde."/".$fechahasta;
      $sw_contabilizacion=verificarContabilizacion_ingresos_dcto($DCTO);
      if($sw_contabilizacion==0){ 
        $index++;
        ?>
      <tr>
        <td class="text-center"><?=$index;?></td>
        <td class="text-center"><?=$DCTO;?></td>
        <td class="text-left"><?=$IDPROVEEDOR;?></td>
        <td class="text-left"><?=$DOCUM;?></td>
        <td class="text-center"><?=$FECHA1;?></td>
        <td class="text-left"><?=$RUC;?></td>
        <td class="text-right"><?=$REFE1;?></td>
        <td class="text-left"><?=$REFE;?></td>
        <td class="text-right"><?=formatNumberDec($total_venta);?></td>
      </tr>
      <?php   
      }                               
    }?>
  </tbody>
</table>
