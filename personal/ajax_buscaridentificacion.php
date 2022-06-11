<?php
require_once '../conexion.php';
$identificacion = $_POST["identificacion"];

$dbh = new Conexion();
$query_retiro = "SELECT codigo,paterno,materno,primer_nombre,cod_estadopersonal from personal where cod_estadoreferencial=1 and identificacion ='$identificacion' ";
$statementTiposRetiro = $dbh->query($query_retiro);
$contador=0;
while ($row = $statementTiposRetiro->fetch()){ 
    $codigo=$row['codigo'];
    $paterno=$row['paterno'];
    $materno=$row['materno'];
    $primer_nombre=$row['primer_nombre'];
    $cod_estadopersonal=$row['cod_estadopersonal'];
    switch ($cod_estadopersonal) {
        case '1':
            echo "1@".$primer_nombre." ".$paterno." ".$materno;
        break;
        case '2':
            echo "2@".$primer_nombre." ".$paterno." ".$materno;;
        break;
        case '3':
            echo "3@".$primer_nombre." ".$paterno." ".$materno;;
        break;
    }
    $contador++;
}
if($contador==0){
    echo "0@";
}
?>
    


