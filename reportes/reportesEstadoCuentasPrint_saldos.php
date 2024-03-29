<?php //ESTADO FINALIZADO

// require_once __DIR__.'/../conexion.php';

// require_once __DIR__.'/../functions.php';
// require_once __DIR__.'/../functionsGeneral.php';
// require_once  __DIR__.'/../fpdf_html.php';
// require_once '../layouts/bodylogin2.php';
// $dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
// $gestion = $_POST["gestion"];
// $cuenta = $_POST["cuenta"];
// $unidad = $_POST["unidad"];
// $proveedores=$_POST["proveedores"];
// $fecha_desde=$_POST["fecha_desde"];
// $fecha=$_POST["fecha_hasta"];
// $desde=$fecha_desde;
// $hasta=$fecha;
// $tipo_cp=$_POST["tipo_cp"];
// $ver_saldo=$_POST["ver_saldo"];

$proveedoresString=implode(",", $proveedores);

$proveedoresStringAux="and d.cod_cuentaauxiliar in ($proveedoresString)";

if(count($proveedores)==(int)$_POST["numero_proveedores"]){
  $proveedoresStringAux="";
}


$StringCuenta=implode(",", $cuenta);
$StringUnidades=implode(",", $unidad);

$stringGeneraCuentas="";

foreach ($cuenta as $cuentai ) {    
    $stringGeneraCuentas.=nameCuenta($cuentai).",";
    # code...
}
$stringGeneraUnidades="";
foreach ($unidad as $unidadi ) {    
    $stringGeneraUnidades.=" ".abrevUnidad($unidadi)." ";
    # code...
}

$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

$i=0;$saldo=0;
$indice=0;
$totalCredito=0;
$totalDebito=0;

$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];

$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-icon">
                        <div class="float-right col-sm-2">
                            <h6 class="card-title">Exportar como:</h6>
                        </div>
                        <h4 class="card-title"> 
                            <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:50px;">
                            Estado de Cuentas
                        </h4>
                      <!-- <h4 class="card-title text-center">Reporte De Activos Fijos Por Unidad</h4> -->
                      <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                      <h6 class="card-title">Gestion: <?= $NombreGestion; ?></h6>
                      <h6 class="card-title">Cuenta: <?=$stringGeneraCuentas;?></h6>
                      <h6 class="card-title">Unidad:<?=$stringGeneraUnidades?></h6>             
                      <div class="row">
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Oficina: </b> <small><?=$unidadAbrev?></small></h6></div>
                        <div class="col-sm-6"><h5 class="card-title"><b>Centro de Costo - Area: </b> <small><?=$areaAbrev?></small></h6></div>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php 
                            $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixedEstadoCuentas">'.
                                '<thead>'.
                                    '<tr class="">'.
                                        '<th class="text-left">-</th>'.
                                        '<th class="text-left">Proveedor/Cliente</th>'.                                        
                                        '<th class="text-right">Debe</th>'.
                                        '<th class="text-right">Haber</th>'.
                                        '<th class="text-right">Saldo</th>'.
                                    '</tr>'.
                                '</thead>'.
                                '<tbody>';
                                foreach ($cuenta as $cuentai ) {
                                    $nombreCuenta=nameCuenta($cuentai);//nombre de cuenta
                                    $html.='<tr style="background-color:#9F81F7;">
                                        <td style="display: none;"></td>
                                        <td class="text-left small" colspan="2">CUENTA</td>
                                        <td class="text-left small" colspan="3">'.$nombreCuenta.'</td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                    </tr>'; 
                                    $sqlFechaEstadoCuenta="and e.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'"; 
                                        
                                    if(isset($_POST['cierre_anterior'])){
                                      $sqlFechaEstadoCuenta="and e.fecha<='$hasta 23:59:59'";  
                                    }

                                    $sql="SELECT d.cod_cuentaauxiliar as cod_cuentaaux,sum(e.monto)as monto_ec,sum(d.haber)as haber,sum(d.debe) as debe, (select ca.nombre from cuentas_auxiliares ca where ca.codigo=d.cod_cuentaauxiliar) as nombre,(SELECT c.tipo from configuracion_estadocuentas c where c.cod_plancuenta=d.cod_cuenta)as tipoDebeHaber
                                        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc
                                          where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen=0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) $proveedoresStringAux and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY d.cod_cuentaauxiliar  order by e.fecha"; //ca.nombre, 
                                     // echo $sql;
                                    $stmtUO = $dbh->prepare($sql);
                                    $stmtUO->execute();
                                    $index=1;
                                    while ($row = $stmtUO->fetch()) {
                                        $cod_cuentaauxX=$row['cod_cuentaaux'];
                                        $monto_ecX=$row['monto_ec'];
                                        $debeX=$row['debe'];
                                        $haberX=$row['haber'];
                                        $nombreX=$row['nombre'];
                                        $tipoDebeHaberX=$row['tipoDebeHaber'];   
                                        //PAGADO
                                        $sql="SELECT sum(e.monto)as monto_ec,sum(d.haber)as haber,sum(d.debe) as debe

                                        FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc, cuentas_auxiliares ca  where e.cod_comprobantedetalle=d.codigo and cc.codigo=d.cod_comprobante and e.cod_cuentaaux=ca.codigo and cc.cod_estadocomprobante<>2 and d.cod_cuenta in ($cuentai) and e.cod_comprobantedetalleorigen>0 and cc.cod_gestion= '$NombreGestion' $sqlFechaEstadoCuenta and cc.cod_unidadorganizacional in ($StringUnidades) and d.cod_cuentaauxiliar in ($cod_cuentaauxX) and d.cod_unidadorganizacional in ($unidadCostoArray) and d.cod_area in ($areaCostoArray) GROUP BY d.cod_cuentaauxiliar  order by e.fecha";      

                                         echo $sql;
                                        $stmt_d = $dbh->prepare($sql);
                                        $stmt_d->execute();
                                        $monto_ecD=0;
                                        $debeD=0;
                                        $haberD=0;
                                        while ($row_d = $stmt_d->fetch()) {
                                            $monto_ecD=$row_d['monto_ec'];
                                            $haberD=$row_d['haber'];
                                            $debeD=$row_d['debe'];
                                            // $tipoDebeHaberD=$row_d['tipoDebeHaber'];
                                        }
                                        
                                        if($tipoDebeHaberX==2){//proveedor
                                            $totalCredito=$totalCredito+$haberX;
                                            $totalDebito=$totalDebito+$debeD;
                                            $saldo_X=$haberX-$debeD;
                                            $errorX="";
                                            if($haberX<>$monto_ecX){
                                                $errorX="(*ERROR*)";
                                            }
                                            $errorD="";
                                            if($debeD<>$monto_ecD){
                                                $errorD="(*ERROR*)";
                                            }
                                            $html.='<tr class="bg-white" >
                                                <td class="text-center small">'.$index.'</td>
                                                <td class="text-left small">'.$nombreX.'</td>
                                                <td class="text-right small">'.formatNumberDec($debeD).' '.$errorD.'</td>
                                                <td class="text-right small">'.formatNumberDec($haberX).' '.$errorX.'</td>
                                                <td class="text-right small">'.formatNumberDec($saldo_X).'</td>
                                            </tr>'; 
                                        }else{
                                            $totalCredito=$totalCredito+$haberD;
                                            $totalDebito=$totalDebito+$debeX;
                                            $saldo_X=$debeX-$haberD;
                                            $errorX="";
                                            if($debeX<>$monto_ecX){
                                                $errorX="(*ERROR*)";
                                            }
                                            $errorD="";
                                            if($haberD<>$monto_ecD){
                                                $errorD="(*ERROR*)";
                                            }
                                            $html.='<tr class="bg-white" >
                                                <td class="text-center small">'.$index.'</td>
                                                <td class="text-left small">'.$nombreX.'</td>
                                                <td class="text-right small">'.formatNumberDec($debeX).' '.$errorX.'</td>
                                                <td class="text-right small">'.formatNumberDec($haberD).' '.$errorD.'</td>
                                                <td class="text-right small">'.formatNumberDec($saldo_X).'</td>
                                            </tr>'; 
                                        }
                                        $index++;
                                    }    
                                }
                                
                                $totalSaldo=$totalDebito-$totalCredito;
                                if($totalSaldo<0){
                                    $totalSaldo=$totalSaldo*(-1);
                                }                         
                                $html.='<tr>                                            
                                    
                                    <td style="display: none;"></td>
                                    <td class="text-right small" colspan="2">Total:</td>
                                    <td class="text-right small font-weight-bold">'.formatNumberDec($totalDebito).'</td>
                                    <td class="text-right small font-weight-bold">'.formatNumberDec($totalCredito).'</td>
                                    <td class="text-right small font-weight-bold">'.formatNumberDec($totalSaldo).'</td>
                                </tr>  
                                </tbody>
                            </table>';
                            echo $html;

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</div>

