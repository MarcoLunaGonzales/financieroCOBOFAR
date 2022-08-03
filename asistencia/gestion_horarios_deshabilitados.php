<?php
require_once 'conexion.php';
//require_once 'asistencia/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT h.codigo,t.descripcion as tipo,h.descripcion,h.hora_ingreso,h.hora_salida from horarios h join horarios_tipo t on t.codigo=h.tipo where h.estado=0 order by t.codigo;";
$stmt = $dbh->prepare($sql);
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('tipo', $tipoX);
$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('hora_ingreso', $hora_ingresoX);
$stmt->bindColumn('hora_salida', $hora_salidaX);

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-default card-header-icon">
            <div class="card-icon">
              <i class="material-icons">timer</i>
            </div>
            <h4 class="card-title">Horarios Deshabilitados</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <div class="" id="data_activosFijos">
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center">TIPO HORARIO</th>
                      <th class="text-center">DESCRIPCIÓN</th>
                      <th class="text-center">HORA INGRESO</th>
                      <th class="text-center">HORA SALIDA</th>
                      <th class="text-center">OPCIONES</th>                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {              
                     ?>
                      <tr>
                          <td width="15%" class="text-left small"><?=$tipoX;?></td>
                          <td class="text-left small"><?=$descripcionX;?></td>
                          <td width="15%" class="text-center small"><?=$hora_ingresoX;?></td>
                          <td width="15%" class="text-center small"><?=$hora_salidaX?></span></td>  
                          <td width="10%" class="td-actions text-right">
                           <a href="asistencia/gestion_horarios_delete.php?codigo=<?=$codigoX?>&e=1" target="_blank" > 
                                  <i class="material-icons" title="Recuperar" style="color:green">restore_from_trash</i>
                                </a>
                          </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php
        if($globalAdmin==1){
        ?>
        <div class="card-footer fixed-bottom">           
          <a class="btn btn-default text-white" href="?opcion=rpt_gestion_horarios_from">Volver</a>

        </div>
        <?php
        }
        ?>
      </div>
    </div>  
  </div>
</div>


<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos...</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>
