<?php
// SERVICIO WEB PARA FACTURAS

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $datos = json_decode(file_get_contents("php://input"), true); 

    //Parametros de consulta
    $accion=NULL;
    if(isset($datos['accion'])&&isset($datos['sIdentificador'])&&isset($datos['sKey'])&&isset($datos['u'])){
        if($datos['sIdentificador']=="bolfincobo"&&$datos['sKey']=="rrf656nb2396k6g6x44434h56jzx5g6"){
            $accion=$datos['accion']; //recibimos la accion
            $u=$datos['u'];//recibimos el id personal

            $estado=0;
            $mensaje="";

            if($accion=="verificar_fichaactivo"){
                $caf=$datos['caf'];//recibimos el codigo string activo
                $datosResp = verificar_fichaactivo_f($caf);//llamamos a la funcion 
                if($datosResp[0]==1){
                    $estado=0;
                    $resultado=array(
                        "lst"=>$datosResp,
                        "estado"=>true,
                        "mensaje"=>"Correcto",    
                        );
                }else{
                    $estado=1;
                    $resultado=array(
                        "estado"=>false,
                        "mensaje"=>"ACTIVO FIJO NO EXISTE",    
                        );
                }
            }elseif($accion=="sincronizar_datosAF"){
                if(verificarUsuarioActivo($u)){
                    try{
                        $lstActivos = sincronizar_datosAF_f($u);//llamamos a la funcion
                        $totalComponentes=count($lstActivos);
                        $resultado=array(
                            "estado"=>true,
                            "mensaje"=>"Lista de Planillas obtenida correctamente", 
                            "lst"=>$lstActivos, 
                            "totalComponentes"=>$totalComponentes
                            );
                    }catch(Exception $e){
                       $resultado=array(
                        "estado"=>false,
                        "mensaje"=>"Hubo un error al momento de Sincronizar");
                    }
                }else{
                    $resultado=array(
                        "estado"=>false,
                        "mensaje"=>"PERSONAL NO ACTIVO"
                    );
                }
            }else{
                $resultado=array(
                    "estado"=>false,
                    "mensaje"=>"No existe la Accion Solicitada."
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

function verificar_fichaactivo_f($caf){
    require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $existe=0;
    $codigoactivo="";
    $otrodato="";
    $area="";
    $uo="";
    $responsable1="";
    $responsable2="";
    $rubro="";
    $tipobien="";
    $d10_valornetobs_aux="";
    $sql="SELECT af.codigo,af.codigoactivo,af.otrodato,a.nombre as area,uo.nombre as uo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as responsable1,(select CONCAT_WS(' ',p2.primer_nombre,p2.paterno,p2.materno) from personal p2 where p2.codigo=af.cod_responsables_responsable2) as responsable2,d.nombre as rubro,tb.tipo_bien as tipobien,af.valorinicial
        from activosfijos af join areas a on af.cod_area=a.codigo join unidades_organizacionales uo on af.cod_unidadorganizacional=uo.codigo join personal p on af.cod_responsables_responsable=p.codigo join depreciaciones d on af.cod_depreciaciones=d.codigo join tiposbienes tb on af.cod_tiposbienes=tb.codigo
        where af.codigoactivo like '$caf'";

        // echo $sql; 
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('codigo', $codigo);
    $stmt->bindColumn('codigoactivo', $codigoactivo);
    $stmt->bindColumn('otrodato', $otrodato);
    $stmt->bindColumn('area', $area);
    $stmt->bindColumn('uo', $uo);
    $stmt->bindColumn('responsable1', $responsable1);
    $stmt->bindColumn('responsable2', $responsable2);
    $stmt->bindColumn('rubro', $rubro);
    $stmt->bindColumn('tipobien', $tipobien);
    $stmt->bindColumn('valorinicial', $valorinicial);
    while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
        $stmt2 = $dbh->prepare("SELECT md.d10_valornetobs
        from mesdepreciaciones m, mesdepreciaciones_detalle md
        WHERE m.codigo = md.cod_mesdepreciaciones 
        and md.cod_activosfijos = $codigo and m.estado=1 order by m.codigo desc limit 1");
        $stmt2->execute();
        $row2 = $stmt2->fetch();
        $d10_valornetobs_aux = $row2["d10_valornetobs"];
        if($d10_valornetobs_aux==null){
            $d10_valornetobs_aux=$valorinicial;
        }
        $existe=1;
    }
    return array($existe,$codigoactivo,$otrodato,$area,$uo,$responsable1,$responsable2,$rubro,$tipobien,$d10_valornetobs_aux);
}
function verificarUsuarioActivo($id){
    require_once '../conexion.php';
    $dbh = new Conexion();
    $sql="select codigo from personal where cod_estadopersonal <>3 and cod_estadoreferencial<>2 and codigo='$id'";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $stmt->bindColumn('codigo', $codigo);
    $valor=false;
    while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
        $valor=true; 
    }
    return $valor;
}

function sincronizar_datosAF_f($id){
    require_once '../conexion.php';
    $fecha_actual=date('Y-m-d');
    $dbh = new Conexion();
    // Preparamos
    $sql="SELECT af.codigo,af.codigoactivo,af.otrodato,a.nombre as area,uo.nombre as uo,CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno)as responsable1,(select CONCAT_WS(' ',p2.primer_nombre,p2.paterno,p2.materno) from personal p2 where p2.codigo=af.cod_responsables_responsable2) as responsable2,d.nombre as rubro,tb.tipo_bien as tipobien,af.valorinicial, (SELECT md.d10_valornetobs
        from mesdepreciaciones m, mesdepreciaciones_detalle md
        WHERE m.codigo = md.cod_mesdepreciaciones 
        and md.cod_activosfijos = af.codigo and m.estado=1 order by m.codigo desc limit 1) as valorNeto
    from activosfijos af join areas a on af.cod_area=a.codigo join unidades_organizacionales uo on af.cod_unidadorganizacional=uo.codigo join personal p on af.cod_responsables_responsable=p.codigo join depreciaciones d on af.cod_depreciaciones=d.codigo join tiposbienes tb on af.cod_tiposbienes=tb.codigo
    where af.cod_estadoactivofijo=1 and af.cod_area in (select iaf.cod_area from inventarios_af iaf where iaf.cod_estado=1 and iaf.cod_responsable ='$id' and iaf.fecha_fin>='$fecha_actual') limit 1";
        // echo $sql; 
    $stmt = $dbh->prepare($sql);
    $filas = array();
    // $resp = false;
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
