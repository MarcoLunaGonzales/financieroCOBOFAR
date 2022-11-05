<?php
// SERVICIO WEB PARA FACTURAS
$fecha_x=date('Y-m-d');
$nombre_archivo="log_solper_".$fecha_x.".txt";
//limpiamos en archivo
//$arch = fopen ($nombre_archivo, "w+") or die ("nada");
// fwrite($arch,"");
// fclose($arch);
//archivo limpiado


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $estado=0;
            $mensaje="";
            if($accion=="listPermisos"){
                $codPersonal=$datos['codPersonal'];//recibimos el codigo personal
                try{
                    $lst = listaPermisos($codPersonal);//llamamos a la funcion 
                    $totalComponentes=count($lst);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Lista de items obtenida correctamente", 
                        "lst"=>$lst, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de listar los items");
                }
            }elseif($accion=="listTipoPermisos"){

                $codPersonal=$datos['codPersonal'];//recibimos el codigo personal
                try{
                    $lst = listaTiposPermisos();//llamamos a la funcion 
                    $totalComponentes=count($lst);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Lista de items obtenida correctamente", 
                        "lst"=>$lst, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de listar los items");
                }
            }elseif($accion=="totalFeriados"){
                // $codPersonal=$datos['codPersonal'];//recibimos el codigo personal
                // echo "aaaqui";
                $fi=$datos['fi'];//recibimos fecha inicio
                $ff=$datos['ff'];//recibimos fecha final
                $cantidad = obtenerTotalferiados_fechas($fi,$ff);//llamamos a la funcion 
                // $totalComponentes=count($cantidad);
                $resultado=array(
                    "estado"=>true,
                    "mensaje"=>"Lista de items obtenida correctamente", 
                    "cantidad"=>$cantidad
                    );

            }elseif($accion=="solicitudPermisoSave"){
                //en un log capturamos json de ingreso
                $archivo=fopen("logs/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de 
                fwrite($archivo,"*****");
                fwrite($archivo, "".PHP_EOL);
                $string_datos=implode("|", $datos);
                //guardar en log
                fwrite($archivo,"ENTRANDO:".$string_datos);
                fwrite($archivo, "".PHP_EOL);

                $cod_personal=$datos['cod_personal'];
                $motivo=$datos['motivo'];
                $fecha_inicio=$datos['fecha_inicio'];
                $hora_inicio=$datos['hora_inicio'];
                $fecha_final=$datos['fecha_final'];
                $hora_final=$datos['hora_final'];
                $observaciones=$datos['observaciones'];
                $globalUser=$datos['globalUser'];
                $fecha_evento=$datos['fecha_evento'];
                $dias_permiso=$datos['dias_permiso'];
                $cod_sucursal=$datos['cod_sucursal'];
                $minutos_solicitados=$datos['minutos_solicitados'];
    
                $estado = solicitudPermisoSave_bd($cod_personal,$motivo,$fecha_inicio,$hora_inicio,$fecha_final,$hora_final,$observaciones,$globalUser,$fecha_evento,$dias_permiso,$cod_sucursal,$minutos_solicitados);
                if($estado){
                    $mensaje="Datos guardados correctamente";
                }else{
                    $mensaje="Hubo un problema al guardar los datos";
                }
                $resultado=array(
                    "estado"=>$estado,
                    "mensaje"=>$mensaje
                    );
                
                //en un log capturamos json salida en log
                fwrite($archivo, "SALIENDO:".json_encode($resultado));
                fwrite($archivo, "".PHP_EOL);
            }else{
                $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"No existe la accion solicitada.", 
                    "lst"=>null, 
                    "totalComponentes"=>0
                    );
                //en un log capturamos json de ingreso
                $archivo=fopen("logs/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de 
                fwrite($archivo,"*****");
                fwrite($archivo, "".PHP_EOL);
                $string_datos=implode("|", $datos);
                //guardar en log
                fwrite($archivo,"ENTRANDO:".$string_datos);
                fwrite($archivo, "".PHP_EOL);
                //en un log capturamos json salida en log
                fwrite($archivo, "SALIENDO:".json_encode($resultado));
                fwrite($archivo, "".PHP_EOL);
            }
        }else{
            $resultado=array(
                "estado"=>false,
                "mensaje"=>"ACCESO DENEGADO!. Credenciales Incorrectas.");
            //en un log capturamos json de ingreso
            $archivo=fopen("logs/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de 
            fwrite($archivo,"*****");
            fwrite($archivo, "".PHP_EOL);
            $string_datos=implode("|", $datos);
            //guardar en log
            fwrite($archivo,"ENTRANDO:".$string_datos);
            fwrite($archivo, "".PHP_EOL);
            //en un log capturamos json salida en log
            fwrite($archivo, "SALIENDO:".json_encode($resultado));
            fwrite($archivo, "".PHP_EOL);
        }
    }else{
        $resultado=array(
            "estado"=>false,
            "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
        //en un log capturamos json de ingreso
        $archivo=fopen("logs/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de 
        fwrite($archivo,"*****");
        fwrite($archivo, "".PHP_EOL);
        $string_datos=implode("|", $datos);
        //guardar en log
        fwrite($archivo,"ENTRANDO:".$string_datos);
        fwrite($archivo, "".PHP_EOL);
        //en un log capturamos json salida en log
        fwrite($archivo, "SALIENDO:".json_encode($resultado));
        fwrite($archivo, "".PHP_EOL);
    }

    header('Content-type: application/json');
    echo json_encode($resultado); 
}else{
    $resultado=array(
                "estado"=>false,
                "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
    header('Content-type: application/json');
    echo json_encode($resultado);
}

function listaPermisos($cod_personal_q){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT pp.codigo,pp.cod_personal,pp.cod_tipopermiso,tpp.nombre as nombre_tipopermiso,pp.fecha_inicial,pp.hora_inicial,pp.fecha_final,pp.hora_final,pp.observaciones,pp.cod_estado,epp.nombre as nombre_estado,pp.fecha_evento,pp.dias_permiso,pp.minutos_permiso,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal)as nombre_personal,pp.cod_personal_autorizado,pp.observaciones_rechazo,pp.created_at,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal_autorizado)as nombre_personal_autorizado,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.cod_personal_aprobado)as nombre_personal_aprobado,a.abreviatura as area,(select CONCAT_WS(' ',p.primer_nombre,p.paterno) from personal p where p.codigo=pp.created_by)as nombre_personal_solicitado
    from personal_permisos pp join estados_permisos_personal epp on pp.cod_estado=epp.codigo join tipos_permisos_personal tpp on pp.cod_tipopermiso=tpp.codigo join areas a on pp.cod_area=a.codigo
    where (pp.created_by=$cod_personal_q or pp.cod_personal=$cod_personal_q)   order by pp.created_at desc limit 10";
    $stmt = $dbh->prepare($sql);
    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }else{
        //echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}

function listaTiposPermisos(){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT codigo,nombre FROM tipos_permisos_personal where cod_estadoreferencial=1 order by nombre";//solo mostrar mayor a 2022
    $stmt = $dbh->prepare($sql);
    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }else{
        //echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}

function obtenerTotalferiados_fechas($fecha_inicial,$fecha_final){
    require_once '../conexion.php';
  $dbh = new Conexion();
  $stmt = $dbh->prepare("SELECT count(*) as contador from dias_feriados
  where fecha BETWEEN '$fecha_inicial' and '$fecha_final'");
  $stmt->execute();
  $valor=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $valor=$row['contador'];
  }
  return($valor);
}

function solicitudPermisoSave_bd($cod_personal,$motivo ,$fecha_inicio,$hora_inicio,$fecha_final,$hora_final,$observaciones,$globalUser,$fecha_evento,$dias_permiso,$cod_sucursal,$minutos_solicitados){
    require_once '../conexion.php';
  $dbh = new Conexion();
    $cod_estadoreferencial=3;//estado enviado
    $cod_sistema=3;//viene desde la icobofar
    $sql="INSERT INTO personal_permisos (cod_personal, cod_tipopermiso,fecha_inicial,hora_inicial, fecha_final,hora_final,observaciones,cod_estado,created_at,created_by,fecha_evento,dias_permiso, cod_area,minutos_permiso,cod_sistema) VALUES ($cod_personal,'$motivo' ,'$fecha_inicio','$hora_inicio','$fecha_final','$hora_final','$observaciones','$cod_estadoreferencial',NOW(),'$globalUser','$fecha_evento','$dias_permiso','$cod_sucursal','$minutos_solicitados','$cod_sistema')";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
    // $fecha_x=date('Y-m-d');
    // $nombre_archivo="log_solper_".$fecha_x.".txt";
    // //limpiamos en archivo
    // //$arch = fopen ($nombre_archivo, "w+") or die ("nada");
    // // fwrite($arch,"");
    // // fclose($arch);
    // //archivo limpiado
    // $archivo=fopen("logs/".$nombre_archivo, "a") or die ("#####0#####");//a de apertura de 
    // fwrite($archivo,"sql:".$sql);
    // fwrite($archivo, "".PHP_EOL);
    return($flagSuccess);
}