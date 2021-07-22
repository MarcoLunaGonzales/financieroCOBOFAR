<?php

error_reporting(0);
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

try{
    $stmt = $dbh->prepare("SELECT Nro, CONCAT_WS('    ',PATERNO,MATERNO,NOMBRES) AS NOMBRE, CARGO,TOTAL_GANADO,AP_VEJEZ_10,RIESGO_P_1_71,COM_AFP_05,APORT_SOL_05,TOTAL_DESCUENTOS,LIQUIDO_PAGABLE from planillas_sueldos order by Nro");
    //Ejecutamos
    $stmt->execute();
    // $result = $stmt->fetch();
    


$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             
             '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      
    '</script>';
    $index=1;
while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	$Nro = $index;
	    $NOMBRE = $result['NOMBRE'];
	    $CARGO = $result['CARGO'];
	    $TOTAL_GANADO = $result['TOTAL_GANADO'];
	    $AP_VEJEZ_10 = $result['AP_VEJEZ_10'];
	    $RIESGO_P_1_71 = $result['RIESGO_P_1_71'];
	    $COM_AFP_05 = $result['COM_AFP_05'];
	    $APORT_SOL_05 = $result['APORT_SOL_05'];
	    $TOTAL_DESCUENTOS = $result['TOTAL_DESCUENTOS'];
	    $LIQUIDO_PAGABLE = $result['LIQUIDO_PAGABLE'];
			$html.='<table width="100%" class="table"><tr>
					<td >'.
						'<table width="100%">'.
			               '<tbody>'.
			                '<tr align="left">'.
			                    '<td  class="td-border-none">'.
			                        '<table>'.
			                                '<tr>
			                                	<td align="left" width="37%" class="td-border-none">
								                    <span><b>CORPORACION BOLIVIANA DE FARMACIAS S.A.</b></span><br><br>
								                      <span><small><small>Av. Landaeta  Nro. 836 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; N° PAT 651 - 1 - 956<br><br>
								                      LA PAZ - BOLIVIA<br><br>
								                      NIT: 1022039027<br><br>
								                    </span></small></small>
								                </td>
			                                </tr>'.                                
			                        '</table>'.
			                    '</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>PAPELETA DE SUELDOS</b>
								</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>RETROACTIVOS Gestion 2021</b>
								</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>(En Bolivianos)</b>
								</td>'.
			                '</tr>'.
			                '<tr align="left">'.
			                    '<td class="td-border-none"><b>NOMBRE: </b>'.$NOMBRE.'
								</td>'.
			                '</tr>'.
			                '<tr align="left">'.
			                    '<td class="td-border-none"><b>CARGO: </b>'.$CARGO.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Nro:</b> '.$Nro.'
								</td>'.
			                '</tr>'.
			                
			                '<tr align="left">'.
			                    '<td class="td-border-none">
			                    	<table width="100%">'.
				                        '<tr>
				                        	<td align="center" width="50%" class="td-border-none">
							                    <span><b>INGRESOS</b></span>
							                </td>
							                <td align="center" width="50%" class="td-border-none">
							                    <span><b>EGRESOS</b></span>
							                </td>
				                        </tr>'.  
				                        '<tr>
				                        	<td align="left" class="td-border-none">
							                    <span>Reintegros: '.number_format($TOTAL_GANADO,2,'.',',').'</span>
							                </td>
							                <td align="right" class="td-border-none">
							                    <span>Ap. Vejez '.number_format($AP_VEJEZ_10,2,'.',',').'</span><br><br>
							                      <span>Riesgo Prof. '.number_format($RIESGO_P_1_71,2,'.',',').'<br><br>
							                      Com.AFP 0.5% '.number_format($COM_AFP_05,2,'.',',').'<br><br>
							                      Apo.Sol 0.5% '.number_format($APORT_SOL_05,2,'.',',').'<br><br>
							                    </span>
							                </td>
				                        </tr>'. 
				                        '<tr>
				                        	<td align="left" class="td-border-none">
							                    <span><b>Total: '.number_format($TOTAL_GANADO,2,'.',',').'</b></span>
							                </td>
							                <td align="right" class="td-border-none">
							                    <span><b>Total Egresos: '.number_format($TOTAL_DESCUENTOS,2,'.',',').'</b>
							                    </span>
							                </td>
				                        </tr>'.  
				                        '<tr>
				                        	<td align="left" colspan="2" class="td-border-none">
							                    <span>Liquido Pagable: '.number_format($LIQUIDO_PAGABLE,2,'.',',').'</span>
							                </td>
							                
				                        </tr>'.                                
			                        '</table>
								</td>'.
			                '</tr>'.
			                '<tr align="center">
			                	<td class="td-border-none"><br><br><br>Recibi Conforme</td>
			                </tr>'.
			                '</tbody>'.
			            '</table>'. 
					'</td>
					<td class="td-border-none" width="5%"></td>
					<td >'.
						'<table width="100%">'.
			               '<tbody>'.
			                '<tr align="left">'.
			                    '<td  class="td-border-none">'.
			                        '<table>'.
			                                '<tr>
			                                	<td align="left" width="37%" class="td-border-none">
								                    <span><b>CORPORACION BOLIVIANA DE FARMACIAS S.A.</b></span><br><br>
								                      <span><small><small>Av. Landaeta Nro. 836 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  N° PAT 651 - 1 - 956<br><br>
								                      LA PAZ - BOLIVIA<br><br>
								                      NIT: 1022039027<br><br>
								                    </span></small></small>
								                </td>
			                                </tr>'.                                
			                        '</table>'.
			                    '</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>PAPELETA DE SUELDOS</b>
								</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>RETROACTIVOS Gestion 2021</b>
								</td>'.
			                '</tr>'.
			                '<tr align="center">'.
			                    '<td class="td-border-none"><b>(En Bolivianos)</b>
								</td>'.
			                '</tr>'.
			                '<tr align="left">'.
			                    '<td class="td-border-none"><b>NOMBRE: </b>'.$NOMBRE.'
								</td>'.
			                '</tr>'.
			                '<tr align="left">'.
			                    '<td class="td-border-none"><b>CARGO: </b>'.$CARGO.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Nro:</b> '.$Nro.'
								</td>'.
			                '</tr>'.
			                
			                '<tr align="left">'.
			                    '<td class="td-border-none">
			                    	<table width="100%">'.
				                        '<tr>
				                        	<td align="center" width="50%" class="td-border-none">
							                    <span><b>INGRESOS</b></span>
							                </td>
							                <td align="center" width="50%" class="td-border-none">
							                    <span><b>EGRESOS</b></span>
							                </td>
				                        </tr>'.  
				                        '<tr>
				                        	<td align="left" class="td-border-none">
							                    <span>Reintegros: '.number_format($TOTAL_GANADO,2,'.',',').'</span>
							                </td>
							                <td align="right" class="td-border-none">
							                    <span>Ap. Vejez '.number_format($AP_VEJEZ_10,2,'.',',').'</span><br><br>
							                      <span>Riesgo Prof. '.number_format($RIESGO_P_1_71,2,'.',',').'<br><br>
							                      Com.AFP 0.5% '.number_format($COM_AFP_05,2,'.',',').'<br><br>
							                      Apo.Sol 0.5% '.number_format($APORT_SOL_05,2,'.',',').'<br><br>
							                    </span>
							                </td>
				                        </tr>'. 
				                        '<tr>
				                        	<td align="left" class="td-border-none">
							                    <span><b>Total: '.number_format($TOTAL_GANADO,2,'.',',').'</b></span>
							                </td>
							                <td align="right" class="td-border-none">
							                    <span><b>Total Egresos: '.number_format($TOTAL_DESCUENTOS,2,'.',',').'</b>
							                    </span>
							                </td>
				                        </tr>'.  
				                        '<tr>
				                        	<td align="left" colspan="2" class="td-border-none">
							                    <span>Liquido Pagable: '.number_format($LIQUIDO_PAGABLE,2,'.',',').'</span>
							                </td>
							                
				                        </tr>'.                                
			                        '</table>
								</td>'.
			                '</tr>'.
			                '<tr align="center">
			                	<td class="td-border-none"><br><br><br>Recibi Conforme</td>
			                </tr>'.
			                '</tbody>'.
			            '</table>'. 
					'</td>
					</tr>
					</table>';
			$html.='<div style="page-break-after: always"></div>';
			$index++;
}


    '</body>'.
	'</html>';

echo $html;
//descargarPDFHorizontal_boletas("COBOFAR - ",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>