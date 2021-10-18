<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$tipoProveedorCliente=$_GET["tipo"];

$sql="";
if($tipoProveedorCliente==1){
	$sql="select p.codigo, p.nombre from af_proveedores p where p.cod_estado=1 order by p.nombre";	
}
if($tipoProveedorCliente==2){
	$sql="select c.codigo, c.nombre from clientes c where c.cod_estadoreferencial=1 order by c.nombre";
}
if($tipoProveedorCliente==3){//Personal
    $sql="SELECT codigo, CONCAT_WS(' ',primer_nombre,paterno,materno)as nombre from personal where cod_estadopersonal=1 and cod_estadoreferencial=1 order by nombre";
}
if($tipoProveedorCliente==4){//Sucursal
    $sql="SELECT a.codigo, a.nombre,a.abreviatura from areas a where  a.cod_estado=1 and centro_costos=1
    order by 2";
}
if($tipoProveedorCliente==5){//personal desvinculado where 
    $sql="SELECT codigo, CONCAT_WS(' ',primer_nombre,paterno,materno)as nombre from personal where cod_estadopersonal=3 and cod_estadoreferencial=1 order by nombre";
}
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':nombre', $nombre);
$stmt->execute();

?>
<select name="proveedor_cliente" id="proveedor_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
    <?php 
        while ($row = $stmt->fetch()){ 
    ?>
         <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
     <?php 
        } 
    ?>
 </select>