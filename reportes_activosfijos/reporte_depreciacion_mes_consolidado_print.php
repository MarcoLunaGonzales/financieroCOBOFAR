<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES

$gestion = $_POST["gestion"];
$mes2 = $_POST["mes"];
$unidadOrganizacional=$_POST["unidad_organizacional"];
$cod_depreciaciones=$_POST["cod_depreciaciones"];

$unidadOrgString=implode(",", $unidadOrganizacional);
$depreciacionesString=implode(",", $cod_depreciaciones);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringDepreciaciones="";
foreach ($cod_depreciaciones as $valor ) {    
    $stringDepreciaciones.=" ".abrevDepreciacion($valor)." ";
}
$gestion=nameGestion($gestion);


//listamos las oficinas


$sql="SELECT (select uo.nombre from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)oficina,(select ar.nombre from areas ar where ar.codigo=af.cod_area)area,sum(md.d10_valornetobs) as valorneto
from mesdepreciaciones m, mesdepreciaciones_detalle md, activosfijos af
WHERE  m.codigo = md.cod_mesdepreciaciones and md.cod_activosfijos = af.codigo
 and af.cod_unidadorganizacional in ($unidadOrgString) and af.cod_depreciaciones in ($depreciacionesString)  and m.mes=$mes2 and m.gestion=$gestion 
 group by af.cod_unidadorganizacional,af.cod_area
 ORDER BY 1,2";
 // echo $sql;
 $stmt2 = $dbh->prepare($sql);
// Ejecutamos                                        
$stmt2->execute();
//resultado

$stmt2->bindColumn('oficina', $oficina_x);
$stmt2->bindColumn('area', $area_x);
$stmt2->bindColumn('valorneto', $valorneto);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card" >
                <div class="card-header <?=$colorCard;?> card-header-icon">
                    <h4 class="card-title"> 
                        <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                        Depreciación De Activos Fijos x Mes x Sucursal
                    </h4>
                    <h6 class="card-title">Mes: <?php echo nombreMes($mes2); ?><br>
                        Gestion: <?php echo $gestion; ?>
                    </h6> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th ><small>Oficina</small></th>
                                <th ><small>Area</small></th>
                                <th ><small>Valor Neto</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sumRubroValorNeto=0;
                                while ($row = $stmt2->fetch()) {
                                    //totales
                                    $sumRubroValorNeto+=$valorneto;
                                    ?>
                                   <tr>
                                        <td class="text-left small"><small><?=$oficina_x;?></td>
                                            <td class="text-left small"><small><?=$area_x;?></td>
                                        <td class="text-right small"><small><?=formatNumberDec($valorneto); ?></small></td>
                                    </tr>
                                    <?php 
                                }?>
                                <?php
                            ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-dark text-white">
                                <th colspan="2">Total :</th>
                                <td class="text-right small"><?=formatNumberDec($sumRubroValorNeto); ?></td>
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