<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../functions.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_mes = $_GET["cod_mes"];//
	$cod_uo = $_GET["codigo_uo"];//
	
	$nombre_gestion=nameGestion($cod_gestion);
	if($cod_uo==-100){		
		$sql="SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo from personal_area_distribucion where cod_estadoreferencial=1 and cod_uo<>0 and cod_uo<>'' GROUP BY cod_uo";
        // echo $sql;
        $stmtUO=$dbh->prepare($sql);
		$stmtUO->execute();
		$nombre_uo="";
		$string_cod_uo="";
		while ($row = $stmtUO->fetch()) 
		{			
			$nombre_uo.=$row['nombre_uo'].",";
			$string_cod_uo.=$row['cod_uo'].",";
		}
		$nombre_uo=trim($nombre_uo,",");
		$cod_uo=trim($string_cod_uo,",");
	}else{
		$nombre_uo=nameUnidad($cod_uo);
	}

?>

<style>
	  table ,tr td{
    border:1px solid red
}
tbody {
    display:block;
    height:500px;
    overflow:auto;
}
thead, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
}
thead {
    width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
}
tfoot, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
}
tfoot {
    width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
}
table {
    width:2000px !important;
}
</style>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">            
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="widtd:50%; max-width:50px;">
                Planilla De Sueldos
            </h4>                  
            <h6 class="card-title"><small>
              Codigo Planilla: <?=$cod_planilla;?><br>
              Gestion: <?=$nombre_gestion; ?> / Mes: <?=$cod_mes; ?><br>              
              Oficina: <?=$nombre_uo;?>
              </small>                    
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
							<table width="2000px !important" class="table table-condensed table-bordered table-sm table-striped mb-0" id="tablePaginatorHeaderFooter123">
                <thead>
                	<tr class="bg-dark text-white">                  
                    <th width="1%"><small>#</small></th> 
                    <th width="3%"><small>Area</small></th>                   
                    <th width="4%"><small>CI EXT</small></th>
                    <th><small>Paterno</small></th>
                    <th><small>Materno</small></th>
                    <th><small>Nombres</small></th>
                    <!-- <th ><small>Portje</small></th> -->
                    <th width="2%"><small>Días Trab</small></th>
                    <th><small>Haber Básico</small></th>
                    <th><small>Bono<br>Ant</small></th>
                    <th class="bg-success text-white"><a id="botonBonos" style="border:none;" class="bg-success text-white small">+Bon</a> </th>
                    <?php
                    	$swBonosOtro=false;
                      	$sqlBonos = "SELECT cod_bono,(select b.abreviatura from bonos b where b.codigo=cod_bono) as nombre_bono
                              from bonos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)
                              order by cod_bono ASC";
                        // echo $sqlBonos;
												$stmtBonos = $dbh->prepare($sqlBonos);
												$stmtBonos->execute();                      
												$stmtBonos->bindColumn('cod_bono',$cod_bono);
												$stmtBonos->bindColumn('nombre_bono',$nombre_bono);
												while ($row = $stmtBonos->fetch()) 
												{ ?>
													<th class="bonosDet bg-success text-white" style="display:none"><small><?=$nombre_bono;?></small></th>           
													<?php
													$arrayBonos[] = $cod_bono;
													$swBonosOtro=true;
												}
                    ?>
                    <th><small>Mont Bonos</small></th>                            
                    <th style="background:#aeb6bf;"><small>Tot Gan</small></th>
                    <th style="background:#e59866;"><a id="botonAportes" style="border:none;background:#e59866;color:white;"  class="small">+Aport</a></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>AFP.F</small></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>AFP.P</small></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>A.Sol(13)</small></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>A.Sol(25)</small></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>A.Sol(35)</small></th>
                    <th class="aportesDet" style="display:none;background:#e59866;"><small>RC-IVA</small></th>
                    <!-- <th><small>Atrasos</small></th> -->
                    <th><small>Antic</small></th>
                    <!-- <th><small>Dotac</small></th> -->
                    <th style="background:#d98880;"><a id="botonOtrosDescuentos" style="border:none;background:#d98880;" class="small">+Desc</a> </th>
                    <?php  
                      $swDescuentoOtro=false;                  
                      $sqlDescuento = "SELECT cod_descuento,(select d.abreviatura from descuentos d where d.codigo=cod_descuento) as nombre_descuentos
                              from descuentos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)
                              order by cod_descuento ASC";
                      $stmtDescuento = $dbh->prepare($sqlDescuento);
                      $stmtDescuento->execute();                      
                      $stmtDescuento->bindColumn('cod_descuento',$cod_descuento);
                      $stmtDescuento->bindColumn('nombre_descuentos',$nombre_descuentos);
                      while ($row = $stmtDescuento->fetch()) 
                      { ?>
                        <th class="DescuentosOtros" style="display:none;background:#d98880;"><small><?=$nombre_descuentos;?></small></th>
                        <?php
                        $arrayDescuentos[] = $cod_descuento;
                        $swDescuentoOtro=true;
                      }
                    ?>
                    <th><small>Monto Desc</small></th>     
                    <th style="background:#5d6d7e;" class="text-white"><small>Liqu Pag</small></th>                    
                    <th><small>Seg De Sal</small></th>
                    <th><small>Ries Prof</small></th>
                    <th><small>Proviv</small></th>
                    <th><small>Apo Patr Sol</small></th>
                    <th><small>Tot Apo Pat</small></th>
                	</tr>                                  
	              </thead>
	              <tbody>
									<?php 
									$index=1;
									$sum_total_basico=0;
									$sum_total_b_antiguedad=0;
									$sum_total_o_bonos=0;
									$sum_total_m_bonos=0;
									$sum_total_t_ganado=0;
									$sum_total_m_aportes=0;
									$sum_total_atrasos=0;
									$sum_total_anticipos=0;
									$sum_total_dotaciones=0;
									$sum_total_o_descuentos=0;
									$sum_total_m_descuentos=0;
									$sum_total_l_pagable=0;
									$sum_total_a_patronal=0;
									//$dias_trabajados_asistencia=30;//ver datos
										// $dias_trabajados_por_defecto = obtenerValorConfiguracionPlanillas(22); //por defecto
									$dias_trabajados_por_defecto=30; 
									
										$sql = "SELECT ppm.cod_personalcargo,ppm.cod_gradoacademico,ppm.dias_trabajados,ppm.horas_pagadas,ppm.haber_basico,
												ppm.bono_academico,ppm.bono_antiguedad,ppm.monto_bonos,ppm.total_ganado,ppm.monto_descuentos,
												ppm.liquido_pagable,ppm.afp_1,ppm.afp_2,ppm.dotaciones,pad.porcentaje,ppm.descuentos_otros,ppm.bonos_otros,

												pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.rc_iva,pp.anticipo,pp.seguro_de_salud,pp.riesgo_profesional,pp.provivienda,pp.a_patronal_sol,pp.total_a_patronal,

											(SELECT ga.nombre from personal_grado_academico ga where ga.codigo=ppm.cod_gradoacademico) as grado_academico,
									        p.primer_nombre,p.paterno,p.materno,p.identificacion,
									        (select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as lug_emision,
									  		p.lugar_emision_otro,pad.cod_uo,ppm.cod_area,a.abreviatura as nombre_area
											FROM personal p
									    join planillas_personal_mes ppm on ppm.cod_personalcargo=p.codigo
									      join planillas_personal_mes_patronal pp on pp.cod_planilla=ppm.cod_planilla and pp.cod_personal_cargo=ppm.cod_personalcargo
									    join areas a on ppm.cod_area=a.codigo
									    join personal_area_distribucion pad on ppm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1
											where ppm.cod_planilla=$cod_planilla and pad.cod_uo in($cod_uo)  
											order by ppm.correlativo_planilla";
											// echo $sql;
										$stmtPersonal = $dbh->prepare($sql);
										$stmtPersonal->execute();	
										$stmtPersonal->bindColumn('cod_personalcargo', $cod_personalcargo);
										$stmtPersonal->bindColumn('primer_nombre', $nombrePersonal);
										$stmtPersonal->bindColumn('paterno', $paterno);
										$stmtPersonal->bindColumn('materno', $materno);
										$stmtPersonal->bindColumn('identificacion', $doc_id);
										$stmtPersonal->bindColumn('lug_emision', $lug_emision);
										$stmtPersonal->bindColumn('lugar_emision_otro', $lug_emision_otro);
										$stmtPersonal->bindColumn('cod_gradoacademico', $cod_gradoacademico);
										$stmtPersonal->bindColumn('grado_academico', $grado_academico);
										$stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados_asistencia);
										$stmtPersonal->bindColumn('horas_pagadas', $horas_pagadas);
										$stmtPersonal->bindColumn('haber_basico', $haber_basico);
										$stmtPersonal->bindColumn('bono_academico', $bono_academico);
										$stmtPersonal->bindColumn('bono_antiguedad', $bono_antiguedad);
										$stmtPersonal->bindColumn('monto_bonos', $monto_bonos);
										$stmtPersonal->bindColumn('total_ganado', $total_ganado);
										$stmtPersonal->bindColumn('monto_descuentos', $monto_descuentos);
										$stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
										$stmtPersonal->bindColumn('afp_1', $afp_1);
										$stmtPersonal->bindColumn('afp_2', $afp_2);
										$stmtPersonal->bindColumn('dotaciones', $dotaciones);
										$stmtPersonal->bindColumn('porcentaje', $porcentaje);
										$stmtPersonal->bindColumn('cod_uo', $cod_uo_xy);
										$stmtPersonal->bindColumn('cod_area', $cod_area_xy);
										$stmtPersonal->bindColumn('nombre_area', $nombreAreaxy);
										$stmtPersonal->bindColumn('a_solidario_13000', $a_solidario_13000);
										$stmtPersonal->bindColumn('a_solidario_25000', $a_solidario_25000);
										$stmtPersonal->bindColumn('a_solidario_35000', $a_solidario_35000);
										$stmtPersonal->bindColumn('rc_iva', $rc_iva);
										$stmtPersonal->bindColumn('anticipo', $anticipo);
										$stmtPersonal->bindColumn('seguro_de_salud', $seguro_de_salud);
										$stmtPersonal->bindColumn('riesgo_profesional', $riesgo_profesional);

										$stmtPersonal->bindColumn('provivienda', $provivienda);
										$stmtPersonal->bindColumn('a_patronal_sol', $a_patronal_sol);
										$stmtPersonal->bindColumn('total_a_patronal', $total_a_patronal);

										$stmtPersonal->bindColumn('descuentos_otros', $descuentos_otros);
										$stmtPersonal->bindColumn('bonos_otros', $bonos_otros);
										
	


										while ($row = $stmtPersonal->fetch()) 
										{  
	
			                  
			                  //dividiendo montos a su porcentaje respectivo
			                  $haber_basico_tp=$haber_basico*$porcentaje/100;
			                  $bono_antiguedad_tp=$bono_antiguedad*$porcentaje/100;
			                  $monto_bonos_tp=$monto_bonos*$porcentaje/100;
			                  $total_ganado_tp=$total_ganado*$porcentaje/100;
			                  // $atrasos_tp=$atrasos*$porcentaje/100;
			                  $anticipo_tp=$anticipo*$porcentaje/100;
			                  $monto_descuentos_tp=$monto_descuentos*$porcentaje/100;
			                  // $dotaciones_tp=$dotaciones*$porcentaje/100;
			                  $seguro_de_salud_tp=$seguro_de_salud*$porcentaje/100;
			                  $riesgo_profesional_tp=$riesgo_profesional*$porcentaje/100;
			                  $provivienda_tp=$provivienda*$porcentaje/100;
			                  $a_patronal_sol_tp=$a_patronal_sol*$porcentaje/100;

			                  $liquido_pagable_tp=$liquido_pagable*$porcentaje/100;
			                  $total_a_patronal_tp=$total_a_patronal*$porcentaje/100;
			                  $sum_total_basico+=$haber_basico_tp;
			                  $sum_total_b_antiguedad+=$bono_antiguedad_tp;
			                  $sum_total_m_bonos+=$monto_bonos_tp;
			                  $sum_total_t_ganado+=$total_ganado_tp;                          
			                  // $sum_total_atrasos+=$atrasos_tp;
			                  $sum_total_anticipos+=$anticipo_tp;
			                  $sum_total_m_descuentos+=$monto_descuentos_tp;
			                  // $sum_total_dotaciones+=$dotaciones_tp;
			                  $sum_total_l_pagable+=$liquido_pagable_tp;
			                  $sum_total_a_patronal+=$total_a_patronal_tp;


			                ?>
			              	<tr >                                                        
			                    <td class="text-center small" width="1%"><small><?=$index;?></small></td>
			                    <td class="text-left small" width="3%"><small><?=$nombreAreaxy;?></small></td>
			                    <td class="text-center small" width="4%"><small><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></small></td>
			                    <td class="text-left small"><small><?=$paterno;?></small></td>
			                    <td class="text-left small"><small><?=$materno;?></small></td>
			                    <td class="text-left small"><small><?=$nombrePersonal;?></small></td>
			                    <td class="text-center small" width="2%"><small><?=$dias_trabajados_asistencia;?></small></td>   
			                    <td class="text-center small"><small><?=formatNumberDec($haber_basico_tp);?></small></td>
			                    <td class="text-center small"><small><?=formatNumberDec($bono_antiguedad_tp);?></small></td>
			                    <?php
			                    if($swBonosOtro)
			                    {


			                    		$bonos_otros_x=$bonos_otros*$porcentaje/100;
														  $sum_total_o_bonos+=$bonos_otros_x;
			                      ?> 
			                      <td class="text-center small bg-success text-white"><small><?=formatNumberDec($bonos_otros_x);?></small></td>
			                      <?php
			                      set_time_limit(300);
		                      	for ($j=0; $j <count($arrayBonos);$j++){ 
		                          $cod_bono_aux=$arrayBonos[$j];                          
		                          $sqlBonosOtrs = "SELECT bpm.cod_bono,bpm.monto,b.cod_tipocalculobono
		                                from bonos_personal_mes bpm,bonos b 
		                                where   bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and  bpm.cod_bono=$cod_bono_aux and bpm.cod_estadoreferencial=1";
		                          $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
		                          $stmtBonosOtrs->execute();
		                          $resultBonosOtros=$stmtBonosOtrs->fetch();
		                          $cod_bonosX=$resultBonosOtros['cod_bono'];
		                          $montoX=$resultBonosOtros['monto'];
		                          $tipoBonoX=$resultBonosOtros['cod_tipocalculobono'];
		                          if($tipoBonoX==2){
		                          	// $porcen_monto=30*100/$dias_trabajados_asistencia;
		                          	$porcen_monto=$dias_trabajados_asistencia*100/$dias_trabajados_por_defecto;
								    						$montoX_aux=$porcen_monto*$montoX/100;
		                          }else $montoX_aux=$montoX;
		                          $montoX_tp=$montoX_aux*$porcentaje/100;
		                          if($cod_bonosX==$cod_bono_aux){ ?>
		                            <td  class="bonosDet small" style="display:none"><small><?=formatNumberDec($montoX_tp);?></small></td>
		                          <?php                            
		                          }else{ $montoAux=0; ?>                                                          
		                            <td  class="bonosDet small" style="display:none"><small><?=formatNumberDec($montoAux);?></small></td>
		                          <?php                            
		                          }
		                      	}
			                    }else{
			                      $sumabonos_otros=0;
			                      ?>
			                      <td class="small"><small><?=formatNumberDec($sumabonos_otros);?></small></td>
			                      <?php
			                    }
			                  	$afp_1_tp=$afp_1*$porcentaje/100;
			                  	$afp_2_tp=$afp_2*$porcentaje/100;
			                  	$a_solidario_13000_tp=$a_solidario_13000*$porcentaje/100;
			                  	$a_solidario_25000_tp=$a_solidario_25000*$porcentaje/100;
			                  	$a_solidario_35000_tp=$a_solidario_35000*$porcentaje/100;
			                  	$rc_iva_tp=$rc_iva*$porcentaje/100;
			                  	$monto_aportes_tp = $afp_1_tp+$afp_2_tp+$a_solidario_13000_tp+$a_solidario_25000_tp+$a_solidario_35000_tp+$rc_iva_tp;
			                  	$sum_total_m_aportes+=$monto_aportes_tp;
			                    ?>  
			                    <td class="small"><small><?=formatNumberDec($monto_bonos_tp);?></small></td>
			                    <td class="small" style="background:#aeb6bf;"><small><?=formatNumberDec($total_ganado_tp);?></small></td>
			                    <td style="background:#e59866;" class="small"><small><?=formatNumberDec($monto_aportes_tp);?></small></td> 
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($afp_1_tp);?></small></td>
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($afp_2_tp);?></small></td>
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($a_solidario_13000_tp);?></small></td>
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($a_solidario_25000_tp);?></small></td>
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($a_solidario_35000_tp);?></small></td>
			                    <td class="aportesDet small" style="display:none"><small><?=formatNumberDec($rc_iva_tp);?></small></td>
			                    <td class="small"><small><?=formatNumberDec($anticipo_tp);?></small></td>
			                    <!-- <td class="small"><small></small></td> formatNumberDec($dotaciones_tp)-->
			                    <?php
			                    if($swDescuentoOtro)
			                    {
		

			                      $sumaDescuentos_otros_tp=$descuentos_otros*$porcentaje/100;
			                      $sum_total_o_descuentos+=$sumaDescuentos_otros_tp;
			                      ?> 
			                      <td style="background:#d98880;" class="small text-center"><small><?=formatNumberDec($sumaDescuentos_otros_tp);?></small></td>
			                      <?php
			                        for ($j=0; $j <count($arrayDescuentos); $j++) { 
			                          $cod_descuento_aux=$arrayDescuentos[$j];                          
			                          $sqlDescuentos = "SELECT cod_descuento,monto
			                                from descuentos_personal_mes 
			                                where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and  cod_descuento=$cod_descuento_aux and cod_estadoreferencial=1";
			                          $stmtDescuentos = $dbh->prepare($sqlDescuentos);
			                          $stmtDescuentos->execute();
			                          $resultDescOtros=$stmtDescuentos->fetch();
			                          $cod_descuentosX=$resultDescOtros['cod_descuento'];
			                          $montoX=$resultDescOtros['monto'];
			                          $montoX_tp=$montoX*$porcentaje/100;

			                          if($cod_descuentosX==$cod_descuento_aux){ ?>
			                            <td  class="DescuentosOtros small" style="display:none"><small><?=formatNumberDec($montoX_tp);?></small></td>
			                          <?php                            
			                          }else{ $montoAux=0; ?>                                                          
			                            <td  class="DescuentosOtros small" style="display:none"><small><?=formatNumberDec($montoAux);?></small></td>
			                          <?php                            
			                          }
			                        }  	                     
			                        

			                    }else{
			                      $sumaDescuentos_otros_tp=0;
			                      ?>
			                      <td class="small"><small><?=formatNumberDec($sumaDescuentos_otros_tp);?></small></td>
			                      <?php
			                      // $monto_descuentosX_tp=$monto_descuentos_tp+$sumaDescuentos_otros_tp;
			                    }
			                    ?>
			                                          
			                    <td class="text-center small"><small><?=formatNumberDec($monto_descuentos_tp);?></small></td>
			                    <td class="small text-white" style="background:#5d6d7e;"><small><?=formatNumberDec($liquido_pagable_tp);?></small></td>
			                    <td  class="text-center small"><small><?=formatNumberDec($seguro_de_salud_tp);?></small></td>
			                    <td class="text-center small"><small><?=formatNumberDec($riesgo_profesional_tp);?></small></td>
			                    <td class="text-center small"><small><?=formatNumberDec($provivienda_tp);?></small></td>
			                    <td class="text-center small"><small><?=formatNumberDec($a_patronal_sol_tp);?></small></td>
			                    <td class="text-center small"><small><?=formatNumberDec($total_a_patronal_tp);?></small></td>
			                </tr> 
			                	<?php 
			                  $index+=1;
			            	}
									?>                      
	              </tbody>
	              <tfoot>
	                <tr class="bg-dark text-white">                  
	                <th class="text-center small" width="1%"><small></small></th>
	                <th class="text-center small" width="3%"><small></small></th>
	                <th class="text-center small" width="4%"><small></small></th>
	                <th class="text-center small" colspan="3"><small>Total</small></th>
	                <th class="text-center small" width="2%"><small>-</small></th>
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_basico);?></small></th>
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_b_antiguedad);?></small></th>
	                <th class="bg-success text-white small bg-success text-white"><small><?=formatNumberDec($sum_total_o_bonos);?> </small></th>
	                <?php
	                	
	                	for ($i=0; $i <count($arrayBonos) ; $i++) { ?>
	                    <th class="bonosDet bg-success text-white small" style="display:none"><small>-</small></th><?php                        
	                	}  
	                ?>
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_m_bonos);?></small></th>                            
	                <th class="text-center small" style="background:#aeb6bf;"><small><?=formatNumberDec($sum_total_t_ganado);?></small></th>
	                <th style="background:#e59866;" class="small"><small><?=formatNumberDec($sum_total_m_aportes);?></small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                <th class="aportesDet small" style="display:none;background:#e59866;"><small>-</small></th>
	                
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_anticipos);?></small></th>
	                <!-- <th class="text-center small"><small></small></th> formatNumberDec($sum_total_dotaciones) -->
	                <th style="background:#d98880;" class="small"><small><?=formatNumberDec($sum_total_o_descuentos);?></small></th>
	                <?php  
	                  for ($i=0; $i <count($arrayDescuentos) ; $i++) { ?>
	                    <th class="DescuentosOtros small" style="display:none;background:#d98880;"><small>-</small></th><?php                        
	                	}
	                ?>
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_m_descuentos);?></small></th>                                        
	                <th class="small text-white" style="background:#5d6d7e;"><small><?=formatNumberDec($sum_total_l_pagable);?></small></th>                    
	                <th class="text-center small"><small>-</small></th>
	                <th class="text-center small"><small>-</small></th>
	                <th class="text-center small"><small>-</small></th>
	                <th class="text-center small"><small>-</small></th>
	                <th class="text-center small"><small><?=formatNumberDec($sum_total_a_patronal);?></small></th>
	              </tr>
	            </tfoot>               
             	</table>                                
            </div>                 
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

<script type="text/javascript">
  
  $("#botonBonos").on("click", function(){
    $(".bonosDet").toggle();

  });
  $("#botonDescuetos").on("click", function(){
    $(".descuentosDet").toggle();
  });

  $("#botonAportes").on("click", function(){
    $(".aportesDet").toggle();
  });

  $("#botonOtrosDescuentos").on("click", function(){
    $(".DescuentosOtros").toggle();
  });
</script>