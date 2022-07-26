<?php //ESTADO FINALIZADO

require_once '../conexion_comercial2.php';
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
$id_sucursal=$_GET['id_sucursal'];
$glosa=$_GET['glosa'];

$patron1="[\n|\r|\n\r]";
$glosa = preg_replace($patron1, ", ", $glosa);//quitamos salto de linea
$glosa = str_replace('"', " ", $glosa);//quitamos comillas dobles  
$glosa = str_replace("'", " ", $glosa);//quitamos comillas simples
$glosa = str_replace('<', "(", $glosa);//quitamos comillas dobles
$glosa = str_replace('>', ")", $glosa);//quitamos comillas dobles
$p=$_GET['p'];



$cuentaSucursales=1057; //obtenerValorConfiguracion();
$cuentaAlmacen=1058;
$cuentaDevolucion=5134;


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
            <h4 class="card-title text-center">Seleccionar Traspasos Sucursales</h4>            
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
                    <th width="5%"><small><b>N° Traspaso</b></small></th>
                    <th width="15%"><small><b>Datos Traspaso</b></small></th>
                    <th width="10%"><small><b>Sucursal</b></small></th>
                    <th width="10%"><small><b>Destino</b></small></th>
                    <th width="10%"><small><b>Fecha</b></small></th>
                    <th width="5%"><small><b>Monto<br>Traspaso</b></small></th>
                    <th width="6%"><small><b>Monto<br>Ingresado</b></small></th>
                    <th width="6%"><small><b>Diferencia</b></small></th>
                    <th width="6%"><small><b>Compr.</b></small></th>                                                 
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                   $sql="SELECT tipo_comprobante,cod_almacen,almacen_origen,fecha,round(sum(costo_salida),2) as costo_salida,cod_almacen_destino,almacen_destino,round(sum(costo_ingreso),2) as costo_ingreso,nro_traspasos,cod_traspasos,cod_area_ingreso,cod_area_salida,cod_comprobantes

                    FROM (
                    SELECT CASE
                        WHEN ((s.cod_almacen<>1000 and s.cod_almacen<>1078) and (s.almacen_destino<>1000 and s.almacen_destino<>1078)) THEN 'S-S'
                        WHEN (s.cod_almacen=1000 and (s.almacen_destino<>1078 and s.almacen_destino<>1000)) THEN 'A-S'
                        WHEN ((s.cod_almacen<>1000 and s.cod_almacen<>1078) and s.almacen_destino=1078) THEN 'S-V'
                        WHEN ((s.cod_almacen<>1000 and s.cod_almacen<>1078) and s.almacen_destino=1000) THEN 'S-A'
                        WHEN (s.cod_almacen=1078 and (s.almacen_destino<>1000 and s.almacen_destino<>1078)) THEN 'V-S'
                        WHEN (s.cod_almacen=1000 and s.almacen_destino=1078) THEN 'A-V'
                        WHEN (s.cod_almacen=1078 and s.almacen_destino=1000) THEN 'V-A'       
                        ELSE 'N-N'
                    END
                    AS tipo_comprobante
                    ,s.cod_almacen,asa.nombre_almacen as almacen_origen,s.fecha,(cts.costo_unitario*sum(IFNULL(sd.cantidad_unitaria,0))+cts.costo_unitario*sum(IFNULL(sd.cantidad_envase,0))*m.cantidad_presentacion) as costo_salida,
                    s.almacen_destino as cod_almacen_destino,ai.nombre_almacen as almacen_destino,
                    (cti.costo_unitario*IFNULL(id.cantidad_unitaria,0)+cti.costo_unitario*IFNULL(id.cantidad_envase,0)*m.cantidad_presentacion) as costo_ingreso,concat(' T-',s.nro_correlativo) as nro_traspasos,s.cod_salida_almacenes as cod_traspasos,ci.cod_area as cod_area_ingreso,cs.cod_area as cod_area_salida,s.costeo_cod_comprobante as cod_comprobantes  
                    FROM ingreso_detalle_almacenes id 
                    join ingreso_almacenes i on i.cod_ingreso_almacen=id.cod_ingreso_almacen
                    join material_apoyo m on m.codigo_material=id.cod_material
                    join salida_almacenes s on s.cod_salida_almacenes=i.cod_salida_almacen
                    join salida_detalle_almacenes sd on sd.cod_salida_almacen=s.cod_salida_almacenes AND sd.cod_material=id.cod_material
                    JOIN costoscobofar.costo_transaccion cti on cti.cod_documento=id.cod_ingreso_almacen and cti.cod_material=id.cod_material and cti.cod_tipodocumento=1
                    JOIN costoscobofar.costo_transaccion cts on cts.cod_documento=sd.cod_salida_almacen and cts.cod_material=sd.cod_material and cts.cod_tipodocumento=0
                    join almacenes ai on ai.cod_almacen=i.cod_almacen
                    join almacenes asa on asa.cod_almacen=s.cod_almacen
                    join ciudades ci on ai.cod_ciudad=ci.cod_ciudad
                    join ciudades cs on asa.cod_ciudad=cs.cod_ciudad
                    where s.fecha>='$fechaDesde' and s.fecha<='$fechahasta' and s.salida_anulada=0 and i.ingreso_anulado=0
                    and ai.cod_tipoalmacen=1 and asa.cod_tipoalmacen=1 and asa.cod_ciudad='$id_sucursal'
                    GROUP BY s.cod_almacen,s.fecha,s.almacen_destino,s.cod_salida_almacenes,id.cod_material  
                    ) lie
                    GROUP BY lie.cod_almacen,lie.fecha,lie.cod_almacen_destino,lie.cod_traspasos
                    order by 1,almacen_origen,fecha,cod_almacen_destino;";
                  //echo $sql;
                  $resp=mysqli_query($enlaceCon,$sql);
                  while($row=mysqli_fetch_array($resp)){ 
                    $cod_traspasos=$row['cod_traspasos'];
                    $nro_traspasos=$row['nro_traspasos'];
                    $almacen_origen=$row['almacen_origen'];
                    $almacen_destino=$row['almacen_destino'];
                    $datos_ingreso_origen="Registro de Traspasos por día";
                    $FECHA1=$row['fecha'];
                    $costo_salida=$row['costo_salida'];
                    $costo_ingreso=$row['costo_ingreso'];
                    $diferencia=$costo_salida-$costo_ingreso;
                    $estiloDiferencia="";
                    if($diferencia!=0){
                        $estiloDiferencia="style='color:red';";
                    }


                    $cod_almacen=$row['cod_almacen'];
                    $cod_almacen_destino=$row['cod_almacen_destino'];
                    $areaSalida=$row["cod_area_salida"];
                    $areaIngreso=$row["cod_area_ingreso"];
                    $nombreTraspasos="Traspasos de productos de ".$almacen_origen." a ".$almacen_destino." de fecha ".date("d/m/Y",strtotime($FECHA1));                    

                    $cuentaSalida=0;
                    $cuentaIngreso=0;
                    $tipo_comprobante=$row["tipo_comprobante"];
                    $estiloTipo="";                    
                    switch ($tipo_comprobante) {
                      case 'S-S':  
                        $estiloTipo="style='background:#DAF7A6;color#000;'";
                        $nombreTraspasos.=" (Entre Sucursales)";
                        $cuentaSalida=$cuentaSucursales;
                        $cuentaIngreso=$cuentaSucursales;
                        break;
                      case 'A-S':  
                        $estiloTipo="style='background:#286DDE ;color#fff;'";
                        $nombreTraspasos.=" (Almacen - Sucursales)";
                        $cuentaSalida=$cuentaAlmacen;
                        $cuentaIngreso=$cuentaSucursales;
                      break;
                      case 'S-V': 
                        $estiloTipo="style='background:#23BDB2;color#fff;'"; 
                        $nombreTraspasos.=" (Sucursales - Vencidos)";
                        $cuentaSalida=$cuentaSucursales;
                        $cuentaIngreso=$cuentaDevolucion;
                      break;
                      case 'S-A':  
                        $estiloTipo="style='background:#FFC300;color#000;'"; 
                        $nombreTraspasos.=" (Sucursales - Almacen)";
                        $cuentaSalida=$cuentaSucursales;
                        $cuentaIngreso=$cuentaAlmacen;
                      break;
                      case 'V-S':  
                        $estiloTipo="style='background:#BB23BD;color#fff;'"; 
                        $nombreTraspasos.=" (Vencidos - Sucursales)";
                        $cuentaSalida=$cuentaDevolucion;
                        $cuentaIngreso=$cuentaSucursales;
                      break;
                      case 'A-V':  
                        $estiloTipo="style='background:#BD2348;color#fff;'";
                        $nombreTraspasos.=" (Almacen - Vencidos)";
                        $cuentaSalida=$cuentaAlmacen;
                        $cuentaIngreso=$cuentaDevolucion; 
                      break;
                      case 'V-A':  
                        $estiloTipo="style='background:#23BD28;color#fff;'";
                        $nombreTraspasos.=" (Vencidos - Almacen)";
                        $cuentaSalida=$cuentaDevolucion;
                        $cuentaIngreso=$cuentaAlmacen; 
                      break;
                    }
                    $cuentaAuxiliarSalida=obtenerCuentaAuxiliarInventario($cuentaSalida,$areaSalida);
                    $cuentaAuxiliarIngreso=obtenerCuentaAuxiliarInventario($cuentaIngreso,$areaIngreso);

                    $codComprobanteGeneral=$row["cod_comprobantes"];
                    $botonEstado="";
                    if($row["cod_comprobantes"]!=""){
                      $botonEstado="<a class='text-danger' href='../comprobantes/imp.php?comp=".$codComprobanteGeneral."&mon=1' target='_blank'><i class='material-icons'>print</i></a>";
                    }

                    $debeOficial=number_format($costo_salida,2,'.','');
                    $haberOficial=number_format($costo_ingreso,2,'.','');
                    $diferenciaOficial=number_format($diferencia,2,'.','');
                      $index++;
                      ?>
                    <tr>
                      <td class="text-center small"><?=$index;?></td>
                      <td class="text-center small"><b><?=$nro_traspasos;?></b></td>
                      <td class="text-left small" <?=$estiloTipo?>><?=$datos_ingreso_origen;?> <b>(<?=$tipo_comprobante?>)</b></td>
                      <td class="text-left small"><?=$almacen_origen;?></td>
                      <td class="text-left small"><b><?=$almacen_destino;?></b></td>
                      <td class="text-center small"><b><?=$FECHA1;?></b></td>
                      <td class="text-right small"><?=formatNumberDec($costo_salida);?></td>
                      <td class="text-right small"><?=formatNumberDec($costo_ingreso);?></td>
                      <td class="text-right small" <?=$estiloDiferencia?>>
                        <input type="hidden" name="tipo_comprobante<?=$index?>" id="tipo_comprobante<?=$index?>" value="<?=$tipo_comprobante?>">
                        <input type="hidden" name="cod_comprobante<?=$index?>" id="cod_comprobante<?=$index?>" value="<?=$codComprobanteGeneral?>">
                        <input type="hidden" name="diferencia<?=$index?>" id="diferencia<?=$index?>" value="<?=$diferenciaOficial?>">
                        <input type="hidden" name="cod_traspasos<?=$index?>" id="cod_traspasos<?=$index?>" value="<?=$cod_traspasos?>">
                        <input type="hidden" name="nombre_traspasos<?=$index?>" id="nombre_traspasos<?=$index?>" value="<?=$nombreTraspasos?>">
                        <input type="hidden" name="cuenta_salida<?=$index?>" id="cuenta_salida<?=$index?>" value="<?=$cuentaSalida?>">
                        <input type="hidden" name="cuenta_ingreso<?=$index?>" id="cuenta_ingreso<?=$index?>" value="<?=$cuentaIngreso?>">
                        <input type="hidden" name="cuenta_salida_aux<?=$index?>" id="cuenta_salida_aux<?=$index?>" value="<?=$cuentaAuxiliarSalida?>">
                        <input type="hidden" name="cuenta_ingreso_aux<?=$index?>" id="cuenta_ingreso_aux<?=$index?>" value="<?=$cuentaAuxiliarIngreso?>">
                        <input type="hidden" name="area_salida<?=$index?>" id="area_salida<?=$index?>" value="<?=$areaSalida?>">
                        <input type="hidden" name="area_ingreso<?=$index?>" id="area_ingreso<?=$index?>" value="<?=$areaIngreso?>">

                        <input type="hidden" name="haber<?=$index?>" id="haber<?=$index?>" value="<?=$debeOficial?>">
                        <input type="hidden" name="debe<?=$index?>" id="debe<?=$index?>" value="<?=$haberOficial?>">
                        <?=formatNumberDec($diferencia);?></td>
                        <td class="text-right small"><?=$botonEstado?></td>
                    </tr>
                    <?php   
                    // }
                  }?>
                </tbody>
              </table>
              <input type="hidden" name="contador_items" id="contador_items" value="<?=$index?>">            
            </div>
          </div>
          <div class="card-footer fixed-bottom">
              <button type="submit" class="btn btn-rose">Generar Comprobantes</button>
              <!-- <button type="button" class="btn btn-info" >Histórico</button> -->
              <!-- <a class="btn btn-info" target="blank" onClick="historico_ingresos_almacen_nuevo('<?=$fechaDesde?>','<?=$fechahasta?>','<?=$id_sucursal?>')">Histórico</a> -->
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