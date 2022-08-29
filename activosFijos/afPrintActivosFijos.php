<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require '../assets/phpqrcode/qrlib.php';

require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();
set_time_limit(300);


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
$rubros=$_POST["rubros"];
$tipo=$_POST["tipo"];
$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);
$rubrosString=implode(",", $rubros);

$fecha_desde=$_POST["fecha_desde"];
$fecha_hasta=$_POST["fecha_hasta"];


// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}
$stringRubros="";
foreach ($rubros as $valor ) {    
    $stringRubros.=" ".abrevDepreciacion($valor)." ";
}
if($tipo==1){
  $sqladd=" and tipo_af=1 and cod_depreciaciones in ($rubrosString)";
}else{
  $sqladd="and tipo_af=2";
}


$sqlActivos="SELECT codigo,codigoactivo,otrodato,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as cod_unidadorganizacional,
(select a.abreviatura from areas a where a.codigo=cod_area) as cod_area,
(select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as cod_depreciaciones,
tipoalta,
DATE_FORMAT(fechalta, '%d/%m/%Y')as fechalta,valorinicial,valorresidual,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable) as cod_responsables_responsable,
(select CONCAT_WS(' ',r.paterno,r.materno,r.primer_nombre) from personal r where r.codigo=cod_responsables_responsable2) as cod_responsables_responsable2,
(select e.nombre from estados_activofijo e where e.codigo=cod_estadoactivofijo) as estado_af,
(select t.tipo_bien from tiposbienes t where t.codigo=cod_tiposbienes)as tipo_bien
from activosfijos 
where cod_estadoactivofijo = 1 and cod_unidadorganizacional in ($unidadOrgString) and cod_area in ($areaString) $sqladd and fechalta between '$fecha_desde' and '$fecha_hasta' ";  

//echo $sqlActivos;

$stmtActivos = $dbh->prepare($sqlActivos);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigoX);
$stmtActivos->bindColumn('codigoactivo', $codigoActivoX);
$stmtActivos->bindColumn('otrodato', $activoX);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmtActivos->bindColumn('fechalta', $fecha_alta);
$stmtActivos->bindColumn('tipoalta', $tipo_alta);
$stmtActivos->bindColumn('valorinicial', $valor_inicial);
$stmtActivos->bindColumn('valorresidual', $valor_residual);
$stmtActivos->bindColumn('cod_responsables_responsable', $responsables_responsable);
$stmtActivos->bindColumn('cod_responsables_responsable2', $responsables_responsable2);
$stmtActivos->bindColumn('estado_af', $estado_af);
// $stmtActivos->bindColumn('nombre_uo2', $nombre_uo2);
$stmtActivos->bindColumn('tipo_bien', $tipo_bien);
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
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">  Reporte De Activos Fijos</h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                  <h6 class="card-title">Rubros: <?=$stringRubros?></h6>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">

                    <table class="table table-condensed" id="tablePaginatorFixed2">
                      <thead class="bg-secondary text-white">
                        <tr >
                          <th class="text-center">-</th>
                          <th class="font-weight-bold">Codigo</th>
                          <th class="font-weight-bold">Of/Area</th>
                          <th class="font-weight-bold">Rubro</th>
                          <th class="font-weight-bold">Activo</th>
                          <th class="font-weight-bold">Tipo Alta</th>
                          <th class="font-weight-bold">Fecha Alta</th>
                          <th class="font-weight-bold">Valor Ini.</th>
                          <th class="font-weight-bold">Valor Neto.</th>
                          <th class="font-weight-bold">Vida Util.</th>
                          <th class="font-weight-bold">Respo1</th>
                          <th class="font-weight-bold">Respo2</th>
                          <th class="font-weight-bold">Estado AF</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                          $contador = 0;
                          while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {

                            $stmt2 = $dbh->prepare("SELECT d10_valornetobs,d11_vidarestante 
                            from mesdepreciaciones m, mesdepreciaciones_detalle md
                            WHERE m.codigo = md.cod_mesdepreciaciones 
                            and md.cod_activosfijos = $codigoX and m.estado=1 order by m.codigo desc limit 1");
                            // Ejecutamos
                            //$stmt2->bindParam(':mes',$mes2);
                            // $stmt2->bindParam(':codigo',$codigo_af);

                            $stmt2->execute();
                            //resultado
                            // $stmt2->bindColumn('mes', $mes3);
                            // $stmt2->bindColumn('gestion', $gestion3);
                            // $stmt2->bindColumn('ufvinicio', $ufvinicio);
                            // $stmt2->bindColumn('ufvfinal', $ufvfinal);
                            // //$stmt2->bindColumn('estado', $estado);
                            // //$stmt2->bindColumn('codigo1', $codigo1);
                            // $stmt2->bindColumn('cod_mesdepreciaciones', $cod_mesdepreciaciones);
                            // $stmt2->bindColumn('cod_activosfijos', $cod_activosfijos);
                            // $stmt2->bindColumn('d2_valorresidual', $d2_valorresidual);
                            // $stmt2->bindColumn('d3_factoractualizacion', $d3_factoractualizacion);
                            // $stmt2->bindColumn('d4_valoractualizado', $d4_valoractualizado);
                            // $stmt2->bindColumn('d5_incrementoporcentual', $d5_incrementoporcentual);
                            // $stmt2->bindColumn('d6_depreciacionacumuladaanterior', $d6_depreciacionacumuladaanterior);
                            // $stmt2->bindColumn('d7_incrementodepreciacionacumulada', $d7_incrementodepreciacionacumulada);
                            // $stmt2->bindColumn('d8_depreciacionperiodo', $d8_depreciacionperiodo);
                            // $stmt2->bindColumn('d9_depreciacionacumuladaactual', $d9_depreciacionacumuladaactual);
                            // $stmt2->bindColumn('d10_valornetobs', $d10_valornetobs);
                            // $stmt2->bindColumn('d11_vidarestante', $d11_vidarestante);
                            $rowdePre = $stmt2->fetch();
                            $d10_valornetobs_aux = $rowdePre["d10_valornetobs"];
                            $d11_vidarestante_aux = $rowdePre["d11_vidarestante"];

                            if($d10_valornetobs_aux==null){
                                $d10_valornetobs_aux=$valorinicial;
                            }

                            $contador++;   
                        ?>
                        <tr>
                          <td class="text-center small"><?=$contador;?></td>
                          <td class="text-center small"><?=$codigoActivoX;?></td>
                          <td class="text-center small"><?=$cod_unidadorganizacional;?>/<?=$cod_area;?></td>
                          <td class="text-left small"><?= $cod_depreciaciones; ?></td>
                          <td class="text-left small"><?= $activoX; ?></td>
                          <td class="text-left small"><?= $tipo_alta; ?></td>
                          <td class="text-center small"><?= $fecha_alta; ?></td>
                          <td class="text-left small"><?= formatNumberDec($valor_inicial); ?></td>
                          <td class="text-left small"><?= formatNumberDec($d10_valornetobs_aux); ?></td>
                          <td class="text-left small"><?= $d11_vidarestante_aux; ?></td>
                          
                          <td class="text-left small"><?= $responsables_responsable; ?></td>
                          <td class="text-left small"><?= $responsables_responsable2; ?></td>

                          <td class="text-left small"><?= $estado_af; ?></td>
                        </tr>
                        <?php 
                            } 
                        ?>
                      </tbody>
                    </table>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>