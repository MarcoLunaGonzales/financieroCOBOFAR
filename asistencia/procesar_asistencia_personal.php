<?php
require("../conexion_comercial_oficial.php");
require_once '../layouts/bodylogin2.php';
require_once '../styles.php';

require_once '../conexion.php';
$dbhFin = new Conexion();

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

$sql="SELECT codigo,cod_area,turno 
  from personal where cod_estadopersonal=1 and cod_estadoreferencial=1 and cod_unidadorganizacional=2 and cod_area=1";//solo sucursales LP
$stmtPersonal = $dbhFin->prepare($sql);
$stmtPersonal->execute();
$stmtPersonal->bindColumn('codigo', $codPersonal);
$stmtPersonal->bindColumn('cod_area', $cod_area);
$stmtPersonal->bindColumn('turno', $cod_turno);
while ($row = $stmtPersonal->fetch()) {
  $fechaInicio='2022-06-01';
  $fechaFinal='2022-06-30';
  while($fechaInicio<=$fechaFinal){
    $fechaInicio_x=$fechaInicio;
    $minutosAtraso=0;
    $minutosExtras=0;
    $minutosAsignados=0;
    $minutosTrabajados=0
    $minutosAbandono=0;
    $estadoAsistencia=0;
    
    $hora_entrada="";
    $hora_salida="";
    $horaMarcado="";
    $horaMarcadoFin="";

    //obtener esta data de acuerdo a lo que arme davod
    $cod_horario=1;
    $array_asistencia[1]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');//(Turno Mañana, Tarde, Noche, Continuo)
    $array_asistencia[2]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');
    $array_asistencia[3]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');
    $array_asistencia[4]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');
    $array_asistencia[5]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');
    $array_asistencia[6]=array(1,'7:30','15:30','15:30','23:00','23:00','07:30');
    $array_asistencia[7]=array(2,'7:30','21:00');//domingo
    $array_asistencia[0]=array(2,'7:30','21:00');//feriado
    
    $diaSemana=date('N',strtotime($fechaInicio_x));
    if(isset($array_asistencia[$diaSemana])){//vemos si existe ese día
      $datos=$array_asistencia[$diaSemana];
      $tipo_turno=$datos[0];
      if($tipo_turno==1){//horario normal
        if($cod_turno==1){
          $hora_entrada=$datos[1];//$hora_entradaTM
          $hora_salida=$datos[2];//$hora_salidaTM
        }else if($cod_turno==2){
          $hora_entrada=$datos[3];//$hora_entradaTT
          $hora_salida=$datos[4];//$hora_salidaTT
        }else{
          // $turno="OF_CENTRAL";
        }
        // $hora_entradaTM=$datos[1];
        // $hora_salidaTM=$datos[2];
        // $hora_entradaTT=$datos[3];
        // $hora_salidaTT=$datos[4];
        // $hora_entradaTN=$datos[5];
        // $hora_salidaTN=$datos[6];
      }else{//horario continuo
        $hora_entrada=$datos[1];
        $hora_salida=$datos[2];
      }
      //Minutos Trabajados Asignados 
      $dateTimeAsg1 = date_create($hora_entrada.':00'); 
      $dateTimeAsg2 = date_create($hora_salida.':00'); 
      $differenceAsg = date_diff($dateTimeAsg1, $dateTimeAsg2);   
      // $dif_hora= $differenceAsg->h;
      // $minutes = $differenceAsg->days * 24 * 60;
      $minutosAsignados += $differenceAsg->h * 60;
      $minutosAsignados += $differenceAsg->i;
      $sqlPersonal="SELECT min(r.fecha_marcado) as fecha_marcado,max(r.fecha_marcado)as fecha_marcado_salida
        FROM marcados_personal r
        where  r.fecha_marcado BETWEEN '$fechaInicio_x 00:00:00' and '$fechaInicio_x 23:59:59' and r.cod_funcionario in ($codPersonal)  and r.cod_ciudad in (select c.cod_ciudad from ciudades c where c.cod_area=$cod_area)
        group by r.cod_ciudad,r.cod_funcionario,DATE(r.fecha_marcado)
        order by r.fecha_marcado";
         // echo $sqlPersonal."<br><br>";
      // $sucursal=0;
      $respPersonal=mysqli_query($dbh,$sqlPersonal);
      // echo $sqlPersonal;
      while($datPersonal=mysqli_fetch_array($respPersonal)){
        $estadoAsistencia=1;//tiene asistencia
        // $cod_turno=$datPersonal['turno'];
        // $codPersonal=$datPersonal[0];    
        $fecha=explode(" ",$datPersonal['fecha_marcado']);
        $fechaMarcado=$fecha[0];
        $horaMarcado=explode(":",$fecha[1])[0].":".explode(":",$fecha[1])[1];
        $fechaFin=explode(" ",$datPersonal['fecha_marcado_salida']);
        $fechaMarcadoFin=$fechaFin[0];
        $horaMarcadoFin=explode(":",$fechaFin[1])[0].":".explode(":",$fechaFin[1])[1];
        if($horaMarcadoFin==$horaMarcado){//si marco dos veces en una misma hora
          $horaMarcadoFin="-";
        }

        //CALCULAMOS ATRASOS Y DEMAS

        //Minutos Trabajados Marcados
        $dateTimeMarc1 = date_create($horaMarcado.':00'); 
        $dateTimeMarc2 = date_create($horaMarcadoFin.':00'); 
        $differenceMarc = date_diff($dateTimeMarc1, $dateTimeMarc2);   
        // $dif_hora= $differenceMarc->h;
        // $minutes = $differenceMarc->days * 24 * 60;
        $minutosTrabajados += $differenceMarc->h * 60;
        $minutosTrabajados += $differenceMarc->i;
        
        
        $tarde = strtotime($horaMarcado)-strtotime($hora_entrada);
        if($tarde>=0){//echo "LLEGO A TIEMPO\n";        
          $dateTimeObject1 = date_create($hora_entrada.':00'); 
          $dateTimeObject2 = date_create($horaMarcado.':00'); 
          $difference = date_diff($dateTimeObject1, $dateTimeObject2);   
          $dif_hora= $difference->h;
          $minutosCalc = $difference->days * 24 * 60;
          $minutosCalc += $difference->h * 60;
          $minutosCalc += $difference->i;
          $minutosAtraso+=$minutosCalc;
        }
        $minutosExtras=$minutosTrabajados-$minutosAsignados;
        if($minutosExtras<0){
          $minutosExtras=0;
        }
      }
    }else{//no existe ese dia
      $estadoAsistencia=2;
    }

    // $sqlInsert="INSERT INTO asistencia_procesada(fecha,cod_personal,cod_sucursal,cod_horario,entrada_asig,salida_asig,marcado1,marcado2,minutos_asignados,minutos_trabajados,minutos_atraso, minutos_extras,estado_asistencia) 
    // VALUES ('$fechaInicio_x','$codPersonal','$cod_area','$cod_horario','$hora_entrada','$hora_salida','$horaMarcado','$horaMarcadoFin','$minutosAsignados','$minutosTrabajados','$minutosAtraso','$minutosExtras','$estadoAsistencia')";
    // //echo $sqlInsert."RRRR"; 
    // $stmtInsert = $dbhFin->prepare($sqlInsert);
    // $flagSuccessInsert=$stmtInsert->execute();
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

}
?>
</table></center>




