<?php //ESTADO FINALIZADO

require_once '../conexion_comercial_oficial.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';
$fechaDesde=$_GET['fi'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$desde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];
$fechahasta=$_GET['ff'];
$fechahastaTitulo= explode("-",$fechahasta);
$hasta=$fechahastaTitulo[2].'/'.$fechahastaTitulo[1].'/'.$fechahastaTitulo[0];
$fechaTitulo="De ".$desde." a ".$hasta;
$id_proveedor=$_GET['idprov'];
// $glosa=$_GET['glosa'];


?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/favicon.png">
            </div>
            <h4 class="card-title text-center">Histórico Ingresos Almacen</h4>            
            <div class="row">
               <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
            </div> 
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed table-striped " style="width:100%">
                <thead>                              
                  <tr>
                    <th width="2%"><small><b>-</b></small></th>
                    <th width="5%"><small><b>N° Ingreso</b></small></th>
                    <th width="25%"><small><b>Datos Ingreso</b></small></th>
                    <th s><small><b>Proveedor</b></small></th>
                    <th width="4%"><small><b>Factura</b></small></th>
                    <th width="10%"><small><b>F. Factura</b></small></th>
                    <th width="5%"><small><b>NIT</b></small></th>
                    <th width="6%"><small><b>AUTORIZA</b></small></th>                                  
                    <th width="6%"><small><b>CODIGO</b></small></th>
                    <th width="4%"><small><b>MONTO</b></small></th>
                    <th width="2%"><small><b>Act.</b></small></th>                      
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                   $sql="SELECT ia.cod_ingreso_almacen,ia.nro_correlativo,p.nombre_proveedor,ia.observaciones,ia.nro_factura_proveedor,ia.created_by,ia.created_date,ia.f_factura_proveedor,ia.con_factura_proveedor,ia.aut_factura_proveedor,ia.monto_factura_proveedor_desc,ia.nit_factura_proveedor,(select CONCAT_WS(' ',f.nombres,f.paterno)  from funcionarios f where f.codigo_funcionario=ia.created_by)as personal_ingreso,ia.monto_factura_proveedor
                    from ingreso_almacenes ia join proveedores p on ia.cod_proveedor=p.cod_proveedor
                    where ia.f_factura_proveedor BETWEEN '$fechaDesde 00:00:00' and '$fechahasta 23:59:59'
                    and ia.cod_tipoingreso=1004 and ia.cod_tipo_doc=1 and ia.estado_guardado>0 and ia.ingreso_anulado=0 and ia.cod_proveedor=$id_proveedor ORDER BY nro_factura_proveedor";
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
                    $monto_factura_bruto=$row['monto_factura_proveedor'];

                    // $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
                    // $total_venta=number_format($total_venta,2,'.','');
                    // $datos_ingreso=$cod_ingreso_almacen."/".$nro_factura_proveedor."/".$FECHA1."/".$nit_factura_proveedor."/".$aut_factura_proveedor."/".$con_factura_proveedor."/".$monto_factura_proveedor_desc."/".$fechaDesde."/".$fechahasta."/".$nro_correlativo;
                    $sw_contabilizacion=verificarContabilizacion_ingresos_dcto($cod_ingreso_almacen);
                    $datos_ingreso_origen=nombreComprobante($sw_contabilizacion);
                    if($sw_contabilizacion>0){ 
                      $index++;
                      ?>
                    <tr>
                      <td class="text-center small"><?=$index;?></td>
                      <td class="text-center small"><?=$nro_correlativo;?></td>
                      <td class="text-left small">Comprobante: <?=$datos_ingreso_origen;?></td>
                      <td class="text-left small"><?=$nombre_proveedor;?></td>
                      <td class="text-left small"><?=$nro_factura_proveedor;?></td>
                      <td class="text-center small"><?=$FECHA1;?></td>
                      <td class="text-left small"><?=$nit_factura_proveedor;?></td>
                      <td class="text-right small"><?=$aut_factura_proveedor;?></td>
                      <td class="text-left small"><?=$con_factura_proveedor;?></td>
                      <td class="text-right small"><?=formatNumberDec($monto_factura_proveedor_desc);?></td>
                      
                      <td class="td-actions text-right">
                      </td>
                    </tr>
                    <?php   
                    }
                  }?>
                </tbody>
              </table>
              <input type="hidden" name="contador_items" id="contador_items" value="<?=$index?>">            
            </div>
          </div>
     

        </div>
      </div>
    </div>  
  </div>
</div>


