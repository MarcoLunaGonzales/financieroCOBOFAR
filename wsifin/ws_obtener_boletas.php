<?php

error_reporting(0);
require '../functions.php';
require '../boletas/boletas_retroactivo_html.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])&&isset($datos['codPlanilla'])&&isset($datos['codGestion'])&&isset($datos['codPersonal'])&&isset($datos['codMes'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $codPlanilla=$datos['codPlanilla'];//recibimos el codigo de planilla
            $codGestion=$datos['codGestion'];//recibimos la gestion de planilla
            $codMes=$datos['codMes'];//recibimos el Mes de planilla
            $codPersonal=$datos['codPersonal'];//recibimos el codigo del personal
            $estado=0;
            $mensaje="";
            if($accion=="ObtenerBoletaRetroactivo"){
                try{
                    $html=generarHtmlBoletaRetroactivo($codPlanilla,$codGestion,$codPersonal);
                    $boleta = datosPDFBoleta($html); 
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Boleta Obtenida Correctamente", 
                        "boleta64"=>$boleta['base64']
                    );            
                }catch(Exception $e){                
                    $mensaje = "Boleta Inexistente";
                    $resultado=array("estado"=>false, 
                        "mensaje"=>$mensaje, 
                        "factura64"=>array());
                }
            }elseif($accion=="ObtenerBoletaSueldos"){
                try{
                    $html=generarHtmlBoletaSueldosMes($codPlanilla,$codGestion,$codMes,$codPersonal);
                    $boleta = datosPDFBoleta($html); 
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Boleta Obtenida Correctamente", 
                        "boleta64"=>$boleta['base64']
                    );            
                }catch(Exception $e){                
                    $mensaje = "Boleta Inexistente";
                    $resultado=array("estado"=>false, 
                        "mensaje"=>$mensaje, 
                        "factura64"=>array());
                }

            }else{
                $resultado=array("estado"=>false, 
                            "mensaje"=>"No existe la accion solicitada."); 
            }
        }else{
            $resultado=array("estado"=>false, 
                            "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
        }
    }else{
        $resultado=array(
            "estado"=>false,
            "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
    }
    header('Content-type: application/json');
    echo json_encode($resultado);
}else{
    $resp=array("estado"=>false, 
                "mensaje"=>"ACCESO DENEGADO!. Usted no tiene permiso para ver este contenido.");
    header('Content-type: application/json');
    echo json_encode($resp);
}


?>