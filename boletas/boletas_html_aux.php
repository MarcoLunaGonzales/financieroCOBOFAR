<?php

	$html.='<div  style="height: 49.4%">';
			$html.='<table width="100%" class="table">
				<tr><td colspan="2" >
					<table width="100%">
					<tr><td width="25%" style="border: 0;"><p><b>CORPORACION BOLIVIANA DE FARMACIAS</b><br>Av.Landaeta Nro 836<br>La Paz - Bolivia<br>NIT:1022039027</p></td>
						<td width="25%" style="border: 0;"><center><span style="font-size: 13px"><b>PAPELETA DE SUELDOS</b></span><br><b>EXPRESADA EN BOLIVIANOS</b></center></td>
						<td width="25%" style="border: 0;"><center><table width="100%"><tr><td style="border: 0;align:left" width="70%">N° PAT. 651-1-956</td><td style="border: 0;" width="30%"><img class="" width="50" height="40" src="../assets/img/favicon.png"></td></tr></table></center></td>
					</tr>
					</table>
				</td></tr>
				<tr><td colspan="2">
					<p>
					<b>MES:</b> '.$mes.' de '.$gestion.'<br>
					<b>NOMBRE:</b> '.$result['apellidos'].' '.$result['nombres'].'<BR>
					<b>CARGO:</b> '.$result['cargo'].'<BR>
					<b>HABER BASICO:</b> '.formatNumberDec($result['haber_basico_pactado']).' BS <br>
					<b>DIAS TRAB:</b> '.$result['dias_trabajados'].'<br><b>Nro. Pla: </b>'.$index_planilla.' </p>
				</td></tr>
				<tr><td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>INGRESOS</b></center></td></tr>
						<tr>
							<td class=text-left" style="border: 0;" valign="top"><p>Haber Basico días<br>Bono Antiguedad<br>Com Sobre Ventas<br>Fallo de Caja<br>Hras Noche<br>Hrs Domingo<br>Hrs. Feriado<br>Hrs. Extraordinarias<br>Reintegros<br>Movilidad<br>Refrigerio</p></td>
							<td class="text-right" style="border: 0;" valign="top"><p>'.formatNumberDec($haber_basico_dias).'<br>'.formatNumberDec($bono_antiguedad).'<br>'.formatNumberDec($com_ventas).'<br>'.formatNumberDec($fallo_caja).'<br>'.formatNumberDec($hrs_noche).'<br>'.formatNumberDec($hras_domingo).'<br>'.formatNumberDec($hrs_feriado).'<br>'.formatNumberDec($hras_extraordianrias).'<br>'.formatNumberDec($reintegro).'<br>'.formatNumberDec($movilidad).'<br>'.$refrigerio.'<BR>&nbsp;<BR>&nbsp;</p></td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><p><b>Total Ingresos:</b></p></td>
							<td class="text-right" style="border: 0;"><p>'.formatNumberDec($suma_ingresos).'</p></td>
						</tr>
					</table>
				</td>
				<td width="50%" valign="top">
					<table width="100%">
						<tr><td colspan="2" style="background:#F2F2F2;border: 0;"><center><b>DEDUCCIONES</b></center></td></tr>
						<tr>
							<td class="text-left" style="border: 0;" valign="top"><p>Ap. Vejez 10%<br>Riesgo Prof. 1.71%<br>Com.AFP 0.5%<br>Apo.Sol 0.5%<br>RC IVA<br>Anticipos<br>Prestamos<br>Inventario<br>Vencidos<br>Atrasos<br>Faltantes Caja<br>Otros Descuentos<br>Aporte Sindical</p></td>
							<td class="text-right" style="border: 0;" valign="top"><p>'.formatNumberDec($Ap_Vejez).'<br>'.formatNumberDec($Riesgo_Prof).'<br>'.formatNumberDec($ComAFP).'<br>'.formatNumberDec($aposol).'<br>'.formatNumberDec($RC_IVA).'<br>'.formatNumberDec($Anticipos).'<br>'.formatNumberDec($Prestamos).'<br>'.formatNumberDec($Inventario).'<br>'.formatNumberDec($Vencidos).'<br>'.formatNumberDec($Atrasos).'<br>'.formatNumberDec($Faltantes_Caja).'<br>'.formatNumberDec($Otros_Descuentos).'<br>'.formatNumberDec($Aporte_Sindical).'</p></td>
						</tr>
						<tr>
							<td class=text-left" style="border: 0;"><p><b>Total Egresos:</b></p></td>
							<td class="text-right" style="border: 0;"><p>'.formatNumberDec($suma_egresos).'</p></td>
						</tr>
					</table>
				</td></tr>
				<tr><td colspan="2" class="text-right" style="background:#F2F2F2;">
					<b>Liquido Pagable: '.formatNumberDec($liquido_pagable).'
				</b></td></tr>
			</table>';
			$html.='<table width="100%">
				<tr>
					<td><center><p>______________________________<br>Recibí Conforme</p></center></td>
				</tr>
			</table>';
			 // $html.='<div style="page-break-after: always"></div>';
	$html.='</div>';


?>