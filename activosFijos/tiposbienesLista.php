<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();


$stmt = $dbh->prepare("SELECT tb.*,d.nombre from tiposbienes tb, depreciaciones d where d.codigo = tb.cod_depreciaciones and tb.cod_estado=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmt->bindColumn('tipo_bien', $tipo_bien);
$stmt->bindColumn('nombre', $nombre);
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
                  <h4 class="card-title"><?=$moduleNamePlural5?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

<thead>
    <tr>
        <th>codigo</th>

        <th>Rubro</th>
        <th>Tipo Bien</th>
        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
        <td><?=$codigo;?></td>

        <td><?=$nombre;?></td>
        <td><?=$tipo_bien;?></td>
        <td  class="td-actions text-right">
        <?php
                                                    if($globalAdmin==1){
                                                        ?>
                                                        <a href='<?=$urlRegistrar_tiposbienes;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                                        <i class="material-icons"><?=$iconEdit;?></i>
                                                        </a>
                                                        <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete5;?>&codigo=<?=$codigo;?>')">
                                                        <i class="material-icons"><?=$iconDelete;?></i>
                                                        </button>
                                                        <?php
                                                    }
                                                    ?>
        </td>
    </tr>
<?php $index++; } ?>
</tbody>

                    
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_tiposbienes;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
