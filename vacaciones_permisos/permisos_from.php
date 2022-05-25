<?php

require_once 'conexion.php';
require_once 'styles.php';

// $cod_personal=$_GET['cod_personal'];

if(isset($_GET["q"])){
    $q=$_GET['q'];
	$a=$_GET['a'];
	$s=$_GET['s'];
	
	$cod_personal_q=$q;
	$cod_sucursal=$s;
}else{
	$cod_personal_q=$_SESSION['globalUser'];
	$cod_sucursal=$_SESSION['globalArea'];
	$q=0;
	$a=0;
	$s=0;
}



$dbh = new Conexion();
$fecha_actual=date('Y-m-d');
?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="vacaciones_permisos/permisos_save.php" method="post" onsubmit="return valida(this)" enctype="multipart/form-data">
			<input type="hidden" id="cod_personal" name="cod_personal" value="<?=$cod_personal_q?>">
			<input type="hidden" id="cod_sucursal" name="cod_sucursal" value="<?=$cod_sucursal?>">
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
				<center><span style="color:black;font-size: 17px;">Solicitante : <b><?=namePersonalCompleto($cod_personal_q)?></b> - Sucursal : <b><?=nameArea($cod_sucursal)?></span></b></center>
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
							?>
							<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
							<?php
						  	}
						  	?>
						</select>
						</div>
					</div>
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
					  <label class="col-sm-1 col-form-label" style="color:black;">Fin *</label>
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
						  <button  title="Calcular días de permiso" class="btn btn-success btn-sm btn-round btn-fab" onclick="ajaxCalcularDiasPermiso();return false;"> <i class="material-icons" style="color:black">cached</i></button>
						</div>
					  </div>
					  <div class="col-sm-2">
						<div class="form-group" id="div_comentario_permisos_obtenidos">
							<input  type='text' class='form-control' readonly="true" style="background: white;color:green;font-size: 18px;"  value="Total días solicitadas : 0">
							<input  type='hidden' id="dias_solicitadas" name="dias_solicitadas" value="0">
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
        var msg="";
       	var dias_solicitadas=$("#dias_solicitadas").val();
        var dias_disponibles=$("#dias_disponibles").val();
        var motivo=$("#motivo").val();
        dias_solicitadas = !isNaN(dias_solicitadas) ? parseInt(dias_solicitadas, 10) : 0; //si es una cadena vacia o cualquier cosa que no sea numero total = 0
		dias_disponibles = !isNaN(dias_disponibles) ? parseInt(dias_disponibles, 10) : 0; //si es una cadena vacia o cualquier cosa que no sea numero total = 0
        if(dias_solicitadas<=0){
			msg += "El Total de días solicitadas debe ser mayor a 0 (o Presione el Botón 'Calcular días Permiso') \n ";        
			ok = false;
        }

        if(motivo==7){
        	if( parseInt(dias_solicitadas, 10)>parseInt(dias_disponibles, 10)){
				msg += "No tienes disponible "+dias_solicitadas+" días :(";        
				ok = false;
	        }
        }

        
        if(ok == false)    
            Swal.fire("error!",msg, "error");
        return ok;
    }
</script>