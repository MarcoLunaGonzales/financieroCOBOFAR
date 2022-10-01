<!-- <meta charset="utf-8"> -->
<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$filas=$_POST['filas'];
$datos=json_decode($_POST['datos']);

$cod_cuenta_configuracion_iva=obtenerValorConfiguracion(3);
for ($fila=0; $fila < count($datos); $fila++) { 

  $totaldebDet=0;$totalhabDet=0;
    if($datos[$fila][2]==""){
      $datos[$fila][2]="0";
    }
    if($datos[$fila][3]==""){
      $datos[$fila][3]="0";
    }

    $unidadDet=codigoUnidadNombre(trim($datos[$fila][0]));//verficia la oficina por el nombre like '%nombre%' retorna codigo
    //echo "<br>".$datos[$fila][1]."<br>";
    if($datos[$fila][1]==null || $datos[$fila][1]=="" || $datos[$fila][1]==" "){
      $areaDet=522;// area por defecto   
    }else{
      $areaDet=codigoAreaNombre(trim($datos[$fila][1]));
    }
    
    //$debe=(float)str_replace(",", ".", str_replace(".", "", $datos[$fila][2]));
    //$haber=(float)str_replace(",", ".", str_replace(".", "", $datos[$fila][3]));
    $debe=(float)str_replace(",", "",$datos[$fila][4]);
    $haber=(float)str_replace(",", "",$datos[$fila][5]);
    $totaldebDet+=$debe;
    $totalhabDet+=$haber;
    $glosa=$datos[$fila][6];
    $cod_cuenta=obtieneCuentaPorNumero(trim($datos[$fila][2]));
    $cod_cuenta_aux=$datos[$fila][3];
    $nombre_cuenta=nameCuenta($cod_cuenta);
    if($cod_cuenta_aux>0){
      $nombre_cuenta_aux=nameCuentaAux($cod_cuenta_aux);
    }else{
      if($cod_cuenta_aux==""){
        $cod_cuenta_aux=0;
      }
      $nombre_cuenta_aux='';
    }
    
    $numero_cuenta=trim($datos[$fila][2]);

    $codigoCuenta=$cod_cuenta;
    $codCuentaAuxDet=$cod_cuenta_aux;
    $nombre_auxiliar=$nombre_cuenta_aux;
    $numeroDet=$numero_cuenta;
    $nombreDet=$nombre_cuenta;
    
  $idFila=(($filas+$fila)+1); 
      ?>      
<div id="div<?=$idFila?>">
 <div class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
      <div class="form-group">
        <span id="numero_fila<?=$idFila?>" style="position:absolute;left:-15px; font-size:16px;font-weight:600; color:#386D93;"><?=$idFila?></span>
      <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" onChange="relacionSolicitudesSIS(<?=$idFila;?>)" data-live-search="true">
         <?php
         if($unidadDet==0){
         ?><option disabled selected="selected" value="">Unidad</option><?php 
         }else{
          ?><option disabled value="">Unidad</option><?php
         }
         $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 3");
       $stmt->execute();
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $codigoX=$row['codigo'];
          $nombreX=$row['nombre'];
          $abrevX=$row['abreviatura'];
          if($codigoX==$unidadDet){
                   ?><option value="<?=$codigoX;?>" selected data-subtext="<?=$nombreX?>"><?=$abrevX;?></option><?php
          }else{
                    ?><option value="<?=$codigoX;?>" data-subtext="<?=$nombreX?>"><?=$abrevX;?></option><?php
          }
          }
          ?>
      </select>
      </div>
    </div>
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="<?=$comboColor;?>" data-live-search="true">
          <?php
                                  if($areaDet==0){
                                   ?><option disabled selected="selected" value="">Area</option><?php 
                                   }else{
                                    ?><option disabled value="">Area</option><?php
                                   }
                          
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 3");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                    if($codigoX==$areaDet){
                                        ?><option value="<?=$codigoX;?>" selected data-subtext="<?=$nombreX?>"><?=$abrevX;?></option><?php
                                      }else{
                                       ?><option value="<?=$codigoX;?>" data-subtext="<?=$nombreX?>"><?=$abrevX;?></option><?php 
                                      }
                                  }
                                   ?>
      </select>
    </div>
  </div>

   <div class="col-sm-4">
        <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$numero_cuenta?>" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="<?=$cod_cuenta?>" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="<?=$cod_cuenta_aux?>" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="row"> 
                                  <div class="col-sm-8">
                                    <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
                                  
                                          </div>
                                  </div>
                                  <div class="col-sm-4">
                                    <div class="btn-group">
                         

                                     <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>   
                                        <div class="btn-group dropdown">
                              <button type="button" class="btn btn-sm btn-success btn-fab dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                                <i class="material-icons">call_split</i>
                              </button>
                              <div class="dropdown-menu">   
                                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionX<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,1);" class="dropdown-item">
                                  <i class="material-icons">bubble_chart</i> x Oficina
                                </a>
                                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionY<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,2);" class="dropdown-item">
                                  <i class="material-icons">bubble_chart</i> x Área
                                </a>
                              </div>
                            </div>  
                                       <input type="hidden" id="tipo_estadocuentas<?=$idFila?>">
                                       <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>">
                                       <input type="hidden" id="proveedorcliente<?=$idFila?>">
                                       <input type="hidden" id="tipo_estadocuentas_casoespecial<?=$idFila?>">
                           
                                       <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>
                                       <!--LIBRETAS BANCARIAS DETALLE-->
               <a title="Libretas Bancarias" id="libretas_bancarias<?=$idFila?>" href="#" onclick="verLibretasBancarias(<?=$idFila;?>);" class="btn btn-sm btn-primary btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestadolib<?=$idFila?>" class="bg-warning"></span></a>       
               <input type="hidden" id="cod_detallelibreta<?=$idFila?>" name="cod_detallelibreta<?=$idFila?>" value="0">
               <input type="hidden" id="descripcion_detallelibreta<?=$idFila?>" value=""> 
               <input type="hidden" id="tipo_libretabancaria<?=$idFila?>" value=""> 
               <!--SOLICITUD DE RECURSOS SIS-->
               <input type="hidden" id="cod_detallesolicitudsis<?=$idFila?>" name="cod_detallesolicitudsis<?=$idFila?>" value="0">
               <input type="hidden" id="cod_actividadproyecto<?=$idFila?>" name="cod_actividadproyecto<?=$idFila?>" value="0">
               <input type="hidden" id="cod_accnum<?=$idFila?>" name="cod_accnum<?=$idFila?>" value="0">
               <!---->  
                                      </div>  
                                  </div>
                                </div>
    </div>
    <?php
                                $numeroCuenta=trim($numeroDet);
                                $nombreCuenta=trim($nombreDet);
                                $existeAux=0;
                              ?><script>var nfac=[];
      itemFacturas.push(nfac);var nest=[];
      itemEstadosCuentas.push(nest);itemFacturas[<?=$idFila?>]=[];filaActiva=<?=$idFila?>;</script><?php
                                   
                              ?><script>setBusquedaCuentaEdit('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','<?=$codCuentaAuxDet?>','<?=$nombre_auxiliar?>');</script>   

    <div class="col-sm-1">
            <div class="form-group">      
              <input class="form-control small clase_saldo" type="text" placeholder="0" value="<?=$debe?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onblur="calcularTotalesComprobante(this.id,event);" onkeypress="return valideKey(event);" onkeyup="moverFlechas(event);" step="any"> 
      </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">     
              <input class="form-control small clase_saldo" type="text" placeholder="0" value="<?=$haber?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onblur="calcularTotalesComprobante(this.id,event);" onkeypress="return valideKey(event);" onkeyup="moverFlechas(event);" step="any">   
      </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">  
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosa?></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
      <a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
             <i class="material-icons">ballot</i>
           </a>
      <a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="facturas-boton btn btn-info btn-sm btn-fab <?=($cod_cuenta_configuracion_iva==$codigoCuenta)?'':'btn-default text-dark d-none';?>" >
              <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
            </a>
            <a title="Actividad Proyecto SIS" id="boton_actividad_proyecto<?=$idFila?>" href="#" onclick="verActividadesProyectosSis(<?=$idFila;?>);" class="btn btn-sm btn-orange btn-fab d-none"><span class="material-icons">assignment</span><span id="nestadoactproy<?=$idFila?>" class="bg-warning"></span></a>
            <a title="Solicitudes de Recursos SIS" id="boton_solicitud_recurso<?=$idFila?>" href="#" onclick="verSolicitudesDeRecursosSis(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab d-none"><span class="material-icons text-dark">view_sidebar</span><span id="nestadosol<?=$idFila?>" class="bg-warning"></span></a>
            <!-- <a title="Agregar Fila" id="boton_agregar_fila<?=$idFila?>" href="#" onclick="agregarFilaComprobante(<?=$idFila;?>);return false;" class="btn btn-sm btn-primary btn-fab"><span class="material-icons">add</span></a>    -->           
            <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="quitarFilaComprobante('<?=$idFila;?>');return false;">
                   <i class="material-icons">disabled_by_default</i>
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
