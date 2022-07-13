<?php
// SERVICIO WEB PARA FACTURAS

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])&&isset($datos['codPersonal'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $codPersonal=$datos['codPersonal'];//recibimos el codigo personal
            $estado=0;
            $mensaje="";
            if($accion=="listPlanillasRetroactivos"){
                try{
                    $lstPlanillasRetroactivo = ListPlanillaRetroactivos($codPersonal);//llamamos a la funcion 
                    $totalComponentes=count($lstPlanillasRetroactivo);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Lista de Planillas obtenida correctamente", 
                        "lstPlanillas"=>$lstPlanillasRetroactivo, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de listar las planillas");
                }
            }elseif($accion=="listPlanillasSueldos"){
                try{
                    $lstPlanillasRetroactivo = ListPlanillaSuedos($codPersonal);//llamamos a la funcion 
                    $totalComponentes=count($lstPlanillasRetroactivo);
                    $resultado=array(
                        "estado"=>true,
                        "mensaje"=>"Lista de Planillas obtenida correctamente", 
                        "lstPlanillas"=>$lstPlanillasRetroactivo, 
                        "totalComponentes"=>$totalComponentes
                        );
                }catch(Exception $e){
                   $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"Hubo un error al momento de listar las planillas");
                }
            }elseif($accion=="ObtenerDatosPersonal"){
                $DatosPersonal = ObtenerDatosPersonal($codPersonal);//llamamos a la funcion 
                $totalComponentes=count($DatosPersonal);
                $resultado=array(
                    "estado"=>true,
                    "mensaje"=>"Datos del Personal obtenida correctamente", 
                    "datosPersonal"=>$DatosPersonal, 
                    "totalComponentes"=>$totalComponentes
                    );
            }else{
                $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"No existe la Accion Solicitada.", 
                    "lstPlanillasRetroactivo"=>null, 
                    "totalComponentes"=>0
                    );
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


function ListPlanillaRetroactivos($codigo_personal){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT p.codigo,p.cod_gestion,(select g.nombre from gestiones g where g.codigo=p.cod_gestion) as nombre_gestion
    from planillas_retroactivos p join planillas_retroactivos_detalle pd on p.codigo=pd.cod_planilla
    where p.cod_estadoplanilla=3 and pd.cod_personal=$codigo_personal and p.cod_gestion>=3585";//solo mostrar mayor a 2022
    $stmt = $dbh->prepare($sql);
    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }else{
        echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}
function ListPlanillaSuedos($codigo_personal){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT p.codigo,p.cod_gestion,p.cod_mes,(select m.nombre from meses m where m.codigo=p.cod_mes) as nombre_mes,(select g.nombre from gestiones g where g.codigo=p.cod_gestion) as nombre_gestion
        from planillas p join planillas_personal_mes pd on p.codigo=pd.cod_planilla
        where p.cod_estadoplanilla=3 and pd.cod_personalcargo=$codigo_personal and p.cod_gestion>=3585";//solo mostrar mayor a 2022
    $stmt = $dbh->prepare($sql);
    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }else{
        echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}

function ObtenerDatosPersonal($codigo_personal){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT p.paterno,p.materno,p.primer_nombre,c.nombre as cargo
        from personal p join cargos c on p.cod_cargo=c.codigo
        where p.codigo=$codigo_personal";
    $stmt = $dbh->prepare($sql);
    $resp = false;
    $filas = array();
    if($stmt->execute()){
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resp = true;
    }else{
        echo "Error: Listar Componentes";
        $resp=false;
        exit;       
    }
    return $filas;
}
