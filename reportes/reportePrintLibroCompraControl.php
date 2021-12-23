<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
$cod_mes_x = $_POST["cod_mes_x"];

$stringMesX=implode(",", $cod_mes_x);

$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);

$nombre_gestion=nameGestion($gestion);
$nombre_mes=nombreMes($cod_mes_x[0]);
if(count($cod_mes_x)>1){
  $nombre_mes=nombreMes($cod_mes_x[0])."-".nombreMes($cod_mes_x[count($cod_mes_x)-1]);
}

if (isset($_POST["check_rs_librocompras"])) {
  $check_rs_librocompras=$_POST["check_rs_librocompras"]; 
  if($check_rs_librocompras){
    $razon_social=$_POST["razon_social"]; 
    $sql_rs=" and f.razon_social like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}

// echo $areaString;
$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra,cc.codigo as cod_comprobante,f.desc_total
  FROM facturas_compra f, comprobantes_detalle c, comprobantes cc 
  WHERE cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo and cc.cod_estadocomprobante<>2 and cc.cod_unidadorganizacional in ($stringUnidadesX) and MONTH(cc.fecha) in ($stringMesX) and YEAR(cc.fecha)=$nombre_gestion $sql_rs ORDER BY f.fecha asc, f.nit, f.nro_factura";

//echo $sql;

$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_comprobante', $codComprobante);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tipo_compra', $tipo_compra);  
$stmt2->bindColumn('desc_total', $desc_total);

$cant_unidad=sizeof($unidad);

if($cant_unidad>1){
  $cod_unidad_x=5;
}else{  
  
  if($stringUnidadesX==9 || $stringUnidadesX==10 ){
    $cod_unidad_x=$stringUnidadesX;
  }else{    
    $cod_unidad_x=5;
  }
}

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in ($cod_unidad_x)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

?>
 <script> 
          gestion_reporte='<?=$nombre_gestion;?>';
          mes_reporte='<?=$nombre_mes;?>';
 </script>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="60" height="50" src="../assets/img/favicon.png">
                  </div>                  
                  <h3 class="card-title text-center" ><b>Control de Compras IVA</b>
                    <span><br><h6>
                    Del Periodo: <?=$nombre_mes;?>/<?=$nombre_gestion;?><br>
                    Expresado En Bolivianos</h6></span></h3>                                    
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                        <table id="libro_compras_rep_2" class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr style="border:2px solid;">
                                  <th colspan="7" class="text-left"><small> Razón Social : <?=$razon_social?><br>Sucursal : <?=$sucursal?></small></th>   
                                  <th colspan="6" class="text-left"><small> Nit : <?=$nit?><br>Dirección : <?=$direccion?></small></th>   
                              </tr>
                              <tr >
                                  <th width="2%" style="border:2px solid;"><small><b>-</b></small></th>   
                                  <th style="border:2px solid;" width="6%"><small><small><b>C</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Fecha</b></small></small></th>                                
                                  <th style="border:2px solid;" width="6%"><small><small><b>NIT</b></small></small></th>
                                  <th style="border:2px solid;" width="20%"><small><small><b>Razón Social </b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Nro. Factura</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Autorización</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Imp Tot Compra</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Imp No Sujeto a IVA</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Sub Total</b></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Desc, Bonif, Y reba.</b></small></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><small><b>Importe Base para  IVA</b></small></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><small><b>Crédito Fiscal </b></small></small></small></th>
                                  <th style="border:2px solid;" width="6%"><small><small><b>Código de Control</b></small></small></th>  
                                  <th style="border:2px solid;" width="6%"><small><small><b>Tipo Compra</b></small></small></th> 
                              </tr>                                  
                            </thead>
                            <tbody>
                              <?php
                              $index=0; 
                              $total_importe=0;
                              $total_no_iva=0;
                              $total_descuentos_iva=0;
                              $total_importe_sujeto_iva=0;
                              $total_iva_obtenido=0;
                              $total_subtotal=0;
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                $importe=$importe+$desc_total;
                                $descuento_no_iva=$ice+$exento;
                                $subTotal=$importe-$descuento_no_iva;
                                $importe_sujeto_iva=$subTotal-$desc_total;
                                $iva_obtenido=$importe_sujeto_iva*13/100;
                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                if($codigo_control==null || $codigo_control==""){$codigo_control=0;}
                                $total_importe+=$importe;
                                $total_no_iva+=$descuento_no_iva;
                                $total_subtotal+=$subTotal;
                                $total_descuentos_iva+=$desc_total;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;
                                ?>
                                <tr>
                                  <td class="text-center small"><?=$index;?></td>
                                  <td class="text-center small"><?=$fecha;?></td>
                                  <td class="text-right small"><?=$nit;?></td>
                                  <td class="text-left small"><span style="padding-left: 15px;"><?=$razon_social;?></span></td>
                                  <td class="text-right small"><?=$nro_factura;?></td>
                                  <td class="text-right small"><?=$nro_autorizacion;?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($descuento_no_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($subTotal);?></td>
                                  <td class="text-right small"><?=formatNumberDec($desc_total);?></td>
                                  <td class="text-right small"><?=formatNumberDec($importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($iva_obtenido);?></td>
                                  <td class="text-center small"><?=$codigo_control;?></td>
                                  <td class="text-center small"><?=$tipo_compra?></td>                                   
                                </tr>
                                <?php                                  
                              }?>
                              <tr style="border:2px solid;">                               
                                 <td class="text-left small" colspan="3" style="border:2px solid;">CI:</td>
                                  <td class="text-left small" colspan="2" style="border:2px solid;">Nombre del Responsable:</td>
                                  <td class="text-center small"><b>SubTotal:</b></td>                                  
                                  <td class="text-right small"><?=formatNumberDec($total_importe);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_no_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_subtotal);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_descuentos_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_importe_sujeto_iva);?></td>
                                  <td class="text-right small"><?=formatNumberDec($total_iva_obtenido);?></td>
                                  <td class="text-right small"></td>
                                  <td class="text-right small"></td>
                                </tr>
                            </tbody>
                        </table>

                    

                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>

