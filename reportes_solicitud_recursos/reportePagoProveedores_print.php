<?php //ESTADO FINALIZADO

require_once '../conexion.php';

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../functionsReportes.php';
require_once '../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES

$personal_busqueda=$_POST["personal_busqueda"];
$stringpersonal_busquedaX=implode(",", $personal_busqueda);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
$fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));

$stringPersonal="";
foreach ($personal_busqueda as $valor ) {    
    $stringPersonal.=" ".namePersonal($valor).", ";
}

$sql="SELECT pl.nombre,pl.fecha,pl.cod_comprobante,pl.cod_estadopagolote,pl.observaciones,pl.nro_correlativo,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=pl.created_by)as personal,(select CONCAT(c.fecha,'|',c.glosa,'|',c.cod_estadocomprobante) from comprobantes c where c.codigo=pl.cod_comprobante) datos_comprobante
from pagos_lotes pl
where pl.cod_estadoreferencial=1 and pl.created_by in ($stringpersonal_busquedaX) and pl.fecha between '$desde 00:00:00' and '$hasta 23:59:59' order by pl.fecha desc";
$stmt2 = $dbh->prepare($sql);
 //echo $sql; 
// Ejecutamos
$stmt2->execute();
//resultado
$stmt2->bindColumn('nombre', $nombre);
$stmt2->bindColumn('fecha', $fecha);
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);
$stmt2->bindColumn('cod_estadopagolote', $cod_estadopagolote);
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('nro_correlativo', $nro_correlativo);
$stmt2->bindColumn('personal', $personal);
$stmt2->bindColumn('datos_comprobante', $datos_comprobante);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/favicon.png">
            </div>
            <h4 class="card-title text-center">Pago Proveedores</h4>     

            <h6 class="card-title">Personal: <?=$stringPersonal;?></h6>
            <div class="row">
               <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
            </div> 
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">
                <thead>                              
                  <tr>
                    <th><small><b>-</b></small></th>   
                    <th><small><b>N° Pago</b></small></th>                                
                    <th><small><b>N° Comprbte</b></small></th>
                    <th><small><b>Responsable</b></small></th>
                    <th><small><b>F.Pago</b></small></th>
                    <th><small><b>F.Comprobante</b></small></th>
                    <th><small><b>Proveedor</b></small></th>
                    <th><small><b>Importe Comp</b></small></th>
                    <th><small><b>Glosa Pago</b></small></th>
                    <th><small><b>Glosa Comprobante</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $importe_real_total=0;
                  $index=0;                   
                  while ($row = $stmt2->fetch()) { 
                    $index++;
                    if($datos_comprobante==null || $datos_comprobante==""){
                      $fecha_compt=0;
                      $glosa_compt="SIN COMPROBANTE";
                      $estado_compt=0;
                      $estilo_glosa="style='color:red;'";
                    }else{
                      List($fecha_compt, $glosa_compt, $estado_compt)=explode("|", $datos_comprobante);  
                      $estilo_glosa="";

                      if($estado_compt==2){
                        $estilo_glosa="style='color:red;'";
                      }

                    }
                    $nombre_comprobante=nombreComprobante($cod_comprobante);
                    $importe_comprobante_datos=obtenerTotalesDebeHaberComprobante($cod_comprobante);
                    $importe_comprobante=$importe_comprobante_datos[0];
                    
                    // $importe_real_total+=$importe_real;
                      
                      ?>
                      <tr>
                        <td class="text-center small"><?=$index;?></td>
                        <td class="text-left small"><?=$nro_correlativo;?></td>
                        <td class="text-right small" <?=$estilo_glosa?>><?=$nombre_comprobante;?></td>
                        <td class="text-right small"><small><?=$personal;?></small></td>
                        <td class="text-left small"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                        <td class="text-left small"><?=strftime('%d/%m/%Y',strtotime($fecha_compt));?></td>
                        <td class="text-right small"><?=$nombre;?><br></td>
                        <td class="text-left small"><?=formatNumberDec($importe_comprobante);?></td>
                        <td class="text-left small"><?=$observaciones;?></td>
                        <td class="text-left small" <?=$estilo_glosa?>><?=$glosa_compt;?></td>
                      </tr>
                    <?php 
                  }
                  $dbh=null;
                  $stmt2=null;
                  ?>
                </tbody>
               <!--  <tfoot>
                  <tr>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                    <td></td>                      
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>                      
                    <td>-</td>
                    <td>-</td>
                    <td class="text-left small">TOTALES</td>
                    <td class="text-left small"></td>
                    <td class="text-left small"></td>
                  </tr>  
                </tfoot> -->
                
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

