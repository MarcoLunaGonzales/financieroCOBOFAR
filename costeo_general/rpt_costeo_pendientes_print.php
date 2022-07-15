<?php //ESTADO FINALIZADO
$sw_excel=1;
require_once '../styles.php';

if($sw_excel==1){
  require_once '../layouts/bodylogin2.php';
}

require_once '../conexion_comercial2.php'; 
require_once '../functions.php';
require_once '../functionsGeneral.php';


$fechaDesde=$_GET['fecha_desde'];
$fechaDesdeTitulo= explode("-",$fechaDesde);
$desde=$fechaDesdeTitulo[2].'/'.$fechaDesdeTitulo[1].'/'.$fechaDesdeTitulo[0];
$fechahasta=$_GET['fecha_hasta'];
$fechahastaTitulo= explode("-",$fechahasta);
$hasta=$fechahastaTitulo[2].'/'.$fechahastaTitulo[1].'/'.$fechahastaTitulo[0];
$fechaTitulo="De ".$desde." a ".$hasta;


$existe=0;
$sql="SELECT count(*) as conteo from costoscobofar.costo_promedio_mes where cod_gestion=YEAR('$fechaDesde') and cod_mes=MONTH('$fechaDesde');";
$resp=mysqli_query($enlaceCon,$sql);

while($dat=mysqli_fetch_array($resp)){ 
   $existe=$dat[0];
}

$estiloMostrarExiste="d-none";
$estiloMostrar="";
if($existe>0){
   $estiloMostrar="d-nonde"; 
   $estiloMostrarExiste="d-none";
 ?>
 <?php 
}

?>

<div id="cargando_datos" class="<?=$estiloMostrar?>">
<div  style="z-index: 9999;position: fixed;width: 100%;top:0;background: rgba(255, 255, 255);height: 100vh;color:#0A7576;padding: 50px;">
  <center>    
  <img src="../assets/img/clientes.jpg" width="200px">
  <br><br>  
  <p style='font-size: 30px;'><b>PROCESANDO COSTOS</b></p>
  <p style='font-size: 15px;'>Este proceso puede demorar unas horas. Espere por favor...</p>
  <img src='../assets/img/farmacias_bolivia_loop.gif' width='400px' heigth='100px' style='position:fixed;left:40px;bottom:20px;z-index: 9998;'>
  </center>
</div>
</div>

<?php 

?>
<div class="content" id="mensaje_ok" class="<?=$estiloMostrar?>">
  <div class="container-fluid">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title">Proceso Costeo Sucursales</h4>
            </div>
            <div class="card-body">
              <div class="row"><i class="material-icons">check</i> <p>El proceso de costeo a finalizado: <b id="tiempo_costeo"></b> Tiempo Duración</p>                
              </div>
           </div>
            <div class="card-footer">
              <a href="rpt_costeo_pendientes_from.php" class="btn btn-success text-white">Regresar al formulario</a>
      </div>
          </div>    
    </div>         
  </div>
</div>

<div class="content" id="mensaje_existen" class="<?=$estiloMostrarExiste?>">
  <div class="container-fluid">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title">Proceso Costeo Sucursales</h4>
            </div>
            <div class="card-body">
              <div class="row"><i class="material-icons">check</i> <p>El proceso de costeo a finalizado: <b id="tiempo_costeo"></b> Tiempo Duración</p>                
              </div>
           </div>
            <div class="card-footer">
              <a href="rpt_costeo_pendientes_from.php" class="btn btn-success text-white">Regresar al formulario</a>
            </div>
          </div>    
    </div>         
  </div>
</div>





<script type="text/javascript">  
  // var parametros={"fecha_desde":"<?=$fechaDesde?>","fecha_hasta":"<?=$fechahasta?>"};
  // $.ajax({
  //       type: "GET",
  //       dataType: 'html',
  //       url: "costeo_general.php",
  //       data: parametros,
  //       success:  function (resp) {
  //         var r = resp.split("#####");
  //         $("#tiempo_costeo").html(r[1]);
  //         $("#cargando_datos").addClass("d-none");     
  //       }
  // });
</script>




