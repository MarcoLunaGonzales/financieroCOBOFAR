<?php
require_once '../conexion.php';
session_start();
$data=explode("-",$_GET['codigo']);
$tipo=$data[0][0];
$mes=(int)$data[0][1].$data[0][2];
$numero=(int)$data[1];
$gestion=$_SESSION['globalNombreGestion'];
$dbh = new Conexion();
$sql="SELECT c.codigo FROM comprobantes c join tipos_comprobante t on t.codigo=c.cod_tipocomprobante
where t.abreviatura='$tipo' AND MONTH(c.fecha)='$mes' and c.numero='$numero' and year(c.fecha)='$gestion' limit 1;";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$existe=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existe=$row['codigo'];
}
echo "#####".$existe;
?>