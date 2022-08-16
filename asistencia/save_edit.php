<?php

require_once '../conexion.php';
require_once '../functions.php';

session_start();
$dbh = new Conexion();
$globalUser=$_SESSION['globalUser'];

$codigo_asistencia=$_POST['codigo_asistencia'];
$ingresoMarcado=$_POST["hora_ingreso"];
$salidaMarcado=$_POST['hora_salida'];

// $ingreso_asignado=$_POST['ingreso_asignado'];
// $salida_asignado=$_POST['salida_asignado'];


//Capturamos datos
$sql="SELECT entrada_asig,salida_asig,marcado1,marcado2 from asistencia_procesada where codigo=$codigo_asistencia";
$stmtFecha = $dbh->prepare($sql);
$stmtFecha->execute();
$stmtFecha->bindColumn('marcado1', $marcado1);
$stmtFecha->bindColumn('marcado2', $marcado2);
$stmtFecha->bindColumn('entrada_asig',$ingreso_asignado);
$stmtFecha->bindColumn('salida_asig',$salida_asignado);
while ($rowFecha = $stmtFecha->fetch()) {
  
}

//reproceso de atraso
$minutosAtraso=0;
$minutosTrabajados=0;
$minutosAbandono=0;
$minutosExtras=0;

// $minutos_tolerancia=5;
$minutos_tolerancia=obtenerValorConfiguracionPlanillas(37);

$tarde = strtotime($ingresoMarcado)-strtotime($ingreso_asignado);
if($tarde>=0){//echo "LLEGO A TIEMPO\n";        
    $dateTimeObject1 = date_create($ingreso_asignado); 
    $dateTimeObject2 = date_create($ingresoMarcado); 
    $difference = date_diff($dateTimeObject1, $dateTimeObject2);    
    $minutosAtraso_x = $difference->h * 60;
    $minutosAtraso_x += $difference->i;
    if($minutosAtraso_x>$minutos_tolerancia){
        $minutosAtraso+=$minutosAtraso_x;
    }

}
// echo $salidaMarcado."*";
if($salidaMarcado<>"" && $salidaMarcado<>null){//si no marco su hora salida
    //Minutos Trabajados Marcados
    $dateTimeMarc1 = date_create($ingresoMarcado); 
    $dateTimeMarc2 = date_create($salidaMarcado); 
    $differenceMarc = date_diff($dateTimeMarc1, $dateTimeMarc2);   
    // $dif_hora= $differenceMarc->h;
    // $minutes = $differenceMarc->days * 24 * 60;
    $minutosTrabajados += $differenceMarc->h * 60;
    $minutosTrabajados += $differenceMarc->i;
    //minutos abandono trabajados
    $dateTimeAbandono1 = date_create($salida_asignado); 
    $dateTimeAbandono2 = date_create($salidaMarcado); 
    $differenceAbandono = date_diff($dateTimeAbandono1, $dateTimeAbandono2);   
    $abandono = strtotime($salida_asignado)-strtotime($salidaMarcado);
    if($abandono>=0){//echo "Salio antes\n";        
      $minutosAbandono += $differenceAbandono->h * 60;
      $minutosAbandono += $differenceAbandono->i;          
      // $minutosAbandono +=
    }else{//Salió Despues
      $minutosExtras += $differenceAbandono->h * 60;
      $minutosExtras += $differenceAbandono->i;          
    }
}else{
    $dateTimeObject1 = date_create($ingreso_asignado); 
    $dateTimeObject2 = date_create($salida_asignado); 
    $difference = date_diff($dateTimeObject1, $dateTimeObject2);    
    $minutosAbandono += $difference->h * 60;
    $minutosAbandono += $difference->i;
    // $minutosAbandono+=$minutosAsignados;
}





// Prepare
$sql="UPDATE asistencia_procesada set marcado1='$ingresoMarcado',marcado2='$salidaMarcado',minutos_trabajados='$minutosTrabajados',minutos_atraso='$minutosAtraso',minutos_extras='$minutosExtras',minutos_abandono='$minutosAbandono' ,modified_by='$globalUser',modified_at=NOW(),marcado3='$marcado1',marcado4='$marcado2' where codigo=$codigo_asistencia";
$stmt = $dbh->prepare($sql);
$flagSuccess=$stmt->execute();

$flagSuccess=2;
if($flagSuccess){
    echo 1;
}else echo 2;


?>