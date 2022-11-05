<?php
// require("../conexion_comercial_oficial.php");
require_once '../layouts/bodylogin2.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../conexion.php';
$dbhFin = new Conexion();



$minutos_tolerancia=obtenerValorConfiguracionPlanillas(37);

$sql="SELECT max(fecha) as fecha from asistencia_procesada where cod_uo=1";
$stmtFecha = $dbhFin->prepare($sql);
$stmtFecha->execute();
$stmtFecha->bindColumn('fecha', $fechaUltimoProc);
while ($rowFecha = $stmtFecha->fetch()) {
  $fechaUltimoProc=$fechaUltimoProc;
}

// $fechaFinal=date('Y-m-d');
$fecha_actual = date("Y-m-d");
//RESTO 1 día
$fechaFinal=date("Y-m-d",strtotime($fecha_actual."- 1 days"));

// $fechaFinal="2022-08-07";
//if($fechaFinal>$fechaUltimoProc){//validacion para no repetir ese día

// $fechaUltimoProc="2022-09-01";
 $fechaFinal="2022-10-31";//es necesario esta linea, por que el biometrico solo se carga hasta el ultimo mes


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

$sql="SELECT codigo,cod_area,turno,identificacion
  from personal where cod_estadopersonal=1 and cod_estadoreferencial=1 and cod_unidadorganizacional=1";//solo Of Central  and cod_area=500 and codigo=32
$stmtDatosPer = $dbhFin->prepare($sql);
$stmtDatosPer->execute();
$stmtDatosPer->bindColumn('codigo', $codPersonal);
$stmtDatosPer->bindColumn('cod_area', $cod_area);
$stmtDatosPer->bindColumn('identificacion', $identificacion);
// $stmtDatosPer->bindColumn('turno', $cod_turno);
while ($rowDatosPer = $stmtDatosPer->fetch()) {
  $fechaInicio=$fechaUltimoProc;
  while($fechaInicio<=$fechaFinal){
    $fechaInicio_x=$fechaInicio;
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
    //obtener esta data de acuerdo a lo que arme david
    $cod_horario=1;
    $array_asistencia[1]=array(2,'08:30','12:30','14:30','19:00','00:00','00:00','08:30','17:30');//(Turno Mañana, Tarde, Noche, Continuo)
    $array_asistencia[2]=array(2,'08:30','12:30','14:30','19:00','00:00','00:00','08:30','17:30');
    $array_asistencia[3]=array(2,'08:30','12:30','14:30','19:00','00:00','00:00','08:30','17:30');
    $array_asistencia[4]=array(2,'08:30','12:30','14:30','19:00','00:00','00:00','08:30','17:30');
    $array_asistencia[5]=array(2,'08:30','12:30','14:30','19:00','00:00','00:00','08:30','17:30');
    $array_asistencia[6]=array(2,'00:00','00:00','00:00','00:00','00:00','00:00','08:30','13:00');
    
    $diaSemana=date('N',strtotime($fechaInicio_x));
    if(isset($array_asistencia[$diaSemana])){//vemos si existe ese día
      $datos=$array_asistencia[$diaSemana];
      $tipo_turno=$datos[0];
      // $cod_turno=1;
      if($tipo_turno==1){//horario normal
        $hora_entrada=$datos[1];//$hora_entradaTM
        $hora_salida=$datos[2];//$hora_salidaTM
      }else{//horario continuo
        $hora_entrada=$datos[7];
        $hora_salida=$datos[8];
      }
      //Minutos Trabajados Asignados 
      $dateTimeAsg1 = date_create($hora_entrada.':00'); 
      $dateTimeAsg2 = date_create($hora_salida.':00'); 
      $differenceAsg = date_diff($dateTimeAsg1, $dateTimeAsg2);      
      $minutosAsignados += $differenceAsg->h * 60;
      $minutosAsignados += $differenceAsg->i;
      //Hora marcado ENTRADAS*
      $sql="SELECT min(a.fecha) as entrada,max(a.fecha)as salida
            from asistencia_oficinacentral  a 
            where a.fecha BETWEEN '$fechaInicio_x 00:00:00' and '$fechaInicio_x 23:59:59'  and identificacion like '$identificacion' and a.tipo_marcacion in (0,1)
            group by DATE(a.fecha)
            order by a.fecha";
          // echo $sql."<br><br>";
      $stmtPersonal = $dbhFin->prepare($sql);
      $stmtPersonal->execute();
      $stmtPersonal->bindColumn('entrada', $entradaMarcada);
      $stmtPersonal->bindColumn('salida', $salidaMarcada);
      // $stmtPersonal->bindColumn('identificacion', $identificacion);
      // $stmtPersonal->bindColumn('turno', $cod_turno);
      $sw_marcado=false;
      while ($datPersonal = $stmtPersonal->fetch()) {
        $sw_marcado=true;
        $fecha=explode(" ",$entradaMarcada);
        $fechaMarcado=$fecha[0];
        $horaMarcado=explode(":",$fecha[1])[0].":".explode(":",$fecha[1])[1];
        $fechaFin=explode(" ",$salidaMarcada);
        $fechaMarcadoFin=$fechaFin[0];
        $horaMarcadoFin=explode(":",$fechaFin[1])[0].":".explode(":",$fechaFin[1])[1];

        $horaMarcadodoble=date('H:i',strtotime($horaMarcado." + 1 hours"));//si se marcó dos veces en una sola hora
        if($horaMarcadoFin==$horaMarcado || ($horaMarcadoFin>=$horaMarcado && $horaMarcadoFin<=$horaMarcadodoble)){//si marco dos veces en una misma hora
          $horaMarcadoFin="-";
        }
      }
      if($sw_marcado){
        //CALCULAMOS MINUTOS ATRASOS Y DEMAS
        // Minutos Atraso
        $tarde = strtotime($horaMarcado)-strtotime($hora_entrada);
        if($tarde>=0){//echo "LLEGO A TIEMPO\n";        
          $dateTimeObject1 = date_create($hora_entrada.':00'); 
          $dateTimeObject2 = date_create($horaMarcado.':00'); 
          $difference = date_diff($dateTimeObject1, $dateTimeObject2);
          // $minutosAtraso += $difference->h * 60;
          // $minutosAtraso += $difference->i;

          $minutosAtraso_x = $difference->h * 60;
          $minutosAtraso_x += $difference->i;
          if($minutosAtraso_x>$minutos_tolerancia){
              $minutosAtraso+=$minutosAtraso_x;
          }

        }
        if($horaMarcadoFin<>"-"){
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
        
        // $minutosExtras=$minutosTrabajados-$minutosAsignados;
        // if($minutosExtras<0){
        //   $minutosExtras=0;
        // }
        $estadoAsistencia=1;
      }else{//no marcó ese día
        $estadoAsistencia=3;
      }
    }else{//no existe ese dia
      $estadoAsistencia=2;
    }

    $sqlInsert="INSERT INTO asistencia_procesada(fecha,cod_personal,cod_sucursal,cod_horario,entrada_asig,salida_asig,marcado1,marcado2,minutos_asignados,minutos_trabajados,minutos_atraso, minutos_extras,estado_asistencia,minutos_abandono,cod_uo) 
    VALUES ('$fechaInicio_x','$codPersonal','$cod_area','$cod_horario','$hora_entrada','$hora_salida','$horaMarcado','$horaMarcadoFin','$minutosAsignados','$minutosTrabajados','$minutosAtraso','$minutosExtras','$estadoAsistencia','$minutosAbandono','1')";
    //echo $sqlInsert."RRRR"; 
    $stmtInsert = $dbhFin->prepare($sqlInsert);
    $flagSuccessInsert=$stmtInsert->execute();
    ?>
      <tr>
        <td><?=$fechaInicio_x?></td>
        <td><?=$codPersonal?></td> 
        <td><?=$cod_area?></td> 
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
      </tr>

    <?php
    $fechaInicio=date('Y-m-d',strtotime($fechaInicio.'+1 day'));
  }//end while fecha
  // echo $fechaInicio."*";
}
?>
</table></center>




