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
$lista= obtenerIngresosPendientesContabilizarHistorico();

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
                      <h4 class="">INGRESOS ALMACEN CENTRAL HISTORICO<span class="badge bg-info d-none" id="n_list_div_1"></span></h4>
                    </div>
                  </a>
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
                        $codComprobante=$listas->cod_comprobante;
                      ?>
                      <tr>
                        <td class="small"><?=abrevUnidad_solo(1)?> - <?=abrevArea_solo(512)?></td>
                        <td class="font-weight-bold" align="center"><?=$numeroIngTitulo?></td>
                        <td><?=$listas->fecha?></td>
                        <td><?=$listas->tipo_ingreso?></td>
                        <td class="font-weight-bold"><?=$listas->proveedor?></td>
                        <td><?=$listas->observaciones?></td>
                        <td class="font-weight-bold" align="right"><?=$montoFactura?></td>
                        <td class="td-actions text-right">
                          <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                       <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=-1')" class="dropdown-item">
                                                 <i class="material-icons text-muted">monetization_on</i> BIMONETARIO (Bs - Usd)
                                      </a>
                                      <div class="dropdown-divider"></div>
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div> 
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
                <a href="<?=$urlList4?>" target="_blank" class="btn btn-danger float-right">Volver</a>
              </div>    
            </div>
          </div>  
        </div>
    </div>

