<?php //ESTADO FINALIZADO

require_once '../conexion.php';

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../functionsReportes.php';
// require_once '../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES

$personal_busqueda=$_POST["personal_busqueda"];
$stringpersonal_busquedaX=implode(",", $personal_busqueda);

$cod_tipocomprobante=$_POST["cod_tipocomprobante"];
$stringcod_tipocomprobanteX=implode(",", $cod_tipocomprobante);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
$fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));

$stringPersonal="";
foreach ($personal_busqueda as $valor ) {    
  $stringPersonal.=" ".namePersonal($valor).", ";
}

$sql="SELECT uo.nombre as uo,cod_gestion,(select e.nombre from estados_comprobantes e where e.codigo=c.cod_estadocomprobante) as nombre_estado,(select tc.abreviatura from tipos_comprobante tc where tc.codigo=c.cod_tipocomprobante)as tipo_comprobante,c.fecha,c.numero,c.glosa,c.created_at,c.modified_at,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=c.created_by)as personal_crea,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=c.modified_by)as personal_mod
from comprobantes c join unidades_organizacionales uo on c.cod_unidadorganizacional=uo.codigo
where c.created_by in ($stringpersonal_busquedaX) and c.fecha between '$desde 00:00:00' and '$hasta 23:59:59' and c.cod_tipocomprobante in ($stringcod_tipocomprobanteX)
order by c.fecha,c.numero";
$stmt2 = $dbh->prepare($sql);
 // echo $sql; 
// Ejecutamos
$stmt2->execute();
//resultado
$stmt2->bindColumn('uo', $uo);
$stmt2->bindColumn('cod_gestion', $cod_gestion);
$stmt2->bindColumn('nombre_estado', $nombre_estado);
$stmt2->bindColumn('tipo_comprobante', $tipo_comprobante);
$stmt2->bindColumn('fecha', $fecha);
$stmt2->bindColumn('numero', $numero);
$stmt2->bindColumn('glosa', $glosa);
$stmt2->bindColumn('created_at', $created_at);
$stmt2->bindColumn('modified_at', $modified_at);
$stmt2->bindColumn('personal_crea', $personal_crea);
$stmt2->bindColumn('personal_mod', $personal_mod);

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
            <h4 class="card-title text-center">Comprobantes Generados</h4>
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
                    <th><small><b>Personal</b></small></th>
                    <th><small><b>Unidad</b></small></th>
                    <th><small><b>Gestión</b></small></th>
                    <th><small><b>Fecha</b></small></th>                  
                    <th><small><b>Numero</b></small></th>
                    <th><small><b>Estado</b></small></th>
                    <th><small><b>Glosa</b></small></th>
                    <th><small><b>F. Creación</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $importe_real_total=0;
                  $index=0;                   
                  while ($row = $stmt2->fetch()) { 
                    $index++;
                    $title="SIN MODIFICACION";
                    if($personal_mod<>null){
                      $title="Modificado por :".$personal_mod." en Fecha: ".strftime('%d/%m/%Y',strtotime($modified_at));
                    }
                    $mes=strftime('%m',strtotime($fecha));
                    // $importe_comprobante_datos=obtenerTotalesDebeHaberComprobante($cod_comprobante);
                    // $importe_comprobante=$importe_comprobante_datos[0];

                      ?>
                      <tr>
                        <td class="text-center small"><?=$index;?></td>
                        <td class="text-left small"><?=$personal_crea;?></td>
                        <td class="text-left small"><small><?=$uo;?></small></td>
                        <td class="text-right small"><small><?=$cod_gestion;?></small></td>
                        <td class="text-left small"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                        <td class="text-left small"><?=$tipo_comprobante;?><?=str_pad($mes, 2, "0", STR_PAD_LEFT)?>-<?=str_pad($numero, 5, "0", STR_PAD_LEFT);?></td>
                        <td class="text-left small"><?=$nombre_estado;?></td>
                        <td class="text-left small" width="60%"><?=$glosa;?></td>
                        <td class="text-left small" title="<?=$title?>"><?=strftime('%d/%m/%Y',strtotime($created_at));?></td>
                      </tr>
                    <?php 
                  }
                  $dbh=null;
                  $stmt2=null;
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

