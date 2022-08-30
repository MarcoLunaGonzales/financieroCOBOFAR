<?php
require_once '../conexion3.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
// session_start();
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
$porcentaje_aport_afp=obtenerValorConfiguracionPlanillas(12);
$porcentaje_aport_sol=obtenerValorConfiguracionPlanillas(15);

//html del reporte
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF_planillas.css" rel="stylesheet" />'.
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
              <td><center><span style="font-size: 13px"><b>PLANILLA DE SUELDOS Y SALARIOS</b></span><BR>Correspondientes al mes de '.$mes.' '.$gestion.'<br><b>EXPRESADO EN BOLIVIANOS</b></center></td>
              <td width="25%"><center>N° PAT. 651-1-956</center></td>
              </tr>
            </table>'.
         '</header>';

          $html.='<table class="table" style="font-size: 5px !important;">'.
            '<thead >'.
            '<tr class="table-title bold text-center" style="font-size: 7px !important;">'.
              '<td width="1%">Nro</td>'.
              '<td>CI EXT</td>'.
              '<td width="3%">Paterno</td>'.
              '<td width="3%">Materno</td>'.
              '<td width="4%">Nombre</td>'.
              '<td width="2%">Sex</td>'.
              '<td width="3%">Fech Nac</td>'.
              '<td width="7%">Cargo</td>'.
              '<td width="3%">Fech Ing</td>'.
              '<td width="2%">Hrs Trab</td>'.
              '<td width="2%">Días Trab</td>'.
              '<td width="3%">Haber Basico</td>'.
              '<td width="3%">Haber Basico DTrab</td>'.
              '<td>Bono Ant</td>'.
              '<td>Noch</td>'.
              '<td>Dom</td>'.
              '<td>Feri ado</td>'.
              '<td>Mov</td>'.
              '<td>Refr</td>'.
              '<td>Rein tegro</td>'.
              '<td>Com Ven</td>'.
              '<td>Fallo</td>'.
              '<td>Hrs Extr</td>'.
              '<td width="3%">Tota Gana</td>'.
              '<td>Ap Vejez 10%</td>'.
              '<td>Ries Prof 1.71%</td>'.
              '<td>Com Afp 0.5%</td>'.
              '<td>Ap Sol 0.5%</td>'.
              '<td>Ap Sol 13</td>'.
              '<td>RC-IVA</td>'.              
              '<td>Anti cipo</td>'.
              '<td>Pres tamo</td>'.
              '<td>In vent</td>'.
              '<td>Ven cid</td>'.
              '<td>Atr aso</td>'.
              '<td>Fal Caj</td>'.
              '<td>O Desc</td>'.
              '<td>Apo Sind</td>'.
              '<td>Tot Desc</td>'.
              '<td width="3%">Liq Pag</td>';
            $html.='</tr>'.
           '</thead>'.
           '<tbody>';
            $index=1;
            $codArea=0;
            $cod_turno_aux=0;
            $rc_iva=0;
            // $atrasos=0;
            $anticipo=0;
            // $monto_bonos_otros=0;
            // $data = obtenerPlanillaSueldosRevision($codPlanilla);
            $sql="SELECT p.codigo,pm.cod_area,a.nombre as area, p.primer_nombre as nombres,p.paterno,p.materno,
            p.identificacion as ci,p.ing_planilla,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,pm.haber_basico_pactado,pm.haber_basico as haber_basico2,
            pm.dias_trabajados,pm.bono_academico,pm.bono_antiguedad,pm.total_ganado,pm.monto_descuentos,pm.liquido_pagable,pm.afp_1,pm.afp_2,pad.porcentaje,pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.rc_iva,pp.atrasos,pp.anticipo,pp.seguro_de_salud,pp.riesgo_profesional,p.fecha_nacimiento,(select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision) as emision,(select tg.abreviatura from tipos_genero tg where tg.codigo=p.cod_genero)as genero,(select pp2.abreviatura from personal_pais pp2 where pp2.codigo=p.cod_nacionalidad) as nacionalidad,pm.turno,pm.correlativo_planilla
            FROM personal p
            join planillas_personal_mes pm on pm.cod_personalcargo=p.codigo
              join planillas_personal_mes_patronal pp on pp.cod_planilla=pm.cod_planilla and pp.cod_personal_cargo=pm.cod_personalcargo
            join areas a on pm.cod_area=a.codigo
            join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1
            where pm.cod_planilla=$codPlanilla and p.cod_cajasalud=2 
            order by pm.correlativo_planilla";
            // echo $sql;
            $stmt = $dbh->prepare($sql);
            $stmt->execute();

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
            
            $subtotal_seguro_salud=0;
            $subtotal_riesgo=0;
            $subtotal_comafp=0;
            $subtotal_apsol=0;
            $subtotal_apsol13=0;

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
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

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
                case 3:
                  $turno_nombre="";
                  break;
              }
              if($cod_turno==3){
                if($codArea!=$row['cod_area']){
                  $html.='<tr style="font-size: 7px !important;">'.
                        '<td colspan="40"><center>Departamento / Sucursal: <b>'.$row['area'].'</b></center></td>';
                      $html.='</tr>';
                  $codArea=$row['cod_area'];
                }
              }else{
                if($cod_turno_aux!=$cod_turno){
                  $html.='<tr style="font-size: 7px !important;">'.
                        '<td colspan="40"><center>Departamento / Sucursal: <b>'.$row['area'].' '.$turno_nombre.'</b></center></td>';
                      $html.='</tr>';
                  $cod_turno_aux=$cod_turno;
                }  
              }

              $html.='<tr>'.
                '<td class="text-center">'.$index.'</td>'.
                '<td class="text-left">'.$row['ci'].' '.$emision.'</td>'.
                '<td class="text-left">'.$row['paterno'].'</td>'.
                '<td class="text-left">'.$row['materno'].'</td>'.
                '<td class="text-left">'.$row['nombres'].'</td>'.
                '<td class="text-left">'.$sexo.'</td>'.
                // '<td><small><small><small>'.$nacion.'</small></small></small></td>'.
                '<td>'.strftime('%d/%m/%Y',strtotime($fechaNac)).'</td>'.
                '<td class="text-left">'.$row['cargo'].'</td>'.
                // '<td class="text-left">'.$turno_nombre.'</td>'.
                '<td class="text-left">'.strftime('%d/%m/%Y',strtotime($row['ing_planilla'])).'</td>'.
                '<td class="text-right">'.$hrsTrabajadas.'</td>'.
                '<td class="text-right">'.$dias_trabajados_planilla.'</td>'.
                '<td class="text-right">'.formatNumberDec($row['haber_basico_pactado']*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($row['haber_basico2']*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($row['bono_antiguedad']*$porcentaje/100).'</td>';
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
                      $html.='<td  class="text-right">'.formatNumberDec($montoX_refri*$porcentaje/100).'</td>';
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
                    $html.='<td  class="text-right">'.formatNumberDec($montoX_aux*$porcentaje/100).'</td>';  
                  }
                }
                $html.='<td class="text-right">'.formatNumberDec($row['total_ganado']*$porcentaje/100).'</td>';

                $seguro_de_salud=$row['seguro_de_salud'];//10%
                $riesgo_profesional=$row['riesgo_profesional'];//1.71%
                $ComAFP=$row['total_ganado']*$porcentaje_aport_afp/100;//0.5%
                $aposol=$row['total_ganado']*$porcentaje_aport_sol/100;//0.5%
                $aporte_sol13=$row['a_solidario_13000']+$row['a_solidario_25000']+$row['a_solidario_35000'];

                $html.='<td class="text-right">'.formatNumberDec($seguro_de_salud*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($riesgo_profesional*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($ComAFP*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($aposol*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($aporte_sol13*$porcentaje/100).'</td>';

                $html.='<td class="text-right">'.formatNumberDec($rc_iva*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($anticipo*$porcentaje/100).'</td>';
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
                  $html.='<td  class="text-right">'.formatNumberDec($montoX_tp).'</td>';
                }  
                
              $html.='<td class="text-right">'.formatNumberDec($row['monto_descuentos']*$porcentaje/100).'</td>'.
                '<td class="text-right">'.formatNumberDec($row['liquido_pagable']*$porcentaje/100).'</td>';
              $html.='</tr>';
              //suma de totales
              $subtotal_haberbasico_traba+=$row['haber_basico2']*$porcentaje/100;                  
              $subtotal_bantig+=$row['bono_antiguedad']*$porcentaje/100; 
              //$total_bonos_otros +=($monto_bonos_otros)*$porcentaje/100;
              $subtotal_totalganado+=$row['total_ganado']*$porcentaje/100;
                
    

              $subtotal_seguro_salud+=$seguro_de_salud*$porcentaje/100; 
              $subtotal_riesgo+=$riesgo_profesional*$porcentaje/100; 
              $subtotal_comafp+=$ComAFP*$porcentaje/100; 
              $subtotal_apsol+=$aposol*$porcentaje/100; 
              $subtotal_apsol13+=$aporte_sol13*$porcentaje/100; 
              
              $subtotal_rciva+=$rc_iva*$porcentaje/100;              
              //$total_otros_descuentos+=($sumaDescuentos_otros+$atrasos)*$porcentaje/100;
              $subtotal_anticipo+=$anticipo*$porcentaje/100;
              $subtotal_totdesc+=$row['monto_descuentos']*$porcentaje/100; 
              $subtotal_liqpag+=$row['liquido_pagable']*$porcentaje/100; 
              $index++;
            }
      $html.='</tbody>';
      $html.='<tfoot><tr>'.
          '<td style="border: 0;" colspan="11" class="text-center"><b>'.formatNumberDec($subtotal_dias).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_haberbasico).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_haberbasico_traba).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bantig).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bnoche).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bdomin).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bferi).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bmov).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_brefr).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_breint).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bventas).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bfallo).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_bhrsex).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_totalganado).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_seguro_salud).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_riesgo).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_comafp).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_apsol).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_apsol13).'</b></td>'.

          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_rciva).'</b></td>'.              
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_anticipo).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_dprest).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_dinvt).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_dvencid).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_datraso).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_dfalcaj).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_dodesc).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_daposind).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_totdesc).'</b></td>'.
          '<td style="border: 0;" class="text-center"><b>'.formatNumberDec($subtotal_liqpag).'</b></td>'.
          '</tr>
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

 descargarPDFHorizontal("Planilla_CPS_".$mes."_".$gestion,$html);
?>
