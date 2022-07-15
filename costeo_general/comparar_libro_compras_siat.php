<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once 'configModule.php';

set_time_limit(0);
$dbh = new Conexion();
$sql="SELECT nit,razon_social,factura,autoriza_cuf,fecha,imp from libro_compras_qr_siat order by nit,fecha ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('nit', $nit);
$stmt->bindColumn('razon_social', $nombre);
$stmt->bindColumn('factura', $nfactura);
$stmt->bindColumn('autoriza_cuf', $auto);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('imp', $importe);
?>
<table class="table table-bordered table-condensed" >
<thead>
  <tr>
    <th><small><small><b>Fecha</b></small></small></th>
    <th><small><small><b>NIT</b></small></small></th>
    <th><small><small><b>RAZON</b></small></small></th>
    <th><small><small><b>NFACT</b></small></small></th>
    <th><small><small><b>AUTO</b></small></small></th>
    <!-- <th><small><small><b>COD_CONTROl</b></small></small></th> -->
    <th><small><small><b>IMP</b></small></small></th>
    <th style="background: red;"><small><small><b></b></small></small></th>
    <th><small><small><b>F.Ingreso</b></small></small></th>
    <th><small><small><b>F.Factura</b></small></small></th>
    <th><small><small><b>NIT BD</b></small></small></th>
    <th><small><small><b>RAZON BD</b></small></small></th>
    <th><small><small><b>NFACT BD</b></small></small></th>
    <th><small><small><b>AUTO BD</b></small></small></th>
    <!-- <th><small><small><b>COD_CONTROl BD</b></small></small></th> -->
    <th><small><small><b>IMP BD</b></small></small></th>
    <th></th>
  </tr>
</thead>
<tbody>
  <?php $index=1;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
    $datos=verificar_nit_fac_fecha($nit,$nfactura,$fecha);
    if($datos=='NO ENCONTRADO'){
      $datos=verificar_nit_fac_monto($nit,$nfactura,$importe);
    }
    if($datos=='NO ENCONTRADO'){
      $datos=verificar_nit_fac($nit,$nfactura);
    }
    if($datos=='NO ENCONTRADO'){
      $nombreProvBD="";
      $nit_BD="";
      $codControl_BD="";
      $Nfac_BD="";
      $auto_BD="";
      $fecha_BD="";
      // $mfac_BD="";
      $fecha_t="";
      $dcto="";
      $importe_BD="";
    }else{
      $array_datos=explode('#####', $datos);
      $nombreProvBD=$array_datos[0];
      $nit_BD=$array_datos[1];
      $codControl_BD=$array_datos[2];
      $Nfac_BD=$array_datos[3];
      $auto_BD=$array_datos[4];
      $fecha_BD=$array_datos[5];
      // $fecha_t=$array_datos[7];
      $fecha_t="";
      $dcto=$array_datos[7];
      $importe_BD=$array_datos[6];
    }
    $importe=number_format($importe,2,'.','');
    //$fecha=date("d/m/Y", strtotime($fecha));
    if($fecha==$fecha_BD)
      $label_fecha='<span style="color:green;">';
    else
      $label_fecha='<span style="color:red;">';
    if($nit==$nit_BD)
      $label_nit='<span style="color:green;">';
    else
      $label_nit='<span style="color:red;">';
    if($nfactura==$Nfac_BD)
      $label_nfac='<span style="color:green;">';
    else
      $label_nfac='<span style="color:red;">';
    if($auto==$auto_BD)
      $label_auto='<span style="color:green;">';
    else
      $label_auto='<span style="color:red;">';
    // if($codigo==$codControl_BD)
    //   $label_cc='<span style="color:green;">';
    // else
    //   $label_cc='<span style="color:red;">';
    //$nombreProvBD=obtenerNombreProveedor($IDPROVEEDOR_BD);
    // if($nombre==$nombreProvBD)
    //   $label_nombre='<span style="color:green;">';
    // else{
    //   $nombreProvBD.="###NO ENCONTRADO";
    //   $label_nombre='<span style="color:red;">';
    // }


    if($nombre==$nombreProvBD)
      $label_nombre='<span style="color:green;">';
    else{
      $nombreProvBD.="###NO ENCONTRADO";
      $label_nombre='<span style="color:red;">';
    }

    if($importe==$importe_BD)
      $label_importe='<span style="color:green;">';
    else{
      $label_importe='<span style="color:red;">';
    }

    $label_datos_conta=$nit."@@@".$nfactura."@@@".$auto."@@@".$codigo."@@@".$fecha;
    //==============
    $label_datos_bd=$dcto;
    ?>
    <tr>
      <td ><small><?=$label_fecha.$fecha;?></span></small></td>
      <td ><small><?=$label_nit.$nit;?></span></small></td>
      <td ><small><?=$label_nombre.$nombre;?></span></small></td>
      <td ><small><?=$label_nfac.$nfactura;?></span></small></td>
      <td ><small><?=$label_auto.$auto;?></span></small></td>
      <!-- <td ><small><?=$label_cc.$codigo;?></span></small></td> -->
      <td ><small><?=$label_importe.$importe;?></span></small></td>
      <td style="background: red;"><small></small></td>
      <td ><small><?=$fecha_t;?></span></small></td>
      <td ><small><?=$label_fecha.$fecha_BD;?></span></small></td>
      <td ><small><?=$label_nit.$nit_BD;?></span></small></td>
      <td ><small><?=$label_nombre.$nombreProvBD;?></span></small></td>
      <td ><small><?=$label_nfac.$Nfac_BD;?></span></small></td>
      <td ><small><?=$label_auto.$auto_BD;?></span></small></td>
      <!-- <td ><small><?=$label_cc.$codControl_BD;?></span></small></td> -->
      <td ><small><?=$label_importe.$importe_BD;?></span></small></td>
      <td class="td-actions text-right">
        <a target="blank" href='comparar_libro_compras_update.php?datos_conta=<?=$label_datos_conta;?>&datos_bd=<?=$label_datos_bd;?>' class="<?=$buttonEdit;?>" title="Actualizar">
            <i class="material-icons">cached</i>
        </a>                        
      </td>
    </tr>
  <?php $index++; } 

  $stmt = null;
  $dbh = null;

  ?>
</tbody>
</table>
