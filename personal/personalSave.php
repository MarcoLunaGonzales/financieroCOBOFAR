<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'rrhh/configModule.php';
ini_set('display_errors',1);

$dbh = new Conexion();
$dbhS = new Conexion();

$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//para mostrar errores en la ejecucion

try {
    
    $codigo = $_POST["codigo"];
    //datos personales
    $primer_nombre = $_POST["primer_nombre"];
    $paterno = $_POST["paterno"];
    $materno = $_POST["materno"];
    $cod_tipoIdentificacion = $_POST["cod_tipoIdentificacion"];
    $tipo_identificacionOtro = $_POST["tipo_identificacionOtro"];
    $identificacion = $_POST["identificacion"];
    $cod_lugar_emision = $_POST["cod_lugar_emision"];
    $lugar_emisionOtro = $_POST["lugar_emisionOtro"];
    $cod_nacionalidad = $_POST["cod_nacionalidad"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];//
    $cod_genero = $_POST["cod_genero"];//
    $cod_estadocivil = $_POST["cod_estadocivil"];//
    $cod_pais = $_POST["cod_pais"];
    $cod_departamento = $_POST["cod_departamento"];
    $cod_ciudad = $_POST["cod_ciudad"];
    $ciudadOtro = $_POST["ciudadOtro"];
    $direccion = $_POST["direccion"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $celular = $_POST["celular"];
    //dato empresarial
    $cod_cargo = $_POST["cod_cargo"];
    $cod_unidadorganizacional = $_POST["cod_uo"];
    $cod_area = $_POST["cod_area"];
    $jubilado = $_POST["jubilado"];
    $cod_tipopersonal = $_POST["cod_tipopersonal"];
    $haber_basico = $_POST["haber_basico"];
    $apellido_casada = $_POST["apellido_casada"];
    $otros_nombres = $_POST["otros_nombres"];
    $nua_cua_asignado = $_POST["nua_cua_asignado"];    
    $cod_tipoafp = $_POST["cod_tipoafp"];
    $cod_tipoaporteafp = $_POST["cod_tipoaporteafp"];
    $nro_seguro = $_POST["nro_seguro"];
    $cod_estadopersonal = $_POST["cod_estadopersonal"];
    $persona_contacto = $_POST["persona_contacto"];
    $grado_academico=$_POST['grado_academico'];
    $ing_contr=$_POST['ing_contr'];
    $ing_planilla=$_POST['ing_contr'];
    $bandera=1;
    $email_empresa=$_POST['email_empresa'];
    $tipo_persona_discapacitado=$_POST['tipo_persona_discapacitado'];
    $nro_carnet_discapacidad=$_POST['nro_carnet_discapacidad'];
    $fecha_nac_persona_dis =$_POST['fecha_nac_persona_dis'];
    $personal_confianza=$_POST['personal_confianza'];
    $cuenta_bancaria=$_POST['cuenta_bancaria'];
    $globalUser=$_SESSION['globalUser'];
    $created_by = $globalUser;
    $modified_by = $globalUser;
    $cod_estadoreferencial=1;
    $porcentaje=100;
    if($codigo==0){
        $codigo=obtenerCodigoPersonal();
        $sql="INSERT into personal(codigo,cod_tipo_identificacion,tipo_identificacion_otro,identificacion,cod_lugar_emision,lugar_emision_otro,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,direccion,cod_tipoafp,cod_tipoaporteafp,nro_seguro,cod_estadopersonal,telefono,celular,email,persona_contacto,created_by,modified_by,created_at,modified_at,cod_estadoreferencial,cod_nacionalidad,cod_estadocivil,cod_pais,cod_departamento,cod_ciudad,ciudad_otro,cod_grado_academico,ing_contr,ing_planilla,email_empresa,bandera,personal_confianza,cuenta_bancaria)  values ($codigo,'$cod_tipoIdentificacion','$tipo_identificacionOtro','$identificacion','$cod_lugar_emision','$lugar_emisionOtro','$fecha_nacimiento','$cod_cargo','$cod_unidadorganizacional','$cod_area','$jubilado','$cod_genero','$cod_tipopersonal','$haber_basico','$paterno','$materno','$apellido_casada','$primer_nombre','$otros_nombres','$nua_cua_asignado','$direccion','$cod_tipoafp','$cod_tipoaporteafp','$nro_seguro','$cod_estadopersonal','$telefono','$celular','$email','$persona_contacto','$created_by','$modified_by',NOW(),NOW(),'$cod_estadoreferencial','$cod_nacionalidad','$cod_estadocivil','$cod_pais','$cod_departamento','$cod_ciudad','$ciudadOtro','$grado_academico','$ing_contr','$ing_planilla','$email_empresa','$bandera','$personal_confianza','$cuenta_bancaria')";
        //echo $sql;
        $stmt = $dbh->prepare($sql);
        $flagSuccess=$stmt->execute();
        //pesonal area distribucion 
        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_area_distribucion(cod_personal,cod_uo,cod_area,porcentaje,cod_estadoreferencial,created_by,modified_by) 
        values ('$codigo','$cod_unidadorganizacional','$cod_area','$porcentaje','$cod_estadoreferencial','$created_by','$modified_by')");
        $stmtDistribucion->execute(); 
        //actualizamos la parte de personal discapacitado
        if($tipo_persona_discapacitado==0){
            $fecha_nac_persona_dis=null;
            $nro_carnet_discapacidad=null;
        }
        $stmtDiscapacitado = $dbh->prepare("INSERT INTO personal_discapacitado(codigo,tipo_persona_discapacitado,nro_carnet_discapacidad,fecha_nac_persona_dis,cod_estadoreferencial)
                                            values($codigo,$tipo_persona_discapacitado,'$nro_carnet_discapacidad','$fecha_nac_persona_dis',$cod_estadoreferencial)");
        $flagSuccess=$stmtDiscapacitado->execute();
        //parte de imagen
        // $imagenANT = $resultANT['imagen'];
        if (strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar
            
            $stmt3 = $dbh->prepare("INSERT into personalimagen (codigo, imagen) values (:codigo, :imagen)");             
            $stmt3->bindParam(':codigo', $codigo);
            $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo
            $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
            //esta guardando en activosfijos\imagenes
            //echo $archivo;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $archivo))
                echo "correcto";
            else
                echo "error".$_FILES["image"]["error"];//sale error 0

            $flagSuccess=$stmt3->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListPersonal);

    }else{
        $sqlUpdate="UPDATE personal set cod_tipo_identificacion='$cod_tipoIdentificacion',tipo_identificacion_otro='$tipo_identificacionOtro',identificacion='$identificacion',cod_lugar_emision='$cod_lugar_emision',lugar_emision_otro='$lugar_emisionOtro',fecha_nacimiento='$fecha_nacimiento',cod_cargo='$cod_cargo',cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',jubilado='$jubilado',cod_genero='$cod_genero',cod_tipopersonal='$cod_tipopersonal',haber_basico='$haber_basico',paterno='$paterno',materno='$materno',apellido_casada='$apellido_casada',primer_nombre='$primer_nombre',otros_nombres='$otros_nombres',nua_cua_asignado='$nua_cua_asignado',direccion='$direccion',cod_tipoafp='$cod_tipoafp',cod_tipoaporteafp='$cod_tipoaporteafp',nro_seguro='$nro_seguro',cod_estadopersonal='$cod_estadopersonal',telefono='$telefono',celular='$celular',email='$email',persona_contacto='$persona_contacto',modified_by='$modified_by',modified_at=NOW(),cod_nacionalidad='$cod_nacionalidad',cod_estadocivil='$cod_estadocivil',cod_pais='$cod_pais',cod_departamento='$cod_departamento',cod_ciudad='$cod_ciudad',ciudad_otro='$ciudad_otro',cod_grado_academico='$grado_academico',ing_contr='$ing_contr',ing_planilla='$ing_planilla',email_empresa='$email_empresa',bandera='$bandera',personal_confianza='$personal_confianza',cuenta_bancaria='$cuenta_bancaria' where codigo=$codigo";
        $stmt = $dbh->prepare($sqlUpdate);
        $flagSuccess=$stmt->execute();
        //sacamos el id de area distribucion area distribucion
        $stmtPer = $dbhS->prepare("SELECT codigo 
                from personal_area_distribucion 
                where cod_personal=:cod_personal ORDER BY 1 DESC");
        $stmtPer->bindParam(':cod_personal', $codigo);
        $stmtPer->execute();
        $resultPer=$stmtPer->fetch();
        $codigo_areaDP=$resultPer['codigo'];
        $stmtDistribucion = $dbh->prepare("UPDATE personal_area_distribucion 
            set cod_uo=:cod_uo,cod_area=:cod_area,porcentaje=:porcentaje,monto=:haber_basico where codigo=:codigo_areaDP");
        $stmtDistribucion->bindParam(':codigo_areaDP', $codigo_areaDP);
        $stmtDistribucion->bindParam(':cod_uo', $cod_unidadorganizacional); 
        $stmtDistribucion->bindParam(':cod_area', $cod_area); 
        $stmtDistribucion->bindParam(':porcentaje', $porcentaje);            
        $stmtDistribucion->bindParam(':haber_basico', $haber_basico);   
        $stmtDistribucion->execute();     
        //actualizamos la parte de personal discapacitado        
        $stmtDiscapacitado = $dbh->prepare("UPDATE personal_discapacitado set tipo_persona_discapacitado = :tipo_persona_discapacitado,
            nro_carnet_discapacidad=:nro_carnet_discapacidad,fecha_nac_persona_dis=:fecha_nac_persona_dis,cod_estadoreferencial=:cod_estadoreferencial
        where codigo = :codigo");
        //bind
        $stmtDiscapacitado->bindParam(':codigo', $codigo);        
        $stmtDiscapacitado->bindParam(':tipo_persona_discapacitado', $tipo_persona_discapacitado);
        $stmtDiscapacitado->bindParam(':nro_carnet_discapacidad', $nro_carnet_discapacidad);
        $stmtDiscapacitado->bindParam(':fecha_nac_persona_dis', $fecha_nac_persona_dis);
        $stmtDiscapacitado->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
        $flagSuccess=$stmtDiscapacitado->execute();
        //parte de imagen
        //imagen anterior
        $stmtANT = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
        //Ejecutamos;
        $stmtANT->bindParam(':codigo',$codigo);
        $stmtANT->execute();
        $resultANT = $stmtANT->fetch();
        //$codigo = $result['codigo'];
        $imagenANT = $resultANT['imagen'];
        if ($imagenANT != $_FILES['image']['name'] AND strlen($_FILES['image']['name']) > 1){//solo si es diferente actualizar
            $results = $dbh->query("SELECT * from personalimagen where codigo = ".$codigo)->fetchAll(PDO::FETCH_ASSOC);
            if(count($results))     
            {
                $stmt3 = $dbh->prepare("UPDATE personalimagen set imagen = :imagen where codigo = :codigo");    
            } else {
                $stmt3 = $dbh->prepare("INSERT into personalimagen (codigo, imagen) values (:codigo, :imagen)");
            }                
            $stmt3->bindParam(':codigo', $codigo);
            $stmt3->bindParam(':imagen', $_FILES['image']['name']);//la url esta poniendo
            $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image']['name'];
            //esta guardando en activosfijos\imagenes
            //echo $archivo;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $archivo))
                echo "correcto";
            else
                echo "error".$_FILES["image"]["error"];//sale error 0

            $flagSuccess=$stmt3->execute();
        }
        showAlertSuccessError($flagSuccess,$urlListPersonal);
    }
    
} catch(PDOException $ex){
    //manejar error
    echo "<br><br><br>"."Un error ocurrio".$ex->getMessage();
}
?>