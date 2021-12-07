<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];
$cod_mes=$_SESSION['globalMes'];
$nombre_mes=nombreMes($cod_mes);

$dbh = new Conexion();

// Preparamo
$sql="SELECT codigo,nombre FROM meses where cod_estado=1";
//echo "<br><br><br>".$sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);

$cantidad=0;


// //Mostrar tipo bono
// $stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codigoBono");
// // Ejecutamos
// $stmtb->execute();
// // bindColumn
// $stmtb->bindColumn('nombre', $nombreBono);

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
                  <h4 class="card-title">Kardex Personal</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Mes</th>
                          <th>Cantidad de Registros</th>
                          <th class="text-right">Acciones</th>
                        </tr>
                      </thead>
                      <tbody><?php
                      $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre."/".$nombreGestion;?></td>
                          <td class="text-center"><?=$cantidad." registros";?></td>
                          <td class="td-actions text-right">
                            <a href='#' rel="tooltip" class="<?=$buttonMorado;?>">
                              <i class="material-icons" title="Ver Personal">playlist_add</i>
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
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                <a href="bonos/descargarExcelGlobal.php" target="_blank" class="btn btn-info"><span class="material-icons">download</span>Descargar Plantilla</a>
                <button class="btn btn-success" onClick="location.href='<?=$urlSubirExcelGlobal;?>'">Subir Excel</button>
                <button class="btn btn-rose" onClick="procesar_bonos_descuentos_planilla('<?=$nombre_mes?>','<?=$cod_mes?>')">Procesar o reprocesar</button>
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
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>

