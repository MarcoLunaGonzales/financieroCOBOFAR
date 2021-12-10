<?php
require_once '../conexion3.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
session_start();
set_time_limit(0);

$cod_mes=$_GET['cod_mes'];
$cod_gestion=$_GET['cod_gestion'];
$codPlanilla=$_GET['codigo_planilla'];
//nombre de unidad
$dbh = new Conexion3();
$mes=strtoupper(nombreMes($cod_mes));
$gestion=nameGestion($cod_gestion);

$dias_trabajados_mes=30; 
$hrsTrabajadas=8;

$sqlBonos = "SELECT codigo from bonos where cod_estadoreferencial=1";
// echo $sqlBonos;
$stmtBonos = $dbh->prepare($sqlBonos);
$stmtBonos->execute();                      
$stmtBonos->bindColumn('codigo',$cod_bono);
$arrayBonos=[];
while ($row = $stmtBonos->fetch()) 
{ 
  $arrayBonos[] = $cod_bono;
}

// var_dump($arrayBonos);
$sqlDescuento = "SELECT codigo from descuentos where cod_estadoreferencial=1";
$stmtDescuento = $dbh->prepare($sqlDescuento);
$stmtDescuento->execute();                      
$stmtDescuento->bindColumn('codigo',$cod_descuento);
$arrayDescuentos=[];
while ($row = $stmtDescuento->fetch()) 
{ 
  $arrayDescuentos[] = $cod_descuento;
}

//html del reporte
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      'if ( isset($pdf) ) {'. 
        '$font = Font_Metrics::get_font("helvetica", "normal");'.
        '$size = 9;'.
        '$y = $pdf->get_height() - 24;'.
        '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
        '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
      '}'.
    '</script>';
$html.=  '<header class="header">'.            
            '<table width="100%">
              <tr>
              <td width="25%"><p>CORPORACION BOLIVIANA DE FARMACIAS<br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:1022039027<br>N° Empleador Ministerio de Trabajo 1146072502</p></td>
              <td><center><span style="font-size: 13px"><b>PLANILLA DE SUELDOS Y SALARIOS</b></span><BR>Correspondientes al mes de '.$mes.' '.$gestion.'<br><b>EXPRESADA EN BOLIVIANOS</b></center></td>
              <td width="25%"><center>N° PAT. 651-1-956</center></td>
              </tr>
            </table>'.
         '</header>';

          $html.='<table class="table">'.
            '<thead>'.
            '<tr class="table-title bold text-center">'.
              '<td width="1%"><small><small>Nro</small></small></td>'.
              '<td width="4%"><small><small>CI EXT</small></small></td>'.
              '<td width="10%"><small><small>Apellidos y Nombres</small></small></td>'.
              '<td width="2%"><small><small>Sex</small></small></td>'.
              // '<td width="2%">Nac ión</small></small></td>'.
              // '<td width="4%">Fech Nac</small></small></td>'.
              '<td><small><small>Cargo</small></small></td>'.
              '<td><small><small>Turn</small></small></td>'.
              '<td><small><small>Fech Ing</small></small></td>'.
              '<td><small><small>Hrs Trab</small></small></td>'.
              '<td><small><small>Días Trab</small></small></td>'.
              '<td><small><small>Haber Basico</small></small></td>'.
              '<td><small><small>Haber Basico DTrab</small></small></td>'.
              '<td><small><small>Bono Ant</small></small></td>'.
              // '<td width="4%"><small><small>Otr Bonos</small></small></td>'.
              '<td><small><small>Noch</small></small></td>'.
              '<td><small><small>Dom</small></small></td>'.
              '<td><small><small>Feri ado</small></small></td>'.
              '<td><small><small>Mov</small></small></td>'.
              '<td><small><small>Refr</small></small></td>'.
              '<td><small><small>Rein tegro</small></small></td>'.
              '<td><small><small>Com Ven</small></small></td>'.
              '<td><small><small>Fallo</small></small></td>'.
              '<td><small><small>Hrs Extr</small></small></td>'.
              '<td><small><small>Tota Gana</small></small></td>'.
              '<td><small><small>AFP F</small></small></td>'.
              '<td><small><small>AFP P</small></small></td>'.
              '<td><small><small>Ap.Sol</small></small></td>'.
              '<td><small><small>RC-IVA</small></small></td>'.              
              '<td><small><small>Anti cipo</small></small></td>'.
              // '<td>Otr Desc</small></small></td>'.
              '<td><small><small>Pres tamo</small></small></td>'.
              '<td><small><small>In vent</small></small></td>'.
              '<td><small><small>Ven cid</small></small></td>'.
              '<td><small><small>Atr aso</small></small></td>'.
              '<td><small><small>Fal Caj</small></small></td>'.
              '<td><small><small>O Desc</small></small></td>'.
              '<td><small><small>Apo Sind</small></small></td>'.
              '<td><small><small>Tot Desc</small></small></td>'.
              '<td ><small><small>Liq Pag</small></small></td>'.
              '<td width="4%"><small><small>FIRMA</small></small></td>';
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            $rc_iva=0;
            // $atrasos=0;
            $anticipo=0;
            // $monto_bonos_otros=0;
            $data = obtenerPlanillaSueldosRevision($codPlanilla);

            //INICIAR valores de las sumas
            $subtotal_dias=0;
            $subtotal_haberbasico=0;
            $subtotal_haberbasico_traba=0;
            $subtotal_bantig=0;
            $subtotal_bnoche=0;
            $subtotal_bdomin=0;
            $subtotal_bferi=0;
            $subtotal_bmov=0;
            $subtotal_brefr=0;
            $subtotal_breint=0;
            $subtotal_bventas=0;
            $subtotal_bfallo=0;
            $subtotal_bhrsex=0;
            $subtotal_totalganado=0;
            $subtotal_afpf=0;
            $subtotal_afpp=0;
            $subtotal_rciva=0;
            $subtotal_anticipo=0;
            $subtotal_dprest=0;
            $subtotal_dinvt=0;
            $subtotal_dvencid=0;
            $subtotal_datraso=0;
            $subtotal_dfalcaj=0;
            $subtotal_dodesc=0;
            $subtotal_daposind=0;
            $subtotal_totdesc=0;
            $subtotal_liqpag=0;

            $subtotal_aporSol=0;

            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
              $cod_personalcargo=$row['codigo'];
              $dias_trabajados_planilla=$row['dias_trabajados'];
              //$monto_bonos_otros=obtenerPlanillaSueldoRevisionBonos($cod_personal_cargo,$cod_gestion,$cod_mes,$dias_trabajados_planilla,$dias_trabajados_mes);
              $porcentaje=$row['porcentaje'];
              $rc_iva=$row['rc_iva'];
              $atrasos=$row['atrasos'];
              $anticipo=$row['anticipo'];
              $fechaNac=$row['fecha_nacimiento'];
              $emision=$row['emision'];
              $sexo=$row['genero'];
              $nacion=$row['nacionalidad'];

              $cod_turno=$row['turno'];

              $turno_nombre="";
              switch ($cod_turno) {
                case 1:
                  $turno_nombre="TM";
                  break;
                case 2:
                  $turno_nombre="TT";
                  break;
                case 10:
                  $turno_nombre="";
                  break;
              }
             
              // $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos from descuentos_personal_mes 
              //   where  cod_personal=$cod_personal_cargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
              // $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
              // $stmtDescuentosOtros->execute();
              // $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
              // $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];
              if($codArea!=$row['cod_area']){
                // if($codArea!=0){
                //   $html.='<tr>'.
                //   '<td style="border: 0;" colspan="8" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dias).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_haberbasico).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_haberbasico_traba).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bantig).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bnoche).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bdomin).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bferi).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bmov).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_brefr).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_breint).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bventas).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bfallo).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_bhrsex).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_totalganado).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_afpf).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_afpp).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_rciva).'</small></small></small></small></td>'.              
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_anticipo).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dprest).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dinvt).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dvencid).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_datraso).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dfalcaj).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_dodesc).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_daposind).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_totdesc).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" class="text-right"><small><small><small><small>'.formatNumberDec($subtotal_liqpag).'</small></small></small></small></td>'.
                //   '<td style="border: 0;" ></td></tr>';
                // }
                
                  
                $html.='<tr>'.
                      '<td colspan="36"><center>Departamento / Sucursal: <b>'.$row['area'].'</b></center></td>';
                    $html.='</tr>';
                $codArea=$row['cod_area'];
              }
              $html.='<tr>'.
                '<td class="text-center"><small><small><small>'.$index.'</small></small></small></td>'.
                '<td class="text-left"><small><small><small>'.$row['ci'].' '.$emision.'</small></small></small></td>'.
                '<td class="text-left"><small><small><small>'.$row['apellidos']." ".$row['nombres'].'</small></small></small></td>'.
                '<td class="text-left"><small><small><small>'.$sexo.'</small></small></small></td>'.
                // '<td><small><small>'.$nacion.'</small></small></td>'.
                // '<td><small><small>'.strftime('%d/%m/%Y',strtotime($fechaNac)).'</small></small></td>'.
                '<td class="text-left"><small><small><small>'.$row['cargo'].'</small></small></small></td>'.
                '<td class="text-left"><small><small><small>'.$turno_nombre.'</small></small></small></td>'.
                '<td class="text-left"><small><small><small>'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.$hrsTrabajadas.'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.$dias_trabajados_planilla.'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['haber_basico_pactado']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['haber_basico2']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['bono_antiguedad']*$porcentaje/100).'</small></small></small></td>';
                $montoX_refri=0;//para el refrigerio
                for ($j=0; $j <count($arrayBonos);$j++){ 
                  $cod_bono_aux=$arrayBonos[$j];                          
                  $sqlBonosOtrs = "SELECT bpm.monto,b.cod_tipocalculobono
                        from bonos_personal_mes bpm,bonos b 
                        where   bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and  bpm.cod_bono=$cod_bono_aux and bpm.cod_estadoreferencial=1";
                  $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
                  $stmtBonosOtrs->execute();
                  $resultBonosOtros=$stmtBonosOtrs->fetch();
                  $montoX=$resultBonosOtros['monto'];
                  $tipoBonoX=$resultBonosOtros['cod_tipocalculobono'];
                  if($tipoBonoX==2){
                    $porcen_monto=$dias_trabajados_planilla*100/$dias_trabajados_mes;
                    $montoX_aux=$porcen_monto*$montoX/100;
                  }else $montoX_aux=$montoX;


                  // $monto_bonos_otros+=$montoX_aux;
                  if($cod_bono_aux==15 or $cod_bono_aux==16){//sumamos en uno los refrigerios
                    $montoX_refri+=$montoX_aux;
                    if($cod_bono_aux==16){
                       $subtotal_brefr+=$montoX_refri*$porcentaje/100;
                      $html.='<td  class="text-right"><small><small><small>'.formatNumberDec($montoX_refri*$porcentaje/100).'</small></small></small></td>';
                    }
                  }else{
                    switch ($cod_bono_aux) {
                      case 11:
                          $subtotal_bnoche+=$montoX_aux*$porcentaje/100;
                      break;
                      case 12:
                          $subtotal_bdomin+=$montoX_aux*$porcentaje/100;
                      break;
                      case 13:
                          $subtotal_bferi+=$montoX_aux*$porcentaje/100;
                      break;
                      case 14:
                          $subtotal_bmov+=$montoX_aux*$porcentaje/100;
                      break;
                      case 17:
                          $subtotal_breint+=$montoX_aux*$porcentaje/100;
                      break;
                      case 18:
                          $subtotal_bventas+=$montoX_aux*$porcentaje/100;
                      break;
                      case 19:
                          $subtotal_bfallo+=$montoX_aux*$porcentaje/100;
                      break;
                      case 20:
                          $subtotal_bhrsex+=$montoX_aux*$porcentaje/100;
                      break;
                    }
                    $html.='<td  class="text-right"><small><small><small>'.formatNumberDec($montoX_aux*$porcentaje/100).'</small></small></small></td>';  
                  }
                }
                $html.='<td class="text-right"><small><small><small>'.formatNumberDec($row['total_ganado']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['afp_1']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['afp_2']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec(($row['a_solidario_13000']+$row['a_solidario_25000'])*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($rc_iva*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($anticipo*$porcentaje/100).'</small></small></small></td>';
                // $sumaDescuentos_otros=0;
                for ($j=0; $j <count($arrayDescuentos); $j++) { 
                  $cod_descuento_aux=$arrayDescuentos[$j];                          
                  $sqlDescuentos = "SELECT cod_descuento,monto
                        from descuentos_personal_mes 
                        where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and  cod_descuento=$cod_descuento_aux and cod_estadoreferencial=1";
                  $stmtDescuentos = $dbh->prepare($sqlDescuentos);
                  $stmtDescuentos->execute();
                  $resultDescOtros=$stmtDescuentos->fetch();
                  $montoX=$resultDescOtros['monto'];
                  if($montoX==""){
                    $montoX=0;
                  }
                  $montoX_tp=$montoX*$porcentaje/100;
                  switch ($cod_descuento_aux) {
                    case 1:
                        $subtotal_dprest+=$montoX_tp;
                    break;
                    case 2:
                        $subtotal_dinvt+=$montoX_tp;
                    break;
                    case 3:
                        $subtotal_dvencid+=$montoX_tp;
                    break;
                    case 4:
                        $subtotal_datraso+=$montoX_tp;
                    break;
                    case 5:
                        $subtotal_dfalcaj+=$montoX_tp;
                    break;
                    case 6:
                        $subtotal_dodesc+=$montoX_tp;
                    break;
                    case 100:
                        $subtotal_daposind+=$montoX_tp;
                    break;
                  }
                  // $sumaDescuentos_otros+=$montoX_tp;
                  $html.='<td  class="text-right"><small><small><small>'.formatNumberDec($montoX_tp).'</small></small></small></td>';
                }  
                
              $html.='<td class="text-right"><small><small><small>'.formatNumberDec($row['monto_descuentos']*$porcentaje/100).'</small></small></small></td>'.
                '<td class="text-right"><small><small><small>'.formatNumberDec($row['liquido_pagable']*$porcentaje/100).'</small></small></small></td>'.
                '<td><small><small><small></small></small></small></td>';
              $html.='</tr>';
              //suma de totales
              $subtotal_haberbasico_traba+=$row['haber_basico2']*$porcentaje/100;                  
              $subtotal_bantig+=$row['bono_antiguedad']*$porcentaje/100; 
              //$total_bonos_otros +=($monto_bonos_otros)*$porcentaje/100;
              $subtotal_totalganado+=$row['total_ganado']*$porcentaje/100;
              $subtotal_afpf+=$row['afp_1']*$porcentaje/100; 
              $subtotal_afpp+=$row['afp_2']*$porcentaje/100; 
              $subtotal_aporSol+=($row['a_solidario_13000']+$row['a_solidario_25000'])*$porcentaje/100; 
              $subtotal_rciva+=$rc_iva*$porcentaje/100;              
              //$total_otros_descuentos+=($sumaDescuentos_otros+$atrasos)*$porcentaje/100;
              $subtotal_anticipo+=$anticipo*$porcentaje/100;
              $subtotal_totdesc+=$row['monto_descuentos']*$porcentaje/100; 
              $subtotal_liqpag+=$row['liquido_pagable']*$porcentaje/100; 
              $index++;
            }
      $html.='</tbody>';
      $html.='<tfoot><tr>'.
          '<td style="border: 0;" colspan="8" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dias).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_haberbasico).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_haberbasico_traba).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bantig).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bnoche).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bdomin).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bferi).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bmov).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_brefr).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_breint).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bventas).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bfallo).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_bhrsex).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_totalganado).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_afpf).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_afpp).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_aporSol).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_rciva).'</b></small></small></small></small></td>'.              
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_anticipo).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dprest).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dinvt).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dvencid).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_datraso).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dfalcaj).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_dodesc).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_daposind).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_totdesc).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" class="text-right"><small><small><small><small><b>'.formatNumberDec($subtotal_liqpag).'</b></small></small></small></small></td>'.
          '<td style="border: 0;" ></td></tr>
      </tfoot>';
$html.='</table><br><br><br>';        

$html.='<table width="100%">
  <tr >
  <td width="25%"><center><p>______________________________<br>'.obtenerValorConfiguracionPlanillas(26).'<BR>JEFE DE RECURSOS HUMANOS COBOFAR S.A.</p></center></td>
  <td><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(24).'<BR>GERENTE GENERAL COBOFAR S.A.</p></center></td>
  <td width="25%"><center><p>______________________________<BR>'.obtenerValorConfiguracionPlanillas(25).'<BR>REPRESENTANTE LEGAL COBOFAR S.A.</p></center></td>
  </tr>
</table>';

$html.='</body>'.
      '</html>';           

$dbh=null;
$stmtBonos=null;
$stmtDescuento=null;
$stmtBonosOtrs=null;
$stmtDescuentos=null;

 // echo $html;

descargarPDFHorizontal("Planilla_".$mes."_".$gestion,$html);
?>
