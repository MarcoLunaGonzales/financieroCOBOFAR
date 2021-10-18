<?php
switch ($codModulo) {
  case 1:
   $nombreModulo="RRHH";
   $cardTema="card-themes";
   $iconoTitulo="local_atm";
   $estiloHome="#DC5143";
   $fondoModulo="fondo-dashboard-recursoshumanos";
  break;
  case 2:
   $nombreModulo="Activos Fijos";
   $cardTema="card-snippets";
   $iconoTitulo="home_work";
   $estiloHome="#DCB943";
   $fondoModulo="fondo-dashboard-activos";
  break;
  case 3:
   $nombreModulo="Contabilidad";
   $cardTema="card-templates";
   $iconoTitulo="insert_chart_outlined";
   $estiloHome="#1B82DD";
   $fondoModulo="fondo-dashboard-contabilidad";
  break;
  case 4:
   $nombreModulo="Presupuestos / Solicitudes";
   $cardTema="card-guides";
   $iconoTitulo="list_alt";
   $estiloHome="#4FA54F";
   $fondoModulo="fondo-dashboard-solicitudes";
  break;
}
if($codModulo!=3){?>
<section class="after-loop">
  <div class="container">
    <div class="div-center text-center">
      
     
     <img src="assets/img/farmacias_bolivia_loop.gif" width="500" height="150" alt="">
      
       <h3><b><FONT FACE="courier">Modulo <?=$nombreModulo?></FONT></b></h3>
      <p>
        <a href="index.php" class="btn btn-lg" style="background-color: #00ae9b; ">IR A LA PAGINA DE INICIO</a>
      </p>     
    </div>
  </div>
</section>
<?php }else{?>


<style>
  .centrarimagen
  {
    /*filter:brightness(0.8);*/
    position: absolute;
    top:-20px;
    width:100%;
    /*margin-left:-280px;*/
    height:100vh;
    /*margin-top:-185px;*/
    margin:0px;
    height:100%;
    background-image: url("assets/img/login_farmacias.jpg");
    background-repeat: repeat-y !important;
    background-size: cover;
  }
  .fondo_comu
  {
    background-image: url("imagenes/sf.jpg");
    background-size:     cover;
      background-repeat:   no-repeat;
      background-position: center center;              /* optional, center the image */
  }
  #alpha {
  background-color: rgba(0, 0, 0, 0.6);
  width: 150px;
  /*position: absolute;*/
  top: 10px;
  color: #fff;
  padding-top: 1em;
}
</style>
<?php
// echo "<br><br><br><br><br><br>";
require_once 'conexion.php';
$dbh = new Conexion();
  echo "<div class='centrarimagen'>";
  $sql='SELECT DATE_FORMAT(c.created_at,"%d-%m-%Y")as fecha,DATE_FORMAT(c.fecha,"%m")as mes,(select tc.abreviatura from tipos_comprobante tc where tc.codigo=c.cod_tipocomprobante)as tipo_comprobante,c.numero,c.glosa,c.created_at,(select CONCAT_WS(" ",p.primer_nombre,p.paterno) from personal p where p.codigo=c.created_by)as personal
  from comprobantes c 
  where c.cod_estadocomprobante<>2 and c.salvado_temporal=1';
$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
$stmt2->bindColumn('fecha', $fecha);
$stmt2->bindColumn('mes', $mes);
$stmt2->bindColumn('tipo_comprobante', $tipo_comprobante);
$stmt2->bindColumn('numero', $numero);
$stmt2->bindColumn('glosa', $glosa);
$stmt2->bindColumn('created_at', $created_at);
$stmt2->bindColumn('personal', $personal);
$comunicado="";?>
<div class="card" style="width: 90%;left: 5%;top:80px;z-index: 9999;" id="alpha">
  <div class="card-header card-header card-header-text " >
    <div class="card-text" style="background-color:#C70039 !important;color:#fff;">
      <div class="rounded mr-2 float-left" style="height: 16px;width: 16px;background-color: orange;"></div>
      <strong class="mr-auto">COBOFAR - FINANCIERO</strong>                        
    </div>
    <hr> 
  </div>                
  <div class="card-body" >
    <div class="row">
      <div class="col-sm-12">
        <!-- <center><h4><b>IMPORTANTE</b></h4></center>    -->     
        <center><h4><b> ¡Comprobantes Salvados Temporalmente!</b></h4></center>
        
        <div class="table-responsive">
          <table class="table table-condensed" style='overflow-y: scroll;display: block;height:300px;'>
            <thead>
              <tr>
                <th class="text-center"></th>
              </tr>
            </thead>
            <tbody>
              <?php $index=1;
              while ($row2 = $stmt2->fetch(PDO::FETCH_BOUND)) {
              // $date = date("d-m-Y");
              //Incrementando 2 dias
                $mod_date = strtotime($fecha."+ 2 days");
                $created_at_permitido=date("Y-m-d",$mod_date);
                $fecha_actual=date("Y-m-d");
                if($fecha_actual>$created_at_permitido){
                  $estilo_fecha="class='badge badge-danger'";
                }else{
                  $estilo_fecha=" ";
                }
                
                $comunicado="Comprobante: <span style='color:yellow;'><b>".$tipo_comprobante.$mes."-".$numero."</span></b><br>Creado Por: <span style='color:#fad972;'>".$personal."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> En Fecha: <span style='color:#fad972;' ".$estilo_fecha." >".$created_at."</span><br> Glosa: <span style='color: yellow;'>".$glosa."</span><br>";
                ?>
                <tr>
                     <td class="text-left"><?=$comunicado?></td>
                </tr>
              <?php  } ?>
            </tbody>
          </table>
        </div>
        <small class="float-right">© Cobofar 2021.</small>
      </div>
    </div>
  </div>
</div>



<?php }
?>


