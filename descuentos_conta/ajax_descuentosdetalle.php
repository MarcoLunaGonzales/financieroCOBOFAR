<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$idFila=$_GET['idFila'];

?>
<div id="comp_row" class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
      <div class="form-group">        
        <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_sucursal<?=$idFila;?>" id="cod_sucursal<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">Sucursales</option>
              <?php                 
                $sql="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1";
                $stmt3 = $dbh->prepare($sql);
                $stmt3->execute();
                while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$rowsuc['codigo'];
                  $nombreX=$rowsuc['nombre'];
                  $abreviaturaX=$rowsuc['abreviatura'];
                  ?><option value="<?=$codigoX;?>"><?=$nombreX?> - <?=$abreviaturaX?></option><?php 
                }
              ?>
          </select>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">
        <input type="date" step="0.01"  id="fecha<?=$idFila;?>" name="fecha<?=$idFila;?>" class="form-control text-primary text-right" value="1" required="true">
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_personal<?=$idFila;?>" id="cod_personal<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">Personal</option>
              <?php                 
                $sql="SELECT codigo,identificacion,paterno,materno,primer_nombre from personal where cod_estadopersonal in (1,2) and cod_estadoreferencial=1";
                $stmt3 = $dbh->prepare($sql);
                $stmt3->execute();
                while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$rowsuc['codigo'];
                  $paternoX=$rowsuc['paterno'];
                  $maternoX=$rowsuc['materno'];
                  $primer_nombreX=$rowsuc['primer_nombre'];
                  $identificacionX=$rowsuc['identificacion'];
                  ?><option value="<?=$codigoX;?>"><?=$primer_nombreX?> <?=$paternoX?> <?=$maternoX?> (<?=$identificacionX?>)</option><?php 
                }
              ?>
          </select>
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_tipodescuento<?=$idFila;?>" id="cod_tipodescuento<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">Tipo Desc</option>
              <?php                 
                $sql="SELECT codigo,nombre from tipos_descuentos_conta where cod_estadoreferencial=1";
                $stmt3 = $dbh->prepare($sql);
                $stmt3->execute();
                while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$rowsuc['codigo'];
                  $nombreX=$rowsuc['nombre'];
                  ?><option value="<?=$codigoX;?>"><?=$nombreX?></option><?php 
                }
              ?>
          </select>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">
        <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_contracuenta<?=$idFila;?>" id="cod_contracuenta<?=$idFila;?>" data-style="fondo-boton" required="true">
          <option disabled selected="selected" value="">Contra Cuenta</option>
          <?php                 
            $sql="SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1";
            $stmt3 = $dbh->prepare($sql);
            $stmt3->execute();
            while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
              $codigoX=$rowsuc['codigo'];
              $nombreX=$rowsuc['nombre'];
              $numeroX=$rowsuc['numero'];
              ?><option value="<?=$codigoX;?>"><?=$numeroX?> - <?=$nombreX?></option><?php 
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          <input type="number" step="0.01" min="0" id="monto_sistema<?=$idFila;?>" name="monto_sistema<?=$idFila;?>" class="form-control text-primary text-right" value="0" required="true" >
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">
        <input type="number" step="0.01" min="0" id="monto_deposito<?=$idFila;?>" name="monto_deposito<?=$idFila;?>" class="form-control text-primary text-right" value="0" required="true" onchange="diferencia_descuento_personal(<?=$idFila?>)"> 
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">
          <input type="number" step="0.01" min="0" id="monto_diferencia<?=$idFila;?>" name="monto_diferencia<?=$idFila;?>" class="form-control text-primary text-right" value="0" required="true" readonly style="background:#f2d7d5;" >
      </div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <input type="text"  min="0" id="glosa<?=$idFila;?>" name="glosa<?=$idFila;?>" class="form-control text-primary text-right" value="" required="true">
      </div>
    </div>
    <div class="col-sm-1">
      <div class="form-group">
        <a rel="tooltip" title="Eliminar" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="borrarItemDescuentoPersonalConta('<?=$idFila;?>');">
            <i class="material-icons">remove_circle</i>
        </a>      
      </div>
    </div>

  </div>
</div>

<div class="h-divider"></div>

