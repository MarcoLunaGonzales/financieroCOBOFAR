<?php

// error_reporting(E_ALL);
// ini_set('display_errors', '1');
session_start();
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';

$perfilGlobal=$_SESSION['globalPerfil'];
// echo $perfilGlobal;
if($perfilGlobal==2 || $perfilGlobal==13 || $perfilGlobal==1){//perfil de Jefe de RRHH,asistente y admin
  $perfil_sw=true;
  $estilo_perfil="";
}else{
  $perfil_sw=false;
  $estilo_perfil="d-none";
}

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];

$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}

//para la fecha de cumple
$fecha_inicio=date('m-01');
$fecha_fin=date('m-t');
$fecha_actual=date('m-d');

$sql="SELECT codigo,cod_tipo_identificacion,identificacion,cod_lugar_emision,fecha_nacimiento,DATE_FORMAT(fecha_nacimiento,'%d/%m/%Y') as fecha_nacimiento_x,cod_cargo,cod_unidadorganizacional,cod_area,haber_basico,CONCAT_WS(' ',paterno,materno,primer_nombre)as personal,cod_tipoafp,celular,telefono,email,email_empresa,DATE_FORMAT(ing_planilla,'%d/%m/%Y') as ing_planilla,turno 
from personal  where cod_estadopersonal=1 and cod_estadoreferencial=1 and cod_area in ($areaString) and cod_unidadorganizacional in ($unidadOrgString) order by paterno ";  

//echo $sql;
$stmtActivos = $dbh->prepare($sql);
$stmtActivos->execute();
// bindColumn
$stmtActivos->bindColumn('codigo', $codigo);
$stmtActivos->bindColumn('cod_tipo_identificacion', $cod_tipo_identificacion);
$stmtActivos->bindColumn('identificacion', $identificacion);
$stmtActivos->bindColumn('cod_lugar_emision', $cod_lugar_emision);
$stmtActivos->bindColumn('fecha_nacimiento', $fecha_nacimiento);
$stmtActivos->bindColumn('fecha_nacimiento_x', $fecha_nacimiento_x);
$stmtActivos->bindColumn('cod_cargo', $cod_cargo);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('haber_basico', $haber_basico);
$stmtActivos->bindColumn('personal', $personal);
$stmtActivos->bindColumn('celular', $celular);
$stmtActivos->bindColumn('telefono', $telefono);
$stmtActivos->bindColumn('email', $email);
$stmtActivos->bindColumn('email_empresa', $email_empresa);
$stmtActivos->bindColumn('ing_planilla', $ing_planilla);
$stmtActivos->bindColumn('cod_tipoafp', $cod_tipoafp);
$stmtActivos->bindColumn('turno', $turno);
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <h6 class="card-title">Exportar como:</h6>
                  </div>
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:50px;height: 50px;">
                      Reporte del Personal 
                  </h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>
                  <h6 class="card-title" style="color: #1e8449;"> * Cumpleaños del mes (Vigentes)</h6>
                  <h6 class="card-title" style="color: #dc7633;"> * Cumpleaños del mes (Pasados)</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginatorFixed_personal">
                      <thead class="bg-dark text-white">
                        <tr >
                          <th class="font-weight-bold">-</th>
                          <th class="font-weight-bold">Personal</th>
                          <th class="font-weight-bold">C.I.</th>
                          <th class="font-weight-bold">Of/Area</th>
                          <th class="font-weight-bold">Turno</th>
                          <th class="font-weight-bold <?=$estilo_perfil?>">F. Nac.</th>
                          <th class="font-weight-bold <?=$estilo_perfil?>">F. Ing.</th>
                          <th class="font-weight-bold">Cargo</th>
                          <th class="font-weight-bold <?=$estilo_perfil?>">H.Básico</th>
                          <th class="font-weight-bold">Afp</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $contador = 0;
                        while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                          $label="";
                          $label_td=''; 
                          // echo $fecha_inicio."<=".$fecha_nacimiento."----".$fecha_nacimiento."<=".$fecha_fin;
                          $mes_dia=date('m-d',strtotime($fecha_nacimiento));
                          if($fecha_inicio<=$mes_dia && $mes_dia<=$fecha_fin){
                            $label_td='font-weight-bold'; 
                            if($mes_dia>=$fecha_actual){
                              $label='style="color:#1e8449;"';
                            }else{
                              $label='style="color:#dc7633;"';
                            }
                          }
                          $nombre_turno="";
                          switch ($turno) {
                            case 1:
                              $nombre_turno="TM";
                              break;
                            case 2:
                              $nombre_turno="TT";
                              break;
                          }
                          if($identificacion=="")$identificacion=0;
                          $contador++;?>
                          <tr <?=$label?> >
                            <td class="text-center <?=$label_td?>"><?=$contador?></td>
                            <td class="text-left <?=$label_td?>"><?=$personal?></td>
                            <td class="text-left <?=$label_td?>"><?=$identificacion.' '.obtenerlugarEmision($cod_lugar_emision,1)?></td>
                            <td class="text-left <?=$label_td?>"><?=abrevUnidad_solo($cod_unidadorganizacional).'/'.abrevArea_solo($cod_area)?></td>
                            <td class="text-left <?=$label_td?>"><?=$nombre_turno?></td>
                            <td class="text-right <?=$label_td?> <?=$estilo_perfil?>"><?=$fecha_nacimiento_x?></td>
                            <td class="text-right <?=$label_td?> <?=$estilo_perfil?>"><?=$ing_planilla?></td>
                            <td class="text-left <?=$label_td?>"><?=nameCargo($cod_cargo)?></td>
                            <td class="text-center <?=$label_td?> <?=$estilo_perfil?>"><?=formatNumberDec($haber_basico)?></td>
                            <td class="text-left <?=$label_td?>"><?=obtenerNameAfp($cod_tipoafp,1)?></td>
                          </tr>

                          <?php
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
