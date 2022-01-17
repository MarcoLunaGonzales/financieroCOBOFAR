<?php //ESTADO FINALIZADO

require_once '../conexion.php';
require_once '../conexion_sql.php'; 
require_once '../functions.php';
require_once '../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
$fechaDesde=$_GET['fecha_desde'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$desde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];
$fechahasta=$_GET['fecha_hasta'];
$fechahastaTitulo= explode("-",$fechahasta);
$hasta=$fechahastaTitulo[2].'/'.$fechahastaTitulo[1].'/'.$fechahastaTitulo[0];
$fechaTitulo="De ".$desde." a ".$hasta;


$id_proveedor=$_GET['id_proveedor'];
$glosa=$_GET['glosa'];

$p=$_GET['p'];
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/icono_pastilla.png">
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
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed" style="width:100%">
                <thead>                              
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
                    <th><small><b>Actions</b></small></th>                      
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

                   $sql="SELECT DCTO, IDPROVEEDOR, FECHA, GLO, TIPODOC, DOCUM, FECHA1, FECHA2, REFE, REFE1, RUC, MFACTURA,DESCTO1, DESCTO2, DESCTO3, DESCTO4
                   FROM dbo.AMAESTRO
                   WHERE (TIPO = 'A') AND (FECHA1 BETWEEN '$desde 00:00:00' AND '$hasta 23:59:59') AND (STA = 'A') and IDPROVEEDOR=$id_proveedor ORDER BY CAST(DOCUM AS INT)";  
                  //echo $sql;
                  $stmt = $dbh2->prepare($sql);     
                  $stmt->execute();
                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $DCTO=$row['DCTO'];
                    $IDPROVEEDOR=$row['IDPROVEEDOR'];
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
                      <td class="text-left"><?=nameProveedor_antsist($IDPROVEEDOR);?></td>
                      <td class="text-left"><?=$DOCUM;?></td>
                      <td class="text-center"><?=$FECHA1;?></td>
                      <td class="text-left"><?=$RUC;?></td>
                      <td class="text-right"><?=$REFE1;?></td>
                      <td class="text-left"><?=$REFE;?></td>
                      <td class="text-right"><?=formatNumberDec($total_venta);?></td>
                      <td class="td-actions text-right">
                      <input type="hidden" id="ingresos_activado_s<?=$index?>" name="ingresos_activado_s<?=$index?>"  value="0">
                      <input type="hidden" id="dcto_ingreso_s<?=$index?>" name="dcto_ingreso_s<?=$index?>"  value="<?=$DCTO?>">
                        <button title="Editar Ingreso" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalEditar" onclick="agregardatosModalEdicionIngresosAlm('<?=$datos_ingreso;?>')">
                          <i class="material-icons">edit</i>
                        </button>
                        <div class="togglebutton">
                          <label>
                             <input type="checkbox" title="Seleccionar" id="ingresos_seleccionados<?=$index?>" name="ingresos_seleccionados<?=$index?>" onchange="activar_input_ingresos_almacen(<?=$index?>)">
                             <span class="toggle"></span>
                          </label>
                        </div>
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
              <button type="submit" class="btn btn-success">Guardar</button>
          </div>
          </form>

        </div>
      </div>
    </div>  
  </div>
</div>


<!-- modal devolver solicitud -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar Ingreso</h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="fecha_desde_edit" id="fecha_desde_edit" > 
        <input type="hidden" name="fecha_hasta_edit" id="fecha_hasta_edit" > 
        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>DCTO.</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="dcto_edit" id="dcto_edit" style="background-color:#e2d2e0" readonly="true">              
            </div>
          </div>

          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Nro.<br>Factura.</small></label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="factura_edit" id="factura_edit" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small >Fecha</small></label>
          <div class="col-sm-2">
            <div class="form-group" >              
              <input type="date" class="form-control" name="fecha_edit" id="fecha_edit" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small >Monto</small></label>
          <div class="col-sm-2">
            <div class="form-group" >              
              <input type="text" class="form-control" name="monto_edit" id="monto_edit" readonly="true" style="background-color:#e2d2e0">
            </div>
          </div>
        </div>           

        <div class="row">
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>NIT.</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="nit_edit" id="nit_edit" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small >Autorización</small></label>
          <div class="col-sm-4">
            <div class="form-group" >              
              <input type="text" class="form-control" name="autoriza_edit" id="autoriza_edit" style="background-color:white">
            </div>
          </div>
           <label class="col-sm-1 col-form-label" style="color:#7e7e7e"><small>Codigo.<br>Control.</small></label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input type="text" class="form-control" name="codigocontrol_edit" id="codigocontrol_edit" style="background-color:white">              
            </div>
          </div>
        </div>      


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="guardar_edit_ingreso_alm" name="guardar_edit_ingreso_alm" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
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