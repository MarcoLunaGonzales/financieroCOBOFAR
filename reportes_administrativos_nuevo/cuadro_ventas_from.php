<?php
require_once '../layouts/bodylogin2.php';
require_once '../styles.php';

$desde=date("Y-m", strtotime('-1 month'));
$desde.="-01";
$hasta= date("Y-m-t", strtotime($desde));
?>

<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header  card-header-icon">
          <div class="card-icon">
            <i class="material-icons"><?=$iconCard;?></i>
          </div>
          <h4 class="card-title">CUADRO DE VENTAS</h4>
        </div>
        <form action="cuadro_ventas_print.php" method="POST" target="_blank">
        <div class="card-body">
       
            <div class="row">  
               <label class="col-sm-2 col-form-label">Desde</label>
            <div class="col-sm-4">
              <div class="form-group">
                <input type="date" id="desde" name="desde" value="<?=$desde?>" class="form-control">
              </div>
            </div>

      
              <div class="form-group row col-sm-6">
                  <label class="col-sm-2 col-form-label">Hasta</label>
                   <div class="col-sm-4">
                    <div class="form-group">
                      <input type="date" id="hasta" name="hasta" value="<?=$hasta?>" class="form-control">
                    </div>
                  </div>
              </div>
            </div>
           
        </div><!--card body--> 
        <div class="card-footer">
          <button type="submit" class="btn btn-success">Ver Reporte</button>
          
          </div>
       </form> 
      </div>    
    </div>
  </div>     
</div>
