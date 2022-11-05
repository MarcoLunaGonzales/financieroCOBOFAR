<?php
require_once '../conexion.php';
// require_once 'configModule.php';
require_once '../functions.php';
$dbh = new Conexion();
//header('Content-Type: application/json');

$codigo_permiso = $_GET["codigo_permiso"];
$codigo_personal = $_GET['cod_personal'];

$comentario_tipopermiso="";
if($codigo_permiso==7){//tipo permiso a cuenta de vacaciones
    $sql="SELECT ing_planilla FROM personal where codigo=$codigo_personal";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()){ 
        $ing_planilla=$row['ing_planilla'];
    }
    $fecha_actual=date('Y-m-d');
    $stmtEscalas = $dbh->prepare("SELECT anios_inicio,anios_final,dias_vacacion from escalas_vacaciones where cod_estadoreferencial=1");
    $stmtEscalas->execute();
    $stmtEscalas->bindColumn('anios_inicio', $anios_inicio);
    $stmtEscalas->bindColumn('anios_final', $anios_final);
    $stmtEscalas->bindColumn('dias_vacacion', $dias_vacacion);  
    $i=0;
    while ($rowEscalas = $stmtEscalas->fetch(PDO::FETCH_ASSOC))
    {
      $array_escalas[$i]=$anios_inicio.",".$anios_final.",".$dias_vacacion;
      $i++;
    }
    $datos_array=explode('#',obtenerDiasVacacion($ing_planilla,$fecha_actual,$array_escalas));
    $total_dias_vacacion=$datos_array[0];
    // $diferencia_mes_sobrante=$datos_array[1];
    // $diferencia_dias_sobrante=$datos_array[2];
    $dias_uzadas=obtenerDiasVacacionUzadas($codigo_personal,-100);
    $saldo_vacacion=$total_dias_vacacion-$dias_uzadas;

     // $comentario_tipopermiso="Tienes $saldo_vacacion días disponibles de vacación";
    $comentario_tipopermiso="Tienes días disponibles de vacación";
}
?>

<input  type='text' class='form-control' readonly="true" style="background: white;color:red;font-size: 18px;"  value="<?=$comentario_tipopermiso?>">
<input  type='hidden' id="dias_disponibles" name="dias_disponibles" value="<?=$saldo_vacacion?>">




          
