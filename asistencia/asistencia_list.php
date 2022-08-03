<?php
require_once 'conexion.php';
require_once 'styles.php';

// $globalAdmin = $_SESSION["globalAdmin"];
$dbh = new Conexion();

if(isset($_GET['q'])){
$q=$_GET['q'];//cod_personal
$s=$_GET['s'];//cod area
$sql="SELECT ap.codigo,ap.cod_gestion,ap.cod_mes,(select s.nombre from areas s where s.codigo=ap.cod_sucursal)as sucursal,ap.cod_estado,ap.created_at,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.created_by)as creadopor,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=ap.modified_by)as modificadopor,ap.modified_at,ap.modified_by from asistencia_personal ap where ap.cod_estadoreferencial=1 and ap.cod_sucursal in ($s) order by ap.codigo desc limit 20";
   // echo "<br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_gestion', $cod_gestion);
$stmt->bindColumn('cod_mes', $cod_mes);
$stmt->bindColumn('sucursal', $sucursal);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('creadopor', $creadopor);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modificadopor', $modificadopor);

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
            <h4 class="card-title">Asistencia del Personal</h4>
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
                        <a href='asistencia/asistencia_cambiarestado.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>' class="btn btn-info btn-sm <?=$btn_ws?>">
                          <i class="material-icons" title="Enviar Asistencia">send</i>
                        </a>
                        <a href="asistencia/asistencia_detalle.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&cod_mes=<?=$cod_mes?>&cod_gestion=<?=$cod_gestion?>" class="btn btn-success btn-sm <?=$btn_ws?>"><i class="material-icons" title="Editar Asistencia">edit</i></a>
                        <a href="asistencia/asistencia_detalle_view.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>&cod_mes=<?=$cod_mes?>&cod_gestion=<?=$cod_gestion?>" class="btn btn-primary btn-sm <?=$btn_view?>"><i class="material-icons" title="Ver Asistencia">visibility</i></a>
                        <a href='asistencia/asistencia_save_delete.php?codigo=<?=$codigo?>&q=<?=$q?>&s=<?=$s?>' rel="tooltip" class="btn btn-danger btn-sm <?=$btn_delete?>">
                          <i class="material-icons" title="Anular">delete</i>
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
          <div class="card-footer fixed-bottom">
              <a  href="asistencia/asistencia_detalle.php?codigo=0&q=<?=$q?>&s=<?=$s?>" class="btn btn-primary btn-sm btn-round <?=$btn_view?>">Registrar</a>            
          </div>
      </div>
    </div>
  </div>
</div>

<?php
}
?>