<?php
require_once 'conexion.php';
require_once 'styles.php';

// $globalAdmin = $_SESSION["globalAdmin"];

$cod_gestion=$_SESSION['globalNombreGestion'];
$cod_mes=$_SESSION['globalMes'];

$dbh = new Conexion();

$sql="SELECT ap.codigo,ap.cod_gestion,ap.cod_mes,ap.cod_sucursal,(select s.nombre from areas s where s.codigo=ap.cod_sucursal)as sucursal,ap.cod_estado,ap.created_at,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.created_by)as creadopor,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.modified_by)as modificadopor,ap.modified_at,ap.modified_by from asistencia_personal ap where ap.cod_estadoreferencial=1 and ap.cod_estado=3 and ap.cod_gestion=$cod_gestion and ap.cod_mes=$cod_mes order by ap.codigo desc limit 20";
   // echo "<br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
// $stmt->bindColumn('cod_gestion', $cod_gestion);
// $stmt->bindColumn('cod_mes', $cod_mes);
$stmt->bindColumn('sucursal', $sucursal);
$stmt->bindColumn('cod_sucursal', $cod_sucursal);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('creadopor', $creadopor);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modificadopor', $modificadopor);
$array_sucursales_asistencia="";
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-rose card-header-icon">
            <div class="card-icon">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <h4 class="card-title">Asistencia Personal Sucursales</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator100" class="table table-condensed table-bordered">
                <thead>
                  <tr  class='bg-dark text-white'>
                    <th class="text-left">#</th>
                    <th class="text-center">Sucursal</th>
                    <th class="text-center">Gestion</th>
                    <th class="text-center">Mes</th>
                    <th class="text-center">Responsable</th>
                    <th class="text-center">Fecha Creación</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $index = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                      $array_sucursales_asistencia.=$cod_sucursal.",";
                      $sw=0;
                      $btn_delete='';
                      $btn_ws='';
                      $btn_view='';
                      $estilo="";
                      $label="<span>";
                      $titulo_icono="";
                      $titulo_estado="";
                      switch ($cod_estado) {
                        case 2://anulado
                          $nombre_estado="Anulado";
                          $btn_delete='d-none';
                          $btn_ws='d-none';
                          $btn_view='d-none';
                          // $estilo="style='background:#e08982;'";
                          $label="<span class='badge badge-danger'>";
                          break;
                        case 1://Registrado
                            $nombre_estado="Registrado";
                            $label="<span class='badge badge-default'>";
                            $sw=3;//esta en registrado, enviar a autorizacion
                            $titulo_icono="Enviar a Autorización";
                          break;
                        case 3://en revision
                          $nombre_estado="Enviado";
                          $btn_ws='d-none';
                          $btn_delete='d-none';
                          $label="<span class='badge badge-success'>";
                          //$sw=3;//esta en registrado, enviar a autorizacion
                          // $titulo_icono="Enviar a Autorización";
                          break;
                      }
                      ?>
                    <tr <?=$estilo?> >
                      <td class="text-center"><?=$index;?></td>
                      <td class="text-left"><?=$sucursal;?></td>
                      <td class="text-left"><?=$cod_gestion?></td>
                      <td class="text-left"><?=nombreMes($cod_mes)?></td>
                      <td class="text-left" title="Modificado Por:<?=$modificadopor?>"><?=$creadopor?></td>
                      <td class="text-center" title="Fecha Modificación: <?=$modified_at?>"><?=date('d/m/Y',strtotime($created_at));?></td>
                      <td class="text-center"><?=$label.$nombre_estado;?></span></td>
                      <td class="td-actions">
                        <a target="_blank" href="asistencia/asistencia_detalle_view.php?codigo=<?=$codigo?>&q=-100&s=<?=$cod_sucursal?>&cod_mes=<?=$cod_mes?>&cod_gestion=<?=$cod_gestion?>&t=1" class="btn btn-primary btn-sm <?=$btn_view?>"><i class="material-icons" title="Ver Asistencia">visibility</i></a>
                      </td>
                    </tr>
                  <?php
                    $index++;
                   }
                  //Verificar sucursales que faltan
                   $array_sucursales_asistencia=trim($array_sucursales_asistencia,",");
                   $sql_add="";
                   if($array_sucursales_asistencia<>""){
                      $sql_add="and ao.cod_area not in ($array_sucursales_asistencia)";
                   }
                  $sqlSucursales="SELECT ao.cod_area,a.nombre
                    from areas_organizacion ao join areas a on ao.cod_area=a.codigo 
                    where ao.cod_unidad=2 and ao.cod_estadoreferencial=1 and a.distribucion_gastos=1 $sql_add
                    order by a.nombre";
                    // echo $sqlSucursales;
                  $stmtSucursales = $dbh->prepare($sqlSucursales);
                  $stmtSucursales->execute();
                  $stmtSucursales->bindColumn('nombre', $nombre_sucursal);
                
                  ?>
                </tbody>
              </table>
            </div>
          </div>
          </div>
          <div class="card-footer fixed-bottom">
              <button class="btn btn-info btn-round" data-toggle="modal" data-target="#modalAgregarC">Verificar Asistencia</button>          
          </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal agregar -->
<div class="modal fade" id="modalAgregarC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #212f3d;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background: #212f3d; color:white;"><b>Sucursales No Enviados</b></h4>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="tablePaginator100" class="table table-condensed table-bordered table-sm ">
           
            <tbody>
              <?php
              $index_Suc = 1;
              while ($rowSuc = $stmtSucursales->fetch(PDO::FETCH_BOUND)) {
                ?>
                <tr>
                  <td class="text-center"><small><?=$index_Suc;?></small></td>
                  <td class="text-left"><small><?=$nombre_sucursal;?></small></td>
                </tr><?php
                $index_Suc++;
              } ?>
            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">  Cerrar </button>
      </div>
    </div>
  </div>
</div>