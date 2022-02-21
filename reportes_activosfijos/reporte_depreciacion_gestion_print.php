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


$nombre_mes=nombreMes($mes2);
$gestion=nameGestion($gestion);
$fechaUltimo=$gestion."-".$mes2."-01";
$diaUltimo_x=date("t", strtotime($fechaUltimo));
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
                  <h4 class="card-title"><center> 
                    <!-- <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;"> -->
                      ESTADO CONSOLIDADO DE ACTIVOS FIJOS<br>
                      Al <?=$diaUltimo_x?> De <?=$nombre_mes?> <?=$gestion;?><br>
                      (Expresado en Bolivianos)</center>
                  </h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    
                    <!-- *******************TOTAL CONSOLIDADO ***************** -->
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>
                           
                            <tr class="bg-dark text-white">
                                <th>Rubro</th>
                                <th>Valor<br>Inicial</th>
                                <th>Altas</th>
                                <th>Bajas</th>
                                <th>Actualización</th>
                                <th>Valor<br>Actualizado</th>
                                <th>Depreciación<br>Acumulada Anterior</th>
                                <th>Actualización<br>Depreciación Acumulada</th>
                                <th>Depreciación Periodo</th>
                                
                                <th>Depreciación Acumulada</th>
                                <th>Valor Neto</th>                                    
                            </tr>
                            <?php

                                $totalValorAnterior_2=0;
                                $total_rubro_actualizacion_2=0;
                                $total_valor_actualizado_2=0;
                                $total_depreAcumAnt_2=0;
                                $total_actDepAcum_2=0;
                                $total_deprePeriodo_2=0;
                                $total_deprePeriodo_bajas=0;
                                $totalrubro_depreciacion_2=0;
                                $total_valorNeto_2=0;
                                $totalValorAltas_actualizacion=0;
                                $totalValorBajas_actualizacion=0;

                                $stmt2_total = $dbh->prepare("SELECT af.cod_depreciaciones,sum(md.d8_depreciacionperiodo)deprePeriodo,sum(md.d7_incrementodepreciacionacumulada)actDepAcum,sum(md.d5_incrementoporcentual)actualizacion_porcentual
                                    from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
                                    WHERE  m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
                                      and  af.cod_unidadorganizacional in ($unidadOrgString)
                                      and  m.gestion=$gestion 
                                      GROUP BY af.cod_depreciaciones");
                                $stmt2_total->execute();
                                $stmt2_total->bindColumn('actualizacion_porcentual', $actualizacion_porcentual_2);
                                $stmt2_total->bindColumn('actDepAcum', $actDepAcum_2);
                                $stmt2_total->bindColumn('deprePeriodo', $deprePeriodo_2);
                                $stmt2_total->bindColumn('cod_depreciaciones', $cod_depreciaciones_2);

                                // $stmt2_total->bindColumn('valorActualizado', $valorActualizado_2);
                                // $stmt2_total->bindColumn('totalDepreAcumu', $totalDepreAcumu_2);
                                // $stmt2_total->bindColumn('valorNeto', $valorNeto_2);

                                while ($row = $stmt2_total->fetch()) {
                                    $nombreRubros_2=nameDepreciacion($cod_depreciaciones_2);
                                    $string_datos_inicial=obtenerValorInicialDepreciacionGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);//valor inicial de gestion
                                    $datos_array=explode('###', $string_datos_inicial);
                                    
                                    $valorActualizado_inicial=$datos_array[0];
                                    $depreAcumAnt_2=$datos_array[1];
                                    $valorresidual_2=$datos_array[2];

                                    $valor_altas_actualizacion=obterValorAltasAFGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);
                                    $datos_bajas_string=obterValorBajasAFGestion($cod_depreciaciones_2,$gestion,$unidadOrgString);//actualizacion y depreciacion
                                    $datos_bajas_array=explode("###", $datos_bajas_string);
                                    $valor_bajas_actualizacion=$datos_bajas_array[0];

                                    $valor_bajas_depre=$datos_bajas_array[1];

                                    $string_datos=obtenerValorUltimoDepreciacionGestion($cod_depreciaciones_2,$gestion,$mes2,$unidadOrgString);//valor final de Gestion
                                    $datos_array=explode('###', $string_datos);
                                    $valorActualizado_2=$datos_array[0];

                                    // $depreAcumAnt_2=0;
                                    $totalDepreAcumu_2=$datos_array[1];
                                    // $valorNeto_2=$datos_array[2]-$valor_bajas;//
                                    $valorNeto_2=$datos_array[2];


                                    $deprePeriodo_2=$deprePeriodo_2-$valor_bajas_depre;
                                    //totales
                                    $totalValorAnterior_2+=$valorActualizado_inicial;
                                    $total_rubro_actualizacion_2+=$actualizacion_porcentual_2;
                                    $total_valor_actualizado_2+=$valorActualizado_2;
                                    $total_depreAcumAnt_2+=$depreAcumAnt_2;
                                    $total_actDepAcum_2+=$actDepAcum_2;
                                    $total_deprePeriodo_2+=$deprePeriodo_2;
                                    $totalrubro_depreciacion_2+=$totalDepreAcumu_2;
                                    $total_valorNeto_2+=$valorNeto_2;

                                    $totalValorAltas_actualizacion+=$valor_altas_actualizacion;
                                    $totalValorBajas_actualizacion+=$valor_bajas_actualizacion;

                                    // $total_deprePeriodo_bajas+=$valor_bajas_depre;
                                    ?>
                                    <tr>
                                        <td class="small bg-dark text-left text-white"><?=$nombreRubros_2?></td>
                                        <td><?=formatNumberDec($valorActualizado_inicial);?></td>
                                        <td><?=formatNumberDec($valor_altas_actualizacion);?></td>
                                        <td><?=formatNumberDec($valor_bajas_actualizacion);?></td>
                                        <td><?=formatNumberDec($actualizacion_porcentual_2);?></td>
                                        <td><?=formatNumberDec($valorActualizado_2);?></td>
                                        <td><?=formatNumberDec($depreAcumAnt_2); ?></td>
                                        <td><?=formatNumberDec($actDepAcum_2); ?></td>
                                        <td><?=formatNumberDec($deprePeriodo_2); ?></td>
                                        
                                        <td><?=formatNumberDec($totalDepreAcumu_2); ?></td>
                                        <td><?=formatNumberDec($valorNeto_2); ?></td>
                                        </tr>
                                    <?php 
                                }
                            // }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="1">Total :</th>
                                <td><?=formatNumberDec($totalValorAnterior_2); ?></td>
                                <td><?=formatNumberDec($totalValorAltas_actualizacion); ?></td>
                                <td><?=formatNumberDec($totalValorBajas_actualizacion); ?></td>
                                <td><?=formatNumberDec($total_rubro_actualizacion_2); ?></td>
                                <td><?=formatNumberDec($total_valor_actualizado_2);?></td>
                                <td><?=formatNumberDec($total_depreAcumAnt_2);?></td>
                                <td><?=formatNumberDec($total_actDepAcum_2); ?></td>
                                <td><?=formatNumberDec($total_deprePeriodo_2); ?></td>
                                
                                <td><?=formatNumberDec($totalrubro_depreciacion_2); ?></td>
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

