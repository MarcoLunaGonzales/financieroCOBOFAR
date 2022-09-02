<?php

require_once '../conexion.php';
require_once '../styles.php';
require_once '../layouts/bodylogin2.php';

// $globalUser=$_SESSION["globalUser"];
//$dbh = new Conexion();
$dbh = new Conexion();

$codigo=$_GET['codigo'];

if($codigo>0){

    $stmt = $dbh->prepare("SELECT fecha,glosa from descuentos_conta where codigo='$codigo'");
    //Ejecutamos;    
    $stmt->execute();
    $result = $stmt->fetch();
    $glosa_cabecera = $result['glosa'];
    $fecha_cabecera = $result['fecha'];
    // $array_mesdesc=explode("-", $fecha_cabecera);
    $codigo_nuevoGes=$fecha_cabecera;
    
    $sql="select count(*) as contador from descuentos_conta_detalle d  where d.cod_descuento=$codigo";
    $stmtContador = $dbh->prepare($sql);
    $stmtContador->execute();
    $stmtContador->bindColumn('contador', $contReg);
    while ($rowCont = $stmtContador->fetch(PDO::FETCH_BOUND)) {
        $contadorRegistros=$contReg;
    }
}else{
    $glosa_cabecera="";
    $fecha_cabecera=date('Y-m-d');
    $codigo_nuevoGes=$fecha_cabecera;
    $contadorRegistros=0;
}
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="formDesceuntosConta" class="form-horizontal" action="descuentos_detalle_save.php" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
                    <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                    <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                    <div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">DETALLE - DESCUENTOS</h4>                      
                            </div>
                        </div>
                        <div class="card-body ">                            
                            <!-- archivos -->
                            <div class="row">
                                <label class="col-sm-1 col-form-label" >Glosa</label>
                                <div class="col-sm-6">
                                    <!-- <input type="text" name="glosa_cabecera" id="glosa_cabecera" class="form-control" value="<?=$glosa_cabecera?>" required="true"> -->
                                    <textarea rows="1" class="form-control" name="glosa_cabecera" id="glosa_cabecera" required="true"><?=$glosa_cabecera?></textarea>
                                </div>
                                <label class="col-sm-1 col-form-label" >Mes Descuento</label>
                                <div class="col-sm-2">
                                    <!-- <input type="date" name="fecha_cabecera" id="fecha_cabecera" value="<?=$fecha_cabecera?>" class="form-control"  required="true"> -->
                                    <select class="selectpicker form-control form-control-sm" data-live-search="true" name="fecha_cabecera" id="fecha_cabecera" data-style="btn btn-primary" required="true">
                                      <option disabled selected="selected" value="">Mes Descuento</option>
                                      
                                      <?php                 
                                        $sql="SELECT g.nombre as  gestion,mt.cod_mes,m.nombre as mes
                                            from meses_trabajo mt join  gestiones g on mt.cod_gestion=g.codigo join meses m on mt.cod_mes=m.codigo
                                            where mt.cod_estadomesestrabajo<>2";
                                        $stmtGes = $dbh->prepare($sql);
                                        $stmtGes->execute();
                                        while ($rowsuc = $stmtGes->fetch(PDO::FETCH_ASSOC)) {
                                          $gestiony=$rowsuc['gestion'];
                                          $mesy=$rowsuc['cod_mes'];
                                          $nomMesy=$rowsuc['mes'];
                                          $mesy = str_pad($mesy, 2, "0", STR_PAD_LEFT);//2
                                          $codigo_nuevoGesY=$gestiony."-".$mesy."-01";
                                          ?><option <?=($codigo_nuevoGes==$codigo_nuevoGesY)?"selected":"";?> value="<?=$codigo_nuevoGesY;?>"><?=$nomMesy?> - <?=$gestiony?></option><?php 
                                        }
                                      ?>
                                  </select>
                                </div>
                                <div class="col-sm-2">
                                    <center>
                                        <div class="btn-group">
                                            <a title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-default btn-sm">Archivos 
                                                <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning"></span>
                                            </a>
                                        </div> 
                                    </center>
                                </div>
                            </div>
                        
                            <fieldset id="fiel" style="width:100%;border:0;">
                                <button title="Agregar Descuentos" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarDescuentosPersonalConta(this)">
                                    <i class="material-icons">add</i>
                                </button><span style="color:#084B8A;"><b> ADD. Descuentos </b></span>
                                <div class="row" style="background-color:#1a2748">
                                    <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Sucursal</label>
                                    <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Fecha</label>
                                    <label class="col-sm-2 col-form-label" style="color:#ff9c14; text-align: center">Nombre Personal</label>
                                    <label class="col-sm-1 col-form-label" style="color:#ff9c14; text-align: center">Tipo Descuento</label>
                                    <label class="col-sm-2 col-form-label" style="color:#ff9c14; text-align: center">Contra Cuenta</label>
                                    <div class="col-md-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label style="color:#ff9c14; text-align: center">Monto Sis</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label style="color:#ff9c14;text-align: center;">Depositado</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label style="color:#ff9c14;text-align: center">Descuento</label>
                                                </div>
                                            </div>
                                        </div>                                            
                                    </div>
                                    <!-- <label class="col-sm-2 col-form-label" style="color:#ff9c14; text-align: center">Monto Sis</label> -->
                                    <!-- <label class="col-sm-1 col-form-label" style="color:#ff9c14;text-align: center;">Monto Dep</label>
                                    <label class="col-sm-1 col-form-label" style="color:#ff9c14;text-align: center">Diferencia</label> -->
                                    <label class="col-sm-2 col-form-label" style="color:#ff9c14;text-align: center">Glosa</label>
                                    <label class="col-sm-1 col-form-label" style="color:#ff9c14;text-align: center"></label>
                                </div>
                                <div id="div">
                                    <?php
                                    $idFila=1;

                                    $sql="select d.cod_area,d.cod_personal,d.fecha,d.cod_tipodescuento,d.cod_contracuenta,monto_sistema,monto_depositado,diferencia,glosa
                                      from descuentos_conta_detalle d
                                      where d.cod_descuento=$codigo";
                                    $stmt = $dbh->prepare($sql);
                                    $stmt->execute();
                                    $stmt->bindColumn('cod_area', $cod_area);
                                    $stmt->bindColumn('cod_personal', $cod_personal);
                                    $stmt->bindColumn('fecha', $fecha);
                                    $stmt->bindColumn('cod_tipodescuento', $cod_tipodescuento);
                                    $stmt->bindColumn('cod_contracuenta', $cod_contracuenta);
                                    $stmt->bindColumn('monto_sistema', $monto_sistema);
                                    $stmt->bindColumn('monto_depositado', $monto_deposito);
                                    $stmt->bindColumn('diferencia', $monto_diferencia);
                                    $stmt->bindColumn('glosa', $glosa);
                                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                        ?>
                                        <div id="div<?=$idFila?>">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                      <div class="form-group">        
                                                        <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_sucursal<?=$idFila;?>" id="cod_sucursal<?=$idFila;?>" data-style="btn btn-primary" required="true">
                                                              <option disabled selected="selected" value="">Sucursales</option>
                                                              <?php                 
                                                                $sql="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1";
                                                                $stmt3 = $dbh->prepare($sql);
                                                                $stmt3->execute();
                                                                while ($rowsuc = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                                                  $codigoX=$rowsuc['codigo'];
                                                                  $nombreX=$rowsuc['nombre'];
                                                                  $abreviaturaX=$rowsuc['abreviatura'];
                                                                  ?><option <?=($cod_area==$codigoX)?"selected":"";?> value="<?=$codigoX;?>" data-subtext="<?=$nombreX?>"><?=$abreviaturaX?></option><?php 
                                                                }
                                                              ?>
                                                          </select>
                                                      </div>
                                                    </div>
                                                    <div class="col-sm-1">
                                                      <div class="form-group">
                                                        <input type="date" step="0.01" style="font-size: 10.5px;" id="fecha<?=$idFila;?>" name="fecha<?=$idFila;?>" class="form-control text-primary text-right"  required="true" value="<?=$fecha?>">
                                                      </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="cod_personal<?=$idFila;?>" id="cod_personal<?=$idFila;?>" data-style="btn btn-primary" required="true">
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
                                                                  ?><option <?=($cod_personal==$codigoX)?"selected":"";?> value="<?=$codigoX;?>" data-subtext="<?=$identificacionX?>"><?=$primer_nombreX?> <?=$paternoX?> <?=$maternoX?></option><?php 
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
                                                                  ?><option <?=($cod_tipodescuento==$codigoX)?"selected":"";?> value="<?=$codigoX;?>"><?=$nombreX?></option><?php 
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
                                                              ?><option <?=($cod_contracuenta==$codigoX)?"selected":"";?> value="<?=$codigoX;?>" data-subtext="<?=$numeroX?>"><?=$nombreX?></option><?php 
                                                            }
                                                          ?>
                                                        </select>
                                                      </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input type="number" step="0.01" min="0" id="monto_sistema<?=$idFila;?>" name="monto_sistema<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_sistema?>" required="true" onkeyUp="diferencia_descuento_personal(<?=$idFila?>)">
                                                                </div>        
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input type="number" step="0.01" min="0" id="monto_deposito<?=$idFila;?>" name="monto_deposito<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_deposito?>" required="true" onkeyUp="diferencia_descuento_personal(<?=$idFila?>)" >
                                                                </div>        
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <input type="number" step="0.01" min="0" id="monto_diferencia<?=$idFila;?>" name="monto_diferencia<?=$idFila;?>" class="form-control text-primary text-right" value="<?=$monto_diferencia?>" required="true" readonly style="background:#f2d7d5;">
                                                                </div>        
                                                            </div>
                                                        </div>
                                                    </div>
                                                   
                                                    <div class="col-sm-2">
                                                      <div class="form-group">
                                                        <textarea rows="1" class="form-control" name="glosa<?=$idFila;?>" id="glosa<?=$idFila;?>" required="true"><?=$glosa?></textarea>
                                                      </div>
                                                    </div>
                                                    <div class="col-sm-1">
                                                      <div class="form-group">
                                                        <a rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="borrarItemDescuentoPersonalConta('<?=$idFila;?>');">
                                                            <i class="material-icons" title="Eliminar">remove_circle</i>
                                                        </a>      
                                                      </div>
                                                    </div>

                                                  </div>
                                            </div>
                                        </div>
                                        <?php
                                        $idFila=$idFila+1;
                                    } ?>
                                    <div class="h-divider"></div>
                                </div>
                            </fieldset>

                            <div class="row">
                                <div class="col-sm-6">
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">    
                                        <input class="form-control d-none" type="number" step=".01" name="totalSistema" placeholder="0" id="totalSistema" readonly="true">  
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input class="form-control d-none" type="number" step=".01" name="totalDepos" placeholder="0" id="totalDepos" readonly="true">  
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input class="form-control text-primary d-none" type="number" step=".01" name="total_dif" placeholder="0" id="total_dif" readonly="true">   
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                </div>
                            </div>
                        </div>
                                
                        <div class="card-footer fixed-bottom">
                            <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                            <!-- <a href='' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Cancelar </a> -->
                            <button  type="button" onclick="window.close();" class="btn btn-danger" >Cancelar</button>

                            <div class="row col-sm-12">
                                        <div class="col-sm-6">
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="bmd-label-static fondo-boton">Monto Sis</label>    
                                                <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="0" id="totalsistema_fijo" readonly="true">   
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="bmd-label-static fondo-boton">Deposito</label>   
                                                <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="0" id="totalDepos_fijo" readonly="true">   
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="bmd-label-static fondo-boton">Descuento</label>  
                                                <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="0" id="total_dif_fijo" readonly="true">  
                                            </div>
                                        </div>
                                       

                        </div>
                        <?php // require_once 'simulaciones_servicios/modal_subir_archivos.php';?>
                    </div>
                </form>                  
            </div>
        </div>
    </div>
</div>

<!--    end small modal -->
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde; un momento por favor</p>  
  </div>
</div>


<script type="text/javascript">
function valida(f) {

    var ok = true;
    var msg = "Debe tener registrado al menos un descuento\n";  
    if(f.elements["cantidad_filas"].value<=0)
    {    
        ok = false;
    }

    if(ok == false)Swal.fire("Información!",msg, "error");
    return ok;
}
</script>

