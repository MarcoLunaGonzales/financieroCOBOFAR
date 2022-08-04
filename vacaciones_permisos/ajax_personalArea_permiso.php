<?php
require_once '../conexion.php';
require_once '../functions.php';
$dbh = new Conexion();


$codigo=$_GET['codigo'];

$sqlPersonal="SELECT p.codigo,p.paterno,p.materno,p.primer_nombre,a.nombre as area,p.turno
from personal p join areas a on p.cod_area=a.codigo
where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 and p.cod_area=$codigo
order by p.paterno";

?>


<select class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-live-search="true" name="cod_personal" id="cod_personal" required="true">
    <option value="">SELECCIONE UN ITEM</option>
    <?php
      $stmt = $dbh->prepare($sqlPersonal);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoP=$row['codigo'];
        $paternoP=$row['paterno'];
        $maternoP=$row['materno'];
        $primer_nombreP=$row['primer_nombre'];
        $areaP=$row['area'];
        $turnoP=$row['turno'];
        if($turnoP==1){
            $areaP.=" TM";
        }elseif($turnoP==2){
            $areaP.=" TT";
        }
    ?>
    <option value="<?=$codigoP;?>" data-subtext="<?=$areaP?>"><?=$paternoP;?> <?=$maternoP;?> <?=$primer_nombreP;?></option>    
    <?php
    }
    ?>
</select>