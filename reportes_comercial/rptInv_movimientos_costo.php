<?php
set_time_limit(0);

require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functions.php';
require("../conexion_comercial.php");

 $id_proveedor=$_POST['proveedor'];
$sucursal=$_POST['sucursal'];
$fechai=$_POST['fechainicio'];
$fechaf=$_POST['fechafin'];



$id_linea=obtenerIdLineas_nuevo($id_proveedor);

$nombre_proveedor=obtenerNombreProveedor_nuevo($id_proveedor);

$stringProductos=obtenerProductosAlmacen_nuevo($id_linea);

$array_productos=explode(',', $stringProductos);
$sql_sucursales="";
$array_datosproductos=obtenerProductosnombre_presentacionAlmacen($id_linea,(int)date("Y",strtotime($fechai)),(int)date("m",strtotime($fechai)),$sucursal);//id linea, gestion,mes, y cod almacen 

$nombre_producto_array=$array_datosproductos[0];
$presentacion_producto_array=$array_datosproductos[1];
$costo_producto_array=$array_datosproductos[2];
$costo_producto_array_ant=$array_datosproductos[3];


$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($fechai))." al ".strftime('%d/%m/%Y',strtotime($fechaf));
 // echo $sql;
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
             <h4 class="card-title text-center">REPORTE MOVIENTOS COSTO - PRODUCTO</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title" id="nombre_sucursal">Sucursal:</h6>  
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>            
            <div class="table-responsive">

<table class="table table-bordered table-condensed" id="tablePaginator1001">
  <thead>
    <tr><th></th><th></th><th colspan="20" align="center"><b>REPORTE MOVIENTOS DE PRODUCTOS - COSTOS</b></th></tr>
    <tr style="border:1px;">
      <th>CODIGO</th>
      <th>DES</th>
      <th>DIV</th>
      <th>Saldo Ant.</th>
      <th>Ingresos</th>
      <th>Traspasos</th>
      <th>Ventas</th>
      <th>Saldo Final</th>
      <?php
        $sql_listsucursales="SELECT a.cod_almacen,a.nombre_almacen
          from almacenes a join ciudades c on a.cod_ciudad=c.cod_ciudad 
          where a.estado_pedidos=1  and cod_almacen in ($sucursal)
          order by a.nombre_almacen";
         // echo $sql_listsucursales;
        $resp=mysqli_query($dbh,$sql_listsucursales);
        $string_sucursales="";
        while($row=mysqli_fetch_array($resp)){
          $cod_almacen=$row['cod_almacen'];
          $nombre_almacen=$row['nombre_almacen'];
          ?><script>document.getElementById('nombre_sucursal').innerHTML='Sucursal: <?=$nombre_almacen?>';</script><?php
          $informacion=cargarValoresVentasYSaldosProductosArray_prodrotacion($cod_almacen,$fechai,$fechaf,$stringProductos);
          $datosSucursal[$cod_almacen]=$informacion;
          $string_sucursales.=$cod_almacen.",";
        }
          $string_sucursales=trim($string_sucursales,",");
          $array_sucursales=explode(",", $string_sucursales);
      ?>
    </tr>
  </thead>
  <tbody>
    <?php $index=1; $totalValorado=0;$totalSaldoIni=0;$totalIngresosVal=0;$totalTraspasosVal=0;$totalVentasVal=0;
    //var_dump($array_sucursales);
      for ($i=0; $i <count($array_productos) ; $i++)
      { $codigo_producto=$array_productos[$i]; 
        $nombre_producto=$nombre_producto_array[$codigo_producto];
        $cantidad_presentacion=$presentacion_producto_array[$codigo_producto];
        $costo_producto=$costo_producto_array[$codigo_producto];
        $costo_producto_ant=$costo_producto_array_ant[$codigo_producto];
        ?>
        <tr>
            <td><?=$codigo_producto?></td>
            <td style="text-align: left;"><?=$nombre_producto?></td>
            <td><?=number_format($cantidad_presentacion,0,'.','')?></td>

        <?php
        for ($j=0; $j <count($array_sucursales) ; $j++) {
          $cod_sucursal=$array_sucursales[$j];
          //INFO PRODUCTO
          $datosFila=$datosSucursal[$cod_sucursal];
          //datos obtenidos
          $ingresos=$datosFila[0];
          $ingresos_unidad=$datosFila[1];
          $salidas=$datosFila[2];
          $salidas_unidad=$datosFila[3];
          $ventas=$datosFila[4];
          $ventas_unidad=$datosFila[5];
          //SALDO ANTERIOR
          $ingresos_ant=$datosFila[6];
          $ingresos_unidad_ant=$datosFila[7];
          $salidas_ant=$datosFila[8];
          $salidas_unidad_ant=$datosFila[9];

          if(isset($ingresos[$codigo_producto]))
            $variable_ingresos=$ingresos[$codigo_producto]*$cantidad_presentacion;
          else $variable_ingresos=0;
          if(isset($ingresos_unidad[$codigo_producto]))
            $variable_ingresos_unidad=$ingresos_unidad[$codigo_producto];
          else $variable_ingresos_unidad=0;
          if(isset($salidas[$codigo_producto]))
            $variable_salidas=$salidas[$codigo_producto]*$cantidad_presentacion;
          else $variable_salidas=0;
          if(isset($salidas_unidad[$codigo_producto]))
            $variable_salidas_unidad=$salidas_unidad[$codigo_producto];
          else $variable_salidas_unidad=0;
          if(isset($ventas[$codigo_producto]))
            $variable_ventas=$ventas[$codigo_producto]*$cantidad_presentacion;
          else $variable_ventas=0;
          if(isset($ventas_unidad[$codigo_producto]))
            $variable_ventas_unidad=$ventas_unidad[$codigo_producto];
          else $variable_ventas_unidad=0;
          //saldo ant
          if(isset($ingresos_ant[$codigo_producto]))
            $variable_ingresos_ant=$ingresos_ant[$codigo_producto]*$cantidad_presentacion;
          else $variable_ingresos_ant=0;
          if(isset($ingresos_unidad_ant[$codigo_producto]))
            $variable_ingresos_unidad_ant=$ingresos_unidad_ant[$codigo_producto];
          else $variable_ingresos_unidad_ant=0;
          if(isset($salidas_ant[$codigo_producto]))
            $variable_salidas_ant=$salidas_ant[$codigo_producto]*$cantidad_presentacion;
          else $variable_salidas_ant=0;
          if(isset($salidas_unidad_ant[$codigo_producto]))
            $variable_salidas_unidad_ant=$salidas_unidad_ant[$codigo_producto];
          else $variable_salidas_unidad_ant=0;

          $totalIngresos_ant=$variable_ingresos_ant+($variable_ingresos_unidad_ant);
          $totalSalidas_ant=abs($variable_salidas_ant)+(abs($variable_salidas_unidad_ant)); 
          $cantSaldo_ant=$totalIngresos_ant-$totalSalidas_ant;

          $totalIngresos=$variable_ingresos+($variable_ingresos_unidad);
          $totalSalidas=abs($variable_salidas)+(abs($variable_salidas_unidad));           
          //VENTAS
          $cantVentas=$variable_ventas+($variable_ventas_unidad);
          $cantSaldo=+($totalIngresos+$cantSaldo_ant)-$totalSalidas-$cantVentas;
          if($cantSaldo<0){
            $cantSaldo=0;
          }


          
          $valoradoSaldoIni=number_format($cantSaldo_ant*$costo_producto_ant,2,'.','');
          $valoradoIngresos=number_format($totalIngresos*$costo_producto,2,'.','');
          $valoradoTraspaso=number_format($totalSalidas*$costo_producto,2,'.','');
          $valoradoVentas=number_format($cantVentas*$costo_producto,2,'.','');
          //$valoradoMes=number_format($cantSaldo*$costo_producto,2,'.','');
          $valoradoMes=number_format(($valoradoSaldoIni+$valoradoIngresos)-$valoradoTraspaso-$valoradoVentas,2,'.','');
          $totalValorado+=$valoradoMes; 
          $totalSaldoIni+=$valoradoSaldoIni; 
          $totalIngresosVal+=$valoradoIngresos; 
          $totalTraspasosVal+=$valoradoTraspaso; 
          $totalVentasVal+=$valoradoVentas; 
          ?>
            <td><small><?=$valoradoSaldoIni?></small></td>
            <td><small><?=$valoradoIngresos?></small></td>
            <td><small><?=$valoradoTraspaso?></small></td>
            <td><small><?=$valoradoVentas?></small></td>
            <td><small><?=$valoradoMes?></small></td>
          <?php 
        }
        ?></tr><?php
      }
      ?>

       <tr>
            <td colspan="3">TOTALES</td>
            <td><?=number_format($totalSaldoIni,2,'.',',')?></td>
            <td><?=number_format($totalIngresosVal,2,'.',',')?></td>
            <td><?=number_format($totalTraspasosVal,2,'.',',')?></td>
            <td><?=number_format($totalVentasVal,2,'.',',')?></td>
            <th><?=number_format($totalValorado,2,'.',',')?></th>
      </tr>    
        <?php
      $index++; 
     ?>
  </tbody>
</table>

</div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
