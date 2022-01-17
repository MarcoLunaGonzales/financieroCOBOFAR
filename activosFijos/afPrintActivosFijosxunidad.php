<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];
$personal=$_POST["personal"];


$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$personalString=implode(",", $personal);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}
$sqlActivos="SELECT codigoactivo, otrodato as activo, cod_unidadorganizacional,cod_area,cod_depreciaciones,cod_responsables_responsable,cod_responsables_responsable2,estadobien
from activosfijos 
where cod_estadoactivofijo = 1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) and cod_responsables_responsable in ($personalString)";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('activo', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('cod_responsables_responsable', $cod_responsables_responsable);
$stmtActivos->bindColumn('cod_responsables_responsable2', $cod_responsables_responsable2);
$stmtActivos->bindColumn('estadobien', $estadobienX);

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="float-right col-sm-2">
              <h6 class="card-title">Exportar como:</h6>
            </div>
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                Reporte De Activos Fijos Por Oficina
            </h4>
            <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
            <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
            <!-- <h6 class="card-title">Personal: <?=$stringPersonal?></h6> -->
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <?php
              $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixed">'.
                '<thead class="bg-secondary text-white">'.
                  '<tr >'.
                    '<th class="font-weight-bold">-</th>'.
                    '<th class="font-weight-bold"><small>Codigo</small></th>'.
                    '<th class="font-weight-bold"><small>Of/Area</small></th>'.
                    '<th class="font-weight-bold" width="10%"><small>Rubro</small></th>'.
                    '<th class="font-weight-bold"><small>Activo</small></th>'.
                    '<th class="font-weight-bold"><small>Estado</small></th>'.
                    '<th class="font-weight-bold"><small>Respo1</small></th>'.
                    '<th class="font-weight-bold"><small>Respo2</small></th>'.
                  '</tr>'.
                '</thead>'.
                '<tbody>';
                  //<?php  
                    $contador = 0;
                    while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                      $rubro=nameDepreciacion($cod_depreciaciones);
                      $uo=abrevUnidad($cod_unidadorganizacional);
                      $area=abrevArea($cod_area);
                      $personal=nombrePersona($cod_responsables_responsable);
                      $personal2=nombrePersona($cod_responsables_responsable2);
                    $contador++;   
                  $html.='<tr>'.
                    '<td class="text-center small"><small>'.$contador.'</small></td>'.
                    '<td class="text-center small"><small>'.$codigoActivoX.'</small></td>'.
                    '<td class="text-center small"><small>'.$uo.'/'.$area.'</small></td>'.
                    '<td class="text-left small"><small>'.$rubro.'</small></td>'.
                    '<td class="text-left small"><small>'.$activoX.'</small></td>'.
                    '<td class="text-left small"><small>'.$estadobienX.'</small></td>'.
                    '<td class="text-left small"><small>'.$personal.'</small></td>'.
                    '<td class="text-left small"><small>'.$personal2.'</small></td>'.
                  '</tr>';
                    } 
                $html.='</tbody>'.
                
              '</table>';
              echo $html;
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
