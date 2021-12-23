
<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';
  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  
  $gestion=nameGestion($cod_gestion);


//html del reporte
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
             <h4 class="card-title text-center">OVT PLANILLA <?=$gestion?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
<table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
    <thead>
      <tr class="table-title small bold text-center">                  
        <td>Nro</td> 
        <td>Tipo de documento de identidad</td> 
        <td>Numero de documento de identidad</td>
        <td>Lugar de expedición</td>
        <td>Fecha de nacimiento</td>
        <td>Apellido Paterno</td>
        <td>Apellido Materno</td>
        <td>Nombres</td>
        <td>Pais de nacionalidad</td>
        <td>Sexo</td>
        <td>Jubilado</td>
        <td>Aporta a la AFP?</td>
        <td>Persona con discapacidad?</td>
        <td>Tutor de persona con discapacidad</td>
        <td>Fecha de ingreso</td>
        <td>Fecha de retiro</td>
        <td>Motivo retiro</td>
        <td>Caja de salud</td>
        <td>AFP a la que aporta</td>
        <td>NUA/CUA</td>
        <td>Sucursal o ubicacion adicional</td>
        <td>Clasificacion laboral</td>
        <td>Cargo</td>
        <td>Modalidad de contrato</td>
        
        <td>Promedio haber básico</td>
        <td>Promedio bono de antigüedad</td>
        
        <td>Promedio bono producción</td>
        <td>Promedio subsidio frontera</td>

        <td>Promedio trabajo extraordinario y nocturno</td>
        <td>Promedio pago dominical trabajado</td>
        <td>Promedio otros bonos</td>
        <td>Promedio total ganado</td>
        <td>Meses trabajados</td>
        <td>Total ganado después de duodécimas</td>
      </tr>                                  
    </thead>
    <tbody>
      <?php 
      $index=1;
      $Aporta_AFP=1;
      $Fecha_retiro='';
      $Motivo_retiro='';
      $Sucursal_adicional=1;
      $Modalidad_contrato=1;
      $Tipo_contrato=1;


      
      

		$sql = "SELECT (select tip.abreviatura from tipos_identificacion_personal tip where tip.codigo=pad.cod_tipo_identificacion) as tipo_identificacion,pad.identificacion,( select pd.abreviatura from personal_departamentos pd where pd.codigo=pad.cod_lugar_emision)as lugar_emision,pad.fecha_nacimiento,pad.paterno,pad.materno,pad.primer_nombre,(select n.nombre from personal_pais n where n.codigo=pad.cod_nacionalidad)as nacionalidad,(select g.abreviatura from tipos_genero g where g.codigo=pad.cod_genero) as genero,pad.jubilado,
      (select c.nombre from cargos c where c.codigo=pad.cod_cargo)as cargo,(select pd.tipo_persona_discapacitado from personal_discapacitado pd where pd.codigo=pad.codigo)as tipo_personal_discapacidad,pad.ing_planilla,pad.cod_cajasalud,pad.cod_tipoafp,pad.nua_cua_asignado,pad.cod_unidadorganizacional,
      
      ppm.promedio_basico,ppm.promedio_antiguedad,ppm.promedio_obonos,ppm.promedio_ganado,ppm.dias_360,ppm.total_aguinaldo
      from personal pad
      join planillas_aguinaldos_detalle ppm on ppm.cod_personal=pad.codigo
      
      join areas a on pad.cod_area=a.codigo
      where  ppm.cod_planilla=$cod_planilla
      order by pad.cod_unidadorganizacional,a.nombre,pad.paterno";
          // echo $sql."<br><br>";
        $stmtPersonal = $dbh->prepare($sql);
        $stmtPersonal->execute(); 
        
        $stmtPersonal->bindColumn('tipo_identificacion', $tipo_identificacion);
        $stmtPersonal->bindColumn('identificacion', $identificacion);
        $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
        $stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
        $stmtPersonal->bindColumn('paterno', $paterno);
        $stmtPersonal->bindColumn('materno', $materno);
        $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
        $stmtPersonal->bindColumn('nacionalidad', $nacionalidad);
        $stmtPersonal->bindColumn('genero', $genero);
        $stmtPersonal->bindColumn('jubilado', $jubilado);
        $stmtPersonal->bindColumn('tipo_personal_discapacidad', $tipo_personal_discapacidad);
        $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
        $stmtPersonal->bindColumn('cod_cajasalud', $cod_cajasalud);
        $stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
        $stmtPersonal->bindColumn('nua_cua_asignado', $nua_cua_asignado);
        $stmtPersonal->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
        $stmtPersonal->bindColumn('cargo', $cargo);


        $stmtPersonal->bindColumn('promedio_basico', $promedio_basico);
        $stmtPersonal->bindColumn('promedio_antiguedad', $promedio_antiguedad);
        $stmtPersonal->bindColumn('promedio_obonos', $promedio_obonos);
        $stmtPersonal->bindColumn('promedio_ganado', $promedio_ganado);
        $stmtPersonal->bindColumn('dias_360', $dias_360);
        $stmtPersonal->bindColumn('total_aguinaldo', $total_aguinaldo);
      
        while ($row = $stmtPersonal->fetch()) 
        {
          



          switch ($tipo_personal_discapacidad) {
            case 1:
              $Persona_discapacidad=1;
              $Tutordiscapacidad=0;
            break;
            case 2:
              $Persona_discapacidad=0;
              $Tutordiscapacidad=1;
            break;
            case 3:
              $Persona_discapacidad=0;
              $Tutordiscapacidad=0;
            break;
          }
          if($cod_tipoafp==1){//el codigo que se envía al ministerio es volteado.
            $cod_tipoafp=2;
          }else{
            $cod_tipoafp=1;
          }

          if($cod_unidadorganizacional==1)
            $Clasificacion_laboral=4;
          else
            $Clasificacion_laboral=5;
          // $aporte_caja=$afp_1+$afp_2;
          
          ?>
          <tr>
            <td class="text-center small"><?=$index?></td> 
              <td><?=$tipo_identificacion?></td> 
              <td><?=$identificacion?></td>
              <td><?=$lugar_emision?></td>
              <td><?=strftime('%d/%m/%Y',strtotime($fecha_nacimiento))?></td>
              <td class="text-left"><?=$paterno?></td>
              <td class="text-left"><?=$materno?></td>
              <td class="text-left"><?=$primer_nombre?></td>
              <td><?=$nacionalidad?></td>
              <td><?=$genero?></td>
              <td><?=$jubilado?></td>
              <td><?=$Aporta_AFP?></td>
              <td><?=$Persona_discapacidad?></td>
              <td><?=$Tutordiscapacidad?></td>
              <td><?=strftime('%d/%m/%Y',strtotime($ing_planilla))?></td>
              <td><?=$Fecha_retiro?></td>
              <td><?=$Motivo_retiro?></td>
              <td><?=$cod_cajasalud?></td>
              <td><?=$cod_tipoafp?></td>
              <td><?=$nua_cua_asignado?></td>
              <td><?=$Sucursal_adicional?></td>
              <td><?=$Clasificacion_laboral?></td>
              <td class="text-left"><?=$cargo?></td>
              <td><?=$Modalidad_contrato?></td>
              
            <td><?=$promedio_basico?></td>
            <td><?=$promedio_antiguedad?></td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td><?=$promedio_obonos?></td>
            <td><?=$promedio_ganado?></td>
            <td><?=$dias_360?></td>
            <td><?=$total_aguinaldo?></td>
          </tr> <?php 
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
  $dbh=null;
  $stmtPersonal=null;
?>

