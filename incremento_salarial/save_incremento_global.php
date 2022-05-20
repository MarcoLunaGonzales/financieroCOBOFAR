<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';

$dbh = new Conexion();

session_start();
$tipo=4;
$created_at=date('Y-m-d H:i:s');
$fecha_cambio=date('Y-m-d');
$gestion=date('Y');
$created_by=$_SESSION['globalUser'];


$incremento_smn_g=$_POST['incremento_smn_g'];
$incremento_hb_g=$_POST['incremento_hb_g'];
$incremento_smn_monto=$_POST['incremento_smn_monto'];


$array_codPesonal=$_POST['codigo_persona'];//array de codigos de personal
$haber_basico_nuevo=$_POST['hbn'];//array de nuevo haber basico
$haber_basico_ant=$_POST['hba'];//array de nuevo haber basico
$index=0;
// var_dump($haber_basico_ant);
foreach($array_codPesonal as $key => $cod_personal) {
  $haber_basico = $haber_basico_nuevo[$index];
  $haber_basico_anterior = $haber_basico_ant[$index];  
  $descripcion="Inc Salarial ".date('Y').". SMN:".$incremento_smn_g."%, HB:".$incremento_hb_g."%. ".formatNumberDec($haber_basico_anterior)." -> ".formatNumberDec($haber_basico);
  $stmt = $dbh->prepare("UPDATE personal set haber_basico=:haber_basico,haber_basico_anterior=:haber_basico_anterior where codigo = :codigo");
  $stmt->bindParam(':codigo', $cod_personal);
  $stmt->bindParam(':haber_basico', $haber_basico);
  $stmt->bindParam(':haber_basico_anterior', $haber_basico_anterior);
  $flagSuccess=$stmt->execute();     
  //para el historico
  $sql="INSERT into historico_cambios_personal(cod_personal,tipo,descripcion,fecha_cambio,created_by,created_at)
  values(:cod_personal,:tipo,:descripcion,:fecha_cambio,:created_by,:created_at)";
  $stmtInsert = $dbh->prepare($sql);
  $stmtInsert->bindParam(':cod_personal', $cod_personal);
  $stmtInsert->bindParam(':tipo',$tipo);
  $stmtInsert->bindParam(':descripcion',$descripcion);
  $stmtInsert->bindParam(':fecha_cambio',$fecha_cambio);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':created_at',$created_at);
  $flagSuccess=$stmtInsert->execute();
  $index++;
}

if($flagSuccess){
  $id_configuracion_smn=1;
  $id_configuracion_gestion=29;
  // $minimo_salarial_config=obtenerValorConfiguracionPlanillas($id_configuracion_smn);
  // $salario_minimo_nacional_nuevo=floor($minimo_salarial_config+$minimo_salarial_config*$incremento_smn_g/100);
  $salario_minimo_nacional_nuevo=$incremento_smn_monto;

  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion=:valor_configuracion where id_configuracion = :id_configuracion");
  $stmtUpdateConf->bindParam(':id_configuracion', $id_configuracion_smn);
  $stmtUpdateConf->bindParam(':valor_configuracion', $salario_minimo_nacional_nuevo);
  $stmtUpdateConf->execute();     

  $stmtUpdateConf = $dbh->prepare("UPDATE configuraciones_planillas set valor_configuracion=:valor_configuracion where id_configuracion = :id_configuracion");
  $stmtUpdateConf->bindParam(':id_configuracion', $id_configuracion_gestion);
  $stmtUpdateConf->bindParam(':valor_configuracion', $gestion);
  $stmtUpdateConf->execute();

  //creamos la planilla de retroactivos
  $cod_gestion=codigoGestion($gestion);
  $cod_estadoplanilla=1;
  $sqlPlanillaRetro="INSERT into planillas_retroactivos(cod_gestion,cod_estadoplanilla,created_by,created_at) values(:cod_gestion,:cod_estadoplanilla,:created_by,:created_at)";
  $stmtPlanillaRetro = $dbh->prepare($sqlPlanillaRetro);
  $stmtPlanillaRetro->bindParam(':cod_gestion', $cod_gestion);
  $stmtPlanillaRetro->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtPlanillaRetro->bindParam(':created_by',$created_by);
  $stmtPlanillaRetro->bindParam(':created_at',$created_at);
  $stmtPlanillaRetro->execute();
}

$flagSuccess=true;
showAlertSuccessError($flagSuccess,"../index.php?opcion=planillasRetroactivoPersonal");

?>
