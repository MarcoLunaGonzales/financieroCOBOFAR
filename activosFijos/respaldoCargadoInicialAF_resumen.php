<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

$sql="SELECT (select nombre from unidades_organizacionales where codigo=af.cod_unidadorganizacional) as nombre_unidadO,
af.cod_unidadorganizacional as cod_unidadorganizacional
from activosfijos af
where af.cod_unidadorganizacional
GROUP BY (nombre_unidadO)";
$stmtUO = $dbh->prepare($sql);
$stmtUO->execute();
$stmtUO->bindColumn('nombre_unidadO', $nombre_unidadO);
$stmtUO->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);

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
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <!-- <h6 class="card-title">Exportar como:</h6> -->
                  </div>
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                      Resumen Cargado Inicial Gestión 2021 
                  </h4>
                  <h6 class="card-title">
                    Gestion: 2021 - Mes: Enero<br>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <tbody>
                            <?php
                            //while ($row = $stmtUO->fetch()) { ?>
                                <!-- tr class="bg-dark text-white">
                                    <th colspan="11" >Oficina : <?php echo $nombre_unidadO; ?></th>
                                </tr> -->
                                <tr class="bg-info text-white">
                                    <th class=" small bg-primary ">Rubro</th>
                                    <th class=" small bg-primary font-weight-bold">Valor Inicial</th>
                                    <th class=" small bg-primary font-weight-bold">Valor Actualizado</th>
                                    <th class=" small bg-primary font-weight-bold">Depreciación Acumulada Anterior</th>
                                </tr>
                                <?php
                                    $sql="SELECT af.cod_depreciaciones 
                                    from  activosfijos af 
                                    WHERE af.cod_estadoactivofijo=1 and  af.cod_proy_financiacion=0 and af.tipo_af=1 and af.fechalta<='2021-01-01' GROUP BY af.cod_depreciaciones";
                                    $stmt_rubro = $dbh->prepare($sql);
                                    $stmt_rubro->execute();
                                    $stmt_rubro->bindColumn('cod_depreciaciones', $cod_depreciaciones_rubros);
                                    while ($row = $stmt_rubro->fetch()) {
                                      

                                        $nombreRubros=nameDepreciacion($cod_depreciaciones_rubros);
                                        $stmt2 = $dbh->prepare("SELECT sum(af.valorinicial)as valorinicial,sum(depreciacionacumulada)as depreacumulada,sum(valorresidual)as valorresidual
                                        from activosfijos af
                                        WHERE af.cod_estadoactivofijo=1 and  af.cod_proy_financiacion=0 and af.tipo_af=1 and af.fechalta<='2021-01-01' and af.cod_depreciaciones=$cod_depreciaciones_rubros");
                                        // Ejecutamos
                                        $stmt2->execute();
                                        //resultado
                                        $stmt2->bindColumn('valorinicial', $valorNeto);
                                        $stmt2->bindColumn('depreacumulada', $depreAcumAnt);
                                        $stmt2->bindColumn('valorresidual', $valorresidual);
                                
                                        while ($row = $stmt2->fetch()) {
                                            //totales
                                            $totalValorAnterior+=$valorresidual;
                                            $total_depreAcumAnt+=$depreAcumAnt;
                                            $total_valorNeto+=$valorNeto;

                                            ?>
                                            <tr class="">
                                                <td class="small bg-success text-left text-white"><small><?=$nombreRubros?></small></td>
                                                <td class="small"><small><?=formatNumberDec($valorNeto); ?></small></td>
                                                <td class="small"><small><?=formatNumberDec($valorresidual);?></small></td>
                                                <td class="small"><small><?=formatNumberDec($depreAcumAnt); ?></small></td>
                                                </tr>
                                            <?php 
                                        }
                                    }
                                     
                            //}
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="1">Total :</th>
                                <td class="small"><?=formatNumberDec($total_valorNeto); ?></td>
                                <td class="small"><?=formatNumberDec($totalValorAnterior); ?></td>
                                <td class="small"><?=formatNumberDec($total_depreAcumAnt); ?></td>
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

