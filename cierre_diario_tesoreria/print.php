<?php
ob_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();


$desde=$_GET['desde'];
$hasta=$_GET['hasta'];

if($desde==$hasta){
	$fechaCierre="Al ".date("d/m/Y",strtotime($hasta));
}else{
	$fechaCierre="Del ".date("d/m/Y",strtotime($desde))." Al ".date("d/m/Y",strtotime($hasta));
}



?>
<!-- formato cabeza fija para pdf-->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="../assets/libraries/plantillaPDFSolicitudesRecursos.css" rel="stylesheet" />
   </head><body>
<!-- fin formato cabeza fija para pdf--> 

<!--CONTENIDO-->
     <table class="table">
         <tr>
            <td class="s1 text-center" colspan="4">CORPORACION BOLIVIANA DE FARMACIAS S.A.</td>
            <td rowspan="4" class="text-center imagen-td"><img class="imagen-logo-izq_2" src="../assets/img/icono_sm_cobofar.jpg" width="90" height="90"></td>
        </tr>
        <tr>
            <td class="s2 text-center" colspan="4">CIERRE DIARIO DE TESORERIA</td>
        </tr>
        <tr>
            <td class="s2 text-center" colspan="4"><?=$fechaCierre?></td>
        </tr>
        <tr>
            <td class="s3 text-left bg-celeste">Expresado:</td>
            <td class="s3 text-left" colspan="3">Bolivianos</td>            
     </table>



  <table class="table">
                <thead>
                  <tr class="bg-celeste s3 text-center">
                    <td colspan="9">DETALLE DE MOVIMIENTOS</td> 
                  </tr>
                  <tr class="bg-celeste s3 text-center">
                    <td><small>#</small></td>
                    <td><small>FECHA</small></td>
                    <td><small>COMPROBANTE</small></td>
                    <td><small>TOKEN</small></td>
                    <td><small>CHEQUE</small></td> 
                    <td><small>GLOSA</small></td>  
                    <td><small>ENTRADA</small></td>  
                    <td><small>SALIDA</small></td>  
                    <td><small>SALDO</small></td>  
                  </tr>
                </thead>
                <tbody>
                  <?php 
                $dbh = new Conexion();

				// Preparamos
				$stmt = $dbh->prepare("SELECT c.codigo,t.nombre as tipo,c.fecha,c.comprobante,c.token,c.nro_trasaccion_cheque,c.glosa,c.importe,CONCAT(p.primer_nombre,' ',p.paterno) as personal,c.cod_tipocierre,c.cod_comprobante,c.created_by FROM cierre_tesoreria c join personal p on p.codigo=c.cod_personal join tipos_cierre t on t.codigo=c.cod_tipocierre
				where c.estado=1 and c.fecha>='$desde' and c.fecha<='$hasta' order by c.created_at;");
				// Ejecutamos
				$stmt->execute();
				// bindColumn
				$stmt->bindColumn('codigo', $codigoX);
				$stmt->bindColumn('tipo', $tipoX);
				$stmt->bindColumn('fecha', $fechaX);
				$stmt->bindColumn('comprobante', $comprobanteX);
				$stmt->bindColumn('token', $tokenX);
				$stmt->bindColumn('nro_trasaccion_cheque', $chequeX);
				$stmt->bindColumn('glosa', $glosaX);
				$stmt->bindColumn('importe', $importeX);
				$stmt->bindColumn('personal', $personalX);
				$stmt->bindColumn('cod_tipocierre', $cod_tipocierreX);
				$stmt->bindColumn('cod_comprobante', $cod_comprobanteX);
				$stmt->bindColumn('created_by', $created_byX);
                
                 $saldo=obtenerSaldoCierreTesoreria($desde);  
                 $saldo=number_format($saldo,2,'.','');                                
?>

                        <tr class="s3 text-center">
                          <th colspan="8">SALDO DIA ANTERIOR</th>
                          <th style="text-align: right"><?=number_format($saldo,2,'.',',');?></th>                          
                        </tr>
<?php
						$nombre_usuario="";
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
						$nombre_usuario=namePersonal_2($created_byX);                          
                        $entrada="";    
                        $salida="";    
                        if($cod_tipocierreX==1){
                          $saldo=$saldo+number_format($importeX,2,'.','');
                          $entrada=number_format($importeX,2,'.',',');  
                        }else{
                          $saldo=$saldo-number_format($importeX,2,'.','');
                          $salida=number_format($importeX,2,'.',',');  
                        }              
                        
?>
                        <tr class="s3 text-center">
                          <td align="center"><?=$index;?></td>
                          <td><?=date("d/m/Y",strtotime($fechaX));?></td>
                          <td><b><?=$comprobanteX;?></b></td>
                          <td><?=$tokenX;?></td>
                          <td><?=$chequeX;?></td>
                          <td style="text-align: left"><?=$glosaX;?></td>
                          <td style="text-align: right"><?=$entrada;?></td>
                          <td style="text-align: right"><?=$salida;?></td>
                          <td style="text-align: right"><?=number_format($saldo,2,'.',',');?></td>                          
                        </tr>
<?php
							$index++;
                      }
?>
                </tbody>
                <tfoot>
                        <tr class="s3 text-center">
                          <th colspan="8">SALDO FINAL</th>
                          <th style="text-align: right"><?=number_format($saldo,2,'.',',');?></th>
                        </tr>
                </tfoot>
              </table>
<table class="table">
     <tr class="s3 text-center" valign="top">
       <td width="25%" class="text-center"><p>&nbsp;<br>&nbsp;</p></td>
       <td width="25%" class="text-center"></td>
       <td width="25%" class="text-center"></td>


       <!-- <td width="25%" class="text-center"></td>
       <td width="25%" class="text-center"></td> -->
       <!-- <td width="25%" class="text-center"><p>&nbsp;<br>&nbsp;</p></td>

       <td width="25%" class="text-left" ><p>Firma/Sello  _____________<br>Nombre:</p></td> -->
       
     
     </tr>
     <tr class="s3 text-center" valign="top">
       <td width="25%" class="text-center">ELABORADO POR <br><?=$nombre_usuario?></td>
       <td width="25%" class="text-center">CONTABILIDAD<br></td>
       <td width="25%" class="text-center">G.A.F.<br>&nbsp;</td>
       <!-- <td width="25%" class="text-center"></td>
       <td width="25%" class="text-center"></td> -->

       <!-- <td width="25%" class="text-center">CONTABILIDAD<br></td>
       <td width="25%" class="text-center">G.A.F.<br>&nbsp;</td>
       <td width="25%" class="text-center">GERENCIA GRAL.<br></td>
       <td width="25%" class="text-left">C.I. Nº</td> -->
     </tr>
</table>
<!-- PIE DE PAGINA-->     
     <!-- <footer class="footer">
        <table class="table">
          <tr>
            <td class="s4 text-left" width="25%">COBOFAR</td>
            <td class="s4 text-left" width="25%">Codigo: REG-PRE-SA-04-01.05</td>
            <td class="s4 text-left" width="25%">V: 2015-09-21</td>
            <td class="s4 text-left" width="25%"></td>
          </tr>
       </table>
     </footer> -->


<!-- FIN CONTENIDO-->

<!-- formato pie fijo para pdf-->  
</body></html>
<!-- fin formato pie fijo para pdf-->


<?php

$html = ob_get_clean();
descargarPDFSolicitudesRecursos("COBOFAR - Solicitud Recursos",$html);