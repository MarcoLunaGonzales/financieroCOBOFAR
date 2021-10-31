<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
$dbh = new Conexion();

// $codBono=$_POST["codBono"];
$codMes=$_POST["cod_mes"];
$codGestion=$_POST["codGestion"];
$opcionCargar=$_POST["opcionCargar"];
$codEstado="1";

$ruta="bonos/upload/";
echo "<br><br><br><br><br>";
foreach ($_FILES as $key){
    $ruta_temporal=$key["tmp_name"];
    if($key["type"]=="text/csv" or $key["type"]=="application/vnd.ms-excel"){
        //echo "<br>aqui";
        $nombre_nuevo= formatoNombreArchivoExcel();
        $destino=$ruta.$nombre_nuevo;
        move_uploaded_file($ruta_temporal,$destino);
        $alert=true;

    }else{

        $alert=false;
        showAlertSuccessError($alert,$urlList);
    }
}
$flagSuccess=false;
$stmt = $dbh->prepare("SELECT codigo,nombre from bonos where cod_estadoreferencial=1 order by codigo");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_bono);
$stmt->bindColumn('nombre', $nombre_bono);
$bonos_array=array();
$i=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $bonos_array[$i]=$codigo_bono;
    $i++;
}

if($alert==true){
    $delimitador = "|";
    $longitudDeLinea = 1000;
    $x=0;
    $datos=array();
    $fichero=fopen($destino,'r');
    
    //sobreescribir existentes e insertar nuevos
    if($opcionCargar==1){
        while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
            $x++;
            if($x>1){
                $cod_personal=$datos[0];
                // $area=$datos[1];
                // $ci=$datos[2];
                // $nombre=$datos[3];
                $contador_excel=4;
                for ($j=0; $j <count($bonos_array) ; $j++) { 
                    $codBono=$bonos_array[$j];
                    if(isset($datos[$contador_excel])){
                        $monto=formatearNumerosExcel($datos[$contador_excel]);
                    }else{
                        $monto=0;
                    }
                    //inserta nuevos
                    if((verificarBonoPersonaMes($cod_personal, $codMes, $codBono)==0) and (verificarExistenciaPersona($cod_personal)==true)){
                        $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                        VALUES ($codBono,$cod_personal,$codGestion,$codMes,$monto,$codEstado)");
                        $flagSuccess=$stmt->execute();    
                    }else{//actualiza los existentes
                        $stmt = $dbh->prepare("UPDATE bonos_personal_mes SET monto=$monto 
                        WHERE cod_bono=$codBono and cod_gestion=$codGestion and cod_personal=$cod_personal and cod_mes=$codMes and cod_estadoreferencial=1");
                        $flagSuccess=$stmt->execute();  
                    }  
                    $contador_excel++;
                }
            }
        }
        showAlertSuccessError($flagSuccess,$urlList);
    }


    //mantener existentes e insertar nuevos
    if($opcionCargar==2){
        while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
            $x++;
            if($x>1){
                $cod_personal=$datos[0];
                // $area=$datos[1];
                // $ci=$datos[2];
                // $nombre=$datos[3];
                $contador_excel=4;
                for ($j=0; $j <count($bonos_array) ; $j++) { 
                    $codBono=$bonos_array[$j];
                    if(isset($datos[$contador_excel])){
                        $monto=formatearNumerosExcel($datos[$contador_excel]);
                    }else{
                        $monto=0;
                    }
                    //inserta nuevos
                    if((verificarBonoPersonaMes($cod_personal, $codMes, $codBono)==0) and (verificarExistenciaPersona($cod_personal)==true)){
                        $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                        VALUES ($codBono,$cod_personal,$codGestion,$codMes,$monto,$codEstado)");
                        $flagSuccess=$stmt->execute();    
                    }  
                    $contador_excel++;
                }
            }
        }
        showAlertSuccessError($flagSuccess,$urlList);
    }
    //borrar todo y cargar de nuevo
    if($opcionCargar==3){

        while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
            $x++;
            //borramos logicamente
            $stmte = $dbh->prepare("UPDATE bonos_personal_mes SET cod_estadoreferencial=2 
            WHERE  cod_gestion=$codGestion and cod_mes=$codMes");
            $flagSuccess=$stmte->execute(); 
            if($x>1){
                $cod_personal=$datos[0];
                //eliminar lógicamente los existentes
                if(verificarExistenciaPersona($cod_personal)){
                    // $area=$datos[1];
                    // $ci=$datos[2];
                    // $nombre=$datos[3];
                    $contador_excel=4;
                    for ($j=0; $j <count($bonos_array); $j++) { 
                        $codBono=$bonos_array[$j];
                        if(isset($datos[$contador_excel])){
                            $monto=formatearNumerosExcel($datos[$contador_excel]);
                        }else{
                            $monto=0;
                        }
                        //inserta nuevos
                        $stmt = $dbh->prepare("INSERT INTO bonos_personal_mes (cod_bono, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                        VALUES ('$codBono','$cod_personal','$codGestion','$codMes','$monto','$codEstado')");
                        $flagSuccess=$stmt->execute();
                        $contador_excel++;
                    }    
                }
            }
        }
        showAlertSuccessError($flagSuccess,$urlList);
    }
    fclose($fichero); 
   unlink($destino); 


}
?>