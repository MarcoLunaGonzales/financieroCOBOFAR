<meta charset="utf-8">
<?php
  header("Pragma: public");
  header("Expires: 0");
  $filename = "cuadro de ventas nuevo.xls";
  header("Content-type: application/x-msdownload");
  header("Content-Disposition: attachment; filename=$filename");
  header("Pragma: no-cache");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
require_once '../conexion_comercial_oficial.php';
require_once '../functions.php';

$fechai=$_POST['desde'];
$fechai_x=date('d/m/Y', strtotime($fechai));
//echo $fechai;
$fechaf=$_POST['hasta'];
$fechaf_x=date('d/m/Y', strtotime($fechaf));
//echo $fechaf;
$datos_suc=obtenerSucursalesActivas();
// var_dump($datos_suc);
$array_sucursales=$datos_suc[0];
$array_sucursales_nombre=$datos_suc[1];
?>
<table class="table table-bordered" id="tablePaginator">
  <tr><td colspan="11"><b>Corporación Boliviana de Farmacias S.A.</b></td></tr>
  <tr><td colspan="11"><center><h1><b>VENTAS DEL MES DE FEBRERO 2022</b></h1></center></td></tr>
  <tr style='background:#93a7dd;'>
      <td rowspan="2"><b>N°</b></td>
      <td rowspan="2"><b>CODIGO</b></td>
      <td rowspan="2"><b>NOMBRE DE SUCURSAL</b></td>
      <td colspan="3"><b>FACTURAS AUTOMATICAS</b></td>
      <td colspan="3"><b>FACTURAS MANUALES</b></td>
      <td><b>TOTAL1</b></td>
      <td><b>TOTAL2</b></td>
    </tr>
    <tr style='background:#93a7dd;'>
      <td><b>Del</b></td>
      <td><b>Al</b></td>
      <td><b>Monto1</b></td>
      <td><b>Del</b></td>
      <td><b>Al</b></td>
      <td><b>Monto2</b></td>
      <td><b>MONTO3</b></td>
      <td><b>DEBITO</b></td>
    </tr>
    <?php $index=1;
    $suma_Monto1=0;
    $suma_Monto2=0;
    $suma_Monto3=0;
    $Monto2=0;
    for ($i=0; $i < count($array_sucursales); $i++) 
    { 
      $cod_almacen=$array_sucursales[$i];                  
      $nombre_almacen=$array_sucursales_nombre[$cod_almacen];
      $cod_ciudad=$cod_almacen;
      $del_x=0;
      $al_x=0;
      $Monto1=0;
      $sql="SELECT a.cod_dosificacion from salida_almacenes a where a.cod_almacen in ($cod_almacen) and a.cod_tiposalida=1001 and a.cod_tipo_doc in (1) and CONCAT(a.fecha,' ',a.hora_salida) BETWEEN '$fechai 00:00:00' and '$fechaf 23:59:59' GROUP BY a.cod_dosificacion ORDER BY a.cod_dosificacion";
      $resp=mysqli_query($dbh,$sql);
      while ($row2 = mysqli_fetch_array($resp)) {
        $cod_dosificacion=$row2['cod_dosificacion'];
        $datos_fac=obtenerInicioFinFacturas_nuevo($cod_dosificacion,$fechai,$fechaf);
        $del=$datos_fac[0];
        $al=$datos_fac[1];
        if($del_x==0){
          $del_x=$del;
          $al_x=$al;
        }else{
          $al_x=$al;
        }
        $Monto1+=obtenerVentasTotales_nuevo($cod_dosificacion,$fechai,$fechaf);
      } 
      $suma_Monto1+=$Monto1;
      ?>
      <tr>
        <td><?=$index;?></td>
        <td><?=$cod_ciudad;?></td>
        <td ><?=$nombre_almacen;?></td>
        <td><?=$del_x;?></td>
        <td><?=$al_x;?></td>
        <td><?=number_format($Monto1,2,'.',',');?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr><?php 
      $index++; 
    } ?>

    <tr>
      <td colspan="3"><b>TOTAL</b></td>
      <td colspan="2"></td>
      <td><?=number_format($suma_Monto1,2,'.',',');?></td>
      <td colspan="2"></td>
      <td><?=number_format($suma_Monto2,2,'.',',');?></td>
      <td><?=number_format($suma_Monto3,2,'.',',');?></td>
      <td><?=number_format(0,2,'.',',');?></td>
    </tr>
</table>
<br><br><br>
<table class="table table-bordered" id="tablePaginator">
  <tr>
    <td colspan="3"><center>--------------------------------------------<br><b>ING. ISMAEL SULLCAMANI<br>ELABORADO</b></center></td>
    <td></td>
    <td colspan="3"><center>--------------------------------------------<br><b>ING. MARCO LUNA<br>ENTREGUÉ CONFORME</b></center></td>
    <td></td>
    <td colspan="3"><center>--------------------------------------------<br><b>LIC. AMED INDA<br>RECIBÍ CONFORME</b></center></td>
  </tr>
</table>
