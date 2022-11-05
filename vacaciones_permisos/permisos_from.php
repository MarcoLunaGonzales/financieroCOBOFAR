<?php

require_once 'conexion.php';
require_once 'styles.php';

// $cod_personal=$_GET['cod_personal'];

$sw_personal=false;
if(isset($_GET["q"])){
    $q=$_GET['q'];
	$a=$_GET['a'];
	$s=$_GET['s'];
	$cod_personal_q=$q;
	$cod_sucursal=$s;
	$add_sqlArea=" and a.codigo in ($cod_sucursal)";
	$cod_uo=0;
}else{
	$cod_personal_q=$_SESSION['globalUser'];
	$cod_sucursal=$_SESSION['globalArea'];
	$q=0;
	$a=0;
	$s=0;

	// $codigo_personalRRHH=8;
	$string_configuracion=obtenerValorConfiguracionPlanillas(34);
	$array_personal_respo_audi=explode(",", $string_configuracion);
	$sw_personal_admin=false;
	for ($i=0; $i <count($array_personal_respo_audi) ; $i++) { 
	    if($cod_personal_q==$array_personal_respo_audi[$i]){
	        $sw_personal_admin=true;
	    }
	}


	if($sw_personal_admin){//responsable de RRHH
		$add_sqlArea=" ";
		$sw_personal=true;
	}else{
		$add_sqlArea=" and a.codigo in ($cod_sucursal)";
	}

	$cod_uo=$_SESSION['globalUnidad'];
}

$dbh = new Conexion();
$fecha_actual=date('Y-m-d');
?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="vacaciones_permisos/permisos_save.php" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">			
		  	<input type="hidden" id="q" name="q" value="<?=$q?>">
		  	<input type="hidden" id="a" name="a" value="<?=$a?>">
		  	<input type="hidden" id="s" name="s" value="<?=$s?>">
			<div class="card">
			  	<div class="card-header card-header-rose card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Solicitud de Permisos</h4>
					</div>				
			  </div>
			  <div class="card-body ">
				<div class="row">
					<label class="col-sm-2 col-form-label" style="color:black;">Sucursal *</label>
					<div class="col-sm-4">
						<div class="form-group">
						<select class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true" name="cod_sucursal" id="cod_sucursal" required="true" onChange="ajaxPesonalAreaPermisos(this);">
							
						  	<?php
						  		if($sw_personal){
						  			echo '<option value="">SELECCIONE UN ITEM</option>';
						  		}
						  		//$sqlArea="SELECT a.codigo,a.nombre,a.abreviatura from areas a where  $add_sqlArea order by a.nombre";
						  		$sqlArea="SELECT a.codigo,a.nombre,a.abreviatura from personal p join areas a on p.cod_area=a.codigo
						  		where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 $add_sqlArea
						  		GROUP BY a.codigo order by a.nombre";
							  $stmtArea = $dbh->prepare($sqlArea);
							$stmtArea->execute();
							while ($rowArea = $stmtArea->fetch(PDO::FETCH_ASSOC)) {
								$codigoA=$rowArea['codigo'];
								$nombreA=$rowArea['nombre'];
								$abreviaturaP=$rowArea['abreviatura'];
							?>
							<option  value="<?=$codigoA;?>" data-subtext="<?=$abreviaturaP?>"><?=$nombreA;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
					</div>
					<label class="col-sm-1 col-form-label" style="color:black;">Personal *</label>
					<div class="col-sm-4">
						<div class="form-group" id="contenedor_personal">
						<select class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true" name="cod_personal" id="cod_personal" required="true">
							<!-- <option value="">SELECCIONE UN ITEM</option> -->
						  	<?php
						  	$sqlPersonal="SELECT p.codigo,p.paterno,p.materno,p.primer_nombre,a.nombre as area,p.turno
								from personal p join areas a on p.cod_area=a.codigo
								where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 and p.codigo in ($cod_personal_q)
								order by p.paterno";
							  $stmt = $dbh->prepare($sqlPersonal);
							$stmt->execute();
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$codigoP=$row['codigo'];
								$paternoP=$row['paterno'];
								$maternoP=$row['materno'];
								$primer_nombreP=$row['primer_nombre'];
								$areaP=$row['area'];
								$turnoP=$row['turno'];
								if($turnoP==1){
									$areaP.=$areaP." TM";
								}elseif($turnoP==2){
									$areaP.=$areaP." TT";
								}
							?>
							<option value="<?=$codigoP;?>" data-subtext="<?=$areaP?>"><?=$paternoP;?> <?=$maternoP;?> <?=$primer_nombreP;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
					</div>
				</div> 

				<div class="row">
					<label class="col-sm-2 col-form-label" style="color:black;">Motivo *</label>
					<div class="col-sm-4">
						<div class="form-group">
						<select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" name="motivo" id="motivo" data-style="<?=$comboColor;?>" required="true" onChange="ajaxGlosaMotivoPermiso(this);">
							<option value="">SELECCIONE UN ITEM</option>	
						  	<?php
							  $stmt = $dbh->prepare("SELECT codigo,nombre FROM tipos_permisos_personal where cod_estadoreferencial=1 order by nombre");
							$stmt->execute();
							while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								$codigoX=$row['codigo'];
								$nombreX=$row['nombre'];

								if($codigoX==14){//solo visible para of Central
									if($cod_uo==1){?>
										<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	<?php	
									}
									?>

								<?php }else{
									?>
									<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
									<?php
								}

						  	}
						  	?>
						</select>
						</div>
					</div>
					<label class="col-sm-1 col-form-label" style="color:black;"></label>
					  <div class="col-sm-4" >
					  	<div class="form-group" id="div_comentario_permisos">
							<input  type='hidden' id="dias_disponibles" name="dias_disponibles" value="0">
						</div>
					</div>
				</div> 
				<div id="div_rangofechas_permisos">
					<div class="row" >
					  <label class="col-sm-2 col-form-label" style="color:black;">Inicio *</label>
					  <div class="col-sm-2">
						<div class="form-group">
						  <input  type='date' class='form-control'  id='fecha_inicio' name='fecha_inicio' value="<?=$fecha_actual?>" required>
						</div>
					  </div>
					  <div class="col-sm-1">
						<div class="form-group">
						  <input  type='time' class='form-control'  id='hora_inicio' name='hora_inicio' value="06:00" required>
						</div>
					  </div>
					  <label class="col-sm-2 col-form-label" style="color:black;">Fin *</label>
					  <div class="col-sm-2">
						<div class="form-group">
						  <input  type='date' class='form-control'  id='fecha_final' name='fecha_final' value="<?=$fecha_actual?>" required>
						</div>
					  </div>
					  <div class="col-sm-1">
						<div class="form-group">
						  <input  type='time' class='form-control'  id='hora_final' name='hora_final' value="06:00"  required >
						</div>
					  </div>
					  <div class="col-sm-1">
						<div class="form-group">
							<a href="#" title="Calcular días de permiso" class="btn btn-success btn-sm btn-round btn-fab" onclick="ajaxCalcularDiasPermiso();return false;"> <i class="material-icons" style="color:black">cached</i></a>
						</div>
					  </div>
					</div>
					<div class="row">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
						<div class="form-group" id="div_comentario_permisos_obtenidos">
							<input  type='text' class='form-control' readonly="true" style="background: white;color:green;font-size: 18px;"  value="***---***">
							<input  type='hidden' id="dias_solicitadas" name="dias_solicitadas" value="0">
							<input  type='hidden' id="minutos_solicitados" name="minutos_solicitados" value="0">
						</div>
					  </div>
					</div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label" style="color:black;">Observaciones</label>
				  <div class="col-sm-8">
						<div class="form-group">
						  <input  type='text' class='form-control'  id='observaciones' name='observaciones'>
						</div>
				  </div>
				</div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="btn btn-success">Guardar</button>
				<?php if(!isset($_GET['q'])){?>
					<a href="index.php?opcion=permisosPersonalLista" class="<?=$buttonCancel;?>">Volver</a>
				<?php }else{?>
					<a href="index.php?opcion=permisosPersonalLista&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>" class="<?=$buttonCancel;?>">Volver</a>
				<?php }
				?>
				
			  </div>
			</div>
		  </form>
		</div>
	</div>
</div>


<script type="text/javascript">
    function valida(f) {
       	var ok = true;
       	// ok=false;
        var msg="";
       	var dias_solicitadas=$("#dias_solicitadas").val();
        var dias_disponibles=$("#dias_disponibles").val();
        var minutos_solicitados=$("#minutos_solicitados").val();
        var motivo=$("#motivo").val();
        dias_solicitadas = !isNaN(dias_solicitadas) ? parseInt(dias_solicitadas, 10) : 0; //si es una cadena vacia o cualquier cosa que no sea numero total = 0
		minutos_solicitados = !isNaN(minutos_solicitados) ? parseInt(minutos_solicitados, 10) : 0; //si es una cadena vacia o cualquier cosa que no sea numero total = 0
		// alert(minutos_solicitados.' - '.dias_solicitadas);
        if(dias_solicitadas<=0 && minutos_solicitados<=0){
			msg += "Días solicitadas debe ser mayor a 0 (Presione Botón 'Calcular días Permiso'). \n ";        
			ok = false;
        }

        if(parseInt(motivo, 10)==7){
        	if(parseInt(dias_solicitadas, 10)>parseInt(dias_disponibles, 10)){
				msg += "Días solicitados NO disponible ( "+dias_solicitadas+" días) :(";        
				ok = false;
	        }
        }

        // ok=false;
        if(ok == false)    
            Swal.fire("error!",msg, "error");
        return ok;
    }
</script>