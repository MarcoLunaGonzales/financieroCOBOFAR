<?php

require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];


$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT dc.codigo,dc.fecha,dc.glosa,dc.cod_estado,dc.cod_contabilizado,dc.created_by,dc.created_at,dc.modified_by,dc.modified_by,p.paterno,p.materno,p.primer_nombre
from descuentos_conta  dc join personal p on dc.created_by=p.codigo
where dc.cod_estado<>2 order by dc.codigo desc limit 100";
// echo  "<br><br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('glosa', $glosa);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_contabilizado', $cod_contabilizado);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('primer_nombre', $primer_nombre);
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
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center">Index</th>
                      <th class="text-center">Fecha</th>
                      <th class="text-center">Registrado por</th>
                      <th class="text-center">Nombre Descuento</th>
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
                      $btn_edit="";
                      $btn_printd="";
                      $btn_printc="";                      
                      $btn_enviar="";
                      $label="<span>";
                      $nombre_estado="";
                      $titulo_icono="";
                      switch ($cod_estado) {
                        case 1://registro
                            $label="<span class='badge badge-default'>";
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
                            $label="<span class='badge badge-danger'>";
                          break;
                          case 3://revision
                            $btn_edit="d-none";
                            //$btn_printd="d-none";
                            // $btn_printc="d-none";
                            $btn_enviar="d-none";
                            $label="<span class='badge badge-warning'>";
                            $nombre_estado="En revisión";
                          break;
                          case 4://registro
                            $btn_edit="d-none";
                            //$btn_printd="d-none";
                            $btn_printc="d-none";
                            $btn_enviar="d-none";                 
                            $label="<span class='badge badge-info'>";
                            //$sw=3;//esta en registrado, enviar a autorizacion
                            $titulo_icono="Aprobar Detalle";
                            $nombre_estado="Aprobado";
                          break;
                      }
                     ?>
                      <tr>
                        <td  class="td-actions text-right"><?=$index?></td>
                          <td class="text-center small"><small><?=$fecha;?></small></td>
                          <td class="text-left small"><small><?=$primer_nombre?> <?=$paterno?> <?=$materno?></small></td>
                          <td class="text-left small"><small><?=$glosa;?></small></td>
                          <td class="text-left small" ><small><?=$label.$nombre_estado?></span></small></td>
                          <td class="text-center small"><small><?=$nombre_contabilizado;?></small></td>
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
          <a class="btn btn-success btn-sm" href="descuentos_conta/descuentos_detalle_consolidado.php?codigo=0" target="_blank">Consolidar</a>
        </div>
        
      </div>
    </div>  
  </div>
</div>

