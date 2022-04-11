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
    $filename = "facturas_almacen_pendientes_nuevo.xls";
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

require_once '../conexion_comercial_oficial.php'; 
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
      <th colspan="9"><small><b>FACTURAS PENDIENTES DE INGRESO NUEVO SISTEMA</b></small></th>
    </tr>                                  
    <tr>
      <th><small><b>-</b></small></th>
      <th width="5%"><small><b>Nro. Ingreso</b></small></th>
      <th width="5%"><small><b>Datos Ingreso</b></small></th>
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
    $sql="SELECT ia.cod_ingreso_almacen,ia.nro_correlativo,p.nombre_proveedor,ia.observaciones,ia.nro_factura_proveedor,ia.created_by,ia.created_date,ia.f_factura_proveedor,ia.con_factura_proveedor,ia.aut_factura_proveedor,ia.monto_factura_proveedor_desc,ia.nit_factura_proveedor,(select CONCAT_WS(' ',f.nombres,f.paterno)  from funcionarios f where f.codigo_funcionario=ia.created_by)as personal_ingreso
                    from ingreso_almacenes ia join proveedores p on ia.cod_proveedor=p.cod_proveedor
                    where ia.f_factura_proveedor BETWEEN '$fechaDesde 00:00:00' and '$fechahasta 23:59:59'
                    and ia.cod_tipoingreso=1004 and ia.cod_tipo_doc=1 and ia.estado_guardado>0 and ia.ingreso_anulado=0 and ia.estado_contabilizado<>1  ORDER BY nro_factura_proveedor";
                  //echo $sql;
    $resp=mysqli_query($dbh,$sql);
    while($row=mysqli_fetch_array($resp)){ 
      $cod_ingreso_almacen=$row['cod_ingreso_almacen'];
      $nombre_proveedor=$row['nombre_proveedor'];
      $nro_correlativo=$row['nro_correlativo'];
      $nro_factura_proveedor=$row['nro_factura_proveedor'];

      $personal_ingreso=$row['personal_ingreso'];
      $created_date=$row['created_date'];
      $datos_ingreso_origen="Ingresado por: $personal_ingreso, En Fecha: $created_date";
      $FECHA1=$row['f_factura_proveedor'];
      $con_factura_proveedor=$row['con_factura_proveedor'];
      $aut_factura_proveedor=$row['aut_factura_proveedor'];
      $nit_factura_proveedor=$row['nit_factura_proveedor'];
      $monto_factura_proveedor_desc=$row['monto_factura_proveedor_desc'];
      
      // $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
      // $total_venta=number_format($total_venta,2,'.','');
      // $datos_ingreso=$DCTO."/".$DOCUM."/".$FECHA1."/".$RUC."/".$REFE1."/".$REFE."/".$total_venta."/".$fechaDesde."/".$fechahasta;
      // $sw_contabilizacion=verificarContabilizacion_ingresos_dcto($cod_ingreso_almacen);
      // if($sw_contabilizacion==0){ 
        $index++;
        ?>
      <tr>
        <td class="text-center small"><?=$index;?></td>
        <td class="text-center small"><?=$nro_correlativo;?></td>
        <td class="text-left small"><?=$datos_ingreso_origen;?></td>
        <td class="text-left small"><?=$nombre_proveedor;?></td>
        <td class="text-left small"><?=$nro_factura_proveedor;?></td>
        <td class="text-center small"><?=$FECHA1;?></td>
        <td class="text-left small"><?=$nit_factura_proveedor;?></td>
        <td class="text-right small"><?=$aut_factura_proveedor;?></td>
        <td class="text-left small"><?=$con_factura_proveedor;?></td>
        <td class="text-right small"><?=formatNumberDec($monto_factura_proveedor_desc);?></td>
      </tr>
      <?php   
      // }                               
    }?>
  </tbody>
</table>
