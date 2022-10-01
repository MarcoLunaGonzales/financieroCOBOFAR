<?php
	function obtenerDetalleAtrasosPersonal($cod_personal,$fechaInicio,$fechaFinal,$cod_sucursal,$variable_aux,$sw_otrosistema,$dbh){
	?>
	<tr class="bg-info text-white" style="background:#A6F7C3 !important;color:#000 !important;height:30px;">
	<th>Fecha</th>
	<th>Hora Asign</th>
	<th>Hora Marca</th>
	<th>Hora Asigna</th>
	<th>Hora Marca</th>
	<th>Asignados [Min]</th>
	<th>Trabajado [Min]</th>
	<th>Atraso [Min]</th>
	<th>Permisos</th>'
	<?php
	if($sw_otrosistema==0){ ?>
	  <th>Extras [Min]</th>
	<?php } ?>
	
	<th>Abandono [Min]</th>
	</tr>
	<?php 
	$totalAsignados=0;
	$totalTrabajados=0;
	$totalAtrasos=0;              
	$totalExtras=0;
	$totalAbandono=0;
	$totalPermisos=0;

	if($variable_aux<>'-1000'){//marcacion de sucursal especifica
		$sqlPersonal="SELECT ap.codigo,ap.fecha,ap.cod_sucursal,ap.cod_horario,ap.entrada_asig,ap.salida_asig,ap.marcado1,ap.marcado2,ap.minutos_asignados,ap.minutos_trabajados,ap.minutos_atraso,ap.minutos_extras,ap.minutos_abandono,ap.estado_asistencia
		from asistencia_procesada ap
		where ap.cod_personal=$cod_personal and fecha between '$fechaInicio' and '$fechaFinal' and ap.cod_sucursal=$cod_sucursal  order by ap.fecha ";
	}else{//marcacion diferentes sucursales
		$sqlPersonal="SELECT ap.codigo,ap.fecha,ap.cod_sucursal,ap.cod_horario,ap.entrada_asig,ap.salida_asig,ap.marcado1,ap.marcado2,ap.minutos_asignados,ap.minutos_trabajados,ap.minutos_atraso,ap.minutos_extras,ap.minutos_abandono,ap.estado_asistencia
		from asistencia_procesada ap
		where ap.cod_personal=$cod_personal and fecha between '$fechaInicio' and '$fechaFinal' and ap.cod_sucursal != $cod_sucursal  order by ap.fecha ";
	}
	

	$stmtPersonal = $dbh->prepare($sqlPersonal);
	$stmtPersonal->execute();
	$stmtPersonal->bindColumn('codigo', $codigo);
	// $stmtPersonal->bindColumn('cod_personal', $cod_personal);
	$stmtPersonal->bindColumn('cod_sucursal', $cod_area);
	$stmtPersonal->bindColumn('fecha', $fecha);
	$stmtPersonal->bindColumn('entrada_asig', $entrada_asig);
	$stmtPersonal->bindColumn('salida_asig', $salida_asig);
	$stmtPersonal->bindColumn('marcado1', $marcado1);
	$stmtPersonal->bindColumn('marcado2', $marcado2);
	$stmtPersonal->bindColumn('minutos_asignados', $minutos_asignados);
	$stmtPersonal->bindColumn('minutos_trabajados', $minutos_trabajados);
	$stmtPersonal->bindColumn('minutos_atraso', $minutos_atraso);
	$stmtPersonal->bindColumn('minutos_extras', $minutos_extras);
	$stmtPersonal->bindColumn('minutos_abandono', $minutos_abandono);
	$stmtPersonal->bindColumn('estado_asistencia', $estado_asistencia);
	while ($row = $stmtPersonal->fetch()) { 
		$label_abandono="";
		$label_extras="";
		$label_atraso="";
		$sw_edit=true;
		// $sw_edit=false;
		switch ($estado_asistencia) {
		  case 0://sin asistencia
		    $marcado1="X";
		    $marcado2="X";
		    $label_entrada="style='color:red;font-weight:bold;'";
		    $label_salida="style='color:red;font-weight:bold;'";
		  break;
		  case 1://Asistencia
		    if($minutos_atraso>0){                      
		      $label_entrada="style='color:red;font-weight:bold;'";
		      $label_atraso="style='color:red;font-weight:bold;'";
		    }else{
		      if($marcado1>$entrada_asig){//si está dentro de la tolerancia
		        $label_entrada="style='color:blue;font-weight:bold;'";
		      }else{
		        $label_entrada="style='color:green;font-weight:bold;'";
		      }
		    }
		    if($minutos_abandono>0){
		      $label_salida="style='color:red;font-weight:bold;'";
		      $label_abandono="style='color:red;font-weight:bold;'";
		    }else{
		      $label_salida="style='color:green;font-weight:bold;'";
		      $label_extras="style='color:green;font-weight:bold;'";
		    }
		  break;
		  case 2://Dia sin trabajo
		    $sw_edit=false;
		    $marcado1="X";
		    $marcado2="X";
		    $entrada_asig="X";
		    $salida_asig="X";
		    $label_entrada="style='color:blue;font-weight:bold;'";
		    $label_salida="style='color:blue;font-weight:bold;'";
		  break;
		  default:
		  break;
		}
		$sql="SELECT IFNULL(sum(dias_permiso),0)as dias_permiso,IFNULL(sum(minutos_permiso),0)as minutos_permiso from personal_permisos where cod_personal=$cod_personal and cod_area=$cod_area and cod_estado=5 and fecha_inicial <='$fecha' and '$fecha' <=fecha_final";
		// echo $sql."<br>";
		$stmtPermisos = $dbh->prepare($sql);
		$stmtPermisos->execute();
		$stmtPermisos->bindColumn('dias_permiso', $dias_permiso);
		$stmtPermisos->bindColumn('minutos_permiso', $minutos_permiso);
		$minutos_permiso=0;
		$dias_permiso=0;
		while ($rowPermisos = $stmtPermisos->fetch()) {
		  if($dias_permiso>0){//dia faltado con permiso
		    $marcado1="P";
		    $marcado2="P";
		    $label_entrada="style='color:orange;font-weight:bold;'";
		    $label_salida="style='color:orange;font-weight:bold;'";
		  }
		  if($minutos_permiso>0){//minutos de permiso
		    $minutos_permiso.=" Min.";
		  }
		}
		if($marcado2=='00:00:00'){
		  $marcado2="X";
		  $label_salida="style='color:red;font-weight:bold;'";
		}
		$totalAsignados+=$minutos_asignados;
		$totalTrabajados+=$minutos_trabajados;
		$totalAtrasos+=$minutos_atraso;
		// $totalPermisos+=$minutos_permiso;
		$totalExtras+=$minutos_extras;
		$totalAbandono+=$minutos_abandono;                
		$fecha_formato=nombreDia(date('N',strtotime($fecha)))." ".date('d',strtotime($fecha))." ".abrevMes(date('m',strtotime($fecha)));
		$datos_envio=$codigo."#".$fecha."#".$marcado1."#".$marcado2."#".$entrada_asig."#".$salida_asig."#".$fechaInicio."#".$fechaFinal."#".$cod_personal;
		?>
		<tr >
		  <td style="background:#A6B1F7" class="text-left"><b><?=$fecha_formato?></b></td>
		  <td><?=$entrada_asig?></td>
		  <td <?=$label_entrada?>><?=$marcado1?></td>
		  <td><?=$salida_asig?></td>
		  <td <?=$label_salida?>><div class="row"><div class="col-md-8"><?=$marcado2?></div><div class="col-md-1">
		  	<?php
		  	if($sw_edit){ ?>
		  		<button title="Editar Marcado" class="btn btn-success btn-sm" style="padding: 0;font-size:5px;width:18px;height:18px;" type="button" data-toggle="modal" data-target="#modalEditar" onclick="agregardatosModalEditAsistencia('<?=$datos_envio?>')">
		          <i class="material-icons">edit</i>
		        </button>
		        <?php } ?> 
		        </div></div></td>
		  <td><?=$minutos_asignados?></td>
		  <td><?=$minutos_trabajados?></td>
		  <td <?=$label_atraso?>><?=$minutos_atraso?></td>
		  <td><?=$minutos_permiso?></td>
			<?php if($sw_otrosistema==0){ ?>
				<td <?=$label_extras?>><?=$minutos_extras?></td>
			<?php } ?>
		  <td <?=$label_abandono?>><?=$minutos_abandono?></td>
		  <td></td>
		</tr>
	<?php } ?>
	
	<tr style="background:#A6F7C3 !important;color:#000 !important;height:30px;">
	<td><b>- </b></td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td><?=$totalAsignados?></td>
	<td><?=$totalTrabajados?></td>
	<td><?=$totalAtrasos?></td>
	<td><?=$totalPermisos?></td>
	<?php
	if($sw_otrosistema==0){?>
	    <td><?=$totalExtras?></td>
	<?php } 
	?>
	<td><?=$totalAbandono?></td>
	</tr>
	<?php 

}
?>