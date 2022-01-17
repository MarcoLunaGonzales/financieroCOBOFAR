<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();


// $valor_forma_pago=obtenerValorConfiguracion(76);
// $valor_razon_social=obtenerValorConfiguracion(77);
$valor_validacion=obtenerValorConfiguracion(90); //validación de Libretas Bancarias en Comprobante
$cod_contracuentapagos=obtenerValorConfiguracion(38);//contra cuenta pago proveedores
$cod_tipocomprobantepago=obtenerValorConfiguracion(108);//TIPO COMPROBANTE PAGO PROVEEDORES

// if($valor_forma_pago==1)
//   $sw_sf_fp="checked";
// else $sw_sf_fp="";

// if($valor_razon_social==1)
//   $sw_f_rs="checked";
// else $sw_f_rs="";

if($valor_validacion==1)
  $sw_v_lc="checked";
else $sw_v_lc="";
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="utilitarios/configuracion_edit_sf_save.php" method="post">
              <div class="card">
                <div class="card-header card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Configuraciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed  table-sm">
                      <thead class="fondo-boton">
                        <tr>
                          <th align="center">Configuración</th>
                          <th width="40%" align="center">H/D</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- <tr>
                          <td class="text-left">Edición de Forma de Pago en SF</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  name="modal_check_sf" id="modal_check_sf" <?=$sw_sf_fp?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>                          
                        </tr> -->
                        <!-- <tr>
                          <td class="text-left">Edición de Razón Social en Facturas</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  id="modal_check_f" name="modal_check_f" <?=$sw_f_rs?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>
                        </tr> -->
                        <tr>
                          <td class="text-left">Validación de Libretas Bancarias en Comprobantes</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  id="modal_check_lb" name="modal_check_lb" <?=$sw_v_lc?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-left">Contra Cuenta Pago Proveedores (Haber)</td>
                          <td>
                              <div class="col-sm-10">
                                <div class="form-group">
                                  <select name="contra_cuentas_pagos" id="contra_cuentas_pagos" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    
                                    <?php 
                                    $sqlTipobien="SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1 order by nombre";
                                    $stmtTipoBien = $dbh->prepare($sqlTipobien);
                                    $stmtTipoBien->execute();
                                    while ($row = $stmtTipoBien->fetch()){ ?>
                                        <option <?php if($cod_contracuentapagos == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-left">Tipo Comprobante Pago Proveedores</td>
                          <td>
                              <div class="col-sm-10">
                                <div class="form-group">
                                  <select name="tipo_comprobante_pagoproveedores" id="tipo_comprobante_pagoproveedores" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    
                                    <?php 
                                    $sqlTipobien="SELECT codigo,nombre from tipos_comprobante where cod_estadoreferencial=1 order by nombre";
                                    $stmtTipoBien = $dbh->prepare($sqlTipobien);
                                    $stmtTipoBien->execute();
                                    while ($row = $stmtTipoBien->fetch()){ ?>
                                        <option <?php if($cod_tipocomprobantepago == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                          </td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td><td></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td><td></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td><td></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td><td></td>
                        </tr>
                        <tr>
                          <td>&nbsp;</td><td></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                </div>
              </div>
              
               </form>
            </div>
          </div>  
        </div>
    </div>
