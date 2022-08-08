<?php
set_time_limit(0);

require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functions.php';
require("../conexion_comercial.php");

 $id_proveedor=obtenerProveedor_presentacionAlmacen_nuevo();
$sucursal=implode(",",$_POST['sucursal']);
$fechai=$_POST['fechainicio'];
$fechaf=$_POST['fechafin'];

$array_proveedores=$id_proveedor;
$stringProveedor=implode(",",$id_proveedor);
$array_datosproveedor=obtenerProveedornombre_presentacionAlmacen_nuevo($stringProveedor);
$nombre_proveedor_array=$array_datosproveedor;



$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($fechai))." al ".strftime('%d/%m/%Y',strtotime($fechaf));
 // echo $sql;

$totalValoradoGen=0;$totalSaldoIniGen=0;$totalIngresosValGen=0;$totalTraspasosValGen=0;$totalVentasValGen=0;

$totalIngresosAlmaAntValGen=0;
$totalIngresosSucAntValGen=0;
$totalIngresosAlmaActValGen=0;
$totalIngresosSucActValGen=0;
$totalIngresosOtroActValGen=0;
$totalTraspasosVenValGen=0;
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="50" height="40" src="../assets/img/favicon.png">
            </div>
             <h4 class="card-title text-center">REPORTE MOVIMIENTOS  COSTOS - SUCURSAL DETALLADO</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title" id="nombre_sucursal">Sucursal:</h6>  
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">

<table class="table table-bordered table-condensed" id="tablePaginator1001">
  <thead>
    <tr>
      
      <th colspan="23" align="center"><b>REPORTE MOVIMIENTOS  COSTOS - SUCURSAL DETALLADO</b></th></tr>
    <tr><th rowspan="3">CODIGO</th>
      <th rowspan="3">SUCURSAL</th>
      <th rowspan="3">Saldo Ant.</th><th colspan="5">INGRESOS</th><th rowspan="3">Traspasos<br>Sucursales</th>
      <th rowspan="3">Traspasos<br>Vencidos</th>
      <th rowspan="3">Ventas</th>
      <th rowspan="3">Saldo Final</th></tr>
    <tr><th colspan="2">ENVIADOS EL MES ANTERIOR</th><th colspan="2">ENVIADOS EL MES ACTUAL</th><th rowspan="2">Ingresos<br>Otros</th><!-- <th colspan="3">MES POSTERIOR</th> --></tr>
    <tr style="border:1px;">
      
      <th>Ingresos<br>Almacen Central</th>
      <th>Ingresos<br>Sucursales</th>
      <!-- <th>Ingresos<br>Otros</th> -->
      <th>Ingresos<br>Almacen Central</th>
      <th>Ingresos<br>Sucursales</th>
      
<!--       <th>Ingresos<br>Almacen Central</th>
      <th>Ingresos<br>Sucursales</th>
      <th>Ingresos<br>Otros</th> -->
      
      <?php
      if($_POST['tipo_cerrada']==0){
        $stringBusqueda=" cod_almacen in ($sucursal)";
      }else{
        $stringBusqueda=" (a.estado_pedidos!=1 or a.estado_pedidos is null  or cod_almacen in ($sucursal)) and cod_tipoalmacen=1 and c.cod_impuestos>0";
      }
        $sql_listsucursales="SELECT a.cod_almacen,a.nombre_almacen
          from almacenes a join ciudades c on a.cod_ciudad=c.cod_ciudad 
          where $stringBusqueda
          order by a.nombre_almacen";
          //echo $sql_listsucursales;
        $resp=mysqli_query($dbh,$sql_listsucursales);
        $string_sucursales="";
        $array_sucursales_nombres=[];
        while($row=mysqli_fetch_array($resp)){
          $cod_almacen=$row['cod_almacen'];
          $nombre_almacen=$row['nombre_almacen'];          
          $informacion=cargarValoresVentasYSaldosProductosArray_prodrotacion_provIngresoDetalle($cod_almacen,$fechai,$fechaf,$stringProveedor);
          // $informacion=cargarValoresVentasYSaldosProductosArray_prodrotacion_provPromedio($cod_almacen,$fechai,$fechaf,$stringProveedor);
          $datosSucursal[$cod_almacen]=$informacion;
          $string_sucursales.=$cod_almacen.",";
          $array_sucursales_nombres[$cod_almacen]=$nombre_almacen;
        }
          $string_sucursales=trim($string_sucursales,",");
          $array_sucursales=explode(",", $string_sucursales);

          $sucursalesGlosa=implode(",",$array_sucursales_nombres);
      ?>
     <script>document.getElementById('nombre_sucursal').innerHTML='Sucursales: <?=$sucursalesGlosa?>';</script>
    </tr>
  </thead>
  <tbody>
    <?php $index=1; 
    //var_dump($array_sucursales);
    for ($j=0; $j <count($array_sucursales) ; $j++) {
       $cod_sucursal=$array_sucursales[$j];
       $nombre_sucursal=$array_sucursales_nombres[$cod_sucursal];
       ?>
        <tr>
            <td><?=$cod_sucursal?></td>
            <td style="text-align: left;"><?=$nombre_sucursal?></td>
        <?php
$totalValorado=0;$totalSaldoIni=0;$totalIngresosVal=0;$totalTraspasosVal=0;$totalTraspasosValVen=0;$totalVentasVal=0;
$totalIngresosAlmaAntVal=0;
$totalIngresosSucAntVal=0;
$totalIngresosAlmaActVal=0;
$totalIngresosSucActVal=0;
$totalIngresosOtroActVal=0;
      for ($i=0; $i <count($array_proveedores) ; $i++){ 
        $codigo_proveedor=$array_proveedores[$i]; 
        $nombre_proveedor=$nombre_proveedor_array[$codigo_proveedor];
        
          //INFO PRODUCTO
          $datosFila=$datosSucursal[$cod_sucursal];
          //datos obtenidos
          $ingresos=$datosFila[0];
          $ingresos_costo=$datosFila[1];
          $salidas=$datosFila[2];
          $salidas_costo=$datosFila[3];
          $ventas=$datosFila[4];
          $ventas_costo=$datosFila[5];
          //SALDO ANTERIOR
          $ingresos_ant=$datosFila[6];
          $ingresos_costo_ant=$datosFila[7];
          $salidas_ant=$datosFila[8];
          $salidas_costo_ant=$datosFila[9];
          $salidas_costo_ven=$datosFila[10];

          $ingresos_alma_ant=$datosFila[11];
          $ingresos_suc_ant=$datosFila[12];
          $ingresos_alma_act=$datosFila[13];
          $ingresos_suc_act=$datosFila[14];
          $ingresos_otro_act=$datosFila[15];

          if(isset($ingresos[$codigo_proveedor]))
            $variable_ingresos=$ingresos[$codigo_proveedor];
          else $variable_ingresos=0;
          if(isset($ingresos_costo[$codigo_proveedor]))
            $variable_ingresos_costo=$ingresos_costo[$codigo_proveedor];
          else $variable_ingresos_costo=0;
          if(isset($salidas[$codigo_proveedor]))
            $variable_salidas=$salidas[$codigo_proveedor];
          else $variable_salidas=0;
          if(isset($salidas_costo[$codigo_proveedor]))
            $variable_salidas_costo=$salidas_costo[$codigo_proveedor];
          else $variable_salidas_costo=0;
          if(isset($ventas[$codigo_proveedor]))
            $variable_ventas=$ventas[$codigo_proveedor];
          else $variable_ventas=0;
          if(isset($ventas_costo[$codigo_proveedor]))
            $variable_ventas_costo=$ventas_costo[$codigo_proveedor];
          else $variable_ventas_costo=0;
          //saldo ant
          if(isset($ingresos_ant[$codigo_proveedor]))
            $variable_ingresos_ant=$ingresos_ant[$codigo_proveedor];
          else $variable_ingresos_ant=0;
          if(isset($ingresos_costo_ant[$codigo_proveedor]))
            $variable_ingresos_costo_ant=$ingresos_costo_ant[$codigo_proveedor];
          else $variable_ingresos_costo_ant=0;
          if(isset($salidas_ant[$codigo_proveedor]))
            $variable_salidas_ant=$salidas_ant[$codigo_proveedor];
          else $variable_salidas_ant=0;
          if(isset($salidas_costo_ant[$codigo_proveedor]))
            $variable_salidas_costo_ant=$salidas_costo_ant[$codigo_proveedor];
          else $variable_salidas_costo_ant=0;
          if(isset($salidas_costo_ven[$codigo_proveedor]))
            $variable_salidas_costo_ven=$salidas_costo_ven[$codigo_proveedor];
          else $variable_salidas_costo_ven=0;

          if(isset($ingresos_alma_ant[$codigo_proveedor]))
            $variable_ingresos_alma_ant=$ingresos_alma_ant[$codigo_proveedor];
          else $variable_ingresos_alma_ant=0;
          if(isset($ingresos_suc_ant[$codigo_proveedor]))
            $variable_ingresos_suc_ant=$ingresos_suc_ant[$codigo_proveedor];
          else $variable_ingresos_suc_ant=0;
          if(isset($ingresos_alma_act[$codigo_proveedor]))
            $variable_ingresos_alma_act=$ingresos_alma_act[$codigo_proveedor];
          else $variable_ingresos_alma_act=0;
          if(isset($ingresos_suc_act[$codigo_proveedor]))
            $variable_ingresos_suc_act=$ingresos_suc_act[$codigo_proveedor];
          else $variable_ingresos_suc_act=0;
          if(isset($ingresos_otro_act[$codigo_proveedor]))
            $variable_ingresos_otro_act=$ingresos_otro_act[$codigo_proveedor];
          else $variable_ingresos_otro_act=0;
 

          $cantSaldo_ant=$variable_ingresos_costo_ant-abs($variable_salidas_costo_ant);

          $totalIngresos=$variable_ingresos_costo;
          $totalSalidas=$variable_salidas_costo;
          $totalSalidasVen=$variable_salidas_costo_ven;           

          $totalIngresosAlmaAnt=$variable_ingresos_alma_ant;
          $totalIngresosSucAnt=$variable_ingresos_suc_ant;
          $totalIngresosAlmaAct=$variable_ingresos_alma_act;
          $totalIngresosSucAct=$variable_ingresos_suc_act;
          $totalIngresosOtroAct=$variable_ingresos_otro_act;
          //VENTAS
          $cantVentas=$variable_ventas_costo;

          $cantSaldo=+($totalIngresos+$cantSaldo_ant)-$totalSalidas-$cantVentas-$totalSalidasVen;          
          if($cantSaldo<0){
            $cantSaldo=0;
          }


          $valoradoMes=number_format($cantSaldo,2,'.','');
          $valoradoSaldoIni=number_format($cantSaldo_ant,2,'.','');
          $valoradoIngresos=number_format($totalIngresos,2,'.','');
          $valoradoTraspaso=number_format($totalSalidas,2,'.','');
          $valoradoTraspasoVen=number_format($totalSalidasVen,2,'.','');
          $valoradoVentas=number_format($cantVentas,2,'.','');

          $valoradoIngresosAlmaAnt=number_format($totalIngresosAlmaAnt,2,'.','');
          $valoradoIngresosSucAnt=number_format($totalIngresosSucAnt,2,'.','');
          $valoradoIngresosAlmaAct=number_format($totalIngresosAlmaAct,2,'.','');
          $valoradoIngresosSucAct=number_format($totalIngresosSucAct,2,'.','');
          $valoradoIngresosOtroAct=number_format($totalIngresosOtroAct,2,'.','');


          $totalValorado+=$valoradoMes; 
          $totalSaldoIni+=$valoradoSaldoIni; 
          $totalIngresosVal+=$valoradoIngresos; 
          $totalTraspasosVal+=$valoradoTraspaso;
          $totalTraspasosValVen+=$valoradoTraspasoVen; 
          $totalVentasVal+=$valoradoVentas;


          $totalIngresosAlmaAntVal+=$valoradoIngresosAlmaAnt;
          $totalIngresosSucAntVal+=$valoradoIngresosSucAnt;
          $totalIngresosAlmaActVal+=$valoradoIngresosAlmaAct;
          $totalIngresosSucActVal+=$valoradoIngresosSucAct;
          $totalIngresosOtroActVal+=$valoradoIngresosOtroAct;

          $totalValoradoGen+=$valoradoMes; 
          $totalSaldoIniGen+=$valoradoSaldoIni; 
          $totalIngresosValGen+=$valoradoIngresos; 
          $totalTraspasosValGen+=$valoradoTraspaso; 
          $totalVentasValGen+=$valoradoVentas; 
          $totalTraspasosVenValGen+=$valoradoTraspasoVen; 
          
          $totalIngresosAlmaAntValGen+=$valoradoIngresosAlmaAnt;
          $totalIngresosSucAntValGen+=$valoradoIngresosSucAnt;
          $totalIngresosAlmaActValGen+=$valoradoIngresosAlmaAct;
          $totalIngresosSucActValGen+=$valoradoIngresosSucAct;
          $totalIngresosOtroActValGen+=$valoradoIngresosOtroAct;
        }
        ?>
        <td><?=number_format($totalSaldoIni,2,'.',',')?></td>
            <!-- <td><?=number_format($totalIngresosVal,2,'.',',')?></td> -->
            <td><?=number_format($totalIngresosAlmaAntVal,2,'.',',')?></td>
            <td><?=number_format($totalIngresosSucAntVal,2,'.',',')?></td>
            <td><?=number_format($totalIngresosAlmaActVal,2,'.',',')?></td>
            <td><?=number_format($totalIngresosSucActVal,2,'.',',')?></td>
            <td><?=number_format($totalIngresosOtroActVal,2,'.',',')?></td>
            <td><?=number_format($totalTraspasosVal,2,'.',',')?></td>
            <td><?=number_format($totalTraspasosValVen,2,'.',',')?></td>
            <td><?=number_format($totalVentasVal,2,'.',',')?></td>
            <td><?=number_format($totalValorado,2,'.',',')?></td>

        </tr><?php


      }
      ?> 
       <tr>
            <td colspan="2">TOTALES</td>
            <td><?=number_format($totalSaldoIniGen,2,'.',',')?></td>
            <!-- <td><?=number_format($totalIngresosValGen,2,'.',',')?></td> -->
            <td><?=number_format($totalIngresosAlmaAntValGen,2,'.',',')?></td>
            <td><?=number_format($totalIngresosSucAntValGen,2,'.',',')?></td>
            <td><?=number_format($totalIngresosAlmaActValGen,2,'.',',')?></td>
            <td><?=number_format($totalIngresosSucActValGen,2,'.',',')?></td>
            <td><?=number_format($totalIngresosOtroActValGen,2,'.',',')?></td>            
            <td><?=number_format($totalTraspasosValGen,2,'.',',')?></td>
            <td><?=number_format($totalTraspasosVenValGen,2,'.',',')?></td>
            <td><?=number_format($totalVentasValGen,2,'.',',')?></td>
            <th><?=number_format($totalValoradoGen,2,'.',',')?></th>
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
