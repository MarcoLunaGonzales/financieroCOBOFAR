<?php
//Antes de modulo descuentos 19/07/2022
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
$stmt = $dbh->prepare("SELECT codigo,nombre from descuentos where cod_estadoreferencial=1 and tipo_descuento=1 order by codigo");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_descuento);
$stmt->bindColumn('nombre', $nombre_descuento);
$descuentos_array=array();
$i=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    $descuentos_array[$i]=$codigo_descuento;
    $i++;
}
$cod_descuento_as=100; //aporte al sindicato
$cod_estadoreferencial=1;
if($alert==true){
    $delimitador = ";";
    $longitudDeLinea = 1000;
    $x=0;
    $datos=array();
    $fichero=fopen($destino,'r');
    //borrar todo y cargar de nuevo
    if($opcionCargar==3){
        //borramos logicamente
        $stmte = $dbh->prepare("DELETE from descuentos_personal_mes WHERE cod_gestion=$codGestion and cod_mes=$codMes");
        $stmte->execute();
        $stmtkardexDelete = $dbh->prepare("DELETE from personal_kardex_mes WHERE cod_gestion=$codGestion and cod_mes=$codMes");
        $stmtkardexDelete->execute();
        $stmtAnticipoDelete = $dbh->prepare("DELETE from anticipos_personal WHERE cod_gestion=$codGestion and cod_mes=$codMes");
        $stmtAnticipoDelete->execute(); 
        while((($datos=fgetcsv($fichero,$longitudDeLinea,$delimitador))!=FALSE)){
            $x++;
            if($x>1){
                $cod_personal=$datos[0];
                if($cod_personal>0){
                    // $ci=$datos[1];
                    // $nombre=$datos[2];
                    // $area=$datos[3];
                    $faltas=$datos[4];
                    $faltas_sin_descuento=$datos[5];
                    $dias_vacacion=$datos[6];
                    $dias_trabajados_l_s=$datos[7];
                    $domingos_normal=$datos[8];
                    $feriado_normal=$datos[9];
                    $noche_normal=$datos[10];
                    $domingo_reemplazo=$datos[11];
                    $feriado_reemplazo=$datos[12];
                    $ordinario_reemplazo=$datos[13];
                    $hxdomingo_extras=$datos[14];
                    $hxferiado_extras=$datos[15];
                    $hxdnnormal_extras=$datos[16];
                    $reintegro=$datos[17];
                    //$comision_ventas=$datos[18];
                    $obs_reintegro=$datos[18];  
                    $sqlKardex="INSERT INTO personal_kardex_mes(cod_personal,cod_gestion,cod_mes,faltas,faltas_sin_descuento,dias_vacacion,dias_trabajados,domingos_trabajados_normal,feriado_normal,noche_normal,domingo_reemplazo,feriado_reemplazo,ordianrio_reemplazo,hxdomingo_extras,hxferiado_extras,hxdnnormal_extras,reintegro,obs_reintegro,cod_estadoreferencial) 
                        VALUES ('$cod_personal','$codGestion','$codMes','$faltas','$faltas_sin_descuento','$dias_vacacion','$dias_trabajados_l_s','$domingos_normal','$feriado_normal','$noche_normal','$domingo_reemplazo','$feriado_reemplazo','$ordinario_reemplazo','$hxdomingo_extras','$hxferiado_extras','$hxdnnormal_extras','$reintegro','$obs_reintegro',$cod_estadoreferencial)";
                    //echo $sqlKardex;
                    $stmtKardex = $dbh->prepare($sqlKardex);
                    $flagSuccess=$stmtKardex->execute();                    
                    //**INGRESAMOS ANTICIPOS
                    $anticipos=$datos[19];
                    $stmtAnticipos = $dbh->prepare("INSERT INTO anticipos_personal (cod_gestion,cod_mes,cod_personal,monto,fecha_registro, cod_estadoreferencial) 
                        VALUES ($codGestion,$codMes,$cod_personal,$anticipos,NOW(),$codEstado)");
                    $flagSuccess=$stmtAnticipos->execute();
                    $contador_excel=20;
                    for ($j=0; $j <count($descuentos_array) ; $j++) { 
                        $codDescuento=$descuentos_array[$j];
                        if(isset($datos[$contador_excel])){
                            $monto=formatearNumerosExcel($datos[$contador_excel]);
                        }else{
                            $monto=0;
                        }
                        //inserta nuevos
                        if(verificarExistenciaPersona($cod_personal)){
                            $stmtDescuentos = $dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                            VALUES ($codDescuento,$cod_personal,$codGestion,$codMes,$monto,$codEstado)");
                            $flagSuccess=$stmtDescuentos->execute();    
                        }
                        $contador_excel++;
                    }
                    // //****aporte al sindicato
                    // $aporte_sindicato=obtenerBonoDescuentoPactado($cod_personal,$cod_descuento_as,2);
                    // $stmtSindicato=$dbh->prepare("INSERT INTO descuentos_personal_mes (cod_descuento, cod_personal,cod_gestion,cod_mes,monto, cod_estadoreferencial) 
                    //     VALUES ($cod_descuento_as,$cod_personal,$codGestion,$codMes,$aporte_sindicato,$codEstado)");
                    // $flagSuccess=$stmtSindicato->execute();
                }
            }
        }
        showAlertSuccessError($flagSuccess,'?opcion=planillasSueldoPersonal');
    }
    fclose($fichero); 
    // unlink($destino); 


}
?>