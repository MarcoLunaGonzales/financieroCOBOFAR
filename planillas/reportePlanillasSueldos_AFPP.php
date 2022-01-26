<?php
// require_once '../conexion3.php';


require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';

  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  $cod_mes = $_GET["cod_mes"];//


  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);


?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="50" height="40" src="../assets/img/favicon.png">
            </div>
             <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>-->
             <h4 class="card-title text-center">Planillas AFP Previsión</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
                  <thead>
                    <tr class="table-title small bold text-center">                  
                      <td class="small">TIPO DOC.</td> 
                      <td class="small">NUMERO DOCUMENTO</td>
                      <td class="small">ALFANUMERICO DEL DOCUMENTO</td>
                      <td class="small">NUA / CUA</td>
                      <td class="small">AP. PATERNO</td>
                      <td class="small">AP. MATERNO</td>
                      <td class="small">AP. CASADA</td>
                      <td class="small">PRIMER NOMBRE</td>
                      <td class="small">SEG. NOMBRE</td>
                      <td class="small">NOVEDAD</td>
                      <td class="small">FECHA NOVEDAD</td>
                      <td class="small">DIAS</td>
                      <td class="small">TOTAL GANADO</td>
                      <td class="small">TIPO_COTIZANTE</td>
                      <td class="small">TIPO_ASEGURADO</td>
                    </tr>                                  
                  </thead>
                  <tbody>
                    <?php
                    $index=1;

                    $dias_trabajados_por_defecto=30;
                    $sql = "SELECT (select tip.abreviatura from tipos_identificacion_personal tip where tip.codigo=pad.cod_tipo_identificacion) as tipo_identificacion,pad.identificacion,pad.paterno,pad.materno,pad.apellido_casada,pad.primer_nombre,pad.nua_cua_asignado,ppm.dias_trabajados,ppm.total_ganado
                    from planillas_personal_mes ppm,personal pad
                    where ppm.cod_personalcargo=pad.codigo and cod_planilla=$cod_planilla and pad.cod_estadoreferencial=1 and pad.cod_estadopersonal=1 and pad.cod_tipoafp=2
                    order by pad.paterno";
                         //echo $sql."<br><br>";
                      $stmtPersonal = $dbh->prepare($sql);
                      $stmtPersonal->execute(); 
                      $stmtPersonal->bindColumn('tipo_identificacion', $tipo_identificacion);
                      $stmtPersonal->bindColumn('identificacion', $identificacion);
                      $stmtPersonal->bindColumn('paterno', $paterno);
                      $stmtPersonal->bindColumn('materno', $materno);
                      $stmtPersonal->bindColumn('apellido_casada', $apellido_casada);
                      $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
                      $stmtPersonal->bindColumn('nua_cua_asignado', $nua_cua_asignado);
                      $stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
                      $stmtPersonal->bindColumn('total_ganado', $total_ganado);
                      while ($row = $stmtPersonal->fetch()) 
                      {  
                        $primer_nombre.=" ";
                        $array_nombre=explode(' ', $primer_nombre);
                        
                        ?>
                        <tr>
                          <td class="text-center small"><?=$tipo_identificacion?></td>
                          <td class="text-left small"><?=$identificacion?></td>
                          <td class="text-left small"></td>
                          <td class="text-left small"><?=$nua_cua_asignado?></td>
                          <td class="text-left small"><?=$paterno?></td>
                          <td class="text-left small"><?=$materno?></td>
                          <td class="text-left small"><?=$apellido_casada?></td>
                          <td class="text-left small"><?=$array_nombre[0]?></td>
                          <td class="text-left small"><?=$array_nombre[1]?></td>
                          <td class="text-left small"></td>
                          <td class="text-left small"></td>
                          <td class="text-right small"><?=$dias_trabajados?></td>
                          <td class="text-right small"><?=$total_ganado?></td>
                          <td class="text-left small"></td>
                          <td class="text-left small"></td>
                        </tr><?php
                        $index+=1;
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

<?php

 // echo $html;

?>

