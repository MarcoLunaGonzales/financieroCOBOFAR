<?php
require_once '../conexion.php';


//header('Content-Type: application/json');

$codigo = $_GET["codigo"];


if($codigo==3){//personal retirado 
    $dbh = new Conexion();
    $query_retiro = "SELECT * from tipos_retiro_personal where cod_estadoreferencial=1 order by 2";
    $statementTiposRetiro = $dbh->query($query_retiro);


    ?>
    

<div class="row">
    <label class="col-sm-3 col-form-label" style="color:red;" id="fecha_retiro_div">Fecha de Retiro</label>
    <div class="col-sm-3">
    <div class="form-group" >
        <input class="form-control" type="date" name="fecha_retiro" id="fecha_retiro" required="true" value="" style="color:red;" />
    </div>
    </div>
     <label class="col-sm-2 col-form-label" style="color:red;" id="fecha_retiro_div">Motivo de Retiro</label>
    <div class="col-sm-4">
    <div class="form-group" >
        <select name="cod_tiporetiro" id="cod_tiporetiro" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true">
                <?php while ($row = $statementTiposRetiro->fetch()){ ?>
                    <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                <?php } ?>
              </select>
    </div>
    </div>
</div>

<?php }

?>


