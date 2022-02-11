<?php

$formato=$_POST['formato'];
if($formato==3){
  include 'reporteEstadosResultados_all.php';
}else{

if($formato==2){ ?>
  <meta charset="utf-8">
  <style type="text/css">
    .d-none {display: none !important;}
    .table{
      width: 100%;  
      border-collapse: collapse;}
      .table .fila-primary td{
   padding: 5px;
    border-top: 0px;
    border-right: 0px;
    border-bottom: 1px solid black;
    border-left: 0px;
  }
  .table .fila-totales td{
    padding: 5px;
    border-bottom: 0px;
    border-right: 0px;
    border-top: 1px solid black;
    border-left: 0px;
  }
  .table tr td{
    border: 1px solid black;
  }
  .td-border-none{
    border: none !important;
  }
  .td-border-derecha{
   border-bottom: 1px solid black !important;
   border-right: 1px solid black !important;
   border-top: 1px solid black !important;
   border-left: 0px !important;
  }
  .td-border-centro{
   border-bottom: 1px solid black !important;
   border-right: 0px !important;
   border-top: 1px solid black !important;
   border-left: 0px !important;
  }
  .td-border-izquierda{
   border-bottom: 1px solid black !important;
   border-right: 0px !important;
   border-top: 1px solid black !important;
   border-left: 1px solid black !important;
  }
  .td-border-bottom{
   border-bottom: 1px solid black !important;
   border-right: 0px !important;
   border-top: 0px !important;
   border-left: 0px !important;
  }
  .table .table-title{
   font-size: 12px;
  }
  </style>
  <?php
    header("Pragma: public");
    header("Expires: 0");
    $filename = "COBOFAR - ESTADOS DE RESULTADOS.xls";
    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

}

require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
set_time_limit(0);
$fechaActual=date("Y-m-d");
$gestion=nameGestion($_POST['gestion']);
$fecha=$_POST['fecha_desde'];
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$fechaHasta=$_POST['fecha_hasta'];
$fechaTituloHasta= explode("-",$fechaHasta);
$fechaFormateadaHasta=$fechaTituloHasta[2].'/'.$fechaTituloHasta[1].'/'.$fechaTituloHasta[0];

$moneda=1; //$_POST["moneda"];
$unidades=$_POST['unidad'];
$entidades=$_POST['entidad'];
// $StringEntidad=implode(",", $entidad);
$stringEntidades="";
foreach ($entidades as $valor ) {    
    $stringEntidades.=nameEntidad($valor).",";
}

$area_costo=$_POST['area_costo'];
if(isset($_POST['costos_areas'])){
  $centro_costo_sw=1;
}else{
  $centro_costo_sw=0;
}


$tituloOficinas="";
for ($i=0; $i < count($unidades); $i++) { 
  $tituloOficinas.=abrevUnidad_solo($unidades[$i]).",";
}
// $areas=array("prueba","prueba");//$_POST['area_costo'];
$html = '';
$html.='<html>';
         if($formato==1){
         $html.='<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDFBalance.css" rel="stylesheet" />'.
           '</head>';
          }
$html.='<body>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" width="50px" height="50px" src="../assets/img/icono_sm_cobofar.jpg">'.
            '<div id="header_titulo_texto">'.obtenerValorConfiguracion(43).'</div>'.
         '<div id="header_titulo_texto_inf_pegado">Del '.$fechaFormateada.' al '.$fechaFormateadaHasta.'</div>'.
         '<div id="header_titulo_texto_inf_pegado_Max">Expresado en Bolivianos</div>'.
         '<table class="table pt-2">'.
            '<tr class="bold table-title">'.
              '<td class="td-border-none" width="22%">Entidad: '.$stringEntidades.'</td>'.
              '<td class="td-border-none" width="33%"></td>'.            
            '</tr>'.
            '<tr>'.
            '<td class="td-border-none" colspan="2">Oficinas: '.$tituloOficinas.'</td>'.
            '</tr>'.
         '</table>'.
         '</header>';

$html.='<br><table class="table">'.
           '<tbody>'; 
           $index=1;
           $tBolActivo=0;$tBolPasivo=0;
// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=1 and (p.codigo=4000 or p.codigo=5000) order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
     $sumaNivel1=0;$html1="";
     $stmt2 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                            (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=2 and p.cod_padre='$codigo' order by p.numero");
      $stmt2->execute();                      
      $stmt2->bindColumn('codigo', $codigo_2);
      $stmt2->bindColumn('numero', $numero_2);
      $stmt2->bindColumn('nombre', $nombre_2);
      $stmt2->bindColumn('cod_padre', $codPadre_2);
      $stmt2->bindColumn('nivel', $nivel_2);
      $stmt2->bindColumn('cod_tipocuenta', $codTipoCuenta_2);
      $stmt2->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_2);
      $index_2=1;
      while ($row = $stmt2->fetch(PDO::FETCH_BOUND)) {
        
         $sumaNivel2=0;$html2="";
         $stmt3 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                              (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=3 and p.cod_padre='$codigo_2' order by p.numero");
         $stmt3->execute();                      
         $stmt3->bindColumn('codigo', $codigo_3);
         $stmt3->bindColumn('numero', $numero_3);
         $stmt3->bindColumn('nombre', $nombre_3);
         $stmt3->bindColumn('cod_padre', $codPadre_3);
         $stmt3->bindColumn('nivel', $nivel_3);
         $stmt3->bindColumn('cod_tipocuenta', $codTipoCuenta_3);
         $stmt3->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_3);
         $index_3=1;
         while ($row = $stmt3->fetch(PDO::FETCH_BOUND)) {            
            $sumaNivel3=0;$html3="";
            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='$codigo_3' order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;
            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
               $sumaNivel4=0;$html4="";           
              //listar los montos
              //$detallesReporte=listaSumaMontosDebeHaberComprobantesDetalle($fechaFormateadaHasta,1,$unidades,$areas,$codigo_4,$gestion,$fechaFormateada);
              $detallesReporte=listaSumaMontosDebeHaberComprobantesDetalle_areas($fechaFormateadaHasta,1,$unidades,$area_costo,$codigo_4,$gestion,$fechaFormateada);
               while ($rowComp = $detallesReporte->fetch(PDO::FETCH_ASSOC)) {
                   $cod_cuentaX=$rowComp['cod_cuenta'];
                   $numeroX=$rowComp['numero'];
                   $nombreX=formateaPlanCuenta($rowComp['nombre'], $rowComp['nivel']);
                   $montoX=(float)($rowComp['total_debe']-$rowComp['total_haber']);

                   //ACA VOLVEMOS TODO POSITIVO PARA LA RESTA FINAL
                   //$montoX=abs($montoX);
                   //OBTENEMOS SI ES CUENTA DE INGRESO O GASTO PARA CAMBIARLE DE SIGNO
                   $tipoCuentaIngresoGasto=substr($numeroX, 0, 1);  // 
                   if($tipoCuentaIngresoGasto==4){
                     $montoX=$montoX*-1;
                   }
                   
                   if($codigo==5000){                    
                    $tBolActivo+=$montoX;
                  }else{
                    $tBolPasivo+=$montoX;
                  }
                    $sumaNivel4+=$montoX;  
                    if($montoX>0){
                      $html4.='<tr>'.
                           '<td class="td-border-none text-left">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX<0){
                      $html4.='<tr>'.
                           '<td class="td-border-none text-left">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right">('.number_format(abs($montoX), 2, '.', ',').')</td>';   
                      $html4.='</tr>';      
                    }elseif($montoX==0){
                      $html4.='<tr>'.
                           '<td class="td-border-none text-left">'.formatoNumeroCuenta($numeroX).'</td>'.
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right">-</td>';   
                     $html4.='</tr>';      
                    }
                            
               $index++;  

               //para el  nivel 6 centro costos
                if($centro_costo_sw==1 && $montoX<>0){
                  $arrayUnidades=implode(",",$unidades);
                  $arrayAreas=implode(",",$area_costo);
                  $sql="SELECT a.nombre as nombre_area,d.cod_area,d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber
                    from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante join areas a on a.codigo=d.cod_area join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join plan_cuentas p on p.codigo=d.cod_cuenta 
                    where c.fecha between '$fecha 00:00:00' and '$fechaHasta 23:59:59' and d.cod_unidadorganizacional in ($arrayUnidades) and d.cod_area in ($arrayAreas) and c.cod_estadocomprobante<>2 
                    and  p.cod_padre=$codigo_4 and p.codigo=$cod_cuentaX
                    group by (d.cod_area) 
                    order by a.nombre";
                             // echo $sql."<br>";
                  $stmt6 = $dbh->prepare($sql);
                  $stmt6->execute();                      
                  $stmt6->bindColumn('cod_area', $cod_area_6);
                  $stmt6->bindColumn('total_debe', $total_debe_6);
                  $stmt6->bindColumn('total_haber', $total_haber_6);
                  $stmt6->bindColumn('nombre_area', $nombre_6);
                  $index_6=1;
                  $suma_nivel6=0;
                  while ($row = $stmt6->fetch(PDO::FETCH_BOUND)) {
                    $nombre_6=formateaPlanCuenta($nombre_6,6);
                    $montoX_aux=(float)($total_debe_6-$total_haber_6);
                    if($tipoCuentaIngresoGasto==4){
                     $montoX_aux=$montoX_aux*-1;
                   }                    
                   $suma_nivel6+=$montoX_aux;                    
                    if(number_format($montoX_aux, 2, '.', '')>0){
                      $html4.='<tr  style="color:#9b59b6;font-size:9px">'.
                           '<td class="td-border-none text-left"></td>'.
                           '<td class="td-border-none text-left">'.$nombre_6.'</td>'.
                           '<td class="td-border-none text-right">'.number_format($montoX_aux, 2, '.', ',').'</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>';   
                      $html4.='</tr>';      
                    }elseif(number_format($montoX_aux, 2, '.', '')<0){
                      $html4.='<tr  style="color:#9b59b6;font-size:9px">'.
                           '<td class="td-border-none text-left" ></td>'.
                           '<td class="td-border-none text-left">'.$nombre_6.'</td>'.
                           '<td class="td-border-none text-right">('.number_format(abs($montoX_aux), 2, '.', ',').')</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>';   
                      $html4.='</tr>';      
                    }
                  }
                  if(number_format($suma_nivel6, 2, '.', '')!=number_format($montoX, 2, '.', '') && $suma_nivel6>0){
                    $glosa_error="***REVISAR***";
                    $glosa_error=formateaPlanCuenta($glosa_error,6);
                    $html4.='<tr  style="color:red;font-size:9px">'.
                           '<td class="td-border-none text-left" ></td>'.
                           '<td class="td-border-none text-left">'.$glosa_error.'</td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td>'.
                           '<td class="td-border-none text-right"></td></tr>';      
                  }
                 }//centro costos
               }/* Fin del primer while*/
              if($sumaNivel4>0){
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='<tr class="bold">'.
                  '<td class=" td-border-none text-left">'.formatoNumeroCuenta($numero_4).'</td>'.
                  '<td class=" td-border-none text-left">'.$nombre_4.'</td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right">'.number_format($sumaNivel4, 2, '.', ',').'</td>';   
                $html3.='</tr>';
                $html3.=$html4;       
              }elseif($sumaNivel4<0){
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='<tr class="bold">'.
                  '<td class=" td-border-none text-left">'.formatoNumeroCuenta($numero_4).'</td>'.
                  '<td class=" td-border-none text-left">'.$nombre_4.'</td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right">('.number_format(abs($sumaNivel4), 2, '.', ',').')</td>';   
                $html3.='</tr>';
                $html3.=$html4;       
              }elseif($sumaNivel4==0){
                $sumaNivel3+=$sumaNivel4;  
                $nombre_4=formateaPlanCuenta($nombre_4, $nivel_4);
                $html3.='<tr class="bold">'.
                  '<td class=" td-border-none text-left">'.formatoNumeroCuenta($numero_4).'</td>'.
                  '<td class=" td-border-none text-left">'.$nombre_4.'</td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right">-</td>';   
                $html3.='</tr>';
                $html3.=$html4;       
              } 
            }
            if($sumaNivel3>0){
              $sumaNivel2+=$sumaNivel3;
              $nombre_3=formateaPlanCuenta($nombre_3, $nivel_3);
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-none text-left">'.formatoNumeroCuenta($numero_3).'</td>'.
                  '<td class=" td-border-none text-left">'.$nombre_3.'</td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right">'.number_format($sumaNivel3, 2, '.', ',').'</td>';   
              $html2.='</tr>';
              $html2.=$html3;
            }elseif($sumaNivel3<0){
              $sumaNivel2+=$sumaNivel3;
              $nombre_3=formateaPlanCuenta($nombre_3, $nivel_3);
              $html2.='<tr class="bold">'.
                  '<td class=" td-border-none text-left">'.formatoNumeroCuenta($numero_3).'</td>'.
                  '<td class=" td-border-none text-left">'.$nombre_3.'</td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right"></td>'.
                  '<td class=" td-border-none text-right">('.number_format(abs($sumaNivel3), 2, '.', ',').')</td>';   
              $html2.='</tr>';
              $html2.=$html3;
            }
          }
          if($sumaNivel2>0){
            $sumaNivel1+=$sumaNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='<tr class="bold">'.
                    '<td class="td-border-none text-left">'.formatoNumeroCuenta($numero_2).'</td>'.
                    '<td class="td-border-none text-left">'.$nombre_2.'</td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right">'.number_format($sumaNivel2, 2, '.', ',').'</td>';   
             $html1.='</tr>';
             $html1.=$html2; 
          }
          elseif($sumaNivel2<0){
            $sumaNivel1+=$sumaNivel2;
            $nombre_2=formateaPlanCuenta($nombre_2, $nivel_2);
            $monto_2=0;
            $html1.='<tr class="bold">'.
                    '<td class="td-border-none text-left">'.formatoNumeroCuenta($numero_2).'</td>'.
                    '<td class="td-border-none text-left">'.$nombre_2.'</td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right"></td>'.
                    '<td class="td-border-none text-right">('.number_format(abs($sumaNivel2), 2, '.', ',').')</td>';   
             $html1.='</tr>';
             $html1.=$html2; 
          }
      }

    $nombre=formateaPlanCuenta($nombre, $nivel);
    $monto=0;
    if($sumaNivel1>0){
      $html.='<tr class="bold table-title">'.
                '<td class="td-border-izquierda text-left">'.formatoNumeroCuenta($numero).'</td>'.
                '<td class="td-border-centro text-left" width="50%">'.$nombre.'</td>'.
                '<td class="td-border-centro text-right" width="10%"></td>'.
                '<td class="td-border-centro text-right"></td>'.
                '<td class="td-border-centro text-right"></td>'.
                '<td class="td-border-derecha text-right">'.number_format($sumaNivel1, 2, '.', ',').'</td>';   
     $html.='</tr>';
     $html.=$html1;
    }elseif($sumaNivel1<0){
      $html.='<tr class="bold table-title">'.
                '<td class="td-border-izquierda text-left">'.formatoNumeroCuenta($numero).'</td>'.
                '<td class="td-border-centro text-left" width="50%">'.$nombre.'</td>'.
                '<td class="td-border-centro text-right" width="10%"></td>'.
                '<td class="td-border-centro text-right"></td>'.
                '<td class="td-border-centro text-right"></td>'.
                '<td class="td-border-derecha text-right">('.number_format(abs($sumaNivel1), 2, '.', ',').')</td>';   
     $html.='</tr>';
     $html.=$html1;
    }
}
 $html.='</tbody></table>';
      $totalResultado=$tBolPasivo-$tBolActivo;
      if($totalResultado>=0){
        $html.='<br><table class="table">'.
            '<thead>'.
            '<tr class="bold table-title">'.
              '<td class="text-left" width="85%">Resultado</td>'.
              '<td class="text-right">'.number_format($totalResultado, 2, '.', ',').'</td>'.
            '</tr>';
      }elseif ($totalResultado<0) {
        $html.='<br><table class="table">'.
            '<thead>'.
            '<tr class="bold table-title">'.
              '<td class="text-left" width="85%">Resultado</td>'.
              '<td class="text-right">('.number_format(abs($totalResultado), 2, '.', ',').')</td>'.
            '</tr>';
      }
      
      $html.='</thead>'.
           '<tbody>';
  $html.=    '</tbody></table>';

$html.='</body>'.
      '</html>';

if($formato==2){
  echo $html;
}else{
  descargarPDF("COBOFAR - BALANCE GRAL ",$html);
}

}
// //echo $html;
// descargarPDF("COBOFAR - Estado de Resultados (".$tituloOficinas.")",$html);
?>
