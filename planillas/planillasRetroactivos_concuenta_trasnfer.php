<meta charset="utf-8">
<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$cod_planilla = $_GET["codigo_planilla"];//
$cod_gestion = $_GET["cod_gestion"];//
$cod_mes = $_GET["cod_mes"];//
$mes=strtoupper(abrevMes($cod_mes));
$gestion=nameGestion($cod_gestion);

header("Pragma: public");
header("Expires: 0");
$filename = "ARCHIVO BMSC".$mes.$gestion.".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");


$dbh = new Conexion();
echo '<table class="table">
    <thead>
    	<tr >                  
        <td >CI o NIT</td> 
        <td >Nombre del Beneficiario</td>
        <td >Cuenta destino BMSC</td>
        <td >Fecha de Pago</td>
        <td >Tipo de Pago</td>
        <td >Importe a Abonar</td>
        <td >Codigo otro Banco</td>
        <td >Cuenta otro Banco</td>
        <td>Detalle</td>
    	</tr>                                  
    </thead>
    <tbody>';
    $sql = "SELECT pad.identificacion,CONCAT_WS(' ',pad.paterno,pad.materno,pad.primer_nombre)as personal,pad.cuenta_bancaria,DATE_FORMAT(NOW(),'%d/%m/%Y') as fecha,ppm.liquido_pagable
				from planillas_retroactivos_detalle ppm join personal pad on ppm.cod_personal=pad.codigo join areas a on pad.cod_area=a.codigo
	where cod_planilla=$cod_planilla and ppm.cuenta_habilitada=1 order by pad.paterno";//pad.cod_unidadorganizacional,a.nombre,pad.turno,
		// echo $sql."<br><br>";
	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();	
	$stmtPersonal->bindColumn('identificacion', $identificacion);
	$stmtPersonal->bindColumn('personal', $personal);
	$stmtPersonal->bindColumn('cuenta_bancaria', $cuenta_bancaria);
	$stmtPersonal->bindColumn('fecha', $fecha);
	$stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
	
	while ($row = $stmtPersonal->fetch()) 
	{  
    	echo '<tr>
          <td class="text-left small"><small>'.$identificacion.'</small></td>
          <td class="text-left small"><small>'.$personal.'</small></td>
          <td class="text-left small"><small>'.$cuenta_bancaria.'</small></td>
          <td class="text-left small"><small>'.$fecha.'</small></td>
          <td class="text-left small"><small>1</small></td>
          <td class="text-right small"><small>'.round($liquido_pagable,2).'</small></td>
          <td class="text-right small"><small></small></td>
          <td class="text-right small"><small></small></td>
          <td class="text-left small"><small>RETRO.'.$gestion.'</small></td>
      	</tr>';
  	}                
    echo '</tbody>
 	</table>';
      $dbh=null;
      $stmtPersonal=null;

?>

