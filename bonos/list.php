<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT b.codigo, b.nombre, b.abreviatura, b.observaciones,t.nombre as calculo FROM $table b,tipos_calculobono t where b.cod_estadoreferencial!=2 and b.cod_tipocalculobono=t.codigo order by b.nombre ");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('calculo', $calculo);
$stmt->bindColumn('observaciones', $observaciones);

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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Tipo</th>
                          <th>Observaciones</th>
                          <th class="text-right">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                           
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td class="text-left"><?=$abreviatura;?></td>
                          <td class="text-left"><?=$calculo;?></td>
                          <td class="text-left"><?=$observaciones;?></td>
                          <td class="td-actions text-right">
                            <a href='<?=$urlListMes;?>&codigo=<?=$codigo;?>' class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Detalle">playlist_add</i>
                            </a>
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                            </button>
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
                    <button class="btn btn-rose" onClick="location.href='<?=$urlRegister;?>'">Registrar Bono</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>
