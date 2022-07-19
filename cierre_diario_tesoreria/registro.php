<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();


$tipo=$_GET['t'];

if($tipo==1){
	$imagenTitulo='<img src="assets/img/in.png" width="70">';
	$tituloFormulario=' ENTRADA';
}else{
	$imagenTitulo='<img src="assets/img/out.png" width="70">';
	$tituloFormulario=' SALIDA';
}

//<img src="assets/img/in.png" width="40">

?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<form action="<?=$urlSave?>" method="POST">
			<div class="card">
			  <div class="card-header card-header-info card-header-text">
				<div class="card-text">
				  <h4 class="">NUEVA <?=$tituloFormulario?></h4>
				</div>
			  </div>
			  <div class="card-body ">	
			  <center><?=$imagenTitulo?></center>
			  <hr>
			  	<input type="hidden" name="tipo_cierre" id="tipo_cierre" value="<?=$tipo?>">			  				 
			  	<div class="row">
				  <label class="col-sm-2 col-form-label text-dark font-weight-bold">COMPROBANTE</label>
				  <div class="col-sm-3">
					<div class="form-group">
					  <input class="form-control" placeholder="Ejemplo T07-450" type="text" name="comprobante" id="comprobante" onkeypress="verificarComprobanteExiste();" onkeyup="verificarComprobanteExiste();" onblur="verificarComprobanteExiste();" required>
					</div>
				  </div>
				  <input type="hidden" name="cod_comprobante" id="cod_comprobante">
				  <div class="col-sm-6" id="mensaje_comprobante">
				  	
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label text-dark font-weight-bold">TOKEN</label>
				  <div class="col-sm-3">
					<div class="form-group">
					  <input class="form-control" type="text" name="token" id="token">
					</div>
				  </div>
				  <label class="col-sm-2 col-form-label text-dark font-weight-bold">NRO CHEQUE</label>
				  <div class="col-sm-4">
					<div class="form-group">
					  <input class="form-control" type="text" name="nro_cheque" id="nro_cheque">
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label text-dark font-weight-bold">GLOSA</label>
				  <div class="col-sm-9">
					<div class="form-group">
					  <input class="form-control" type="text" name="glosa" id="glosa" required>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label text-dark font-weight-bold">IMPORTE</label>
				  <div class="col-sm-3">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" name="importe" id="importe" required>
					</div>
				  </div>
				</div>
				<br><br>


				
			  </div>			  
			  <div class="card-footer fixed-bottom ml-auto mr-auto">
				<button class="btn btn-primary" type="submit">guardar <?=$tituloFormulario?></button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>"><span class="material-icons">keyboard_return</span> Volver</a>
			  </div>			  
			</div>
			</form>
		</div>
	
	</div>
</div>

<script type="text/javascript">
$("#comprobante").mask("A00-000000");
function verificarComprobanteExiste(){
	var parametros={"codigo":$("#comprobante").val()}; 
    $.ajax({
    url: "cierre_diario_tesoreria/ajaxBuscarComprobante.php",
    dataType: "html",
    data: parametros,
    type: "GET",
    success: function(resp){
       var r=resp.split("#####");  
       if(parseInt(r[1])>0){
       	$("#cod_comprobante").val(r[1]);
       	$("#mensaje_comprobante").html("<p class='text-success'><i class='material-icons'>check</i>&nbsp;</p>");       
       }else{
       	$("#cod_comprobante").val(0);
       	$("#mensaje_comprobante").html("<p class='text-danger'><i class='material-icons'>clear</i>&nbsp;</p>");       	 
       }   
    }
   });


}
</script>