<?php

require_once 'conexion.php';
// require_once 'configModule.php';
require_once 'styles.php';

// $codigoDescuento=0;
//echo "test cod bono: ".$codigoDescuento;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];
$globalMesActiva=$_SESSION['globalMes'];

$globalUSer=$_SESSION["globalUser"];
$globalNombrePersonal=solonombrePersonal($globalUSer);

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT m.codigo as codigo, m.nombre as nombre
FROM meses m ");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
// $stmt->bindColumn('cantidad',$cantidad);

$cantidad=0;
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Descuentos Consolidado por Mes</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Mes</th>
                          <th>Estado</th>
                          <th>Cantidad de Registros</th>
                          <th width="5px">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      	$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $label_mes="";
                          $label_estado="";
                          $titulo_icono="Enviar a Aprobación";
                          $btn_detalle="";
                          $btn_print="d-none";
                          $btn_enviar="d-none";
                          if($globalMesActiva==$codigo){
                            $btn_enviar="";
                            $label_mes="style='color:#633974;font-weight:bold;'";
                            $label_estado="style='color:#633974;font-weight:bold;'";
                          }
                          $nombreEstado="No Registrado";
                          $stmtEst = $dbh->prepare("SELECT t.codigo,t.nombre
                            from  descuentos_conta_consolidado d join tipos_estado_descuento_consolidado t on d.cod_estado=t.codigo
                            where d.mes=$codigo and d.gestion='$nombreGestion' limit 1");
                          $stmtEst->execute();
                          $stmtEst->bindColumn('codigo', $codigoEstado);
                          $stmtEst->bindColumn('nombre', $nombreEstado);
                          while ($rowEst = $stmtEst->fetch(PDO::FETCH_BOUND)) {
                            $nombreEstado=$nombreEstado;
                            switch ($codigoEstado) {
                              case 1://validado
                              $label_estado="style='color:green;font-weight:bold;'";
                              $btn_detalle="d-none";
                              $btn_enviar="";
                              $btn_print="";
                              $sw=3;
                              break;
                              case 2://anulado
                              $label_estado="style='color:red;font-weight:bold;'";
                              $btn_detalle="d-none";
                              $btn_enviar="d-none";
                              $btn_print="";
                              break;
                              case 3://aprobado
                              $label_estado="style='color:blue;font-weight:bold;'";
                              $btn_detalle="d-none";
                              $btn_enviar="d-none";
                              $btn_print="";
                              break;
                              case 4://contabilizado
                              $label_estado="style='color:orange;font-weight:bold;'";
                              $btn_detalle="d-none";
                              $btn_enviar="d-none";
                              $btn_print="";
                              break;
                            }
                          } ?>
                          <tr <?=$label_mes?>>
                            <td align="center"><?=$index;?></td>
                            <td class="text-left"><?=$nombre."/".$nombreGestion;?></td>
                            <td class="text-center" <?=$label_estado?>><?=$nombreEstado?></td>
                            <td class="text-center"><?=$cantidad." registros";?></td>
                            <td class="td-actions text-left">
                              <a href='descuentos_conta/descuentos_detalle_consolidado.php?cod_mes=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonMorado;?> <?=$btn_detalle?>">
                                <i class="material-icons" title="Ver Descuentos">playlist_add</i>
                              </a>
                              <!-- <a href='index.php?opcion=descuentosCambiarEstado&codigo=<?=$codigo?>&sw=<?=$sw?>' rel="tooltip" class="btn btn-info btn-sm <?=$btn_enviar?>">
                                <i class="material-icons"   title="<?=$titulo_icono?>">send</i>
                              </a> -->
                              <button  type="button" onclick="GuardarConsolidadoDescuentos(<?=$codigo?>,<?=$nombreGestion?>,'<?=$globalNombrePersonal?>',<?=$globalUSer?>)" class="btn btn-sm <?=$btn_enviar?>" style="background: #18537e; color:white;"><i class="material-icons"   title="<?=$titulo_icono?>">send</i></button>
                              <a href="comprobantes/imp.php?comp=<?=$cod_contabilizado;?>&mon=1" target="_blank" class="btn btn-danger <?=$btn_print?>">
                                <i class="material-icons" title="Imprimir Comprobante" >print</i>
                              </a>
                            </td>
                          </tr>
                        <?php
                        	$index++;
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>		  
            </div>
          </div>  
        </div>
    </div>