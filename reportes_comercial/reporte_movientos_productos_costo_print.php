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
$array_datosproductos=obtenerProductosnombre_presentacionAlmacen_nuevo($id_linea);

$nombre_producto_array=$array_datosproductos[0];
$presentacion_producto_array=$array_datosproductos[1];


$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));
 // echo $sql;
?>
<table class="table table-bordered table-condensed" id="tablePaginator1001">
  <thead>
    <tr><th></th><th></th><th colspan="20" align="center"> &nbsp;</th></tr>
    <tr><th></th><th></th><th colspan="20" align="center"><b></b></th></tr>
    <tr><th></th><th></th><th colspan="20" align="center"><b>REPORTE MOVIENTOS DE PRODUCTOS - COSTOS</b></th></tr>
    <tr><th></th><th></th><th colspan="20" align="center"> &nbsp;</th></tr>
    <tr style="border:1px;">
      <th>CODIGO</th>
      <th>DES</th>
      <th>DIV</th>
      <th>Saldo Ant.</th>
      <th>Ingresos</th>
      <th>Traspasos</th>
      <th>Ventas</th>
      <th>Ajustes y Otros</th>
      <th>Saldo Final</th>
      <?php
        $sql_listsucursales="SELECT a.cod_almacen,a.nombre_almacen
          from almacenes a join ciudades c on a.cod_ciudad=c.cod_ciudad 
          where a.estado_pedidos=1  and cod_almacen not in ($sucursal)
          order by a.nombre_almacen";
          $string_sucursales="";
        $resp=mysqli_query($dbh,$sql_listsucursales);
        while($row=mysqli_fetch_array($resp)){
          $cod_almacen=$row['cod_almacen'];
          $nombre_almacen=$row['nombre_almacen'];
          // $AGE1_S=$row['codigo_anterior'];
          // $IP_S=$row['ip'];
          // $fecha_inicio_suc=$row['fecha_inicio'];
          $informacion=cargarValoresVentasYSaldosProductosArray_nuevosis($cod_almacen,$fechai,$fechaf,$fechaf,$stringProductos);
          $datosSucursal[$cod_almacen]=$informacion;
        }
          //para el antiguo sistema
          $array_sucursales=explode(",", $cod_almacen);
      ?>
    </tr>
  </thead>
  <tbody>
    <?php $index=1;
    //var_dump($array_sucursales);
      for ($i=0; $i <count($array_productos) ; $i++)
      { $codigo_producto=$array_productos[$i]; 
        // $nombre_producto=obtener_nombreproducto_alm_nuevosis($codigo_producto);
        // $cantidad_presentacion=obtener_cantidadPresentacion_nuevosis($codigo_producto);
        $nombre_producto=$nombre_producto_array[$codigo_producto];
        $cantidad_presentacion=$presentacion_producto_array[$codigo_producto];
        ?>
        <tr>
            <td><?=$codigo_producto?></td>
            <td><?=$nombre_producto?></td>
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
           $salida_ajuste=$datosFila[6];
           $salida_ajuste_unidad=$datosFila[7];

           $ingresos_costo=$datosFila[7];
           $salidas_costo=$datosFila[7];
           $salida_ajuste_unidad=$datosFila[7];
           $salida_ajuste_unidad=$datosFila[7];
           $salida_ajuste_unidad=$datosFila[7];
           if(isset($ingresos[$codigo_producto]))
            $variable_ingresos=$ingresos[$codigo_producto];
           else
            $variable_ingresos=0;
          if(isset($ingresos_unidad[$codigo_producto]))
            $variable_ingresos_unidad=$ingresos_unidad[$codigo_producto];
           else
            $variable_ingresos_unidad=0;
          if(isset($salidas[$codigo_producto]))
            $variable_salidas=$salidas[$codigo_producto];
           else
            $variable_salidas=0;
          if(isset($salidas_unidad[$codigo_producto]))
            $variable_salidas_unidad=$salidas_unidad[$codigo_producto];
           else
            $variable_salidas_unidad=0;

          if(isset($ventas[$codigo_producto]))
            $variable_ventas=$ventas[$codigo_producto];
           else
            $variable_ventas=0;
          if(isset($ventas_unidad[$codigo_producto]))
            $variable_ventas_unidad=$ventas_unidad[$codigo_producto];
          else
            $variable_ventas_unidad=0;

          if(isset($salida_ajuste[$codigo_producto]))
            $variable_salidas_ajuste=$salida_ajuste[$codigo_producto];
           else
            $variable_salidas_ajuste=0;
          if(isset($salida_ajuste_unidad[$codigo_producto]))
            $variable_salida_ajuste_unidad=$salida_ajuste_unidad[$codigo_producto];
          else
            $variable_salida_ajuste_unidad=0;

          $saldo_anterior=0;  

           $totalIngresos=$variable_ingresos+($variable_ingresos_unidad);
           $totalSalidas=abs($variable_salidas)+(abs($variable_salidas_unidad)); 
           $cantVentas=$variable_ventas+($variable_ventas_unidad);
           $totalSalidas_ajuste=abs($variable_salidas_ajuste)+(abs($variable_salida_ajuste_unidad)); 
           $cantSaldo=$totalIngresos-$totalSalidas-$totalSalidas_ajuste-$cantVentas;
           if($cantSaldo<0){
              $cantSaldo=0;
           }
           //VENTAS
           
          ?>
            <td><small><?=number_format($saldo_anterior,0,'.','');?></small></td>
            <td><small><?=number_format($totalIngresos,0,'.','');?></small></td>
            <td><small><?=number_format($totalSalidas,0,'.','');?></small></td>
            <td><small><?=number_format($cantVentas,0,'.','');?></small></td>
            <td><small><?=number_format($totalSalidas_ajuste,0,'.','');?></small></td>
            <td><small><?=number_format($cantSaldo,0,'.','');?></small></td>
          <?php 
        }
        ?></tr><?php
      }
     
      $index++; 
     ?>
  </tbody>
</table>
