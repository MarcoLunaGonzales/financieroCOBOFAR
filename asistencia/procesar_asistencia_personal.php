<?php

// echo "<div class='cargar-ajax'>
//   <div class='div-loading text-center'>
//      <h4 class='text-warning font-weight-bold' id='texto_ajax_titulo'>Procesando Datos</h4>
//      <p class='text-white'>Aguarde un momento por favor</p>  
//   </div>
// </div>";


require("../conexion_comercial_oficial.php");//cambiar al de reportes
// require("../conexion_comercial.php");

require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../conexion.php';
require_once '../functions.php';
$dbhFin = new Conexion();
$minutos_tolerancia=obtenerValorConfiguracionPlanillas(37);
$sql="SELECT IFNULL(max(fecha),'2022-08-01') as fecha from asistencia_procesada";
$stmtFecha = $dbhFin->prepare($sql);
$stmtFecha->execute();
$stmtFecha->bindColumn('fecha', $fechaUltimoProc);
while ($rowFecha = $stmtFecha->fetch()) {
  $fechaUltimoProc=$fechaUltimoProc;
}
// $fechaUltimoProc="2022-08-07";

$fechaFinal=date('Y-m-d');
// $fechaFinal="2022-08-07";
if($fechaFinal>$fechaUltimoProc){//validacion para no repetir ese día

  ?>
  <center><table class='table table-bordered table-condensed' style='width:80% !important'>
  <tr class='bg-info text-white' style='background:#A6F7C3 !important;color:#000 !important;height:30px;'>
    <td>fechaMarcado</td>
    <td>codPersonal</td> 
    <td>cod_area</td> 
    <td>cod_horario</td> 
    <td>hora_entrada</td> 
    <td>hora_salida</td> 
    <td>horaMarcado</td>
    <td>horaMarcadoFin</td>
    <td>MinutosAsignados</td>
    <td>MinutosTrabajados</td>
    <td>MinutosAtraso</td>
    <td>MinutosExtras</td>
    <td>MinutosAbandono</td>
    <td>estadoAsistencia</td>
  </tr>
  <?php 
  //Todos los horarios
  // $cod_horariox=1;
  // $array_horasx[1]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//lunes
  // $array_horasx[2]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//martes
  // $array_horasx[3]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//miercoles
  // $array_horasx[4]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//jueves
  // $array_horasx[5]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//viernes
  // $array_horasx[6]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//sabado
  // $array_horasx[7]=array(2,'00:00','00:00','00:00','00:00','00:00','00:00','09:00','21:00');//domingo
  // $array_horasx[8]=array(2,'00:00','00:00','00:00','00:00','00:00','00:00','09:00','21:00');//feriado
  // $array_horarios[$cod_horariox]=$array_horasx;
  //obtenemos horarios
  $sqlHorario="SELECT codigo from horarios where cod_estadoreferencial=1";//
  $stmtHorarioCab = $dbhFin->prepare($sqlHorario);
  $stmtHorarioCab->execute();
  $stmtHorarioCab->bindColumn('codigo', $cod_horariox);
  $array_horarios=[];
  while ($rowHorarioCab = $stmtHorarioCab->fetch()) {
    $sqlHorario="SELECT cod_asignacion,ingreso_1,salida_1,ingreso_2,salida_2,ingreso_3,salida_3,ingreso_4,salida_4 from horarios_detalle where cod_horario=$cod_horariox and cod_estadoreferencial=1";//
    // echo "<br><br>".$sqlHorario;
    $stmtHorarioDet = $dbhFin->prepare($sqlHorario);
    $stmtHorarioDet->execute();
    $stmtHorarioDet->bindColumn('cod_asignacion', $cod_asignacion);
    $stmtHorarioDet->bindColumn('ingreso_1', $ingreso_1);
    $stmtHorarioDet->bindColumn('salida_1', $salida_1);
    $stmtHorarioDet->bindColumn('ingreso_2', $ingreso_2);
    $stmtHorarioDet->bindColumn('salida_2', $salida_2);
    $stmtHorarioDet->bindColumn('ingreso_3', $ingreso_3);
    $stmtHorarioDet->bindColumn('salida_3', $salida_3);
    $stmtHorarioDet->bindColumn('ingreso_4', $ingreso_4);
    $stmtHorarioDet->bindColumn('salida_4', $salida_4);
    $array_horasx=[];
    while ($rowHorarioDet = $stmtHorarioDet->fetch()) {
      $cod_tipoHorario=1;
      if($ingreso_4 <> null && $ingreso_4 <> ""){//turno continuo
        $cod_tipoHorario=2;
      }
      $array_horasx[$cod_asignacion]=array($cod_tipoHorario,$ingreso_1,$salida_1,$ingreso_2,$salida_2,$ingreso_3,$salida_3,$ingreso_4,$salida_4);//(Turno Mañana, Tarde, Noche, Continuo)
    }
    $array_horarios[$cod_horariox]=$array_horasx;
  }

  //Obtenemos Array  de Areas y horarios
  //procesamos areas 
  $sql="SELECT a.codigo,a.nombre,(select ha.cod_horario from horarios_area ha where ha.cod_area=a.codigo and ha.estado=1 limit 1)as cod_horario
    from areas_organizacion ao join areas a on ao.cod_area=a.codigo
    where ao.cod_unidad=2 and ao.cod_estadoreferencial=1 and a.cod_estado=1 
    order by a.nombre";//solo sucursales LP//and a.codigo in (80)
    // echo "<br><br><br>".$sql;
  $stmtArea = $dbhFin->prepare($sql);
  $stmtArea->execute();
  $stmtArea->bindColumn('codigo', $cod_area);
  $stmtArea->bindColumn('cod_horario', $cod_horario);
  $i=0;
  $array_horariossuc=[];
  while ($rowArea = $stmtArea->fetch()) {
    $array_areas[$i]=$cod_area;
    $i++;
    //ASIGNAMOS HORARIO A SUCURSAL
    $array_horariossuc[$cod_area]=$array_horarios[$cod_horario];

    // $array_horas[1]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//lunes
    // $array_horas[2]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//martes
    // $array_horas[3]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//miercoles
    // $array_horas[4]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//jueves
    // $array_horas[5]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//viernes
    // $array_horas[6]=array(1,'07:30','15:30','15:30','23:00','00:00','00:00','09:00','21:00');//sabado
    // $array_horas[7]=array(2,'00:00','00:00','00:00','00:00','00:00','00:00','09:00','21:00');//domingo
    // $array_horas[8]=array(2,'00:00','00:00','00:00','00:00','00:00','00:00','09:00','21:00');//feriado
    // $array_horariossuc[$cod_area]=$array_horas;
  }
  //OBTENEMOS DIAS FERIADOS
  $sql="SELECT f.fecha from dias_feriados f WHERE f.cod_estado=1 and f.fecha BETWEEN '$fechaUltimoProc' and '$fechaFinal' order by f.fecha";
  $stmtFeriados = $dbhFin->prepare($sql);
  $stmtFeriados->execute();
  $stmtFeriados->bindColumn('fecha', $fecha_feriado);
  $array_feriados=[];
  while ($rowArea = $stmtFeriados->fetch()) {
    $array_feriados[$fecha_feriado]=$fecha_feriado;
  }
  $sql="SELECT cod_persona,cod_horario from horarios_persona where estado=1";//
  $stmtHrPersona = $dbhFin->prepare($sql);
  $stmtHrPersona->execute();
  $stmtHrPersona->bindColumn('fecha', $fecha_feriado);
  $array_persona_hr=[];
  while ($rowArea = $stmtHrPersona->fetch()) {
    $array_persona_hr[$cod_persona]=$cod_horario;
  }
  $contadorAreas=count($array_areas);
  for ($ix=0; $ix<$contadorAreas; $ix++) {
    $cod_areax=$array_areas[$ix];
    //Hora marcado *
    $sqlPersonal="SELECT DATE(r.fecha_marcado)as fecha_marcado_x,r.cod_funcionario,f.turno,min(r.fecha_marcado) as fecha_marcado,max(r.fecha_marcado)as fecha_marcado_salida
      FROM marcados_personal r join funcionarios f on r.cod_funcionario=f.codigo_funcionario
      where r.fecha_marcado BETWEEN '$fechaUltimoProc 00:00:00' and '$fechaFinal 23:59:59' and r.cod_ciudad in (select c.cod_ciudad from ciudades c where c.cod_area=$cod_areax)
      group by r.cod_funcionario,DATE(r.fecha_marcado)
      order by r.fecha_marcado";
      // echo $sqlPersonal."<br><br>";
    $respPersonal=mysqli_query($dbh,$sqlPersonal);
    // echo $sqlPersonal;
    while($datPersonal=mysqli_fetch_array($respPersonal)){
      $sw_datoshorario=false;
      // $cod_turno=$datPersonal['turno'];
      $fechaMarcado_x=$datPersonal['fecha_marcado_x'];
      $cod_funcionario=$datPersonal['cod_funcionario'];
      $minutosAtraso=0;
      $minutosExtras=0;
      $minutosAsignados=0;
      $minutosTrabajados=0;
      $minutosAbandono=0;
      $estadoAsistencia=0;
      $hora_entrada="";
      $hora_salida="";
      $horaMarcado="";
      $horaMarcadoFin="";
      //verificamos si la persona tiene otro horario
      if(isset($array_persona_hr[$cod_funcionario])){
        $cod_horario=$array_persona_hr[$cod_funcionario];
      }else{
        $cod_horario=0;  
      }

      if($cod_horario>0){//personal tiene asignado un horario? solo se asignará a algunas personas * casos especiales
        // echo "aqui***";
        $array_horas_asignadas=$array_horarios[$cod_horario];
        $diaSemana=date('N',strtotime($fechaMarcado_x));
        if(isset($array_horas_asignadas[$diaSemana])){//vemos si existe ese día
          $datosHorario=$array_horas_asignadas[$diaSemana];
          $sw_datoshorario=true;
        }
      }else{//Si no tiene, buscar la asignacion del area.
        $array_horas_asignadas=$array_horariossuc[$cod_areax];
        $diaSemana=date('N',strtotime($fechaMarcado_x));
        if(isset($array_horas_asignadas[$diaSemana])){//vemos si existe ese día
          $datosHorario=$array_horas_asignadas[$diaSemana];
          $sw_datoshorario=true;
        }
      }

      if($sw_datoshorario){
        $estadoAsistencia=1;//tiene asistencia
        //HORA DE MARCACION
        $fecha=explode(" ",$datPersonal['fecha_marcado']);
        $fechaMarcado=$fecha[0];
        $horaMarcado=explode(":",$fecha[1])[0].":".explode(":",$fecha[1])[1];
        $fechaFin=explode(" ",$datPersonal['fecha_marcado_salida']);
        $fechaMarcadoFin=$fechaFin[0];
        $horaMarcadoFin=explode(":",$fechaFin[1])[0].":".explode(":",$fechaFin[1])[1];
        $horaMarcadodoble=date('H:i',strtotime($horaMarcado." + 2 hours"));
        if($horaMarcadoFin==$horaMarcado || ($horaMarcadoFin>=$horaMarcado && $horaMarcadoFin<=$horaMarcadodoble)){//si marco dos veces en una misma hora
          $horaMarcadoFin="-";
        }

        //HORARIO ASIGNADO A LA SUCURSAL
        $tipo_horario=$datosHorario[0];
        if($tipo_horario==1){//horario normal
          $horaEntrada=date('H:i',strtotime($datosHorario[1]." - 1 hours"));//marcado mañana rago de una hora
          $horaEntradaMax=date('H:i',strtotime($datosHorario[1]." + 1 hours"));//marcado mañana rago de una hora
          if($horaMarcado>=$horaEntrada && $horaMarcado<=$horaEntradaMax){
            $hora_entrada=$datosHorario[1];//$hora_entradaTM
            $hora_salida=$datosHorario[2];//$hora_salidaTM
          }else{
            $hora_entrada=$datosHorario[3];//$hora_entradaTT
            $hora_salida=$datosHorario[4];//$hora_salidaTT
          }
        }else{//horario continuo
          $hora_entrada=$datosHorario[7];
          $hora_salida=$datosHorario[8];
        }
        if(isset($array_feriados[$fechaMarcado_x])){//es feriado?      
          $array_horas_asignadas=$array_horariossuc[$cod_areax];
          $datosHorario=$array_horas_asignadas[8];
          $hora_entrada=$datosHorario[7];//$hora_entrada
          $hora_salida=$datosHorario[8];//$hora_salida
        }

        //Minutos  Asignados de trabajo 
        $dateTimeAsg1 = date_create($hora_entrada.':00'); 
        $dateTimeAsg2 = date_create($hora_salida.':00'); 
        $differenceAsg = date_diff($dateTimeAsg1, $dateTimeAsg2);
        $minutosAsignados += $differenceAsg->h * 60;
        $minutosAsignados += $differenceAsg->i;

        //CALCULAMOS MINUTOS ATRASOS Y DEMAS
        // Minutos Atraso
        $tarde = strtotime($horaMarcado)-strtotime($hora_entrada);
        // $tarde=$tarde-$minutos_tolerancia;      
        if($tarde>=0){//echo "LLEGO A TIEMPO\n";        
          $dateTimeObject1 = date_create($hora_entrada.':00'); 
          $dateTimeObject2 = date_create($horaMarcado.':00'); 
          $difference = date_diff($dateTimeObject1, $dateTimeObject2);   
          // $dif_hora= $difference->h;
          // $minutosCalc = $difference->days * 24 * 60;
          $minutosAtraso_x = $difference->h * 60;
          $minutosAtraso_x += $difference->i;
          if($minutosAtraso_x>$minutos_tolerancia){
              $minutosAtraso+=$minutosAtraso_x;
          }
        }
        if($horaMarcadoFin<>"-"){//si no marco su hora salida
          //Minutos Trabajados Marcados
          $dateTimeMarc1 = date_create($horaMarcado.':00'); 
          $dateTimeMarc2 = date_create($horaMarcadoFin.':00'); 
          $differenceMarc = date_diff($dateTimeMarc1, $dateTimeMarc2);   
          // $dif_hora= $differenceMarc->h;
          // $minutes = $differenceMarc->days * 24 * 60;
          $minutosTrabajados += $differenceMarc->h * 60;
          $minutosTrabajados += $differenceMarc->i;
          //minutos abandono trabajados
          $dateTimeAbandono1 = date_create($hora_salida.':00'); 
          $dateTimeAbandono2 = date_create($horaMarcadoFin.':00'); 
          $differenceAbandono = date_diff($dateTimeAbandono1, $dateTimeAbandono2);   
          $abandono = strtotime($hora_salida)-strtotime($horaMarcadoFin);
          if($abandono>=0){//echo "Salio antes\n";        
            $minutosAbandono += $differenceAbandono->h * 60;
            $minutosAbandono += $differenceAbandono->i;          
            // $minutosAbandono +=
          }else{//Salió Despues
            $minutosExtras += $differenceAbandono->h * 60;
            $minutosExtras += $differenceAbandono->i;          
          }
        }else{
          $minutosAbandono+=$minutosAsignados;
        }
      }
      $sqlInsert="INSERT INTO asistencia_procesada(fecha,cod_personal,cod_sucursal,cod_horario,entrada_asig,salida_asig,marcado1,marcado2,minutos_asignados,minutos_trabajados,minutos_atraso, minutos_extras,estado_asistencia,minutos_abandono) 
        VALUES ('$fechaMarcado_x','$cod_funcionario','$cod_areax','$cod_horario','$hora_entrada','$hora_salida','$horaMarcado','$horaMarcadoFin','$minutosAsignados','$minutosTrabajados','$minutosAtraso','$minutosExtras','$estadoAsistencia','$minutosAbandono')";
        // echo $sqlInsert."RRRR"; 
        $stmtInsert = $dbhFin->prepare($sqlInsert);
        $flagSuccessInsert=$stmtInsert->execute();
      ?>
      <tr>
        <td><?=$fechaMarcado_x?></td>
        <td><?=$cod_funcionario?></td> 
        <td><?=$cod_areax?></td> 
        <td><?=$cod_horario?></td> 
        <td><?=$hora_entrada?></td> 
        <td><?=$hora_salida?></td> 
        <td><?=$horaMarcado?></td>
        <td><?=$horaMarcadoFin?></td>
        <td><?=$minutosAsignados?></td>
        <td><?=$minutosTrabajados?></td>
        <td><?=$minutosAtraso?></td>
        <td><?=$minutosExtras?></td>
        <td><?=$minutosAbandono?></td>
        <td><?=$estadoAsistencia?></td>
      </tr><?php
    }
  }
}

//CORREMOS FALTAS
$sql="SELECT p.codigo,p.cod_area,p.turno,(SELECT IFNULL(h.cod_horario,0) from horarios_persona h where h.estado=1 and h.cod_persona=p.codigo) as cod_horario
  from personal p where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 and p.cod_unidadorganizacional=2";//solo LP
$stmtPersonal = $dbhFin->prepare($sql);//and cod_area in (80)
$stmtPersonal->execute();
$stmtPersonal->bindColumn('codigo', $cod_personaly);
$stmtPersonal->bindColumn('cod_area', $cod_areay);
$stmtPersonal->bindColumn('cod_horario', $cod_horarioy);
$stmtPersonal->bindColumn('turno', $turnoy);
while ($row = $stmtPersonal->fetch()) {
  if($cod_horarioy>0){//personal tiene asignado un horario? solo se asignará a algunas personas * casos especiales
    // echo "aqui***";
    $array_horas_asignadas=$array_horarios[$cod_horarioy];
  }else{//Si no tiene, buscar la asignacion del area.
    $array_horas_asignadas=$array_horariossuc[$cod_areay];
  }

  $array_marcadoxy=[];
  $sql="SELECT fecha from asistencia_procesada where cod_sucursal=$cod_areay and cod_personal=$cod_personaly and fecha BETWEEN '$fechaUltimoProc' and '$fechaFinal'
  order by fecha";
  $stmtPerAsis = $dbhFin->prepare($sql);
  $stmtPerAsis->execute();
  $stmtPerAsis->bindColumn('fecha', $fechaxy);
  while ($rowPerAsis = $stmtPerAsis->fetch()) {
    $array_marcadoxy[$fechaxy]=1;
  }
  $fechaInicioxy=$fechaUltimoProc;
  while($fechaInicioxy<=$fechaFinal){
    if(!isset($array_marcadoxy[$fechaInicioxy])){//no marcó ese día
      $diaSemana=date('N',strtotime($fechaInicioxy));
      if(isset($array_horas_asignadas[$diaSemana])){//trabaja ese dia?
        $datosHorarioxy=$array_horas_asignadas[$diaSemana];
        $tipo_horario=$datosHorarioxy[0];
        if($tipo_horario==1){//horario normal
          
          if($turnoy==1){
            $hora_entradaxy=$datosHorarioxy[1];//$hora_entradaxyTM
            $hora_salidaxy=$datosHorarioxy[2];//$hora_salidaxyTM
          }elseif($turnoy==2){
            $hora_entradaxy=$datosHorarioxy[3];//$hora_entradaxyTT
            $hora_salidaxy=$datosHorarioxy[4];//$hora_salidaxyTT
          }else{
            // $turno="OF_CENTRAL";
          }
        }else{//horario continuo
          $hora_entradaxy=$datosHorarioxy[7];
          $hora_salidaxy=$datosHorarioxy[8];
        }
        // if(isset($array_feriados[$fechaMarcado_x])){//es feriado?
        //     $datosHorarioxy=$array_horas_asignadas[8];
        //     $hora_entrada=$datosHorarioxy[7];//$hora_entrada
        //     $hora_salidaxy=$datosHorarioxy[8];//$hora_salidaxy
        // }
        $minutosAsignadosxy=0;
        //Minutos Trabajados Asignados 
        $dateTimeAsg1 = date_create($hora_entradaxy.':00'); 
        $dateTimeAsg2 = date_create($hora_salidaxy.':00'); 
        $differenceAsg = date_diff($dateTimeAsg1, $dateTimeAsg2);
        $minutosAsignadosxy += $differenceAsg->h * 60;
        $minutosAsignadosxy += $differenceAsg->i;

        $estadoAsistencia=0;
      }else{
        $hora_entradaxy="-";
        $hora_salidaxy="-";
        $minutosAsignadosxy=0;
        $estadoAsistencia=2;
      }
      $sqlInsert="INSERT INTO asistencia_procesada(fecha,cod_personal,cod_sucursal,cod_horario,entrada_asig,salida_asig,marcado1,marcado2,minutos_asignados,minutos_trabajados,minutos_atraso, minutos_extras,estado_asistencia,minutos_abandono) 
      VALUES ('$fechaInicioxy','$cod_personaly','$cod_areay','$cod_horarioy','$hora_entradaxy','$hora_salidaxy','-','-','$minutosAsignadosxy','0','0','0','$estadoAsistencia',0)";
      // echo $sqlInsert."RRRR"; 
      $stmtInsert = $dbhFin->prepare($sqlInsert);
      $flagSuccessInsert=$stmtInsert->execute();
      ?>
      <tr>
        <td><?=$fechaInicioxy?></td>
        <td><?=$cod_personaly?></td> 
        <td><?=$cod_areay?></td> 
        <td><?=$cod_horarioy?></td> 
        <td><?=$hora_entradaxy?></td> 
        <td><?=$hora_salidaxy?></td> 
        <td>-</td>
        <td>-</td>
        <td><?=$minutosAsignadosxy?></td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td>0</td>
        <td><?=$estadoAsistencia?></td>
      </tr>
      <?php

    } 

    $fechaInicioxy=date('Y-m-d',strtotime($fechaInicioxy.'+1 day'));
  }//end while fecha

}

echo "<script language='Javascript'>
  $('.cargar-ajax').addClass('d-none');
    Swal.fire({
      title: 'CORRECTO',
      text: 'PROCESADO CORRECTAMENTE',
      type: 'success'
    }).then(function() {
        location.href='../index.php?opcion=asistenciaPersonal_main';
    });
    </script>";
?>



<!-- </table></center> -->







