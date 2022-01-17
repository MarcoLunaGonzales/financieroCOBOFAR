<?php

set_time_limit(0);
session_start();

//require_once '../layouts/bodylogin.php';

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';

require_once '../rrhh/configModule.php';

$array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
$cod_gestion=$_GET['cod_gestion'];
$cod_mes=$_GET['cod_mes'];


$sqlPersonalDistribucion="SELECT pd.cod_personal, SUM(pd.porcentaje) as porcentaje 
from personal p join personal_area_distribucion pd on p.codigo=pd.cod_personal
where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 
GROUP BY pd.cod_personal";
$stmtPersonalDistribucion = $dbh->prepare($sqlPersonalDistribucion);
$stmtPersonalDistribucion->execute();
$sw_auxiliar=0;
while ($rowPersonal = $stmtPersonalDistribucion->fetch(PDO::FETCH_ASSOC)) 
{
   if($rowPersonal['porcentaje']!=100){
      $sw_auxiliar++;
      $array_personal[]=$rowPersonal['cod_personal'];
   } 
}
if($sw_auxiliar==0){//distribucion de sueldos cuadrando
   $sqlBonos = "SELECT codigo,cod_tipocalculobono,cod_cuenta from bonos where cod_estadoreferencial=1 and codigo in (15,16)";
   // echo $sqlBonos;
   $stmtBonos = $dbh->prepare($sqlBonos);
   $stmtBonos->execute();                      
   $stmtBonos->bindColumn('codigo',$cod_bono);
   $stmtBonos->bindColumn('cod_tipocalculobono',$cod_tipocalculobono);
   $stmtBonos->bindColumn('cod_cuenta',$cod_cuenta);
   $arrayBonos=[];
   while ($row = $stmtBonos->fetch()) 
   { 
     $arrayBonos[] = $cod_bono.'@@@'.$cod_tipocalculobono.'@@@'.$cod_cuenta;
   }

   $sqlAreas = "SELECT codigo,nombre from areas where cod_estado=1 and centro_costos=1 and codigo =108 order by nombre";
   $stmtAreas = $dbh->prepare($sqlAreas);
   $stmtAreas->execute();                      
   $stmtAreas->bindColumn('codigo',$cod_area);
   $stmtAreas->bindColumn('nombre',$nombre_area);
   $arrayAreas=[];
   while ($row = $stmtAreas->fetch()) 
   {
      $arrayAreas[] = $cod_area.'@@@'.$nombre_area;   
   }


   $globalUnidad_ofcen=1;//Oficina central
   $cod_area_ofcen=obtenerValorConfiguracion(29);//area
   $tipoComprobante=3;

   $codEmpresa=1;
   $mesPlanilla=$_GET["cod_mes"];
   $gestionPlanilla=$_GET["cod_gestion"];
   $nombreGestion=nameGestion($gestionPlanilla);
   $nombreMes=nombreMes($mesPlanilla);

   // $mesTrabajo=$_SESSION['globalMes'];


    $anioActual=date("Y");
    $mesActual=date("m");
    $diaActual=date("d");
    $codMesActiva=$_SESSION['globalMes']; 
    $gestionTrabajo=$_SESSION['globalNombreGestion'];
    $month = $gestionTrabajo."-".$codMesActiva;
    $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
    $diaUltimo = date('d', strtotime("{$aux} - 1 day"));
    $horasActual=date("H:i:s");
    if((int)$gestionTrabajo<(int)$anioActual){
      $fechaHoraActual=$gestionTrabajo."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
    }else{
      if((int)$mesActual==(int)$codMesActiva){
          $fechaHoraActual=date("Y-m-d H:i:s");
      }else{
        $fechaHoraActual=$gestionTrabajo."-".$codMesActiva."-".$diaUltimo." ".$horasActual;
      } 
    }
   $ordenDetalle=1;//
   $nroCorrelativo=numeroCorrelativoComprobante($gestionTrabajo,$globalUnidad_ofcen,$tipoComprobante,$codMesActiva);
   //$numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidad_ofcen, $gestionTrabajo, $mesTrabajo);
   $glosaCabecera="Provisión planilla de sueldos y salarios correspondiente al mes de ".$nombreMes." ".$nombreGestion;
   $glosaDetalle=$glosaCabecera;
   $codComprobante=obtenerCodigoComprobante();
   $sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','1','$globalUnidad_ofcen','$gestionTrabajo','1','1','$tipoComprobante','$fechaHoraActual','$nroCorrelativo','$glosaCabecera')";
      $stmtInsertCab = $dbh->prepare($sqlInsertCab);
      $flagSuccess=$stmtInsertCab->execute();
      //trabajamos Primero oficina central *********
      //HABER BASICO
      $cod_tipo=1;
      $totalHaberBasico=monto_planillaGeneral($codigo_planilla, $globalUnidad_ofcen,$cod_area_ofcen,$cod_tipo);//total ganado de toda la unidad
      $cod_cuenta=5010;//cuenta 
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_ofcen','$cod_area_ofcen','$totalHaberBasico','0','$glosaDetalle','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //BONOS ANTIGUEDAD
      $cod_tipo=2;//BONO ANTIGUEDAD
      $totalBono=monto_planillaGeneral($codigo_planilla, $globalUnidad_ofcen,$cod_area_ofcen,$cod_tipo);//total ganado de toda la unidad
      $cod_cuenta=5011;//cuenta 
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_ofcen','$cod_area_ofcen','$totalBono','0','$glosaDetalle','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //BONOS OTROS
      for ($j=0; $j <count($arrayBonos);$j++){ 
         $string_bono=$arrayBonos[$j];
         $array_bono=explode('@@@',$string_bono);
         $cod_bono=$array_bono[0];
         $cod_tipocalculobono=$array_bono[1];
         $cod_cuentaBono=$array_bono[2];
         $totalBono=monto_planillaGeneral_bonos($codigo_planilla,$gestionPlanilla,$mesPlanilla,$globalUnidad_ofcen,$cod_area_ofcen,$cod_bono,$cod_tipocalculobono);//
         if($totalBono>0){ //solo contabilizamos montos >0
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuentaBono','0','$globalUnidad_ofcen','$cod_area_ofcen','$totalBono','0','$glosaDetalle','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            $ordenDetalle++;
         }
      }
      //Proceso para sucursales *********
      //haber basico dias trabajados
      $globalUnidad_lp=2;
      for ($j=0; $j <count($arrayAreas);$j++){ 
         $string_areas=$arrayAreas[$j];
         $array_areas=explode('@@@',$string_areas);
         $cod_area=$array_areas[0];
         $nombre_area=$array_areas[1];

         $cod_tipo=1;
         $totalHaberBasico=monto_planillaGeneral($codigo_planilla, $globalUnidad_lp,$cod_area,$cod_tipo);//total ganado de toda la unidad
         if($totalHaberBasico>0){
            $cod_cuenta=5010;//cuenta 
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_lp','$cod_area','$totalHaberBasico','0','$glosaDetalle','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            $ordenDetalle++;
         }
      }
      //bono antiguedad sucursales
      $cod_tipo=2;
      $cod_cuenta=5011;//cuenta BONO ANTIGU
      for ($j=0; $j <count($arrayAreas);$j++){
         $string_areas=$arrayAreas[$j];
         $array_areas=explode('@@@',$string_areas);
         $cod_area=$array_areas[0];
         $nombre_area=$array_areas[1];
         $totalmonto=monto_planillaGeneral($codigo_planilla, $globalUnidad_lp,$cod_area,$cod_tipo);//total ganado de toda la unidad
         if($totalmonto>0){
            $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_lp','$cod_area','$totalmonto','0','$glosaDetalle','$ordenDetalle')";
            $stmtInsertDet = $dbh->prepare($sqlInsertDet);
            $flagSuccessDet=$stmtInsertDet->execute();
            $ordenDetalle++;
         }
      }
      //bonos otros Sucursales
      //
      for ($j=0; $j <count($arrayBonos);$j++){ 
         $string_bono=$arrayBonos[$j];
         $array_bono=explode('@@@',$string_bono);
         $cod_bono=$array_bono[0];
         $cod_tipocalculobono=$array_bono[1];
         $cod_cuentaBono=$array_bono[2];
         if($cod_bono<>16){
            // if($cod_bono==15){
            //    $cod_tipocalculobono=2;
            // }
            for ($X=0; $X <count($arrayAreas);$X++){
               $string_areas=$arrayAreas[$X];
               $array_areas=explode('@@@',$string_areas);
               $cod_area=$array_areas[0];
               $totalBono=monto_planillaGeneral_bonos($codigo_planilla,$gestionPlanilla,$mesPlanilla,$globalUnidad_lp,$cod_area,$cod_bono,$cod_tipocalculobono);//
               if($totalBono>0){ //solo contabilizamos montos >0
                  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuentaBono','0','$globalUnidad_lp','$cod_area','$totalBono','0','$glosaDetalle','$ordenDetalle')";
                  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
                  $flagSuccessDet=$stmtInsertDet->execute();
                  $ordenDetalle++;
               }
            }
         }
      }

      //liquido pagable
      $cod_tipo=3;
      $montoTotal=monto_planillaGeneral($codigo_planilla,-100,$cod_area_ofcen,$cod_tipo);//total ganado de toda la unidad
      $cod_cuenta=2004;//sueldos por pagar
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_ofcen','$cod_area_ofcen','0','$montoTotal','$glosaDetalle','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //AFPS
      $montoTotal=monto_planillaGeneral_afps($codigo_planilla);
      $cod_cuenta=2008;//afps
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidad_ofcen','$cod_area_ofcen','0','$montoTotal','$glosaDetalle','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //descuentos

      // //anticipos
      // $cod_cuenta=5084;//cuenta anticipos
      // $sqlAnticipos = "SELECT a.cod_personal,a.monto,(select ca.codigo from cuentas_auxiliares ca where ca.cod_cuenta=$cod_cuenta and ca.cod_estadoreferencial=1 and ca.cod_proveedorcliente=a.cod_personal)as cuenta_Auxiliar
      //    from anticipos_personal a where a.cod_mes=$mesPlanilla and a.cod_gestion=$gestionPlanilla and a.monto>0 and a.cod_estadoreferencial=1";
      // $stmtAnticipos = $dbh->prepare($sqlAnticipos);
      // $stmtAnticipos->execute();
      // while ($rowAntici = $stmtAnticipos->fetch(PDO::FETCH_ASSOC)) {
      //    $monto=$rowAntici['monto'];
      //    $cod_personal=$rowAntici['cod_personal'];
      //    $cuenta_Auxiliar=$rowAntici['cuenta_Auxiliar'];
      //    $cod_cuenta=2008;
      //    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cuenta_Auxiliar','$globalUnidad_ofcen','$cod_area_ofcen','0','$monto','$glosaDetalle','$ordenDetalle')";
      //    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      //    $flagSuccessDet=$stmtInsertDet->execute();
      //    $ordenDetalle++;
      //    //falta estado cuenta


            
      // }


      







      // //FIN PERSONAL PROYECTOS
      // $glosaDetalleGeneral="Sueldo correspondiente a: ".$mesPlanilla."/".$gestionPlanilla;
      // //$codUOCentroCosto="829";
      // $codUOCentroCosto=$globalUnidadX;
      // $codAreaCentroCosto="501";

      // //SUELDOS POR PAGAR
      // $totalLiquidoPagable=totalLiquidoPagable($gestionPlanilla, $mesPlanilla, $globalUnidadX);
      // $numeroCuenta="110";
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalLiquidoPagable','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //CAJA PETROLERA
      // // $totalCajaSalud=17819.96;
      // $totalCajaSalud=obtenerTotalCPS($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="120";//por defecto
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalCajaSalud','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //AFP PREVISION BBV
      // // $totalAFPPrevision=15514.25;
      // $totalAFPPrevision=obtenerTotalAFP_prev1($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="121";//por defecto
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //AFP PREVISION BBV
      // // $totalAFPPrevision=298.24;
      // $totalAFPPrevision=obtenerTotalAFP_prev2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="121";//por defecto
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;


      // //AFP FUTURO
      // // $totalAFPFuturo=15528.13;
      // $totalAFPFuturo=obtenerTotalAFP_prev3($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="121";//por defecto
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //AFP PREVISION BBV
      // $totalAFPFuturo=0;
      // $numeroCuenta="121";
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //PROVIVIENDA
      // // $totalProVivienda=1781.20;
      // $totalProVivienda=obtenerTotalprovivienda($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="121";
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;

      // //PROVIVIENDA
      // // $totalProVivienda=1782.79;
      // $totalProVivienda=obtenerTotalprovivienda2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="121";
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;


      // //RC IVA
      // // $totalRCIVA=108.03;
      // $totalRCIVA=obtenerTotalOtrosdescuentos($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      // $numeroCuenta="131";
      // $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRCIVA','$glosaDetalleGeneral','$ordenDetalle')";
      // $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      // $flagSuccessDet=$stmtInsertDet->execute();
      // $ordenDetalle++;
      // //indicamos que ya se realizo el comprbante      
      // $stmtUdatePlanilla = $dbh->prepare("UPDATE planillas set comprobante=1 where codigo=$codigo_planilla");
      // $stmtUdatePlanilla->execute();

   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal1").modal("show");         
      });
   </script>
<?php }else{ 
   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal2").modal("show");         
      });
   </script>
<?php }
?>

<!-- modal  -->
<div class="modal fade" id="mostrarmodal1" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>El Proceso ha finalizado.</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor seleccione una opción.
      </div>       
      <div class="modal-footer">          
        <a href="<?=$urlComprobantesLista2;?>" type="button" class="btn btn-success">Ir a Comprobantes</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mostrarmodal2" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>No Se Pudo Completar El Proceso...</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor verifique que toda la distribución de sueldos esté correctamente distribuidos.<br><br>
         <b>Error en personal:</b> <br>
         <?php
         // $longitud_array=count($array_personal);
         foreach ($array_personal as $cod_personal_x) {
            $nombre_personal_x=obtenerNombrePersonal($cod_personal_x);
            echo "<b>- Nombre: </b>".$nombre_personal_x."(Cod:".$cod_personal_x.").<br>";
         }

         ?>
      </div>       
      <div class="modal-footer">                  
        <a href="<?=$urlPersonalLista2;?>" type="button" class="btn btn-success">Ir a Personal</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>


