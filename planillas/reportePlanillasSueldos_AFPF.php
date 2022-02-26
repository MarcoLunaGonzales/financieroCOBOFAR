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
             <h4 class="card-title text-center">Planillas AFP Futuro</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">

<table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
    <thead>
      <tr class="table-title small bold text-center">                  
        <td class="small">No</td> 
        <td class="small">(13) TIPO</td> 
        <td class="small">(14) No</td>
        <td class="small">(14) EXTENSIÓN</td>
        <td class="small">(15) NUA/CUA</td>
        <td class="small">(A) 1er. APELLIDO (PATERNO)</td>
        <td class="small">(B) 2do. APELLIDO (MATERNO)</td>
        <td class="small">(C) APELLIDO CASADA</td>
        <td class="small">(D)  PRIMER NOMBRE</td>
        <td class="small">(E) SEGUNDO NOMBRE</td>
        <td class="small">(F) DEPARTAMENTO</td>
        <td class="small">(17) NOVEDAD I/R/L/S</td>
        <td class="small">(18) FECHA NOVEDAD dd/mm/aaaa</td>
        <td class="small">(19) DIAS COTIZADOS</td>
        <td class="small">(20) TIPO DE ASEGURADO (M/C/E)</td>
        <td class="small">(21)  TOTAL GANADO DEPENDIENTE < 65 AÑOS O ASEGURADO CON PENSION DEL SIP < 65 AÑOS QUE DECIDE APORTAR AL SIP</td>
        <td class="small">(22) TOTAL GANADO DEPENDIENTE > 65 AÑOS O ASEGURADO CON PENSION DEL SIP > 65 AÑOS QUE DECIDE APORTAR AL SIP</td>
        <td class="small">(23) TOTAL GANADO ASEGURADO CON PENSION DEL SIP < 65 AÑOS QUE DECIDE NO APORTAR AL SIP</td>
        <td class="small">(24)  TOTAL GANADO ASEGURADO CON PENSION AL SIP > 65 AÑOS QUE DECIDE NO APORTAR AL SIP</td>
        <td class="small">(25) COTIZACION ADICIONAL</td>
        <td class="small">(26) TOTAL GANADO FONDO DE VIVIENDA</td>
        <td class="small">(27) TOTAL GANADO FONDO SOLIDARIO</td>
        <td class="small">(28) TOTAL GANADO FONDO SOLIDARIO MINERO</td>
      </tr>                                  
    </thead>
    <tbody>
      <?php
      $index=1;

      $dias_trabajados_por_defecto=30;
      $sql = "SELECT (select tip.abreviatura from tipos_identificacion_personal tip where tip.codigo=pad.cod_tipo_identificacion) as tipo_identificacion,
      ( select pd.abreviatura from personal_departamentos pd where pd.codigo=pad.cod_lugar_emision)as lugar_emision,
      pad.identificacion,pad.paterno,pad.materno,pad.apellido_casada,pad.primer_nombre,pad.nua_cua_asignado,ppm.dias_trabajados,ppm.total_ganado
      from planillas_personal_mes ppm,personal pad
      where ppm.cod_personalcargo=pad.codigo and cod_planilla=$cod_planilla and pad.cod_tipoafp=1
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
        $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
        while ($row = $stmtPersonal->fetch()) 
        {  
          $primer_nombre.=" ";
          $array_nombre=explode(' ', $primer_nombre);
          
          ?>
          <tr>
            <td class="text-center small"><?=$index?></td>
            <td class="text-center small"><?=$tipo_identificacion?></td>
            <td class="text-left small"><?=$identificacion?></td>
            <td class="text-left small"><?=$lugar_emision?></td>
            <td class="text-left small"><?=$nua_cua_asignado?></td>
            <td class="text-left small"><?=$paterno?></td>
            <td class="text-left small"><?=$materno?></td>
            <td class="text-left small"><?=$apellido_casada?></td>
            <td class="text-left small"><?=$array_nombre[0]?></td>
            <td class="text-left small"><?=$array_nombre[1]?></td>
            <td class="text-left small"></td>
            <td class="text-left small"></td>
            <td class="text-left small"></td>
            <td class="text-right small"><?=$dias_trabajados?></td>
            <td class="text-left small"></td>
            <td class="text-right small"><?=$total_ganado?></td>
            <td class="text-left small"></td>
            <td class="text-left small"></td>
            <td class="text-left small"></td>
            <td class="text-left small"></td>
            <td class="text-right small"><?=$total_ganado?></td>
            <td class="text-right small"><?=$total_ganado?></td>
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

