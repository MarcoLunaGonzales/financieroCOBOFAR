<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT c.codigo,t.nombre as tipo,c.fecha,c.comprobante,c.token,c.nro_trasaccion_cheque,c.glosa,c.importe,CONCAT(p.primer_nombre,' ',p.paterno) as personal,c.cod_tipocierre,c.cod_comprobante FROM cierre_tesoreria c join personal p on p.codigo=c.cod_personal join tipos_cierre t on t.codigo=c.cod_tipocierre
where c.estado=1 and c.fecha='$fechaActual' order by c.created_at;");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('tipo', $tipoX);
$stmt->bindColumn('fecha', $fechaX);
$stmt->bindColumn('comprobante', $comprobanteX);
$stmt->bindColumn('token', $tokenX);
$stmt->bindColumn('nro_trasaccion_cheque', $chequeX);
$stmt->bindColumn('glosa', $glosaX);
$stmt->bindColumn('importe', $importeX);
$stmt->bindColumn('personal', $personalX);
$stmt->bindColumn('cod_tipocierre', $cod_tipocierreX);
$stmt->bindColumn('cod_comprobante', $cod_comprobanteX);
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">payments</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?> <a class="btn btn-success btn-fab btn-sm" href="cierre_diario_tesoreria/print.php?desde=<?=$fechaActual?>&hasta=<?=$fechaActual?>" title="IMPRIMIR REPORTE DIARIO" target="_blank"><i class="material-icons">print</i></a></h4>

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <!-- <th>Fecha</th> -->
                          <th>Comprobante</th>
                          <th>Token</th>
                          <!-- <th>Cheque</th> -->
                          <th width="30%">Glosa</th>
                          <th>Entrada</th>
                          <th>Salida</th>
                          <th>Saldo</th>
                          <th class="text-right" width="10%">Quitar</th>
                        </tr>
                      </thead>
                      <tbody>
<?php                 
                 $saldo=obtenerSaldoCierreTesoreria($fechaActual);  
                 $saldo=number_format($saldo,2,'.','');                                
?>

                        <tr>
                          <th colspan="6">SALDO DIA ANTERIOR</th>
                          <th style="text-align: right"><?=number_format($saldo,2,'.',',');?></th>
                          <th></th>
                        </tr>
<?php
						            $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          
                        $entrada="";    
                        $salida="";    
                        if($cod_tipocierreX==1){
                          $saldo=$saldo+number_format($importeX,2,'.','');
                          $entrada=number_format($importeX,2,'.',',');  
                        }else{
                          $saldo=$saldo-number_format($importeX,2,'.','');
                          $salida=number_format($importeX,2,'.',',');  
                        }  

                        $botonUrlComprob=$comprobanteX;
                        if($cod_comprobanteX>0){
                          $botonUrlComprob="<a href='comprobantes/imp.php?comp=$cod_comprobanteX&mon=1' target='_blank'>$comprobanteX</a>";
                        }                 
                        
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <!-- <td ><?=$tipoX;?></td> -->
                          <!-- <td><?=date("d/m/Y",strtotime($fechaX));?></td> -->
                          <td><b><?=$botonUrlComprob;?></b></td>
                          <td><?=$tokenX;?></td>
                          <!-- <td ><?=$chequeX;?></td> -->
                          <td style="text-align: left"><?=$glosaX;?></td>
                          <td style="text-align: right"><?=$entrada;?></td>
                          <td style="text-align: right"><?=$salida;?></td>
                          <td style="text-align: right"><?=number_format($saldo,2,'.',',');?></td>
                          <td class="text-right"><a onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete?>&cod=<?=$codigoX?>')" href="#" title="Eliminar" class="btn btn-danger btn-fab btn-sm"><i class="material-icons">delete</i></a></td>
                        </tr>
<?php
							$index++;
                      }
?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="6">SALDO FINAL</th>
                          <th style="text-align: right"><?=number_format($saldo,2,'.',',');?></th>
                          <th></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <?php
              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                    <a class="btn btn-warning" href="<?=$urlRegistro;?>&t=1"><i class="material-icons">arrow_downward</i> NUEVA ENTRADA</a>
                    <?php 
                    if($saldo>0){
                      ?>
                      <a class="btn btn-rose" href="<?=$urlRegistro;?>&t=2"><i class="material-icons">arrow_upward</i> NUEVA SALIDA</a>
                      <?php
                    }
                    ?>
                    
                    <a class="btn btn-default" href="<?=$urlReporte;?>" target="_blank"><i class="material-icons">table_chart</i> Reporte de Movimiento</a>
              </div>
              <?php
              }
              ?>
              </div>
            </div>
          </div>  
        </div>
    </div>
