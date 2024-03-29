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
    $celular_contacto = $_POST["celular_contacto"];
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
    $cuenta_habilitada=$_POST['cuenta_habilitada'];
    $globalUser=$_SESSION['globalUser'];

    $turno=$_POST['turno'];
    $tipo_trabajo=$_POST['tipo_trabajo'];

    $noche_pactado=$_POST['noche_pactado'];
    $domingo_pactado=$_POST['domingo_pactado'];
    $feriado_pactado=$_POST['feriado_pactado'];
    $movilidad_pactado=$_POST['movilidad_pactado'];
    $refrigerio_pactado1=$_POST['refrigerio_pactado'];
    $refrigerio_pactado2=$_POST['refrigerio_pactado2'];
    $comision_ventas=$_POST['comision_ventas'];
    $fallo_caja=$_POST['fallo_caja'];
    $aporte_sindicato=$_POST['aporte_sindicato'];//descuento

    $cod_cajasalud=$_POST['cod_cajasalud'];


    $created_by = $globalUser;
    $modified_by = $globalUser;
    $cod_estadoreferencial=1;
    //contrato
    $cod_tipocontrato=1;//tipo contrato indefinido
    $val_conf_meses_alerta_indef=obtenerValorConfiguracion(12);
    $fecha_fincontrato="INDEFINIDO";
    $fecha_evaluacioncontrato_x= date("Y-m-d",strtotime($ing_planilla."+ ".$val_conf_meses_alerta_indef." month")); 
    $fecha_evaluacioncontrato = date("Y-m-d",strtotime($fecha_evaluacioncontrato_x."- 1 days")); 
    $porcentaje=100;
    if($codigo==0){
        $codigo=obtenerCodigoPersonal();
        $sql="INSERT into personal(codigo,cod_tipo_identificacion,tipo_identificacion_otro,identificacion,cod_lugar_emision,lugar_emision_otro,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,jubilado,cod_genero,cod_tipopersonal,haber_basico,paterno,materno,apellido_casada,primer_nombre,otros_nombres,nua_cua_asignado,direccion,cod_tipoafp,cod_tipoaporteafp,nro_seguro,cod_estadopersonal,telefono,celular,email,persona_contacto,celular_contacto,created_by,modified_by,created_at,modified_at,cod_estadoreferencial,cod_nacionalidad,cod_estadocivil,cod_pais,cod_departamento,cod_ciudad,ciudad_otro,cod_grado_academico,ing_contr,ing_planilla,email_empresa,bandera,personal_confianza,cuenta_bancaria,turno,tipo_trabajo,cod_cajasalud,cuenta_habilitada)  values ($codigo,'$cod_tipoIdentificacion','$tipo_identificacionOtro','$identificacion','$cod_lugar_emision','$lugar_emisionOtro','$fecha_nacimiento','$cod_cargo','$cod_unidadorganizacional','$cod_area','$jubilado','$cod_genero','$cod_tipopersonal','$haber_basico','$paterno','$materno','$apellido_casada','$primer_nombre','$otros_nombres','$nua_cua_asignado','$direccion','$cod_tipoafp','$cod_tipoaporteafp','$nro_seguro','$cod_estadopersonal','$telefono','$celular','$email','$persona_contacto','$celular_contacto','$created_by','$modified_by',NOW(),NOW(),'$cod_estadoreferencial','$cod_nacionalidad','$cod_estadocivil','$cod_pais','$cod_departamento','$cod_ciudad','$ciudadOtro','$grado_academico','$ing_contr','$ing_planilla','$email_empresa','$bandera','$personal_confianza','$cuenta_bancaria','$turno','$tipo_trabajo','$cod_cajasalud','$cuenta_habilitada')";
        //echo $sql;
        $stmt = $dbh->prepare($sql);
        $flagSuccess=$stmt->execute();
        //pesonal area distribucion 
        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_area_distribucion(cod_personal,cod_uo,cod_area,porcentaje,cod_estadoreferencial,created_by,modified_by,monto) 
        values ('$codigo','$cod_unidadorganizacional','$cod_area','$porcentaje','$cod_estadoreferencial','$created_by','$modified_by','$haber_basico')");
        $stmtDistribucion->execute(); 
        //insertamos el contrato indefinido

        $stmtDistribucion = $dbh->prepare("INSERT INTO personal_contratos(cod_personal, cod_tipocontrato, fecha_iniciocontrato,fecha_fincontrato, fecha_evaluacioncontrato, cod_estadoreferencial, cod_estadocontrato) values('$codigo','$cod_tipocontrato','$ing_planilla','$fecha_fincontrato','$fecha_evaluacioncontrato','1','1')");
        $stmtDistribucion->execute(); 

        //actualizamos la parte de personal discapacitado
        if($tipo_persona_discapacitado==0){
            $fecha_nac_persona_dis=null;
            $nro_carnet_discapacidad=null;
        }
        $stmtDiscapacitado = $dbh->prepare("INSERT INTO personal_discapacitado(codigo,tipo_persona_discapacitado,nro_carnet_discapacidad,fecha_nac_persona_dis,cod_estadoreferencial)
                                            values($codigo,$tipo_persona_discapacitado,'$nro_carnet_discapacidad','$fecha_nac_persona_dis',$cod_estadoreferencial)");
        $flagSuccess=$stmtDiscapacitado->execute();
        //montos Pactados
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (11,$codigo,'$noche_pactado',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (12,$codigo,'$domingo_pactado',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (13,$codigo,'$feriado_pactado',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (14,$codigo,'$movilidad_pactado',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (15,$codigo,'$refrigerio_pactado1',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (16,$codigo,'$refrigerio_pactado2',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();

        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (17,$codigo,'0',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();

        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (18,$codigo,'$comision_ventas',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (19,$codigo,'$fallo_caja',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (20,$codigo,'0',1,1)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="INSERT INTO bonos_personal_pactados(cod_bono,cod_personal,monto,cod_estadoreferencial,tipo_bono_desc) values (100,$codigo,'$aporte_sindicato',1,2)";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        // ****FIn BONOS  DESC PACTADOS

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
        // showAlertSuccessError($flagSuccess,$urlListPersonal);

    }else{
        $sqlUpdate="UPDATE personal set cod_tipo_identificacion='$cod_tipoIdentificacion',tipo_identificacion_otro='$tipo_identificacionOtro',identificacion='$identificacion',cod_lugar_emision='$cod_lugar_emision',lugar_emision_otro='$lugar_emisionOtro',fecha_nacimiento='$fecha_nacimiento',cod_cargo='$cod_cargo',cod_unidadorganizacional='$cod_unidadorganizacional',cod_area='$cod_area',jubilado='$jubilado',cod_genero='$cod_genero',cod_tipopersonal='$cod_tipopersonal',paterno='$paterno',materno='$materno',apellido_casada='$apellido_casada',primer_nombre='$primer_nombre',otros_nombres='$otros_nombres',nua_cua_asignado='$nua_cua_asignado',direccion='$direccion',cod_tipoafp='$cod_tipoafp',cod_tipoaporteafp='$cod_tipoaporteafp',nro_seguro='$nro_seguro',cod_estadopersonal='$cod_estadopersonal',telefono='$telefono',celular='$celular',email='$email',persona_contacto='$persona_contacto',celular_contacto='$celular_contacto',modified_by='$modified_by',modified_at=NOW(),cod_nacionalidad='$cod_nacionalidad',cod_estadocivil='$cod_estadocivil',cod_pais='$cod_pais',cod_departamento='$cod_departamento',cod_ciudad='$cod_ciudad',ciudad_otro='$ciudadOtro',cod_grado_academico='$grado_academico',ing_contr='$ing_contr',ing_planilla='$ing_planilla',email_empresa='$email_empresa',bandera='$bandera',personal_confianza='$personal_confianza',cuenta_bancaria='$cuenta_bancaria',turno='$turno',tipo_trabajo='$tipo_trabajo',cod_cajasalud='$cod_cajasalud',cuenta_habilitada='$cuenta_habilitada' where codigo=$codigo";
        $stmt = $dbh->prepare($sqlUpdate);
        $flagSuccess=$stmt->execute();
        //sacamos el id de area distribucion area distribucion
        // $stmtPer = $dbhS->prepare("SELECT codigo 
        //         from personal_area_distribucion 
        //         where cod_personal='$codigo' ORDER BY 1 DESC");
        // $stmtPer->execute();
        // $resultPer=$stmtPer->fetch();
        // $codigo_areaDP=$resultPer['codigo'];
        // $sql="UPDATE personal_area_distribucion 
        //     set cod_uo='$cod_unidadorganizacional',cod_area='$cod_area',porcentaje='$porcentaje',monto='$haber_basico' where codigo='$codigo_areaDP'";
        
        // $stmtDistribucion = $dbh->prepare($sql);
        // $stmtDistribucion->execute();
        
        //actualizamos contrato activo
        $sql="SELECT codigo 
                from personal_contratos 
                where cod_personal=$codigo and cod_estadoreferencial=1 and cod_estadocontrato=1";
        $stmtPer = $dbhS->prepare($sql);
        $stmtPer->execute();
        $resultPer=$stmtPer->fetch();
        $codigo_contrato=$resultPer['codigo'];
        $Sql="UPDATE personal_contratos set fecha_iniciocontrato='$ing_planilla', fecha_evaluacioncontrato='$fecha_evaluacioncontrato' where codigo=$codigo_contrato";
        $stmtUContrato = $dbh->prepare($sql);  
        $stmtUContrato->execute();


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
        // MONTOS PACTADOS
         $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$noche_pactado' where cod_personal=$codigo and cod_bono=11 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$domingo_pactado' where cod_personal=$codigo and cod_bono=12 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$feriado_pactado' where cod_personal=$codigo and cod_bono=13 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$movilidad_pactado' where cod_personal=$codigo and cod_bono=14 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$refrigerio_pactado1' where cod_personal=$codigo and cod_bono=15 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$refrigerio_pactado2' where cod_personal=$codigo and cod_bono=16 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$comision_ventas' where cod_personal=$codigo and cod_bono=18 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$fallo_caja' where cod_personal=$codigo and cod_bono=19 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();
        $sqlMontosPactados="UPDATE bonos_personal_pactados set monto='$aporte_sindicato' where cod_personal=$codigo and cod_bono=100 and cod_estadoreferencial=1";
        $stmtMontoPactado = $dbh->prepare($sqlMontosPactados);
        $stmtMontoPactado->execute();

        //FIN MOMTOS PACTADOS

        //parte de imagen
        //imagen anterior
        $stmtANT = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
        //Ejecutamos;
        $stmtANT->bindParam(':codigo',$codigo);
        $stmtANT->execute();
        $resultANT = $stmtANT->fetch();
        //$codigo = $result['codigo'];
        $imagenANT = "";
        if(isset($resultANT['imagen'])){
            $imagenANT = $resultANT['imagen'];
        }
        
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
        // showAlertSuccessError($flagSuccess,$urlListPersonal);
    }

    echo "<br><br>";
    if($cod_estadopersonal==3){//estado retirado
        $fecha_retiro=date('Y-m-d');
        $cod_tiporetiro=1;
        if(isset($_POST['fecha_retiro'])){
            echo "*";
            $fecha_retiro=$_POST['fecha_retiro'];
        }
        if(isset($_POST['cod_tiporetiro'])){
            echo "*";
            $cod_tiporetiro=$_POST['cod_tiporetiro'];
        }
        $observaciones="";
        $cod_personal=$codigo;
        $cod_estadocontrato=2;//**nuevo
        //verificamos si todos sus contratos estan fina,izados
        $sqlControlador="SELECT codigo,cod_estadocontrato from personal_contratos where cod_personal=$cod_personal and cod_estadoreferencial=1 and cod_estadocontrato=1 ORDER BY codigo desc limit 1";
        $stmtControlador = $dbh->prepare($sqlControlador);
        $stmtControlador->execute();
        $resultControlador=$stmtControlador->fetch();
        $cod_contrato_aux=$resultControlador['codigo'];
        $cod_estadocontrato_aux=$resultControlador['cod_estadocontrato'];
        if($cod_estadocontrato_aux==1){
            //finalizamos contrato
            $cod_estadocontrato=2;
            $fecha_finalizado=date("Y-m-d H:i:s");
            $sql="UPDATE personal_contratos set cod_estadocontrato=$cod_estadocontrato,fecha_finalizado='$fecha_finalizado' where codigo=$cod_contrato_aux";
            $stmtContrato = $dbh->prepare($sql);
            $stmtContrato->execute();
            /**INSERTAMOS EN PERSONAL RETIROS*/
            $sql="INSERT INTO personal_retiros(cod_personal,cod_tiporetiro,fecha_retiro,observaciones,cod_estadoreferencial) values($cod_personal,$cod_tiporetiro,'$fecha_retiro','$observaciones',1)";
            $stmtRetiros = $dbh->prepare($sql);
            $flagsucces=$stmtRetiros->execute();
            /**cambiamos de estado a personal*/
            $sqlpersonal="UPDATE personal set cod_estadopersonal=3 where codigo=$cod_personal";
            $stmtUP = $dbh->prepare($sqlpersonal);
            $stmtUP->execute();
        }
    }
    showAlertSuccessError($flagSuccess,$urlListPersonal);
    
} catch(PDOException $ex){
    //manejar error
    echo "<br><br><br>"."Un error ocurrio".$ex->getMessage();
}
?>