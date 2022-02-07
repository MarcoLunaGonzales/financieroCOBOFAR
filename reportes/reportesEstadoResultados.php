<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
$query = "select * from depreciaciones";
$statement = $dbh->query($query);

$gestionGlobal=$_SESSION['globalGestion'];

$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-12-31";
?>

<div class="content">
	<div class="container-fluid">
    <div style="overflow-y: scroll;">
      <div class="col-md-12">
        <form id="form1" class="form-horizontal" action="<?=$urlReporteEstadosResultados;?>" method="post" target="_blank">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Estado Resultados Filtro</h4>
          </div>
          </div>
          <div class="card-body ">
            <div class="row">
              <label class="col-sm-2 col-form-label">Entidad</label>
              <div class="col-sm-7">
                <div class="form-group">                            
                          <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)">  -->
                          <select class="selectpicker form-control form-control-sm" name="entidad[]" id="entidad" required onChange="ajax_entidad_Oficina()" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true">                           
                          <?php
                          $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM entidades where cod_estadoreferencial=1 order by 2");
                         $stmt->execute();
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $codigoX=$row['codigo'];
                            $nombreX=$row['nombre'];
                            $abrevX=$row['abreviatura'];
                          ?>
                       <option value="<?=$codigoX;?>"><?=$nombreX?></option>  
                         <?php
                           }
                           ?>
                      </select>
                  </div>
              </div>  
            </div>
            <div class="row">
              <label class="col-sm-2 col-form-label">Gestion</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <select name="gestion" id="gestion" class="selectpicker form-control form-control-sm " data-style="btn btn-info"
                      required="true" onChange="ajaxGestionFechaDesdeER(this);">
                      <option value=""></option>
                      <?php
                        $sql="SELECT * FROM gestiones order by 2 desc";
                        $stmtg = $dbh->prepare($sql);
                        $stmtg->execute();
                        while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                          $codigog=$rowg['codigo'];
                          $nombreg=$rowg['nombre'];
                          // if($codigog!=$gestionGlobal){
                            ?>
                        <option value="<?=$codigog;?>"><?=$nombreg;?></option>
                        <?php 
                          // }     
                        }
                      ?>
                  </select>
                </div>
              </div>
            </div><!--fin campo gestion -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Oficina</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <div id="div_contenedor_oficina1">                              
                  </div>

                </div>
              </div>
            </div><!--fin campo gestion -->

            <div class="row">
              <div class="col-sm-6">
                <div class="row">
               <label class="col-sm-4 col-form-label">Area</label>
               <div class="col-sm-8">
                <div class="form-group">
                        <select class="selectpicker form-control form-control-sm" name="area_costo[]" id="area_costo" data-style="select-with-transition" multiple data-actions-box="true"  data-show-subtext="true" data-live-search="true" required>
                       <?php
                       $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                     $stmt->execute();
                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                      $abrevX=$row['abreviatura'];
                     ?>
                     <option value="<?=$codigoX;?>" selected><?=$abrevX;?></option> 
                       <?php
                         }
                         ?>
                     </select>
                    </div>
                </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="row">
               <label class="col-sm-4 col-form-label">Incluir areas</label>
                     <div class="col-sm-8">
                  <div class="form-group">
                          <div class="form-check">
                              <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" id="costos_areas" name="costos_areas[]" value="1">
                                <span class="form-check-sign">
                                  <span class="check"></span>
                                </span>
                              </label>
                            </div>
                          </div>  
                       </div>     
                  </div>  
               </div>
            </div>
            <div class="row">
                <label class="col-sm-2 col-form-label">Del:</label>
                <div class="col-sm-3">
                  <div class="form-group">
                    <div id="div_contenedor_fechaD">                    
                    </div>
                      
                  </div>
                </div>
                <label class="col-sm-1 col-form-label">Al:</label>
                <div class="col-sm-3">
                  <div class="form-group">
                    <div id="div_contenedor_fechaH">                    
                    </div>
                  </div>
                </div>
            </div><!--fin campo RUBRO -->
            <div class="row">
              <label class="col-sm-2 col-form-label">Formato</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <select name="formato" id="formato" class="selectpicker form-control form-control-sm " data-style="btn btn-rose"
                      required>
                      <option value="1">PDF </option>
                      <option value="2">EXCEL</option>

                      <option value="3">EXCEL FORMATO 2</option>
                  </select>
                </div>
              </div>
            </div><!--fin campo gestion -->
            
          </div>
          <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
            </div>
        </div>
        </form>
      </div>
    </div>
		
	
	</div>
</div>