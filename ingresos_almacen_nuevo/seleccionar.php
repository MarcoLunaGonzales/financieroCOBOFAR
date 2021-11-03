<?php //ESTADO FINALIZADO

require_once '../conexion_comercial_oficial.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';
$fechaDesde=$_GET['fecha_desde'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$desde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];
$fechahasta=$_GET['fecha_hasta'];
$fechahastaTitulo= explode("-",$fechahasta);
$hasta=$fechahastaTitulo[2].'/'.$fechahastaTitulo[1].'/'.$fechahastaTitulo[0];
$fechaTitulo="De ".$desde." a ".$hasta;
$id_proveedor=$_GET['id_proveedor'];
$glosa=$_GET['glosa'];

$patron1="[\n|\r|\n\r]";
$glosa = preg_replace($patron1, ", ", $glosa);//quitamos salto de linea
$glosa = str_replace('"', " ", $glosa);//quitamos comillas dobles  
$glosa = str_replace("'", " ", $glosa);//quitamos comillas simples
$glosa = str_replace('<', "(", $glosa);//quitamos comillas dobles
$glosa = str_replace('>', ")", $glosa);//quitamos comillas dobles
$p=$_GET['p'];
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
            <h4 class="card-title text-center">Seleccionar Ingresos Almacen</h4>            
            <div class="row">
               <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
            </div> 
          </div>
          <form class="" action="save.php" method="POST">
            <input type="hidden" name="p" id="p" value="<?=$p?>">
          <div class="card-body">
            <div class="table-responsive">
              <input type="hidden" name="glosa_ingreso" id="glosa_ingreso" value="<?=$glosa?>">
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
                    <th width="2%"></th> 
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
                    // $DESCTO1=$row['DESCTO1'];
                    // $DESCTO2=$row['DESCTO2'];
                    // $DESCTO3=$row['DESCTO3'];
                    // $DESCTO4=$row['DESCTO4'];
                    
                    // $total_venta=$MFACTURA-number_format($DESCTO1,2,'.','')-$DESCTO2-$DESCTO3-$DESCTO4;
                    // $total_venta=number_format($total_venta,2,'.','');
                    $datos_ingreso=$cod_ingreso_almacen."/".$nro_factura_proveedor."/".$FECHA1."/".$nit_factura_proveedor."/".$aut_factura_proveedor."/".$con_factura_proveedor."/".$monto_factura_proveedor_desc."/".$fechaDesde."/".$fechahasta."/".$nro_correlativo;
                    $sw_contabilizacion=verificarContabilizacion_ingresos_dcto($cod_ingreso_almacen);
                    if($sw_contabilizacion==0){ 
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
                      <td><button title="Editar Ingreso" class="btn btn-success btn-sm" style="padding: 0;font-size:5px;width:18px;height:18px;" type="button" data-toggle="modal" data-target="#modalEditar" onclick="agregardatosModalEdicionIngresosAlm('<?=$datos_ingreso;?>')">
                          <i class="material-icons">edit</i>
                        </button></td>
                      <td class="td-actions text-right">
                      <input type="hidden" id="ingresos_activado_s<?=$index?>" name="ingresos_activado_s<?=$index?>"  value="0">
                      <input type="hidden" id="dcto_ingreso_s<?=$index?>" name="dcto_ingreso_s<?=$index?>"  value="<?=$cod_ingreso_almacen?>">
                        <input type="checkbox"  data-toggle="toggle" title="Seleccionar" id="ingresos_seleccionados<?=$index?>" name="ingresos_seleccionados<?=$index?>" onchange="activar_input_ingresos_almacen(<?=$index?>)">
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
          <div class="card-footer fixed-bottom">
              <button type="submit" class="btn btn-rose">Guardar seleccionados</button>
          </div>
          </form>

        </div>
      </div>
    </div>  
  </div>
</div>


<!-- modal editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Ingreso Almacén</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="fecha_desde_edit" id="fecha_desde_edit" > 
        <input type="hidden" name="fecha_hasta_edit" id="fecha_hasta_edit" > 
        <input type="hidden" name="dcto_edit" id="dcto_edit">

        <div class="row">
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Ingreso</small></label>
          <div class="col-sm-2">
            <div class="form-group">
              <input type="text" class="form-control" name="ningreso_edit" id="ningreso_edit" style="background-color:#e2d2e0" readonly="true">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Nro.<br>Factura.</small></label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="factura_edit" id="factura_edit" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Fecha</small></label>
          <div class="col-sm-2">
            <div class="form-group" >              
              <input type="date" class="form-control" name="fecha_edit" id="fecha_edit" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Monto</small></label>
          <div class="col-sm-2">
            <div class="form-group" >              
              <input type="text" class="form-control" name="monto_edit" id="monto_edit" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div>           

        <div class="row">
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Nit</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nit_edit" id="nit_edit" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Autorización</small></label>
          <div class="col-sm-3">
            <div class="form-group" >              
              <input type="text" class="form-control" name="autoriza_edit" id="autoriza_edit" style="background-color:white">
            </div>
          </div>
           <label class="col-sm-1 col-form-label text-dark font-weight-bold"><small>Codigo<br>Control</small></label>
          <div class="col-sm-3">
            <div class="form-group" >
              <input type="text" class="form-control" name="codigocontrol_edit" id="codigocontrol_edit" style="background-color:white">              
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" id="guardar_edit_ingreso_alm" name="guardar_edit_ingreso_alm" data-dismiss="modal">Guardar Cambios</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#guardar_edit_ingreso_alm').click(function(){
      
      var dcto_edit=document.getElementById("dcto_edit").value;
      var factura_edit=$('#factura_edit').val();
      fecha_desde="";
      fecha_hasta="";
      var fecha_edit=$('#fecha_edit').val();
      var monto_edit="";
      var nit_edit=$('#nit_edit').val();
      var autoriza_edit=$('#autoriza_edit').val();
      var codigocontrol_edit=$('#codigocontrol_edit').val();

      if(autoriza_edit==null || autoriza_edit==0 || autoriza_edit=='' || autoriza_edit==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
       }else{        
        guardar_edit_ingreso_alm(dcto_edit,factura_edit,fecha_edit,monto_edit,nit_edit,autoriza_edit,codigocontrol_edit,fecha_desde,fecha_hasta);
       }      
    });    
  });
</script>