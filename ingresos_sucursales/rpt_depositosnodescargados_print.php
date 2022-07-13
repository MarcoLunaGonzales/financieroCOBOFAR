
<?php

// header("Pragma: public");
//     header("Expires: 0");
//     $filename = "reporte_baja_depositos.xls";
//     header("Content-type: application/x-msdownload");
//     header("Content-Disposition: attachment; filename=$filename");
//     header("Pragma: no-cache");
//     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

require("../conexion_comercial2.php");
require_once '../styles.php';
require_once '../functions.php';
require_once '../layouts/bodylogin2.php';

$fechai=$_POST['fechainicio'];
$fechaf=$_POST['fechafin'];

$sucursal=$_POST['sucursal'];
$tipo_registro=$_POST['tipo_registro'];
?>
<div class='content'>
  <div class='container-fluid'>
    <div class='row'>
    <div class='col-md-12'>
      <div class='card'>

<div class='card-header $colorCard card-header-icon'>
  <h4 class='card-title'> <img  class='card-img-top'  src='../assets/img/favicon.png' style='width:100%; max-width:50px;'>Reporte Registro de Depositos</h4>
  <h6 class='card-title'>Fechas:<?=$fechai?> - <?=$fechaf?></h6>
</div>

<div class='card-body'>
<div class='table-responsive'>
<table class='table table-condensed table-bordered' id='tablePaginatorReport_facturasgeneradas'>
<thead><tr class='bg-info text-white'><th></th><th>Fecha</th><th>Sucursal</th><th>Turno</th><th>Personal</th><th>Fecha Registro</th></tr></thead> <tbody>
<?php
if($tipo_registro==1){
  $consulta="SELECT sa.fecha,a.cod_almacen,a.nombre_almacen,a.cod_ciudad,f.paterno,f.materno,f.nombres,f.turno,sa.cod_chofer,CONCAT_WS('_',sa.fecha,f.codigo_funcionario,a.cod_ciudad)as codigo_nuevo
  from salida_almacenes sa join almacenes a on sa.cod_almacen=a.cod_almacen join funcionarios f on sa.cod_chofer=f.codigo_funcionario
  where  sa.fecha BETWEEN '$fechai' and '$fechaf' and sa.cod_tiposalida=1001  
  GROUP BY sa.fecha,a.cod_almacen,f.codigo_funcionario
  HAVING codigo_nuevo not in (select CONCAT_WS('_',rd.fecha,rd.cod_funcionario,rd.cod_ciudad)as codigo_nuevo
  from registro_depositos rd
    where rd.fecha BETWEEN '$fechai' and '$fechaf' 
  )
  order by a.nombre_almacen,sa.fecha";
}else{//descargados

  $consulta="SELECT sa.fecha,a.cod_almacen,a.nombre_almacen,a.cod_ciudad,f.paterno,f.materno,f.nombres,f.turno,sa.cod_chofer,CONCAT_WS('_',sa.fecha,f.codigo_funcionario,a.cod_ciudad)as codigo_nuevo
  from salida_almacenes sa join almacenes a on sa.cod_almacen=a.cod_almacen join funcionarios f on sa.cod_chofer=f.codigo_funcionario
  where  sa.fecha BETWEEN '$fechai' and '$fechaf' and sa.cod_tiposalida=1001 and a.cod_ciudad in (select c.cod_ciudad from ciudades c where c.cod_area in ($sucursal))
  GROUP BY sa.fecha,a.cod_almacen,f.codigo_funcionario
  HAVING codigo_nuevo in (select CONCAT_WS('_',rd.fecha,rd.cod_funcionario,rd.cod_ciudad)as codigo_nuevo
  from registro_depositos rd
    where rd.fecha BETWEEN '$fechai' and '$fechaf' and rd.cod_ciudad in (select c.cod_ciudad from ciudades c where c.cod_area in ($sucursal)) 
  )
  order by a.nombre_almacen,sa.fecha";
}
$resp = mysqli_query($enlaceCon,$consulta);
$index=1;
while ($dat = mysqli_fetch_array($resp)) {
  $cod_chofer = $dat['cod_chofer'];
  $fecha = $dat['fecha'];
  $nombre_almacen=$dat['nombre_almacen'];
  $paterno=$dat['paterno'];
  $materno=$dat['materno'];
  $nombres=$dat['nombres'];
  $turno=$dat['turno'];
  $cod_ciudad=$dat['cod_ciudad'];

  $nombre_turno="";
  switch ($turno) {
    case 1://mañana
      $nombre_turno="TM";
      break;
    case 2://tarde
      $nombre_turno="TT";
      break;
    case 3://of central
      // code...
      break;
  }
  //verificamos deposito
  $fecha_registro="";
  if($tipo_registro<>1){
    $consulta="select fecha_registro from registro_depositos
    where fecha = '$fecha' and cod_funcionario=$cod_chofer and cod_ciudad=$cod_ciudad";
    $respRegistro = mysqli_query($enlaceCon,$consulta);
    while ($dataRegistro = mysqli_fetch_array($respRegistro)) {
      $fecha_registro=$dataRegistro['fecha_registro'];
    }  
  }
  


  ?>
    <tr>
      <td><?=$index?></td>
      <td><?=$fecha?></td>
      <td class="text-left"><?=$nombre_almacen?></td>
      <td class="text-left"><?=$nombre_turno?></td>
      <td class="text-left"><?=$paterno?> <?=$materno?> <?=$nombres?></td>
      <td><?=$fecha_registro?></td>
    </tr><?php
    $index++;
  
    
}
?>
 </tbody></table>
</div>
</div>


</div>
    </div>
  </div>
  </div>
</div>

<?php 
mysqli_close($enlaceCon);
?>
