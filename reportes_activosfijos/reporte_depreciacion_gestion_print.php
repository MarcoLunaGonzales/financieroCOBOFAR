<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestion"];
// $mes2 = $_POST["mes"];
$unidadOrganizacional=$_POST["unidad_organizacional"];
// $cod_depreciaciones=$_POST["cod_depreciaciones"];

$unidadOrgString=implode(",", $unidadOrganizacional);
// $depreciacionesString=implode(",", $cod_depreciaciones);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}

$mes2=12;//ULTIMA DEPRECIACION

$gestion=nameGestion($gestion);


// $sql="SELECT (select nombre from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,
// af.cod_unidadorganizacional as cod_unidadorganizacional
// from activosfijos af
// where af.cod_unidadorganizacional in ($unidadOrgString)
// GROUP BY (nombre_unidadO)";
// $stmtUO = $dbh->prepare($sql);
// $stmtUO->execute();
// $stmtUO->bindColumn('nombre_unidadO', $nombre_unidadO);
// $stmtUO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);

$totalValorAnterior=0;
$total_rubro_actualizacion=0;
$total_valor_actualizado=0;
$total_depreAcumAnt=0;
$total_actDepAcum=0;
$total_deprePeriodo=0;
$totalrubro_depreciacion=0;
$total_valorNeto=0;
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header  card-header-icon">
                  <div class="float-right col-sm-2">
                    <!-- <h6 class="card-title">Exportar como:</h6> -->
                  </div>
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                      Depreciación De Activos Fijos Por Gestión
                  </h4>
                  <h6 class="card-title">
                    Gestion: <?php echo $gestion; ?> <br>
                    Oficinas: <?=$stringUnidades; ?>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    
                    <!-- *******************TOTAL CONSOLIDADO ***************** -->
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>
                           <tr class="bg-dark text-white">
                                    <th colspan="11" >***CONSOLIDADO GESTIÓN***</th>
                                </tr>
                            <tr class="bg-info text-white">
                                <th class=" small bg-primary ">Rubro</th>
                                <th class=" small bg-primary font-weight-bold">Valor<br>Inicial</th>
                                
                                <th class=" small bg-primary font-weight-bold">Altas</th>
                                <th class=" small bg-primary font-weight-bold">Bajas</th>

                                <th class=" small bg-primary font-weight-bold">Actualización</th>
                                <th class=" small bg-primary font-weight-bold">Valor<br>Actualizado</th>
                                <th class=" small bg-primary font-weight-bold">Depreciación<br>Acumulada Anterior</th>
                                <th class=" small bg-primary font-weight-bold">Actualización<br>Depreciación Acumulada</th>
                                <th class=" small bg-primary font-weight-bold">Depreciación Periodo</th>
                                <th class=" small bg-primary font-weight-bold">Depreciación Acumulada</th>
                                <th class=" small bg-primary font-weight-bold">Valor Neto</th>                                    
                            </tr>
                            <?php

                            $totalValorAnterior_2=0;
                            $total_rubro_actualizacion_2=0;
                            $total_valor_actualizado_2=0;
                            $total_depreAcumAnt_2=0;
                            $total_actDepAcum_2=0;
                            $total_deprePeriodo_2=0;
                            $totalrubro_depreciacion_2=0;
                            $total_valorNeto_2=0;
                            $totalValorAltas=0;
                            $totalValorBajas=0;

                                $stmt2_total = $dbh->prepare("SELECT af.cod_depreciaciones,sum(md.d8_depreciacionperiodo)deprePeriodo,sum(md.d7_incrementodepreciacionacumulada)actDepAcum,sum(md.d5_incrementoporcentual)actualizacion_porcentual,sum(md.d4_valoractualizado)valorActualizado,sum(md.d9_depreciacionacumuladaactual)totalDepreAcumu,sum(md.d10_valornetobs)valorNeto
                                    from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                    WHERE  m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                      and  af.cod_unidadorganizacional in ($unidadOrgString)
                                      and  m.gestion=$gestion and m.mes=$mes2
                                      GROUP BY af.cod_depreciaciones");
                                $stmt2_total->execute();
                                $stmt2_total->bindColumn('actualizacion_porcentual', $actualizacion_porcentual_2);
                                $stmt2_total->bindColumn('actDepAcum', $actDepAcum_2);
                                $stmt2_total->bindColumn('deprePeriodo', $deprePeriodo_2);
                                $stmt2_total->bindColumn('cod_depreciaciones', $cod_depreciaciones_2);

                                $stmt2_total->bindColumn('valorActualizado', $valorActualizado_2);
                                $stmt2_total->bindColumn('totalDepreAcumu', $totalDepreAcumu_2);
                                $stmt2_total->bindColumn('valorNeto', $valorNeto_2);
                                
                                while ($row = $stmt2_total->fetch()) {
                                    $nombreRubros_2=nameDepreciacion($cod_depreciaciones_2);
                                    $string_datos_inicial=obtenerValorInicialDepreciacionGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);//valor inicial de gestion
                                    $datos_array=explode('###', $string_datos_inicial);
                                    
                                    $valorActualizado_inicial=$datos_array[0];
                                    $depreAcumAnt_2=$datos_array[1];
                                    $valorresidual_2=$datos_array[2];

                                    

                                    

                                    // $string_datos=obtenerValorUltimoDepreciacionGestion($cod_depreciaciones_2,$gestion,$mes2,$unidadOrgString);//valor final de Gestion
                                    // $datos_array=explode('###', $string_datos);
                                    // $valorActualizado_2=$datos_array[0];
                                    // // $depreAcumAnt_2=0;
                                    // $totalDepreAcumu_2=$datos_array[1];
                                    // $valorNeto_2=$datos_array[2];


                                    // $valorActualizado_2=$datos_array[0];
                                    // // // $depreAcumAnt_2=0;
                                    //  $totalDepreAcumu_2=$datos_array[1];
                                    // $valorNeto_2=$datos_array[2];


                                    $valor_altas=obterValorAltasAFGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);
                                    $valor_bajas=obterValorBajasAFGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);
                                    //totales
                                    $totalValorAnterior_2+=$valorActualizado_inicial;
                                    $total_rubro_actualizacion_2+=$actualizacion_porcentual_2;
                                    $total_valor_actualizado_2+=$valorActualizado_2;
                                    $total_depreAcumAnt_2+=$depreAcumAnt_2;
                                    $total_actDepAcum_2+=$actDepAcum_2;
                                    $total_deprePeriodo_2+=$deprePeriodo_2;
                                    $totalrubro_depreciacion_2+=$totalDepreAcumu_2;
                                    $total_valorNeto_2+=$valorNeto_2;

                                    $totalValorAltas+=$valor_altas;
                                    $totalValorBajas+=$valor_bajas;
                                    ?>
                                    <tr class="">
                                        <td class="small bg-primary text-left text-white"><small><?=$nombreRubros_2?></small></td>
                                        <td class="small"><small><?=formatNumberDec($valorActualizado_inicial);?></small></td>
                                        <td class="small"><small><?=formatNumberDec($valor_altas);?></small></td>
                                        <td class="small"><small><?=formatNumberDec($valor_bajas);?></small></td>
                                        <td class="small bg-success text-white"><small><?=formatNumberDec($actualizacion_porcentual_2);?></small></td>
                                        <td class="small"><small><?=formatNumberDec($valorActualizado_2);?></small></td>
                                        <td class="small"><small><?=formatNumberDec($depreAcumAnt_2); ?></small></td>
                                        <td class="small bg-success text-white"><small><?=formatNumberDec($actDepAcum_2); ?></small></td>
                                        <td class="small bg-success text-white"><small><?=formatNumberDec($deprePeriodo_2); ?></small></td>
                                        <td class="small"><small><?=formatNumberDec($totalDepreAcumu_2); ?></small></td>
                                        <td class="small"><small><?=formatNumberDec($valorNeto_2); ?></small></td>
                                        </tr>
                                    <?php 
                                }
                            // }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="1">Total :</th>
                                <td class="small"><?=formatNumberDec($totalValorAnterior_2); ?></td>
                                <td class="small"><?=formatNumberDec($totalValorAltas); ?></td>
                                <td class="small"><?=formatNumberDec($totalValorBajas); ?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_rubro_actualizacion_2); ?></td>
                                <td class="small"><?=formatNumberDec($total_valor_actualizado_2);?></td>
                                <td class="small"><?=formatNumberDec($total_depreAcumAnt_2);?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_actDepAcum_2); ?></td>
                                <td class="bg-secondary text-white small"><?=formatNumberDec($total_deprePeriodo_2); ?></td>
                                <td class="small"><?=formatNumberDec($totalrubro_depreciacion_2); ?></td>
                                <td class="small"><?=formatNumberDec($total_valorNeto_2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

