<?php



$sql="SELECT sum((select ct.costo_unitario from costoscobofar.costo_transaccion ct where ct.cod_material=sad.cod_material and ct.cod_documento=sa.cod_salida_almacenes and ct.cod_tipodocumento=0)*sad.cantidad_unitaria) as costo_venta 
      from salida_almacenes sa INNER JOIN salida_detalle_almacenes sad on sad.cod_salida_almacen=sa.cod_salida_almacenes
      where sa.fecha = '$fecha' and sa.cod_tiposalida=1001 and sa.salida_anulada=0 and sa.`cod_almacen` in (select a.`cod_almacen` from `almacenes` a
    where a.`cod_ciudad`='$rpt_territorio' and a.cod_tipoalmacen=1)";  
    // echo $sql."<br>";
   $valor=0;
   require("conexion_comercial.php");
   $resp=mysqli_query($dbh,$sql);
   while($row=mysqli_fetch_array($resp)){ 
      $monto=number_format($row['costo_venta'],1,'.','');
      $valor+=$monto;
   } 
   mysqli_close($dbh);
   return $valor;

?>