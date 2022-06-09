<html>
<head>
	<meta charset="utf-8" />
	<title>Farmacias Bolivia</title>
</head>
<?php
$estilosVenta=1;

$fecha_reporte=date("d/m/Y");
$txt_reporte="Fecha de Reporte <strong>$fecha_reporte</strong>";
$sql_tipo_ingreso="select nombre_tipoingreso from tipos_ingreso where cod_tipoingreso='1003'";
$resp_tipo_ingreso=mysqli_query($dbh,$sql_tipo_ingreso);
$datos_tipo_ingreso=mysqli_fetch_array($resp_tipo_ingreso);
$nombre_tipoingreso=$datos_tipo_ingreso[0];

if($periodo_ingreso==1){
	$titulo_perido="24 Hrs.";
}else if($periodo_ingreso==2){
	$titulo_perido="48 Hrs.";
}else{
	$titulo_perido="> 2 días";
}
if($tipo==3){
	$filtro=2;
}if($tipo==2){
	$filtro=1;
}


	echo "<h3>Verificación de Tiempos en Traspasos $titulo_perido</h3>
	<h3>$nombre_tipoingresomostrar Fecha inicio: <strong>$fecha_ini</strong> Fecha final: <strong>$fecha_fin</strong><br>$txt_reporte</h3>";

	//desde esta parte viene el reporte en si
	$fecha_iniconsulta=$fechai;	
	$fecha_finconsulta=$fechaf;


	$sql="SELECT l.*,(select a1.nombre_almacen from almacenes a1 where a1.cod_almacen=l.cod_almacen) as nom_destino,(select a2.nombre_almacen from almacenes a2 where a2.cod_almacen=l.origen)as nom_origen 

	FROM ((select i.cod_ingreso_almacen, CONCAT(i.fecha,' ',i.hora_ingreso) as fecha_ingreso, ti.nombre_tipoingreso, i.observaciones, i.nota_entrega, i.nro_correlativo, i.ingreso_anulado,(SELECT CONCAT(fecha,' ',hora_salida) FROM salida_almacenes where cod_salida_almacenes=i.cod_salida_almacen) as fecha_salida,0 as central,(SELECT IFNULL(cod_almacen,0) FROM salida_almacenes where cod_salida_almacenes=i.cod_salida_almacen and CONCAT(fecha,' ',hora_salida)>=DATE_SUB(CONCAT(i.fecha,' ',i.hora_ingreso), INTERVAL $periodo_ingreso DAY)) as atiempo,i.cod_almacen,(SELECT IFNULL(cod_almacen,0) FROM salida_almacenes where cod_salida_almacenes=i.cod_salida_almacen) as origen,(SELECT cod_salida_almacenes FROM salida_almacenes where cod_salida_almacenes=i.cod_salida_almacen) as cod_salida_almacenes
		FROM ingreso_almacenes i, tipos_ingreso ti
		where i.cod_tipoingreso=ti.cod_tipoingreso and i.cod_almacen in (select a.cod_almacen  from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgStringDestino)) and i.fecha>='$fecha_iniconsulta'
		and i.fecha<='$fecha_finconsulta' and i.ingreso_anulado=0 and (i.cod_salida_almacen_central=0 or i.cod_salida_almacen_central is null)
		HAVING origen in (select a.cod_almacen  from almacenes a, ciudades c where a.cod_ciudad=c.cod_ciudad and a.cod_tipoalmacen=1 and c.cod_area in ($sucursalgStringOrigen))
		order by i.nro_correlativo)
		) l  where origen>0 order by l.fecha_ingreso desc;";

	//echo $sql;
	$resp=mysqli_query($dbh,$sql);
	echo "<p>Los Traspasos Ingresados fuera de las $titulo_perido Están marcados con <b style='color:#FF5733'>color rojo</b><p>";
	echo "<center><br>
	<table class='table table-condensed table-bordered' width='100%'>";
	echo "<tr class='textomini'><th>Nro.</th><th>Origen</th><th>Destino</th><th>Fecha Salida</th><th>Fecha Ingreso</th><th>Tiempo Transcurrido</th><th>Tipo de Ingreso</th><th>Observaciones</th><th>Actions</th></tr>";
	$index=0;
	while($dat=mysqli_fetch_array($resp))
	{
		$index++;
		$codigo=$dat[0];//ingreso
		$fecha_ingreso=$dat[1];
		$fecha_ingreso_mostrar=strftime('%d-%m-%Y %H:%M',strtotime($dat[1]));
		$fecha_salida_mostrar=strftime('%d-%m-%Y %H:%M',strtotime($dat[7]));
		$nombre_tipoingreso=$dat[2];
		$obs_ingreso=$dat[3];
		$nota_entrega=$dat[4];
		$nro_correlativo=$dat[5];
		$anulado=$dat[6];
		echo "<input type='hidden' name='fecha_ingreso$nro_correlativo' value='$fecha_ingreso_mostrar'>";

		$bandera=0;

		$cod_salida_almacenes=$dat[12];
    $detalle_ingreso=$dat[14];		
		$detalle_destino=$dat[13];		
		
		$date1 = new DateTime($dat[7]);
    $date2 = new DateTime($dat[1]);
    $diff = $date1->diff($date2); 
    $diasTexto="Días"; 
    if((int)$diff->format('%a')==1){
      $diasTexto="Día"; 
    }
    if((int)$diff->format('%h')>0){
      $diasTexto.=" con %h Hrs."; 
    }       
    $hora_rest=$diff->format('%a '.$diasTexto.'');
    $color_fondo="#000";

		if((int)$diff->format('%a')>=$periodo_ingreso){
			$color_fondo="#FF5733";
		}
		$estiloMostrarFila="";

		if($filtro==1){
	    if((int)$diff->format('%a')<$periodo_ingreso){
		    $estiloMostrarFila="d-none";
		  }
    }
    
    $rpt_linea==1;

   if($dat[11]>0){
		if($rpt_linea==0)
		{	echo "<tr style='color:$color_fondo' class='$estiloMostrarFila'><td align='center'>$index</td><td align='center'>$detalle_ingreso</td><td align='center'>$detalle_destino</td><td align='center'>$fecha_salida_mostrar</td><td align='center'>$fecha_ingreso_mostrar</td><td align='center'>$hora_rest</td><td>$nombre_tipoingreso</td><td>&nbsp;$obs_ingreso</td><td class='td-actions text-right'>"; if($sw_excel==1){ echo "<a href='#' rel='tooltip' class='btn btn-warning' onclick='abrir_detalle_modal($cod_salida_almacenes,0);return false;'><i class='material-icons' title='Ver Detalle'>list</i></a>";} echo "</td></tr>";
		}
		if($rpt_linea!=0 and $bandera==1)
		{	echo "<tr style='color:$color_fondo' class='$estiloMostrarFila'><td align='center'>$nro_correlativo</td><td align='center'>$detalle_ingreso</td><td align='center'>$detalle_destino</td><td align='center'>$fecha_salida_mostrar</td><td align='center'>$fecha_ingreso_mostrar</td><td align='center'>$hora_rest</td><td>$nombre_tipoingreso</td><td>&nbsp;$obs_ingreso</td><td><td></tr>";
		}   	
   }     
	}
	echo "</table></center><br>";
?>