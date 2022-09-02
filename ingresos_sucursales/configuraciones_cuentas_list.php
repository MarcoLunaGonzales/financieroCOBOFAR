<?php

require_once 'styles.php';
require_once 'functions.php';
require("conexion_comercial_oficial.php");

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION["globalUnidad"];

$string_configuracion=obtenerValorConfiguracion(116);
$array_personal_respo_audi=explode(",", $string_configuracion);
$sw_personal_audi=false;
for ($i=0; $i <count($array_personal_respo_audi) ; $i++) { 
    if($globalUser==$array_personal_respo_audi[$i]){
        $sw_personal_audi=true;
    }
}

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

              <div class="card">
                <div class="card-header card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Configuraciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-sm table-striped table-hover table-secondary ">
                      <thead class="fondo-boton">
                        <tr>
                          <th align="center">Sucursal</th>
                          <th >Cuenta Bancaria </th>
                          <th >Cuenta Bancaria Secundaria</th>
                          <th >-</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      $sql="SELECT c.cod_ciudad,c.descripcion,c.cod_plancuenta,(select descripcion from cuentas_bancarias where cod_plancuenta=c.cod_plancuenta)as banco,(select descripcion from cuentas_bancarias where cod_plancuenta=c.cod_plancuenta2)as banco2
                        from ciudades c 
                        where c.cod_estadoreferencial=1 and c.cod_ciudad>0
                        order by c.descripcion";
                        // echo $sql; 
                        $resp=mysqli_query($dbh,$sql);
                        while($row=mysqli_fetch_array($resp)){ 
                          $cod_ciudad=$row['cod_ciudad'];
                          $descripcion=$row['descripcion'];
                          $cod_plancuenta=$row['cod_plancuenta'];
                          $cod_plancuenta2=$row['cod_plancuenta2'];
                          $banco=$row['banco'];
                          $banco2=$row['banco2'];
                          $datos=$cod_ciudad."/".$descripcion."/".$banco."/".$banco2;
                          ?>
                          <tr>
                            <td class="text-left"><?=$descripcion?></td>
                            <td class="text-left"><?=$banco?></td>
                            <td class="text-left"><?=$banco2?></td>
                            <td>
                              <?php if($sw_personal_audi){ ?>
                              <button title="Editar Ingreso" class="btn btn-success btn-sm" style="padding: 0;font-size:5px;width:18px;height:18px;" type="button" data-toggle="modal" data-target="#modalEditar" onclick="agregardatosModal_configuracionComercial('<?=$datos;?>')">
                                <i class="material-icons">edit</i>
                              </button>
                              <?php }?>
                            </td>
                          </tr>
                        <?php } ?>
                        
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>


<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background:#2e4053;color:white;"><b>Editar Cuenta Bancaria M/N</b></h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_ciudad" id="cod_ciudad">
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Sucursal : </label>
          <div class="col-sm-3">
            <div class="form-group" >
              <input type="text" class="form-control"  name="nombre" id="nombre" readonly="true" style="background-color:white;color:blue;size: 20px;">
            </div>
          </div>
        </div>           
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Cuenta Actual: </label>
          <div class="col-sm-4">
            <div class="form-group" >
              <input type="text" class="form-control"  name="nombre_cuenta" id="nombre_cuenta" readonly="true" style="background-color:white;color:blue;size: 20px;">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Cuenta Actual Secundaria: </label>
          <div class="col-sm-4">
            <div class="form-group" >
              <input type="text" class="form-control"  name="nombre_cuenta2" id="nombre_cuenta2" readonly="true" style="background-color:white;color:blue;size: 20px;">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 col-form-label text-dark  text-right">Cuenta  Nueva</label>
          <div class="col-sm-7">
            <div class="form-group">
              <select name="cod_cuentaBancaria" id="cod_cuentaBancaria" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                <option value=""></option>
                <?php 
                $queryEdit = "SELECT cod_plancuenta,descripcion,moneda from cuentas_bancarias where estado=1 and cod_moneda=1 order by descripcion";
                $respEdit=mysqli_query($dbh,$queryEdit);
                while($rowEdit=mysqli_fetch_array($respEdit)){  ?>
                    <option  value="<?=$rowEdit["cod_plancuenta"];?>" data-subtext="(<?=$rowEdit['moneda']?>)"><?=$rowEdit["descripcion"];?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 col-form-label text-dark  text-right">Cuenta Secundaria Nueva</label>
          <div class="col-sm-7">
            <div class="form-group">
              <select name="cod_cuentaBancaria2" id="cod_cuentaBancaria2" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                <option value=""></option>
                <?php 
                $queryEdit2 = "SELECT cod_plancuenta,descripcion,moneda from cuentas_bancarias where estado=1 and cod_moneda=1 order by descripcion";
                $respEdit2=mysqli_query($dbh,$queryEdit2);
                while($rowEdit2=mysqli_fetch_array($respEdit2)){  ?>
                    <option  value="<?=$rowEdit2["cod_plancuenta"];?>" data-subtext="(<?=$rowEdit2['moneda']?>)"><?=$rowEdit2["descripcion"];?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" id="guardar_edit_plantilla" name="guardar_edit_plantilla" data-dismiss="modal">Confirmar Actualización</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#guardar_edit_plantilla').click(function(){
      
      var cod_ciudad=document.getElementById("cod_ciudad").value;
      var cod_cuentaBancaria=$('#cod_cuentaBancaria').val();

      var cod_cuentaBancaria2=$('#cod_cuentaBancaria2').val();
      if(cod_cuentaBancaria2==''){
        cod_cuentaBancaria2=0;
      }
      if(cod_ciudad=='' || cod_cuentaBancaria=='' ){
        Swal.fire("Informativo!", "No se permiten Campos Vacíos", "warning");
       }else{        
        guardar_edit_configuracionComercial(cod_ciudad,cod_cuentaBancaria,cod_cuentaBancaria2);
       }      
    });    
  });
</script>