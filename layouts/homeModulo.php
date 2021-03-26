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

?>
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