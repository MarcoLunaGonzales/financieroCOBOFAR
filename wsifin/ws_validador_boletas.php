<?php
// SERVICIO WEB PARA FACTURAS

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])&&isset($datos['codigo_url'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $codigo_url=$datos['codigo_url'];//recibimos el codigo personal
            $estado=0;
            $mensaje="";
            if($accion=="ObtenerValidacionBoletaRetroactivo"){
                try{
                    $lstPlanillasRetroactivo = validadorBoletaRetroactivo($codigo_url);//llamamos a la funcion 
                    $totalComponentes=count($lstPlanillasRetroactivo);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Datos obtenidos correctamente", 
                        "datosBoleta"=>$lstPlanillasRetroactivo, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de listar las planillas");
                }
            }elseif($accion=="ObtenerValidacionBoletaSueldos"){
                try{
                    $lstPlanillas = validadorBoletaSueldos($codigo_url);//llamamos a la funcion 
                    $totalComponentes=count($lstPlanillas);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Datos obtenidos correctamente", 
                        "datosBoleta"=>$lstPlanillas, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de verificar la boleta.");
                }
            }else{
                $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"No existe la Accion Solicitada.");
            }
        }else{
            $resultado=array(
                "estado"=>false,
                "mensaje"=>"ACCESO DENEGADO!. Credenciales Incorrectas.");
        }
    }else{
        $resultado=array(
                "estado"=>false,
                "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
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

function validadorBoletaRetroactivo($codigo_url){
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';
    $dbh = new Conexion();
    $array_codigo=explode('.', $codigo_url);
    // echo $codigo_url;
    if(count($array_codigo)==4){
        $cod_personal=(int)$array_codigo[0];
        $cod_planilla=(int)$array_codigo[1];
        $cod_mes=0;
        $cod_gestion=(int)$array_codigo[2];
        // $numero_exa=hexdec($array_codigo[4]);//llegará en exadecimal
        $numero_exa=$array_codigo[3];//llegará en exadecimal
        $numero_adicional_exa=alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion);
         // echo $numero_exa."-".$numero_adicional_exa;
        // echo $array_codigo[4]."".dechex($numero_adicional);
        if($numero_exa===$numero_adicional_exa){
            $estado=true;
        }else{
            $estado=false;
        }
    }else{
        $estado=false;
    }
    $nombre="";
    $cargo="";
    $filas = array();
    if($estado){
        $sql="SELECT f.primer_nombre as nombres,CONCAT(f.paterno,' ', f.materno) as apellidos,(select c.nombre from cargos c where c.codigo=f.cod_cargo) as cargo,pm.liquido_pagable,(select g.nombre from gestiones g where g.codigo =p.cod_gestion)as gestion
        from planillas_retroactivos p join planillas_retroactivos_detalle pm on p.codigo=pm.cod_planilla join personal f on pm.cod_personal=f.codigo
        where pm.cod_planilla=$cod_planilla and f.codigo=$cod_personal";
        // echo $sql;
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()){
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            exit;       
        }
    }
    return $filas;
}


function validadorBoletaSueldos($codigo_url){
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';
    $dbh = new Conexion();
    $array_codigo=explode('.', $codigo_url);
    if(count($array_codigo)==5){
        // echo $codigo_url;
        $cod_personal=(int)$array_codigo[0];
        $cod_planilla=(int)$array_codigo[1];
        $cod_mes=(int)$array_codigo[2];
        $cod_gestion=(int)$array_codigo[3];
        // $numero_exa=hexdec($array_codigo[4]);//llegará en exadecimal
        $numero_exa=$array_codigo[4];//llegará en exadecimal
        $numero_adicional_exa=alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion);
        // echo $numero_exa."-".$numero_adicional;
        // echo $array_codigo[4]."".dechex($numero_adicional);
        if($numero_exa===$numero_adicional_exa){
            // echo $codigo_url;
            $estado=true;
        }else{
            $estado=false;
        }
    }else{
        $estado=false;
    }
    $filas = array();
    if($estado){
        $sql="SELECT f.primer_nombre as nombres,CONCAT(f.paterno,' ', f.materno) as apellidos,(select c.nombre from cargos c where c.codigo=f.cod_cargo) as cargo,pm.liquido_pagable,(select m.nombre from meses m where m.codigo=p.cod_mes)as mes,(select g.nombre from gestiones g where g.codigo =p.cod_gestion)as gestion
            from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal f on pm.cod_personalcargo=f.codigo
            where pm.cod_planilla=$cod_planilla and f.codigo=$cod_personal";
            // echo $sql;
        $stmt = $dbh->prepare($sql);
        if($stmt->execute()){
            $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            exit;       
        }
    }
    return $filas;
}
