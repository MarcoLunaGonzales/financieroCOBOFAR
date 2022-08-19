<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

$dbh = new Conexion();
//por is es edit
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT cod_personal,fecha_solicitud,fecha_pago,observaciones FROM finiquitos where codigo =$codigo");
    //Ejecutamos;
    $stmt->execute();
    $result = $stmt->fetch();
    $cod_personal = $result['cod_personal'];
    $fecha_solicitud=$result['fecha_solicitud'];
    $fecha_pago=$result['fecha_pago'];
    $observaciones=$result['observaciones'];
}else {    
  $cod_personal=0;
  $fecha_solicitud='';
  $fecha_pago='';
  $observaciones='';
}

$sqlpersonal="SELECT codigo,paterno,materno,primer_nombre, identificacion from personal where cod_estadopersonal=1 and cod_estadoreferencial=1 order by paterno";
// echo "<br><br><br>".$sqlpersonal;
$stmtpersonal = $dbh->prepare($sqlpersonal);
$stmtpersonal->execute();
?>

<div class="content">
	<div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="?opcion=savequinquenios" method="post">
                <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  Quinquenio</h4>
                    </div>
                  </div>
                  <!-- <h4 align="center"> Seleccione al personal Retirado Por favor.</h4> -->
                  <div class="card-body ">
                    
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Personal</label>
                        <div class="col-sm-8">
                          <div class="form-group">
                            <select name="cod_personal" id="cod_personal" data-style="btn btn-info" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true" >
                                  <option value="" <?=($codigo>0)?"disabled":""?>></option>
                                  <?php 
                                      while ($row = $stmtpersonal->fetch()){ 
                                        $dias=100;
                                        ?>
                                       <option <?=($cod_personal==$row["codigo"])?"selected":""?> <?=($dias<89)?"disabled":"";?>  value="<?=$row["codigo"]?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?> </option>
                                   <?php } ?>
                            </select>
                          </div>
                        </div>
                    </div><!--fin campo nombre -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Fecha Solcitud</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="date" name="fecha_solicitud" id="fecha_solicitud" required="true" value="<?=$fecha_solicitud?>" />
                        </div>
                      </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Fecha Pago</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                          <input class="form-control" type="date" name="fecha_pago" id="fecha_pago" required="true" value="<?=$fecha_pago?>" />
                        </div>
                        </div>
                    </div>                    
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Observaciones</label>
                        <div class="col-sm-8">
                        <div class="form-group">
                            <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones?>" />
                        </div>
                        </div>
                    </div>                    
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                    <a href="?opcion=quinquenios_list" class="<?=$buttonCancel;?>">Volver</a>
                  </div>
                </div>
              </form>
            </div>
        </div>		
	</div>
</div>