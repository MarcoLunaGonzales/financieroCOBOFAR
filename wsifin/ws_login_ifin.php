<?php
// SERVICIO WEB PARA FACTURAS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true); 
    //Parametros de consulta
    $accion=NULL;

    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])&&isset($datos['u'])&&isset($datos['p'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $u=$datos['u'];//recibimos el dato personal
            $p=$datos['p'];//recibimos el dato personal
            $estado=0;
            $mensaje="";
            if($accion=="VerificarDatosPersonal"){
                $datosResp = VerificarDatosPersonal($u,$p);//llamamos a la funcion 
                if($datosResp[2]==1){
                    $estado=0;
                    $resultado=array(
                        "id"=>$datosResp[0],
                        "usuario"=>$datosResp[1],
                        "estado"=>true,
                        "mensaje"=>"Correcto",    
                        );
                }else{
                    $estado=1;
                    $resultado=array(
                        "estado"=>false,
                        "mensaje"=>"Error Login",    
                        );
                }
         
            }else{
                $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"No existe la Accion Solicitada.", 
                    "lst"=>null, 
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

function VerificarDatosPersonal($u,$p){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $existe=0;$usuario=0;$nombre="";
    
    $sql="SELECT p.codigo, CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, pd.perfil,pd.admin
            from personal p, personal_datosadicionales pd 
            where p.codigo=pd.cod_personal and pd.usuario='$u' and pd.contrasena='$p' and pd.cod_estado=1";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('codigo', $codigo);
    $stmt->bindColumn('nombre', $nombre);
    while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
        $usuario=$codigo; 
        $nombre=$nombre; 
        $existe=1;
    }
    return array($usuario,$nombre,$existe);
}
