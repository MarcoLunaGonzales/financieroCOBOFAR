<section class="after-loop">
  <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('assets/img/conta.jpg'); background-size: cover;  height: 100px; " >
    <div class="container">
      <div class="div-center text-center">
        <img src="assets/img/farmacias_bolivia1.gif" width="460" height="90" alt="">
        <h3><b><FONT FACE="courier">Bienvenid@! <?=$_SESSION['globalNameUser'];?></FONT></b></h3>
      </div>
      <div class="row">
         <?php if($perfil==1 or $perfil==2){?>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="modulo.php?codigo=2" class="after-loop-item card border-0 card-snippets shadow-lg">
            <div class="card-body d-flex align-items-end flex-column text-right">
               <h4>Activos Fijos</h4>
               <p class="w-85 text-small">Activos Fijos, Depreciación y su Contabilización.</p>
               <i class="material-icons">home_work</i>
            </div>
         </a>
        </div> 
         <?php } ?> 
        <?php if($perfil==1 or $perfil==3){?>
          <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="modulo.php?codigo=1" class="after-loop-item card border-0 card-themes shadow-lg">
            <div class="card-body d-flex align-items-end flex-column text-right">
               <h4>RRHH (Remuneración)</h4>
               <p class="w-85">Gestión de personal & Gestión de planillas & Contabilización.</p>
               <i class="material-icons">local_atm</i>
            </div>
         </a>
        </div>
         <?php } ?>
           
        <?php if($perfil==1 or $perfil==4){?>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="modulo.php?codigo=3" class="after-loop-item card border-0 card-templates shadow-lg">
            <div class="card-body d-flex align-items-end flex-column text-right">
               <h4>Contabilidad</h4>
               <p class="w-85">Modulo Base de Contabilidad & Caja Chica.</p>
               <i class="material-icons">insert_chart_outlined</i>
            </div>
         </a>
        </div>
         <?php } ?>
        <?php if($perfil==1 or $perfil==5){?>
        <div class="col-lg-3 col-md-8 mb-5 mb-lg-0 mx-auto">
         <a href="modulo.php?codigo=4" class="after-loop-item card border-0 card-guides shadow-lg">
            <div class="card-body d-flex align-items-end flex-column text-right">
               <h4> Egresos </h4>
               <p class="w-75"> Sol. de Recursos & Contabilización.</p>
               <i class="material-icons">list_alt</i>
            </div>
         </a>
        </div>
        <?php } ?>
           
        
      </div>
    </div>
  </div>
</section>
