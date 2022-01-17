<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';
require_once '../styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$codPagoLote=$_GET['cod'];
$codigo=0;
$codigosProv=[];
$indexProv=0;
$stmtPago = $dbh->prepare("SELECT pl.nombre,pl.fecha ,(select p.observaciones from pagos_proveedores p where p.cod_pagolote=pl.codigo limit 1)as observaciones from pagos_lotes pl where pl.codigo=$codPagoLote");
$stmtPago->execute();
$obsPago="";
$fechaPago="";
$observacionesPago="";
while ($row = $stmtPago->fetch(PDO::FETCH_ASSOC)) {
   // $codigosProv[$indexProv]=$row['cod_proveedor'];
   $obsPago=$row['nombre'];
   $observacionesPago=$row['observaciones'];
   $fechaPago=strftime('%d/%m/%Y',strtotime($row['fecha']));
   $indexProv++;
}
// $codigo=implode(",", $codigosProv);
?>
<input type="hidden" id="cod_solicitud" value="<?=$codSol?>">
<input type="hidden" id="cod_pagoproveedor" value="<?=$codigoPago?>">

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">edit</i>
                  </div>
                  <h4 class="card-title">Editar Lotes Pagos por Proveedor</h4>
                </div>
                <form id="form-pagos" action="<?='../'.$urlSaveEditLote?>" method="post">
                  <input type="hidden" value="<?=$codPagoLote?>" name="cod_pagoloteedit" id="cod_pagoloteedit">
                <div class="card-body">
                  <div class="row">
                    <table class="table table-condensed table-warning">
                      <tr>
                        <td class="text-right font-weight-bold">Nombre Lote</td>
                        <td class="text-left" width="26%">
                        	<div class="form-group">
                               <input type="text" class="form-control" value="<?=$obsPago?>" name="nombre_lote" id="nombre_lote" required>
                             </div>
                        </td>
                        <td class="text-right font-weight-bold">Fecha del pago</td>
                        <td class="text-left">
                        	<div class="form-group">
                               <input type="text" class="form-control datepicker" name="fecha_pago" id="fecha_pago" value="<?=$fechaPago?>">
                             </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-right font-weight-bold">Observaciones</td>
                        <td class="text-left" width="" colspan="3">
                        	<div class="form-group">
                               <textarea type="text" class="form-control" name="observaciones_pago" id="observaciones_pago"><?=$observacionesPago?></textarea>
                             </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="row col-sm-12">
                     <div class="col-sm-12">
                    <table id="" class="table table-condensed small">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">                           
                          <td class="text-left">Of</td>
                          <td class="text-left">CC</td>
                          <td class="text-left">Tipo/#</td>
                          <td class="text-left">F Comp.</td>
                          <td class="text-left">F.EC</td>
                          <td class="text-left">Proveedor</td>
                          <td class="text-left">Glosa</td>
                          <td class="text-left">Debe</td>
                          <td class="text-left">Haber</td>
                          <td class="text-left">Saldo</td>
                          <td class="text-left">Monto</td>
                          <!-- <td width="10%">Tipo</td>
                          <td width="10%">Bancos</td>
                          <td width="10%">Cheques</td>
                          <td width="10%">Nº Cheque</td>
                          <td width="10%">Beneficiario</td> -->
                          <td width="4%" class="text-right">Actions</td>
                        </tr>
                      </thead>
                      <tbody id="data_pagosproveedores">
                         <?php
                         // $cantidadProveedores=0;
                         
                     include "detallePagosLotes.php";
                      ?> 
                      </tbody>
                    </table>
                  </div>
                  	 <input type="hidden" id="cantidad_proveedores" name="cantidad_proveedores"  value="<?=$cantidadProveedores?>">
                  </div>
                </div>
              </div>
               <?php
              //if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button type="submit" class="btn btn-success">GUARDAR</button> 
                <a href="<?="../".$urlListPagoLotes?>" class="btn btn-danger">VOLVER</a> 
              </div>
              
              </form>  
              <?php
             // }
              ?>
            </div>
          </div>  
        </div>
    </div>


<div class="fixed-plugin" style="background:rgba(33, 97, 140,0.6);"><!-- #21618C  -->
  <a title="Adicionar Proveedores" href="#" onclick="cargarLotesPago()" class="text-white"><i class="material-icons" style="font-size:40px;">view_comfy</i></a>
</div>

<div class="modal fade modal-arriba modal-primary" id="modalLotesPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 90% !important;">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
        <div class="card-text">
          <h4>Lotes Pago - Proveedores</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <label class="col-sm-2 col-form-label">Cuentas</label>
          <div class="col-sm-6">
            <div class="form-group">
              <select class="selectpicker form-control form-control-sm"  name="cuentas_proveedor[]" id="cuentas_proveedor" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-live-search="true" onchange="seleccionar_proveedor_pagos(1)">
                <?php                   
                  $sql="SELECT p.codigo,p.nombre,p.numero from configuracion_estadocuentas c,plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_tipoestadocuenta in (1) order by p.numero";
                   $stmt3 = $dbh->prepare($sql);
                   $stmt3->execute();
                 while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoSel=$rowSel['codigo'];
                  $nombreSelX=$rowSel['nombre'];
                  $numeroSelX=$rowSel['numero'];
                  ?><option value="<?=$codigoSel;?>"><?=$numeroSelX?> - <?=$nombreSelX?></option><?php 
                 }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Proveedor</label>
          <div class="col-sm-6">
            <div class="form-group" id="contenedor_proveedor">
              <select class="selectpicker form-control form-control-sm"  data-live-search="true" name="proveedor" id="proveedor" data-style="btn btn-primary">
                <option  value="">--PROVEEDOR--</option>
                
              </select>
            </div>
          </div> 
          
          <!-- <div class="col-sm-1">
            <a href="#" onclick="cargarDatosProveedorPagosLote(0)" class="btn btn-white btn-sm" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">search</i> Buscar</a>
          </div>  -->   
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Fecha Inicio</label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input class="form-control" type="date" name="fechainicio" id="fechainicio" required="true" value="<?=date('Y-m-d');?>" required="true"/>
            </div>
          </div>
          <label class="col-sm-1 col-form-label">Fecha Fin</label>
          <div class="col-sm-2">
            <div class="form-group" >
              <input class="form-control" type="date" name="fechafin" id="fechafin" required="true" value="<?=date('Y-m-d');?>" required="true"/>
            </div>
          </div> 
          
          <div class="col-sm-1">
            <a href="#" onclick="cargarDatosProveedorPagosLote(1)" class="btn btn-white btn-sm" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">search</i> Buscar</a>
          </div>    
        </div>
        <br>
        <table class="table table-bordered table-condensed small">
          <thead>
            <tr style="background:#21618C; color:#fff;">                           
              <td class="text-left">Of</td>
              <td class="text-left">CC</td>
              <td class="text-left">Tipo/#</td>
              <td class="text-left">F Comp.</td>
              <td class="text-left">F.EC</td>
              <td class="text-left">Proveedor</td>
              <td class="text-left">Glosa</td>
              <td class="text-left">Debe</td>
              <td class="text-left">Haber</td>
              <td class="text-left">Saldo</td>
              <td width="4%" class="text-right">Actions</td>
            </tr> 
          </thead>
          <tbody id="tabla_proveedor">
           
          </tbody>
        </table>
      </div>
      <input type="hidden" id="cantidad_proveedores_modal" name="cantidad_proveedores_modal"value="0">
      <input type="hidden" id="cantidad_proveedores" name="cantidad_proveedores"value="0">
      <div class="card-footer d-flex">
        <a href="#" onclick="agregarLotePago_seleccionados(1)" class="btn btn-white btn-sm mx-auto" style="background:#07B46D; color:#F7FF5A;">SELECCIONAR</a>
      </div>
    </div>  
  </div>
</div>