<?php

require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];


$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$sql="SELECT dc.codigo,dc.fecha,dc.glosa,dc.cod_estado,dc.cod_contabilizado,dc.created_by,dc.created_at,dc.modified_by,dc.modified_by,(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=dc.created_by)as personal
from descuentos_conta  dc
where dc.cod_estado<>2 order by dc.codigo desc limit 100";
// echo  "<br><br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fechaDescuento);
$stmt->bindColumn('glosa', $glosa);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_contabilizado', $cod_contabilizado);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('personal', $personal);
// $stmt->bindColumn('materno', $materno);
// $stmt->bindColumn('primer_nombre', $primer_nombre);
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
            <h4 class="card-title">Descuentos Personal</h4>           
          </div>
          <div class="card-body">
            <div class="table-responsive">              
                <table class="table table-condensed table-bordered table-striped table-sm" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center">Index</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Registrado por</th>
                      <th class="text-center">Nombre Descuento</th>
                      <th class="text-center">Mes Descuento</th>
                      <th class="text-center">Estado</th>
                      <th class="text-center">Comprobante</th>
                      <th class="text-center">Acc/Eventos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      if($cod_contabilizado>0){
                        $contabilizacion=1;
                        $nombre_contabilizado="CONTABILIZADO";
                      }else{
                        $nombre_contabilizado="POR CONTABILIZAR";
                        $contabilizacion=0;
                      }
                      if($created_by==0){
                        $personal=" NO IDENTIFICADO";
                      }
                      $btn_edit="";
                      $btn_printd="";
                      $btn_printc="";                      
                      $btn_enviar="";
                      $label="<span>";
                      $nombre_estado="";
                      $titulo_icono="";
                      switch ($cod_estado) {
                        case 1://registro
                            $label="<span style='color:orange'>";
                            $sw=3;//esta en registrado, enviar a autorizacion
                            $titulo_icono="Generar comprobante y enviar a revisión";
                            // $btn_edit="d-none";
                            //$btn_printd="d-none";
                            $btn_printc="d-none";
                            // $btn_enviar="d-none";
                            $nombre_estado="Registrado";
                          break;
                          case 2://anulado
                            $btn_edit="d-none";
                            $btn_printd="d-none";
                            $btn_printc="d-none";
                            $btn_gencom="d-none";
                            $btn_enviar="d-none";
                            $label="<span style='color:red'>";
                          break;
                          case 3://revision
                            $btn_edit="d-none";
                            //$btn_printd="d-none";
                            // $btn_printc="d-none";
                            $btn_enviar="d-none";
                            $label="<span style='color:green'>";
                            $nombre_estado="Enviado";
                          break;
                      }
                      $datos_fecha=explode('-',$fechaDescuento);
                      $mes_descuento=nombreMes($datos_fecha[1])." - ".$datos_fecha[0];
                     ?>
                      <tr>
                        <td  class="td-actions text-right small"><?=$codigo?></td>
                        <td class="text-center small"><?=date('d/m/Y',strtotime($created_at));?></td>
                        <td class="text-left small"><?=$personal?></td>
                        <td class="text-left small"><?=$glosa;?></td>
                        <td class="text-left small"><?=$mes_descuento;?></td>
                        <td class="text-left small" ><?=$label.$nombre_estado?></span></td>
                        <td class="text-center small"><?=$nombre_contabilizado;?></td>
                        <td class="td-actions text-center">
                          <a href="descuentos_conta/descuentos_detalle.php?codigo=<?=$codigo?>" target="_blank" class="btn btn-success <?=$btn_edit?>">
                            <i class="material-icons" title="Editar Descuentos" >edit</i>
                          </a>
                          <a href="descuentos_conta/descuentos_detalle_print.php?codigo=<?=$codigo?>" target="_blank" class="btn btn-primary <?=$btn_printd?>" >
                            <i class="material-icons" title="Imprimir Descuentos">print</i>
                          </a> 
                          <a href="comprobantes/imp.php?comp=<?=$cod_contabilizado;?>&mon=1" target="_blank" class="btn btn-danger <?=$btn_printc?>">
                            <i class="material-icons" title="Imprimir Comprobante" >print</i>
                          </a>
                          <a href='index.php?opcion=descuentosCambiarEstado&codigo=<?=$codigo?>&sw=<?=$sw?>' rel="tooltip" class="btn btn-info btn-sm <?=$btn_enviar?>">
                            <i class="material-icons" style="color:black"  title="<?=$titulo_icono?>">send</i>
                          </a>
                        </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>
              
            </div>
          </div>
        </div>
        
        <div class="card-footer fixed-bottom">
          <a class="<?=$buttonNormal;?> btn-sm" href="descuentos_conta/descuentos_detalle.php?codigo=0" target="_blank">Registrar</a>
        </div>
        
      </div>
    </div>  
  </div>
</div>

