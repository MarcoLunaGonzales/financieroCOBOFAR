<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalMesActivo=$_SESSION['globalMes'];
$userAdmin=obtenerValorConfiguracion(74);
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();    
$lista= obtenerIngresosPendientesContabilizar();

?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-text">
                  <a href="#">
                    <div id="button_list_div_1" class="card-text bg-default bg-info text-white" style="background:rgba(9, 98, 143 ,1) !important; color:#DEDEDE !important;font-weight:bold !important;">
                      <h4 class="">INGRESOS ALMACEN CENTRAL <span class="badge bg-info d-none" id="n_list_div_1"></span></h4>
                    </div>
                  </a>
                  <h4 class="card-title float-right"><b>Contabilización</b> - Mes y Gestión de Trabajo <b style="color:#FF0000;">[<?=nombreMes($globalMesActivo);?> - <?=$globalNombreGestion?>]</b></h4>
                </div>
                <div class="card-body">
                  <div id="list_div_1">
                    <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr class="bg-info text-white" style="background:rgba(9, 98, 143 ,1) !important; color:#DEDEDE !important;font-weight:bold !important;">
                          <td>Of. - Area</td>
                          <td witdh='10%'>Nº Ingreso.</td>
                          <td witdh='15%'>Fecha Hora</td>
                          <td witdh='5%'>Tipo Ingreso</td>
                          <td witdh='10%'>Proveedor</td>
                          <td witdh='20%'>Observaciones</td>
                          <td witdh='5%'>Monto</td>
                          <td class="text-right" width="5%">Actions</td>
                        </tr>
                      </thead>
                      <tbody>
<?php
                      $index=1;
                      foreach ($lista->lista as $listas) {
                        $codigoIngreso=$listas->cod_ingreso_almacen;
                        $codProveedor=$listas->cod_proveedor;
                        $numeroIngTitulo=$listas->correlativo;
                        $numeroIngTitulo='<a href="#" title="" class="btn btn-info btn-sm btn-round">'.$listas->correlativo.'</a>';
                        $montoFactura=number_format($listas->monto_factura_proveedor,2,'.',',');
                      ?>
                      <tr>
                        <td class="small"><?=abrevUnidad_solo(1)?> - <?=abrevArea_solo(512)?></td>
                        <td class="font-weight-bold" align="center"><?=$numeroIngTitulo?></td>
                        <td><?=$listas->fecha?></td>
                        <td><?=$listas->tipo_ingreso?></td>
                        <td class="font-weight-bold"><?=$listas->proveedor?></td>
                        <td><?=$listas->observaciones?></td>
                        <td class="font-weight-bold" align="right"><?=$montoFactura?></td>
                        <td class="td-actions text-right"><a title="Contabilizar Ingreso Almacen" href="#" onclick="alerts.showSwal('contabilizar-solicitud-recurso','<?=$urlConta?>?admin=0&cod=<?=$codigoIngreso?>'); return false;" class="btn btn-success">
                          <i class="material-icons text-dark">local_atm</i></a>
                        </td>
                      </tr>
                      <?php                
                      }
?>
                      </tbody>
                    </table>
                    <br><br><br><br><br><br><br><br>
                   </div><!--Fin List 1--> 
                   
                </div>
              </div>
              <div class="card-footer fixed-bottom col-sm-9">
                <a href="#" onclick="filaTablaSIS($('#tablas_registradas'));" class="btn btn-rose d-none" id="boton_generar_comprobante"><i class="material-icons">assignment_turned_in</i> Contabilizar Solicitudes <span class='badge bg-white text-rose'> 0</span></a>
                <a href="<?=$urlList6?>" target="_blank" class="btn btn-primary float-right"><i class="material-icons">history</i> <small id="cantidad_eliminados"></small> Histórico</a>
              </div>    
            </div>
          </div>  
        </div>
    </div>

