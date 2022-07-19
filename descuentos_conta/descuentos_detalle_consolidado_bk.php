<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

// $globalUser=$_SESSION["globalUser"];
//$dbh = new Conexion();
// $globalMes=$_SESSION["globalMes"];
$globalMes=$_GET['cod_mes'];
$globalGestion=$_SESSION["globalNombreGestion"];
$globalUSer=$_SESSION["globalUser"];
$globalNombrePersonal=solonombrePersonal($globalUSer);

$nombreMes=nombreMes($globalMes);
$dbh = new Conexion();

?>

<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <!-- <form id="formSoliFactTcp" class="form-horizontal" action="descuentos_detalle_save.php" method="post" onsubmit="return valida(this)" enctype="multipart/form-data"> -->
                    <input type="hidden" name="cod_mes" id="cod_mes" value="<?=$globalMes;?>"/>
                    <input type="hidden" name="gestionCab" id="gestionCab" value="<?=$globalGestion;?>"/>
                    
                    <div class="card">
                        <div class="card-header card-header-info card-header-text" >
                            <div class="card-text" style=" color:#18537e;">
                              <h4 class="card-title">Consolidación de Descuentos<br><?=$nombreMes?> <?=$globalGestion?></h4>
                            </div>
                        </div>
                        <div class="card-body ">                            
                            <div class="table-responsive">              
                                <table class="table table-condensed table-bordered table-striped table-secondary" >
                                    <thead >
                                        <tr style="background:#18537e;color:white;">
                                          <th class="text-center small"><small>#</small></th>
                                          <th class="text-center small"><small>Personal</small></th>
                                          <th class="text-center small"><small>Haber Básico</small></th>
                                          <th class="text-center small"><small>Descuento (<?=$nombreMes?>)</small></th>
                                          <th class="text-center small"><small>Saldo</small></th>
                                          <th class="text-center small"><small>% Descuento</small></th>
                                          <th class="text-center small"><small>Estado</small></th>
                                          <th class="text-center small"><small>-</small></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $index=1;
                                        $Sumadescuento=0;
                                        $Sumasaldo_descuento=0;
                                        $sql="SELECT dd.cod_personal,sum(ddm.monto)as descuento,p.haber_basico,p.primer_nombre,p.paterno,p.materno
                                            from descuentos_conta d  join descuentos_conta_detalle dd on d.codigo=dd.cod_descuento join personal p on dd.cod_personal=p.codigo join descuentos_conta_detalle_mes  ddm on ddm.cod_descuento_detalle=dd.codigo
                                            where d.cod_estado=3 and ddm.mes=$globalMes and ddm.gestion=$globalGestion
                                            GROUP BY dd.cod_personal
                                            order by p.paterno";
                                             // echo $sql;
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
                                        $stmt->bindColumn('cod_personal', $cod_personal);
                                        $stmt->bindColumn('descuento', $descuento);
                                        $stmt->bindColumn('haber_basico', $haber_basico);
                                        $stmt->bindColumn('primer_nombre', $primer_nombre);
                                        $stmt->bindColumn('paterno', $paterno);
                                        $stmt->bindColumn('materno', $materno);
                                        $contadorError=0;
                                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                            $porcentaje=round($descuento*100/$haber_basico,2);
                                            $estado="VALIDO";
                                            $label_error="<span class='badge badge-success'>";
                                            $label_saldo="<span>";
                                            if($porcentaje>20){
                                                $estado="ERROR";
                                                $contadorError++;
                                                $label_error="<span class='badge badge-danger'>";
                                            }
                                            $nombre_personal=$paterno." ".$materno." ".$primer_nombre;
                                            $datos_modal=$cod_personal."###".$nombre_personal."###".$haber_basico."###".$descuento."###".$globalMes."###".$globalGestion;
                                            $queryDet ="SELECT sum(dd.diferencia) as monto_descuento
                                                from descuentos_conta_detalle dd join descuentos_conta d on d.codigo=dd.cod_descuento
                                                where  d.cod_estado=3 and dd.cod_personal=$cod_personal";
                                            $stmtDet = $dbh->query($queryDet);  
                                            $monto_descuento=0;
                                            while ($rowDet = $stmtDet->fetch()){
                                                $monto_descuento=$rowDet["monto_descuento"];
                                            }
                                            $saldo_descuento=$monto_descuento-$descuento;
                                            if($saldo_descuento>0){
                                                $label_saldo="<span style='color:red'>";
                                            }
                                            //TOTALES
                                            $Sumadescuento+=$descuento;
                                            $Sumasaldo_descuento+=$saldo_descuento;

                                            ?>
                                            <tr>
                                                <td class="text-left"><small><?=$index?></small></td>
                                                <td class="text-left"><small><?=$nombre_personal?></small></td>
                                                <td class="text-right"><small><?=formatNumberDec($haber_basico,2);?></small></td>
                                                <td class="text-right" ><small><?=formatNumberDec($descuento,2)?></small></td>
                                                <td class="text-right" ><small><?=$label_saldo?><?=formatNumberDec($saldo_descuento,2)?></span></small></td>
                                                <td class="text-right" ><small><?=$porcentaje?></small></td>
                                                <td class="text-center"><small><?=$label_error.$estado;?></span></small></td>
                                                <td class="td-actions text-center">
                                                    <?php
                                                    if(!isset($_GET['cod_view'])){?>
                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalDetalleDescuentos" onclick="modalDetalleDescuentos('<?=$datos_modal;?>')">
                                                            <i class="material-icons" title="Ver Detalle">article</i>
                                                        </button>
                                                    <?php }
                                                    ?>
                                                </td>
                                              </tr>
                                            <?php
                                            $index=$index+1;
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background:#18537e;color:white;">
                                            <td class="text-left"><small>-</small></td>
                                            <td class="text-left"><small>TOTAL</small></td>
                                            <td class="text-right"><small>-</small></td>
                                            <td class="text-right" ><small><?=formatNumberDec($Sumadescuento,2)?></small></td>
                                            <td class="text-right" ><small><?=formatNumberDec($Sumasaldo_descuento,2)?></small></td>
                                            <td class="text-right" ><small>-</small></td>
                                            <td class="text-center"><small>-</span></small></td>
                                            <td class="td-actions text-center">-</td>
                                          </tr>
                                    </tfoot>
                                </table>
                            </div>      
                        </div>
                        <div class="card-footer fixed-bottom">
                            <a href='#' class="btn btn-warning btn-sm">Ap. Sindicato</a>
                            <button  type="button" onclick="window.close();" class="btn btn-danger btn-sm" >Cerrar</button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<!--end small modal -->
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<!-- modal descuentos detalleS -->
<div class="modal fade" id="modalDetalleDescuentos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">      
      <div class="modal-header" style="background: #45b39d;" >
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background: #45b39d; color:white;">
            <textarea style="background: #45b39d;color:white;font-size: 11px;" readonly="true" type="text" class="form-control small" id="nombre_persona_modal" name="nombre_persona_modal"></textarea>
        </h4>

      </div>
      <div class="modal-body">
         <table class="table table-condensed table-bordered small">
          <thead>
           <tr style="background:white;color:#45b39d;">
             <td>#</td>
             <td width="10%">Tipo Descuento</td>
             <td width="10%">Descuento Bs</td>
             <td width="10%">Descontado Bs</td>
             <td width="10%">Saldo Bs</td>
             <td >Glosa</td>             
             <td></td>
           </tr>
           </thead>
           <tbody id="contenedor_descuento_detalle" style="background:white;">
             
           </tbody>          
         </table>         
      </div>
      <div class="modal-footer">
        <!-- <a  type="button" href="../descuentos_conta/descuentos_detalle_consolidado.php?codigo=0" class="btn btn-danger btn-sm close"  aria-label="Close">Cerrar</a> -->
        <a  type="button"  data-dismiss="modal" class="btn btn-danger btn-sm close"  aria-label="Close">Cerrar</a>
      </div>
    </div>
  </div>
</div>


<!-- modal descuentos detalle MES -->
<div class="modal fade" id="modalDetalleDescuentosUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content" style="background-color:#e2e6e7">
      <div class="modal-header" style="background:#cd6155;" >
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <input type="hidden" name="codigo_detalle" id="codigo_detalle" value="0">
        <input type="hidden" name="gestion_detalle" id="gestion_detalle" value="0">
        <input type="hidden" name="monto_descuento_detalle" id="monto_descuento_detalle" value="0">
        <h4 class="modal-title" id="myModalLabel" style="background:#cd6155; color:white;">
            <textarea style="background: #cd6155;color:white;font-size: 11px;" readonly="true" type="text" class="form-control small" id="nombre_persona_modal2" name="nombre_persona_modal2"> </textarea>
        </h4>
      </div>
      <div class="modal-body">
         <table class="table table-condensed table-bordered small">
          <thead>
           <tr style="background:white;color:#cd6155;">
             <td>#</td>             
             <td>Mes</td>
             <td>Monto</td>
             <td></td>
           </thead>
           <tbody id="contenedor_descuento_detalle_update" style="background:white;">
             
           </tbody>          
         </table>
      </div>
      <div class="modal-footer">
        <button  type="button" onclick="cambiarMesDescuentoPersonal()" class="btn btn-success btn-sm close" aria-label="Close">Guardar</button>
        <a  type="button"  data-dismiss="modal" class="btn btn-danger btn-sm close"  aria-label="Close">Cancelar</a>
      </div>
    </div>
  </div>
</div>

