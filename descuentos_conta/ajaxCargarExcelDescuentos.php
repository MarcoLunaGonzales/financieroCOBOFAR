<!-- <meta charset="utf-8"> -->
<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
// require_once 'configModule.php';


$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$cod_mes=$_SESSION['globalMes'];
$cod_mes = str_pad($cod_mes, 2, "0", STR_PAD_LEFT);//
$fecha_inicio=$nombreGestion.'-'.$cod_mes.'-01';
$fecha_final=date('Y-m-t',strtotime($fecha_inicio));

$filas=$_POST['filas'];
$datos=json_decode($_POST['datos']);

for ($fila=0; $fila < count($datos); $fila++) { 

  // $totaldebDet=0;
  // $totalhabDet=0;
  if($datos[$fila][5]==""){//monto sis 
    $datos[$fila][5]="0";
  }
  if($datos[$fila][6]==""){//monto depositado
    $datos[$fila][6]="0";
  }
  // if($datos[$fila][7]==""){//monto descuento
  //   $datos[$fila][7]="0";
  // }
  //echo "<br>".$datos[$fila][1]."<br>";
  if($datos[$fila][0]==null || $datos[$fila][0]=="" || $datos[$fila][0]==" "){
    $areaDet=522;// area por defecto   
  }else{
    $areaDet=codigoAreaNombre(trim($datos[$fila][0]));//verifica el area por el nombre like '%nombre%' retorna codigo
  }
  $fechaSinFormato=$datos[$fila][1];//fecha en formato 22/09/2022
  $array_fechaSinFormato=explode("/", $fechaSinFormato);
  $fecha=$array_fechaSinFormato[2]."-".$array_fechaSinFormato[1]."-".$array_fechaSinFormato[0];
  $cod_personal=codigoPersonalIdentificacion(trim($datos[$fila][2]));//verifica el ci de la persona y retorna codigo
  $cod_tipodescuento=$datos[$fila][3];
  $cod_contraCuenta=obtieneCuentaPorNumero(trim($datos[$fila][4]));
  
  $monto_sistema=(float)str_replace(",", "",$datos[$fila][5]);
  $monto_depositado=(float)str_replace(",", "",$datos[$fila][6]);
  // $monto_descuento=(float)str_replace(",", "",$datos[$fila][7]);
  $monto_descuento=$monto_sistema-$monto_depositado;

  // $totaldebDet+=$debe;
  // $totalhabDet+=$haber;
  $glosa=$datos[$fila][8];
  
  $idFila=(($filas+$fila)+1); 
      ?>      
  <div id="div<?=$idFila?>">
    <div class="col-md-12">
      <div class="row">
        <div class="col-sm-1">
          <div class="form-group">
            <select class="selectpicker form-control form-control-sm" name="cod_sucursal<?=$idFila;?>" id="cod_sucursal<?=$idFila;?>" data-style="<?=$comboColor;?>" >
              <?php
              if($areaDet==0){
               ?><option disabled selected="selected" value="">Area</option><?php 
              }else{
                ?><option disabled value="">Area</option><?php
              }
              $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $codigoX=$row['codigo'];
                $nombreX=$row['nombre'];
                $abrevX=$row['abreviatura'];
                if($codigoX==$areaDet){
                    ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                  }else{
                   ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                  }
              } ?>
            </select>
          </div>
        </div>
        <div class="col-sm-1">
          <div class="form-group">
            <input type="date" step="0.01" style="font-size: 10.5px;" id="fecha<?=$idFila;?>" name="fecha<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$fecha?>" required="true" >
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_personal<?=$idFila;?>" id="cod_personal<?=$idFila;?>" data-style="btn btn-primary" required="true">
                <option disabled selected="selected" value="">Personal</option>
                <?php                 
                  $sql="SELECT codigo,identificacion,paterno,materno,primer_nombre from personal where cod_estadopersonal in (1) and cod_estadoreferencial=1
                  union
                  select p.codigo,p.identificacion,p.paterno,p.materno,p.primer_nombre
                  from personal p join personal_retiros pr on p.codigo=pr.cod_personal
                  where pr.fecha_retiro BETWEEN '$fecha_inicio' and '$fecha_final'
                  order by 1";
                  
                  $stmt3 = $dbh->prepare($sql);
                  $stmt3->execute();
                  while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                    $codigoX=$rowsuc['codigo'];
                    $paternoX=$rowsuc['paterno'];
                    $maternoX=$rowsuc['materno'];
                    $primer_nombreX=$rowsuc['primer_nombre'];
                    $identificacionX=$rowsuc['identificacion'];

                    ?>
                    <option <?=($cod_personal==$codigoX)?"selected":""?> value="<?=$codigoX;?>" data-subtext="<?=$identificacionX?>"><?=$primer_nombreX?> <?=$paternoX?> <?=$maternoX?></option><?php 
                  }
                ?>
            </select>
          </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
              <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_tipodescuento<?=$idFila;?>" id="cod_tipodescuento<?=$idFila;?>" data-style="btn btn-primary" required="true">
                  <option disabled selected="selected" value="">Tipo Desc</option>
                  <?php                 
                    $sql="SELECT codigo,nombre from tipos_descuentos_conta where cod_estadoreferencial=1";
                    $stmt3 = $dbh->prepare($sql);
                    $stmt3->execute();
                    while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$rowsuc['codigo'];
                      $nombreX=$rowsuc['nombre'];
                      ?><option <?=($cod_tipodescuento==$codigoX)?"selected":""?> value="<?=$codigoX;?>"><?=$nombreX?> (<?=$codigoX?>)</option><?php 
                    }
                  ?>
              </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_contracuenta<?=$idFila;?>" id="cod_contracuenta<?=$idFila;?>" data-style="btn btn-primary" required="true">
              <option disabled selected="selected" value="">Contra Cuenta</option>
              <?php                 
                $sql="SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1 and nivel in (5) order by nombre";
                $stmt3 = $dbh->prepare($sql);
                $stmt3->execute();
                while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$rowsuc['codigo'];
                  $nombreX=$rowsuc['nombre'];
                  $numeroX=$rowsuc['numero'];
                  ?><option <?=($cod_contraCuenta==$codigoX)?"selected":""?> value="<?=$codigoX;?>" data-subtext="<?=$numeroX?>"><?=$nombreX?></option><?php 
                }
              ?>
            </select>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="row">
              <div class="col-md-4">
                  <div class="form-group">
                      <input type="number" step="0.01" min="0" id="monto_sistema<?=$idFila;?>" name="monto_sistema<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_sistema?>" required="true" onChange="diferencia_descuento_personal(<?=$idFila?>)" OnKeyUp="diferencia_descuento_personal(<?=$idFila?>)">
                  </div>        
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                      <input type="number" step="0.01" min="0" id="monto_deposito<?=$idFila;?>" name="monto_deposito<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_depositado?>" required="true" onChange="diferencia_descuento_personal(<?=$idFila?>)" OnKeyUp="diferencia_descuento_personal(<?=$idFila?>)">
                  </div>        
              </div>
              <div class="col-md-4">
                  <div class="form-group">
                      <input type="number" step="0.01" min="0" id="monto_diferencia<?=$idFila;?>" name="monto_diferencia<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_descuento?>" required="true" readonly style="background:#f2d7d5;">
                  </div>        
              </div>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="form-group">
            <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>" required="true"><?=$glosa?></textarea>
          </div>
        </div>
        <div class="col-sm-1">
          <div class="form-group">
            <a rel="tooltip" title="Eliminar" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="borrarItemDescuentoPersonalConta('<?=$idFila;?>');return false;;">
                <i class="material-icons">remove_circle</i>
            </a>      
          </div>
        </div>
      </div>
    </div>
    <div class="h-divider"></div>
  </div>
  <script>$("#cantidad_filas").val(<?=$idFila?>);$("#div"+<?=$idFila?>).bootstrapMaterialDesign();
        numFilas++;
        cantidadItems++;
        filaActiva=numFilas;
  </script>
<?php
}
?>
