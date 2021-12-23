<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_uo = $_GET["codigo_uo"];//
	

	if($cod_uo==-100){		
		$sql="SELECT cod_uo from personal_area_distribucion where cod_estadoreferencial=1 and cod_uo<>0 and cod_uo<>'' GROUP BY cod_uo";
        // echo $sql;
    $stmtUO=$dbh->prepare($sql);
		$stmtUO->execute();

		$string_cod_uo="";
		while ($row = $stmtUO->fetch()) 
		{			
			$string_cod_uo.=$row['cod_uo'].",";
		}
		$cod_uo=trim($string_cod_uo,",");
	}



	$sqlGestion="SELECT nombre from gestiones where codigo=$cod_gestion";
	$stmtGestion=$dbh->prepare($sqlGestion);
	$stmtGestion->execute();
	$resultGestion=$stmtGestion->fetch();
	$nombre_gestion=$resultGestion['nombre'];
	
	$stmtArea = $dbh->prepare("SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area
	 from personal_area_distribucion
  where cod_estadoreferencial=1 and cod_uo in ($cod_uo)
  GROUP BY cod_area order by cod_uo,nombre_area");
  $stmtArea->execute();
  $stmtArea->bindColumn('cod_area', $cod_area_x);
  $stmtArea->bindColumn('nombre_area', $nombre_area_x);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">            
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="widtd:100%; max-width:50px;">
                Planilla De Aguinaldo
            </h4>                  
            <h6 class="card-title"><small>
              Codigo Planilla: <?=$cod_planilla;?><br>
              Gestion: <?=$nombre_gestion;?><br>              
              </small>                    
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
				<table class="table table-bordered table-condensed table-hover" id="200">
                	<thead>
		                <tr class="bg-dark text-white">                  
	                    <th><small>#</small></th> 
	                    <th><small>Area</small></th>                   
	                    <th><small>CI</small></th>
	                    <th><small>Ex.</small></th>
	                    <th><small>Fecha Nac.</small></th>
	                    <th><small>Paterno</small></th>
	                    <th><small>Materno</small></th>
	                    <th><small>Nombres</small></th>                    
	                    <th><small>Fecha Ingreso</small></th>	
	                    <th><small>Cargo</small></th>
	                    <th class="bg-success text-white"><small>Septiembre</small></th>
	                    <th class="bg-success text-white"><small>Octubre</small></th>
	                    <th class="bg-success text-white"><small>Noviembre</small></th>
	                    <th class="bg-success text-white"><small>Sumatoria</small></th>
	                    <th class="bg-success text-white"><small>Promedio Tot Gan</small></th>
	                    <th><small>Meses Trabajados</small></th>
	                    <th class="bg-primary text-white"><small>Total ganado</small></th>                    
		                </tr>                                  
	                </thead>
	                <tbody>
						<?php 
						$index=1;
						$sum_total_sueldo1=0;
            $sum_total_sueldo2=0;
            $sum_total_sueldo3=0;
            $sum_total_promedio_tp=0;
            $sum_total_aguinaldo_tp=0;
            $sum_ssumatoria_ganado=0;

						while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) 
						{
							$sql = "SELECT ppm.cod_personal,ppm.sueldo_1,ppm.sueldo_2,ppm.sueldo_3,ppm.meses_trabajados,ppm.dias_trabajados,pad.porcentaje, ppm.total_aguinaldo, p.primer_nombre as personal, p.paterno,p.materno, p.identificacion as doc_id, (select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as lug_emision,p.ing_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,ppm.dias_360,p.fecha_nacimiento,ppm.sumatoria_ganado,ppm.promedio_ganado
							from planillas_aguinaldos_detalle ppm join personal_area_distribucion pad on ppm.cod_personal=pad.cod_personal and pad.cod_estadoreferencial=1 join personal p on ppm.cod_personal=p.codigo
							where cod_planilla=$cod_planilla and pad.cod_uo in ($cod_uo) and pad.cod_area=$cod_area_x order by p.turno,p.paterno";
								// echo $sql;
							$stmtPersonal 	= $dbh->prepare($sql);
							$stmtPersonal->execute();	

							$stmtPersonal->bindColumn('cod_personal', $cod_personalcargo);
							$stmtPersonal->bindColumn('sueldo_1', $sueldo_1);
							$stmtPersonal->bindColumn('sueldo_2', $sueldo_2);
							$stmtPersonal->bindColumn('sueldo_3', $sueldo_3);
							$stmtPersonal->bindColumn('meses_trabajados', $meses_trabajados);
							$stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
							$stmtPersonal->bindColumn('total_aguinaldo', $total_aguinaldo);
							$stmtPersonal->bindColumn('porcentaje', $porcentaje);
							$stmtPersonal->bindColumn('personal', $personal);
							$stmtPersonal->bindColumn('paterno', $paterno);
							$stmtPersonal->bindColumn('materno', $materno);
							$stmtPersonal->bindColumn('doc_id', $doc_id);
							$stmtPersonal->bindColumn('lug_emision', $lug_emision);
							$stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
							$stmtPersonal->bindColumn('cargo', $cargo);
							$stmtPersonal->bindColumn('dias_360', $dias_360);
							$stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
							$stmtPersonal->bindColumn('sumatoria_ganado', $sumatoria_ganado);
							$stmtPersonal->bindColumn('promedio_ganado', $promedio_ganado);
							while ($row = $stmtPersonal->fetch()) 
							{  
                //dividiendo montos a su porcentaje respectivo
                $sueldo_1_tp=$sueldo_1*$porcentaje/100;
                $sueldo_2_tp=$sueldo_2*$porcentaje/100;
                $sueldo_3_tp=$sueldo_3*$porcentaje/100;
                //$sumatoria_ganado=$sueldo_1_tp+$sueldo_2_tp+$sueldo_3_tp;
                //$promedio_ganado=$sumatoria_ganado/3;
        //         $dias_sueldo=$promedio_ganado/360*$dias_trabajados;
								// $meses_sueldo=$promedio_ganado/12*$meses_trabajados;
                $total_aguinaldo_tp=$total_aguinaldo*$porcentaje/100;
                $sum_total_sueldo1+=$sueldo_1_tp;
                $sum_total_sueldo2+=$sueldo_2_tp;
                $sum_total_sueldo3+=$sueldo_3_tp;
                $sum_ssumatoria_ganado+=$sumatoria_ganado;
                $sum_total_promedio_tp+=$promedio_ganado;
                $sum_total_aguinaldo_tp+=$total_aguinaldo_tp;
                ?>
              	<tr>                                                        
                  <td class="text-center small"><?=$index;?></td>
                  <td class="text-left small"><?=$nombre_area_x;?></td>                    
                  <td class="text-center small"><?=$doc_id;?></td>
                  <td class="text-center small"><?=$lug_emision?></td>
                  <td class="small"><?=strftime('%d/%m/%Y',strtotime($fecha_nacimiento))?></td>
                  <td class="text-left small"><?=$paterno;?></td>
                  <td class="text-left small"><?=$materno;?></td>
                  <td class="text-left small"><?=$personal;?></td>
                  <td class="small"><?=strftime('%d/%m/%Y',strtotime($ing_planilla))?></td>
                  <td class="text-left small"><?=$cargo?></td>
                  <td class="small" ><?=formatNumberDec($sueldo_1_tp);?></td>
                  <td class="small" ><?=formatNumberDec($sueldo_2_tp);?></td>
                  <td class="small" ><?=formatNumberDec($sueldo_3_tp);?></td>
									<td class="small"><?=formatNumberDec($sumatoria_ganado);?></td> 
                  <td class="small"><?=formatNumberDec($promedio_ganado);?></td> 
                  <td  class="text-right small"><?=formatNumberDec($dias_360)?></td>
                  <td class="bg-primary text-white small"><?=formatNumberDec($total_aguinaldo_tp);?></td>
                </tr> 
              	<?php 
                $index+=1;
          		}
						}

						$dbh=null;
						$stmtPersonal=null;
						$stmtArea=null;
						$stmtGestion=null;
						$stmtUO=null;
						?>                      
	                </tbody>
	                <tfoot>
	                    <tr class="bg-dark text-white">                  
	                    <th colspan="10" class="text-center small">Total</th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_sueldo1);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_sueldo2);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_sueldo3);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_ssumatoria_ganado);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_promedio_tp);?></th>
	                    <th class="text-center small">-</th>
	                    <th class="bg-primary text-white small"><?=formatNumberDec($sum_total_aguinaldo_tp);?></th>
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

