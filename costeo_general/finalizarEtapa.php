<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';
require_once '../conexion_comercial2.php'; 

$dbh = new Conexion();


$desde=$_GET['desde'];
$hasta=$_GET['hasta'];

$anio=date("Y",strtotime($desde));
$mes=date("m",strtotime($desde));

$anioAnterior=$anio;
$mesAnterior=$mes-1;
if($mes==1){
  $anioAnterior=$anio-1;
  $mesAnterior=12;
}

?>

<div class="content">
	<div class="container-fluid">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title">Proceso Costeo Sucursales - Finalizar Etapa</h4>
            </div>
            <div class="card-body">
            	<?php 

              $sql="INSERT INTO costoscobofar.costo_promedio_mes (cod_material,costo,cod_mes,cod_gestion,cod_almacen,saldo)
SELECT cod_material,costo,$mes as cod_mes,$anio as cod_gestion,cod_almacen,saldo FROM costoscobofar.costo_promedio_mes where cod_mes=$mesAnterior and cod_gestion=$anioAnterior
and CONCAT(cod_material,'M',cod_almacen) not in (SELECT CONCAT(cod_material,'M',cod_almacen) FROM costoscobofar.costo_promedio_mes where cod_mes=$mes and cod_gestion=$anio);";
  //echo $sql;
              mysqli_query($enlaceCon,$sql);
		        ?>

		        <p>ETAPA FINALIZADA!</p>
            </div>
            <div class="card-footer">
            	<a href="rpt_costeo_pendientes_from.php" class="btn btn-danger">Volver al Formulario</a>
						</div>					
	        
          </div>	  
    </div>         
	</div>
</div>

