<?php
require_once '../conexion.php';
session_start();
$dbh = new Conexion();
$cod_horario=$_GET['cod_horario'];

$sql="select h.descripcion,hd.cod_horario, hd.ingreso_1,hd.salida_1,hd.ingreso_2,hd.salida_2,hd.ingreso_3,hd.salida_3,hd.ingreso_4,hd.salida_4, (select descripcion from horarios_asignaciontipo where codigo=hd.cod_asignacion) as tipo 
from horarios h join horarios_detalle hd on hd.cod_horario=h.codigo  
where hd.cod_horario='$cod_horario' order by h.descripcion,hd.cod_asignacion";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn

$stmt->bindColumn('descripcion', $descripcionX);
$stmt->bindColumn('cod_horario', $cod_horarioX);
$stmt->bindColumn('ingreso_1', $ingreso_1X);
$stmt->bindColumn('salida_1', $salida_1X);
$stmt->bindColumn('ingreso_2', $ingreso_2X);
$stmt->bindColumn('salida_2', $salida_2X);
$stmt->bindColumn('ingreso_3', $ingreso_3X);
$stmt->bindColumn('salida_3', $salida_3X);
$stmt->bindColumn('ingreso_4', $ingreso_4X);
$stmt->bindColumn('salida_4', $salida_4X);
$stmt->bindColumn('tipo', $tipoX);

//echo $sql;
$index=0;
?>
<table class="table table-condensed small table-bordered" id=""><!---->
                  <thead>
                     <tr class="bg-success" style="background: #7D3C98 !important;color:white;">
                      <td class="text-center"></td>
                      <td class="text-center" colspan="2">CONTINUO</td>                      
                      <td class="text-center" colspan="2" style="background: #FF8F00">TURNO MAÑANA</td>                      
                      <td class="text-center" colspan="2" style="background: #FF8F00">TURNO TARDE</td>                      
                      <td class="text-center" colspan="2" style="background: #FF8F00">TURNO NOCHE</td>                      
                    </tr>
                    <tr class="bg-success" style="background: #7D3C98 !important;color:white;">
                      <td class="text-center">DÍA</td>
                      <td class="text-center">INGRESO</td>
                      <td class="text-center">SALIDA</td>                      
                      <td class="text-center" style="background: #FF8F00">INGRESO</td>
                      <td class="text-center" style="background: #FF8F00">SALIDA</td>                      
                      <td class="text-center" style="background: #FF8F00">INGRESO</td>
                      <td class="text-center" style="background: #FF8F00">SALIDA</td>                      
                      <td class="text-center" style="background: #FF8F00">INGRESO</td>
                      <td class="text-center" style="background: #FF8F00">SALIDA</td>                     
                    </tr>

                  </thead>
                  <tbody>
<?php
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {   
                      if($ingreso_1X==""){$ingreso_1X="-";}if($salida_1X==""){$salida_1X="-";}
                      if($ingreso_2X==""){$ingreso_2X="-";}if($salida_2X==""){$salida_2X="-";}
                      if($ingreso_3X==""){$ingreso_3X="-";}if($salida_3X==""){$salida_3X="-";}
                      if($ingreso_4X==""){$ingreso_4X="-";}if($salida_4X==""){$salida_4X="-";}                      
                      
                     ?>
                      <tr class="" style="background: #fff; color:#000;">
                          <td class="text-left small"><?=$tipoX?></td>
                          <td class="text-center"><?=$ingreso_4X?></td>
                          <td class="text-center"><?=$salida_4X?></td> 
                          <td class="text-center"><?=$ingreso_1X?></td>
                          <td class="text-center"><?=$salida_1X?></td> 
                          <td class="text-center"><?=$ingreso_2X?></td>
                          <td class="text-center"><?=$salida_2X?></td> 
                          <td class="text-center"><?=$ingreso_3X?></td>
                          <td class="text-center"><?=$salida_3X?></td>  
                      </tr>
                    <?php  

                    $index++;} ?>
                   </tbody>
                </table>