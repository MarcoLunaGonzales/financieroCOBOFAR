<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
if($_POST['ver']==1){
  header("Pragma: public");
  header("Expires: 0");
  $filename = "reporte_horarios.xls";
  header("Content-type: application/x-msdownload");
  header("Content-Disposition: attachment; filename=$filename");
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
}else{
    require_once __DIR__.'/../functionsGeneral.php';
    require_once  __DIR__.'/../fpdf_html.php';
    require_once '../layouts/bodylogin2.php';
}


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

$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}


//para la fecha de cumple
$fecha_inicio=date('m-01');
$fecha_fin=date('m-t');
$fecha_actual=date('m-d');
 
$sql="SELECT a.nombre,ha.cod_area,ha.cod_horario
 from horarios_area ha 
JOIN horarios h on h.codigo=ha.cod_horario
join areas a on a.codigo=ha.cod_area
where ha.estado=1 and h.activo=1 and h.cod_estadoreferencial=1 and ha.cod_area in ($areaString)
order by a.nombre;
";  
//echo $sql;
$stmtActivos = $dbh->prepare($sql);
$stmtActivos->execute();
// bindColumn
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('nombre', $nombre);
$stmtActivos->bindColumn('cod_horario', $cod_horario);
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
                    <?php 
                    if($_POST['ver']==0){
                      ?><img  class="card-img-top"  src="../marca.png" style="width:50px;height: 50px;"><?php
                    }
                    ?>                    
                      Reporte Horarios 
                  </h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>
                  <h6 class="card-title">Areas: <?=$stringAreas; ?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">

                    <?php 
                    //
                    $htmlCabecera='<table class="table table-bordered table-condensed small" id="libreta_bancaria_reporte_modal">
                      <thead class="bg-dark text-white">
                        <tr style="background:#581845;color:#fff;">
                          <th class="font-weight-bold">-</th>
                          <th class="font-weight-bold">AREA</th>';
                          //el medio de la cabecera se hace al final por el colspan
                    $colspans=[];
                    $sqlTipo="SELECT codigo,descripcion FROM horarios_asignaciontipo order by codigo;";                          
                    $stmtTipo = $dbh->prepare($sqlTipo);
                    $stmtTipo->execute();
                    while ($rowTipo = $stmtTipo->fetch()) {
                      $colspans[$rowTipo['codigo']][0]=0;
                      $colspans[$rowTipo['codigo']][1]=0;
                      $colspans[$rowTipo['codigo']][2]=0;
                      $colspans[$rowTipo['codigo']][3]=0;
                    }      
                    $htmlFinCabecera='</tr>
                      </thead>
                      <tbody>';                     
                        $contador = 0;
                        $htmlCuerpo='';
                        
                        while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                          $contador++;
                          $htmlCuerpo.='<tr>
                            <td class="text-center">'.$contador.'</td>
                            <td class="text-left">'.$nombre.'</td>';
                                                  
                          $stmtTipo = $dbh->prepare($sqlTipo);
                          $stmtTipo->execute();
                          while ($rowTipo = $stmtTipo->fetch()) {
                            $codigoTipo=$rowTipo['codigo'];                          
                            $dato=obtenerDatosTipoHorario($codigoTipo,$cod_horario);
                            if($dato[0]==' A '){
                              $dato[0]='';
                            }
                            if($dato[1]==' A '){
                              $dato[1]='';
                            }
                            if($dato[2]==' A '){
                              $dato[2]='';
                            }
                            if($dato[3]==' A '){
                              $dato[3]='';
                            }
                            $htmlCuerpo.='<td class="columna_m'.$codigoTipo.'">'.$dato[0].'</td>';
                            $htmlCuerpo.='<td class="columna_t'.$codigoTipo.'">'.$dato[1].'</td>';
                            $htmlCuerpo.='<td class="columna_n'.$codigoTipo.'">'.$dato[2].'</td>';
                            $htmlCuerpo.='<td class="columna_c'.$codigoTipo.'">'.$dato[3].'</td>';

                            if($dato[0]!=''){
                              $colspans[$codigoTipo][0]++;
                            }
                            if($dato[1]!=''){
                              $colspans[$codigoTipo][1]++;
                            }
                            if($dato[2]!=''){
                              $colspans[$codigoTipo][2]++;
                            }
                            if($dato[3]!=''){
                              $colspans[$codigoTipo][3]++;
                            } 
                          }  
                          $htmlCuerpo.='</tr>';                                                                        
                        }                         
                        $stmtTipo = $dbh->prepare($sqlTipo);
                        $stmtTipo->execute();
                        $detalleCabecera2=''; 
                        $scriptColumnas='';                        
                        while ($rowTipo = $stmtTipo->fetch()) {
                          $colspanFila=0;
                          
                          if($colspans[$rowTipo['codigo']][0]>0){
                            $colspanFila++;
                            $detalleCabecera2.='<th><small>TM</small></th>';                                       
                          }else{
                            $scriptColumnas.='<script>$(".columna_m'.$rowTipo['codigo'].'").each(function(){$(this).remove();});</script>';
                          }
                          if($colspans[$rowTipo['codigo']][1]>0){
                            $colspanFila++;
                            $detalleCabecera2.='<th><small>TT</small></th>';
                          }else{
                            $scriptColumnas.='<script>$(".columna_t'.$rowTipo['codigo'].'").each(function(){$(this).remove();});</script>';
                          }
                          if($colspans[$rowTipo['codigo']][2]>0){
                            $colspanFila++;
                            $detalleCabecera2.='<th><small>TN</small></th>';                            
                          }else{
                            $scriptColumnas.='<script>$(".columna_n'.$rowTipo['codigo'].'").each(function(){$(this).remove();});</script>';
                          }
                          if($colspans[$rowTipo['codigo']][3]>0){
                            $colspanFila++;
                            $detalleCabecera2.='<th><small>HC</small></th>';                            
                          }else{
                            $scriptColumnas.='<script>$(".columna_c'.$rowTipo['codigo'].'").each(function(){$(this).remove();});</script>';
                          }

                          if($colspanFila>0){
                            $htmlCabecera.='<th class="font-weight-bold" colspan="'.$colspanFila.'">'.$rowTipo['descripcion'].'</th>';                             
                          }
                           
                        }

                        $detalleCabecera='</tr><tr class="bg-primary" style="background:#FFE933 !important;color:#000;"><th></th><th></th>'.$detalleCabecera2.'';

                        $htmlCabecera.=$detalleCabecera.$htmlFinCabecera;
                        $html=$htmlCabecera.$htmlCuerpo;
                        echo $html;
                        ?>
                      </tbody>
                    </table>

                    <?php 
                    echo $scriptColumnas;
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

    <script type="text/javascript">
      $(".columna_n6").each(function(){
        $(this).attr("style","display:block");
      });
    </script>
