<?php
require_once 'conexion.php';
require_once 'styles.php';


// $globalAdmin = $_SESSION["globalAdmin"];
$dbh = new Conexion();

if(isset($_GET['q'])){
  if(isset($_GET['s']) && $_GET['s']<>""){
    $q=$_GET['q'];
    $a=$_GET['a'];//indicador de admin o cargo
    $s=$_GET['s'];//cod ciudad
    $cod_personal_q=$q;
    if($a==1 || $a==30 || $a==31){//1 admin 30 apoyo de regencia 31 regente
      $a=1; //indicador de admin
    }else{
      $a=0;
    }
    $admin=$a;;
    $cod_area=$s;//cod area
  }else{
    $cod_personal_q=-100;
    $cod_area=-100;
    $admin=0;
  }
  $cod_area_x=$cod_area;
}else{
  $cod_personal_q=$_SESSION['globalUser'];
  $cod_area_x=obtenerAreasAdmin_permisos($cod_personal_q);//solo para oficina central
  if($cod_area_x<>""){
    $admin=1;//indicador de admin
  }else{
    $admin=0;//indicador de admin
    $cod_area_x=0;
  }
  $cod_area=$_SESSION['globalArea'];//cod area
}
//Personal admin de RRHH
$string_configuracion=obtenerValorConfiguracionPlanillas(34);
$array_personal_respo_audi=explode(",", $string_configuracion);
$sw_personal_admin=false;
for ($i=0; $i <count($array_personal_respo_audi) ; $i++) { 
    if($cod_personal_q==$array_personal_respo_audi[$i]){
        $sw_personal_admin=true;
    }
}
// if($sw_personal_admin){//responsable de RRHH
//   $add_sqlArea=" a.cod_estado=1 and a.centro_costos=1";
//   $sw_personal=true;
// }else{
//   $add_sqlArea=" a.codigo in ($cod_sucursal)";
// }

//contador de permisos pendientes
$sqlPendiente="SELECT count(*)as contador
from personal_permisos pp join estados_permisos_personal epp on pp.cod_estado=epp.codigo join tipos_permisos_personal tpp on pp.cod_tipopermiso=tpp.codigo 
 where pp.cod_estado=3 and cod_area in ($cod_area_x) order by pp.created_at";
 // echo  "<br><br><br>".$sqlPendiente;
$stmtPendiente = $dbh->prepare($sqlPendiente);
$stmtPendiente->execute();
$resultPendintes=$stmtPendiente->fetch();
if(isset($resultPendintes['contador'])){
  $pendientes_aprobacion=$resultPendintes['contador'];
}else{
  $pendientes_aprobacion=0;
}

$sql="SELECT pp.codigo,pp.cod_personal,pp.cod_tipopermiso,tpp.nombre as nombre_tipopermiso,pp.fecha_inicial,pp.hora_inicial,pp.fecha_final,pp.hora_final,pp.observaciones,pp.cod_estado,epp.nombre as nombre_estado,pp.fecha_evento,pp.dias_permiso,pp.minutos_permiso,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal)as nombre_personal,pp.cod_personal_autorizado,pp.observaciones_rechazo,pp.created_at,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal_autorizado)as nombre_personal_autorizado,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal_aprobado)as nombre_personal_aprobado,a.abreviatura as area,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.created_by)as nombre_personal_solicitado
from personal_permisos pp join estados_permisos_personal epp on pp.cod_estado=epp.codigo join tipos_permisos_personal tpp on pp.cod_tipopermiso=tpp.codigo join areas a on pp.cod_area=a.codigo
 where (pp.created_by=$cod_personal_q or pp.cod_personal=$cod_personal_q)   order by pp.created_at desc limit 50";
  // echo "<br><br><br>".$sql;//and cod_area in ($cod_area)
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
// $stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('cod_tipopermiso', $cod_tipopermiso);
$stmt->bindColumn('nombre_tipopermiso', $nombre_tipopermiso);
$stmt->bindColumn('fecha_inicial', $fecha_inicial);
$stmt->bindColumn('hora_inicial', $hora_inicial);
$stmt->bindColumn('fecha_final', $fecha_final);
$stmt->bindColumn('hora_final', $hora_final);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);
$stmt->bindColumn('fecha_evento', $fecha_evento); 
$stmt->bindColumn('dias_permiso', $dias_permiso); 
$stmt->bindColumn('minutos_permiso', $minutos_permiso); 
$stmt->bindColumn('nombre_personal', $nombre_personal); 
$stmt->bindColumn('created_at', $created_at);

$stmt->bindColumn('cod_personal_autorizado', $cod_personal_autorizado); 
$stmt->bindColumn('observaciones_rechazo', $observaciones_rechazo); 
$stmt->bindColumn('nombre_personal_aprobado', $nombre_personal_aprobado); 
$stmt->bindColumn('nombre_personal_autorizado', $nombre_personal_autorizado); 
$stmt->bindColumn('nombre_personal_solicitado', $nombre_personal_solicitado);  

?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-rose card-header-icon">
            <div class="card-icon" style="background: #dc7633;">
              <i class="material-icons"><?= $iconCard; ?></i>
            </div>
            <!-- <h4 class="card-title">Permisos del Personal</h4> -->
            <h3 style="color:#2c3e50;"><b>Mis Permisos</b></h3>
            <!-- <center><b><span style="color:black;font-size: 17px;"><?=namePersonalCompleto($cod_personal_q)?> -  <?=nameArea($cod_area)?></span></b></center> -->
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginator100" class="table table-condensed table-bordered">
                <thead>
                  <tr  class='bg-dark text-white'>
                    <th class="text-left" width="2%">#</th>
                    <th class="text-center" width="5%">Area/Suc.</th>
                    <th class="text-center" width="15%">Personal</th>
                    <th class="text-center" width="15%">Tipo Permiso</th>
                    <th class="text-center" width="5%">Fecha<br>Solicitud</th>
                    <th class="text-center" width="5%">Salida</th>
                    <th class="text-center" width="5%">Retorno</th>
                    <th class="text-center" width="2%">Total Días</th>
                    <th class="text-center" width="2%">Total Min</th>
                    <th class="text-center">Observaciones</th>
                    <th class="text-center"width="10%">Estado</th>
                    <th class="text-center" width="5%">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $index = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      $sw=0;
                      $btn_delete='';
                      $btn_ws='';
                      $estilo="";
                      $label="<span>";
                      $titulo_icono="";
                      $titulo_estado="";
                      switch ($cod_estado) {
                        case 2://anulado
                          $btn_delete='d-none';
                          $btn_ws='d-none';
                          // $estilo="style='background:#e08982;'";
                          $label="<span class='badge badge-danger'>";
                          break;
                        case 1://Registrado
                          //
                          if($sw_personal_admin){// Revision
                            // $cod_estado=3;  
                            $label="<span class='badge badge-default'>";
                            $sw=4;//esta en registrado, enviar a aprobacion
                            $titulo_icono="Enviar a Aprobación";
                          }else{
                            $label="<span class='badge badge-default'>";
                            $sw=3;//esta en registrado, enviar a autorizacion
                            $titulo_icono="Enviar a Autorización";
                          }
                          break;
                        case 3://en revision
                          $btn_ws='d-none';
                          $btn_delete='d-none';
                          $label="<span class='badge badge-warning'>";
                          //$sw=3;//esta en registrado, enviar a autorizacion
                          // $titulo_icono="Enviar a Autorización";
                          break;
                        case 4://autorizado
                          $btn_ws='d-none';
                          $btn_delete='d-none';
                          $label="<span class='badge badge-info'>";
                          //$sw=3;//esta en registrado, enviar a autorizacion
                          // $titulo_icono="Enviar a Autorización";
                          break;
                        case 5://Aprobado
                          $btn_ws='d-none';
                          $btn_delete='d-none';
                          $label="<span class='badge badge-success'>";
                          //$sw=3;//esta en registrado, enviar a autorizacion
                          // $titulo_icono="Enviar a Autorización";
                          break;
                        case 6://Rechazado
                          $btn_ws='d-none';
                          $btn_delete='d-none';
                          $label="<span class='badge badge-danger'>";
                          $estilo="style='background:#e08982;'";
                          $titulo_estado="Rechazado por: ".$nombre_personal_autorizado." ".$nombre_personal_aprobado;
                          $observaciones_rechazo="<span style='color:red;'>MA : ".$observaciones_rechazo."</span>";
                          //$sw=3;//esta en registrado, enviar a autorizacion
                          // $titulo_icono="Enviar a Autorización";
                          break;
                      }
                      ?>
                    <tr <?=$estilo?> >
                      <td class="text-center"><?=$index;?></td>
                      <td class="text-left"><?=$area?></td>
                      <td class="text-left" title="Solcitado por :<?=$nombre_personal_solicitado?>"><?=$nombre_personal?></td>
                      <td class="text-left"><?=$nombre_tipopermiso?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($created_at));?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($fecha_inicial));?></td>
                      <td class="text-center"><?=date('d/m/Y',strtotime($fecha_final));?></td>
                      <td class="text-center"><?=$dias_permiso;?></td>
                      <td class="text-center"><?=$minutos_permiso;?></td>
                      <td class="text-center"><?=$observaciones." ".$observaciones_rechazo;?></td>
                      <td class="text-center" title="<?=$titulo_estado?>"><?=$label.$nombre_estado;?></span></td>
                      <td class="td-actions">
                          <?php
                          if(isset($_GET['q'])){?>
                            <a href='index.php?opcion=permisosPersonalSaveSW&codigo=<?=$codigo?>&sw=<?=$sw?>&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>' rel="tooltip" class="btn btn-info btn-sm <?=$btn_ws?>">
                              <i class="material-icons" style="color:black"  title="<?=$titulo_icono?>">send</i>
                            </a>
                            <a href='index.php?opcion=permisosPersonalSaveDelete&codigo=<?=$codigo?>&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>' rel="tooltip" class="btn btn-danger btn-sm <?=$btn_delete?>">
                              <i class="material-icons" style="color:black" title="Anular Solicitud de Permiso">delete</i>
                            </a>
                          <?php }else{?>
                            <a href='index.php?opcion=permisosPersonalSaveSW&codigo=<?=$codigo?>&sw=<?=$sw?>' rel="tooltip" class="btn btn-info btn-sm <?=$btn_ws?>">
                              <i class="material-icons" style="color:black"  title="<?=$titulo_icono?>">send</i>
                            </a>
                            <a href='index.php?opcion=permisosPersonalSaveDelete&codigo=<?=$codigo?>' rel="tooltip" class="btn btn-danger btn-sm <?=$btn_delete?>">
                              <i class="material-icons" style="color:black" title="Anular Solicitud de Permiso">delete</i>
                            </a>
                          <?php }
                          ?>
                      </td>
                    </tr>
                  <?php
                    $index++;
                   }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
          <div class="card-footer fixed-bottom">
            <?php if(isset($_GET['q'])){ ?>
              <button class="btn btn-success btn-sm" onClick="location.href='index.php?opcion=permisosPersonalForm&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>'">Nuevo Permiso</button>
              <?php 
              if($admin==1){?>
                <button class="btn btn-primary btn-sm" onClick="location.href='index.php?opcion=permisosPersonalListaADM&q=<?=$q?>&a=<?=$a?>&s=<?=$s?>'" style="background: #dc7633;">Autorización de Permisos <span class="count bg-warning" style="width:20px;height: 20px;font-size: 12px;" ><b><?=$pendientes_aprobacion?></b></span></button>
              <?php } ?>
            <?php }else{?>
              <button class="btn btn-success btn-sm" onClick="location.href='index.php?opcion=permisosPersonalForm'">Nuevo Permiso</button>
              <?php 
              if($admin==1){?>
                <button class="btn btn-default btn-sm" onClick="location.href='index.php?opcion=permisosPersonalListaADM'" style="background: #dc7633;">Autorización de Permisos <span class="count bg-warning" style="width:20px;height: 20px;font-size: 12px;" ><b><?=$pendientes_aprobacion?></b></span></button>
              <?php } ?>

            <?php }?>

            <!-- <button class="btn btn-info btn-sm" onClick="location.href='index.php?opcion=permisosPersonalListaADMrrhh'">Nuevo Permiso Terceros</button> -->

          </div>
      </div>
    </div>
  </div>
</div>