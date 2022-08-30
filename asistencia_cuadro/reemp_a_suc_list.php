<?php

require_once 'conexion.php';
require_once 'styles.php';

if(isset($_GET['q'])){
  $q=$_GET['q'];//cod_personal
  $s=$_GET['s'];//cod area
}
// $codigoDescuento=0;
//echo "test cod bono: ".$codigoDescuento;
$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];
$globalMesActiva=$_SESSION['globalMes'];
$globalUSer=$_SESSION["globalUser"];
$globalNombrePersonal=solonombrePersonal($globalUSer);
$nombreMes=nombreMes($globalMesActiva);
$dbh = new Conexion();
// Preparamos
$stmt = $dbh->prepare("SELECT m.codigo as codigo, m.nombre as nombre
FROM meses m ");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigoMes);
$stmt->bindColumn('nombre', $nombreMes);
// $stmt->bindColumn('cantidad',$cantidad);
// $cantidad=0;

$nombreSucursal=nameArea($s);
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
                  <h4 class="card-title">Reemplazos a Sucursal <span style="color:blue"><b><?=$nombreSucursal?></b></span></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed table-bordered">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Mes</th>
                          <th class="text-center">Responsable</th>
                          <th class="text-center">Fecha Creación</th>
                          <th>Estado</th>
                          <th width="5px">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $label_mes="";
                          $nombre_estado="No Registrado";
                          $sw=0;
                          $btn_delete='d-none';
                          $btn_ws='d-none';
                          $btn_view='d-none';
                          $estilo="";
                          $label="<span>";
                          // $label_estado="";
                          $titulo_icono="";
                          $titulo_estado="";
                          $btn_register="";
                          $created_at="";
                          $creadopor="";
                          $modified_at="";
                          $modificadopor="";
                          $sql="SELECT ap.codigo,ap.cod_gestion,ap.cod_mes,ap.cod_estado,ap.created_at,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.created_by)as creadopor,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.modified_by)as modificadopor,ap.modified_at,ap.modified_by 
                            from asistencia_personal ap 
                            where ap.cod_estadoreferencial=1 and ap.cod_sucursal in ($s) and cod_gestion=$nombreGestion and cod_mes=$codigoMes";
                          $stmtDet = $dbh->prepare($sql);
                          $stmtDet->execute();
                          $stmtDet->bindColumn('codigo', $codigo);
                          $stmtDet->bindColumn('cod_gestion', $cod_gestion);
                          $stmtDet->bindColumn('cod_mes', $cod_mes);
                          // $stmtDet->bindColumn('sucursal', $sucursal);
                          $stmtDet->bindColumn('cod_estado', $cod_estado);
                          $stmtDet->bindColumn('created_at', $created_at);
                          $stmtDet->bindColumn('creadopor', $creadopor);
                          $stmtDet->bindColumn('modified_at', $modified_at);
                          $stmtDet->bindColumn('modificadopor', $modificadopor);
                        while ($rowdet = $stmtDet->fetch(PDO::FETCH_BOUND)) { 
                          $created_at=date('d/m/Y',strtotime($created_at));
                          $sw=0;
                          $btn_delete='';
                          $btn_ws='';
                          $btn_view='';
                          $estilo="";
                          $label="<span>";
                          $titulo_icono="";
                          $titulo_estado="";
                          $btn_register="";
                          switch ($cod_estado) {
                            case 2://anulado
                              $nombre_estado="Anulado";
                              $btn_delete='d-none';
                              $btn_ws='d-none';
                              $btn_view='d-none';
                              // $estilo="style='background:#e08982;'";
                              $label="<span class='badge badge-danger'>";
                              $btn_register="d-none";
                              break;
                            case 1://Registrado
                                $nombre_estado="Registrado";
                                $label="<span class='badge badge-default'>";
                                $sw=3;//esta en registrado, enviar a autorizacion
                                $titulo_icono="Enviar a Autorización";
                                $btn_register="d-none";
                              break;
                            case 3://en revision
                              $nombre_estado="Enviado";
                              $btn_ws='d-none';
                              $btn_delete='d-none';
                              $label="<span class='badge badge-success'>";
                              $btn_register="d-none";
                              //$sw=3;//esta en registrado, enviar a autorizacion
                              // $titulo_icono="Enviar a Autorización";
                              break;
                          }
                        }
                          ?>
                          <tr <?=$label_mes?>>
                            <td align="center"><?=$index;?></td>
                            <td class="text-left"><?=$nombreMes."/".$nombreGestion;?></td>
                            <td class="text-left" title="Modificado Por : <?=$modificadopor?>"><?=$creadopor?></td>
                            <td class="text-center" title="Fecha Modificación : <?=$modified_at?>"><?=$created_at;?></td>
                            <td class="text-center"><?=$label.$nombre_estado;?></span></td>
                            <td class="td-actions text-left">
                              <a href='asistencia_cuadro/asistencia_cambiarestado.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>' class="btn btn-info btn-sm <?=$btn_ws?>">
                                <i class="material-icons" title="Enviar Asistencia">send</i>
                              </a>
                              <a href="asistencia_cuadro/asistencia_detalle.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&cod_mes=<?=$cod_mes?>&cod_gestion=<?=$cod_gestion?>" class="btn btn-success btn-sm <?=$btn_ws?>"><i class="material-icons" title="Editar Asistencia">edit</i></a>
                              <a href="asistencia_cuadro/asistencia_detalle_view.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&cod_mes=<?=$cod_mes?>&cod_gestion=<?=$cod_gestion?>" class="btn btn-primary btn-sm <?=$btn_view?>"><i class="material-icons" title="Ver Asistencia">visibility</i></a>
                              <a href='asistencia_cuadro/asistencia_save_delete.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>' rel="tooltip" class="btn btn-danger btn-sm <?=$btn_delete?>">
                                <i class="material-icons" title="Anular">delete</i>
                              </a>
                              <a  href="asistencia_cuadro/asistencia_detalle.php?codigo=0&q=<?=$q?>&s=<?=$s?>&cod_mes=<?=$codigoMes?>&cod_gestion=<?=$nombreGestion?>" class="btn btn-success btn-sm btn-round <?=$btn_register?>"><i class="material-icons" title="Registrar Cuadro de Reemplazos">add</i></a>
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