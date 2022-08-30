<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';
//$dbh = new Conexion();
$dbh = new Conexion();
$codigo=$codigo;

if($codigo>0){
    $stmt = $dbh->prepare("SELECT *,
    (select ga.nombre from personal_grado_academico ga where ga.codigo=cod_grado_academico) as nombre_grado_academico,
    (select ca.nombre from cargos ca where ca.codigo=cod_cargo) as nombre_cargo,
    (select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as nombre_uo,
    (select a.nombre from areas a where a.codigo=cod_area) as nombre_area
     FROM personal where codigo =:codigo");
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    //resultados
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $cod_tipoIdentificacion = $result['cod_tipo_identificacion'];
    $tipo_identificacionOtro = $result['tipo_identificacion_otro'];
    $identificacion = $result['identificacion'];
    $cod_lugar_emision = $result['cod_lugar_emision'];
    $lugar_emisionOtro = $result['lugar_emision_otro'];
    $fecha_nacimiento = $result['fecha_nacimiento'];
    $cod_cargo = $result['cod_cargo'];
    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $jubilado = $result['jubilado'];
    $cod_genero = $result['cod_genero'];
    $cod_tipopersonal = $result['cod_tipopersonal'];
    $haber_basico = $result['haber_basico'];
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $apellido_casada = $result['apellido_casada'];
    $primer_nombre = $result['primer_nombre'];
    $otros_nombres = $result['otros_nombres'];
    $nua_cua_asignado = $result['nua_cua_asignado'];
    $direccion = $result['direccion'];
    $cod_tipoafp = $result['cod_tipoafp'];
    $cod_tipoaporteafp = $result['cod_tipoaporteafp'];
    $nro_seguro = $result['nro_seguro'];
    $cod_estadopersonal = $result['cod_estadopersonal'];
    $telefono = $result['telefono'];
    $celular = $result['celular'];
    $email = $result['email'];
    $persona_contacto = $result['persona_contacto'];
    $celular_contacto = $result['celular_contacto'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    $cod_nacionalidad = $result['cod_nacionalidad'];
    $cod_estadocivil = $result['cod_estadocivil'];//-
    $cod_pais = $result['cod_pais'];
    $cod_departamento = $result['cod_departamento'];
    $cod_ciudad = $result['cod_ciudad'];
    $ciudadOtro = $result['ciudad_otro'];
    $cod_grado_academico = $result['cod_grado_academico']; 
    $ing_contr = $result['ing_contr'];
    $ing_planilla = $result['ing_planilla'];
    $bandera = $result['bandera'];
    $nombre_grado_academico = $result['nombre_grado_academico'];
    $nombre_cargo = $result['nombre_cargo'];
    $nombre_uo = $result['nombre_uo'];
    $nombre_area = $result['nombre_area'];
    $email_empresa = $result['email_empresa'];
    $personal_confianza = $result['personal_confianza'];
    $cuenta_bancaria = $result['cuenta_bancaria'];
    $cod_turno=$result['turno'];
    $cod_tipotrabajo=$result['tipo_trabajo'];
    $cod_cajasalud=$result['cod_cajasalud'];
    $cuenta_habilitada=$result['cuenta_habilitada'];
    
    

    //personal discapacitado
    $stmtDiscapacitado = $dbh->prepare("SELECT * FROM personal_discapacitado where codigo =:codigo and cod_estadoreferencial=1");
    $stmtDiscapacitado->bindParam(':codigo',$codigo);
    $stmtDiscapacitado->execute();
    $resultDiscapacitado = $stmtDiscapacitado->fetch();
    $cod_tipo_persona_discapacitado = $resultDiscapacitado['tipo_persona_discapacitado'];
    $nro_carnet_discapacidad = $resultDiscapacitado['nro_carnet_discapacidad'];
    $fecha_nac_persona_dis = $resultDiscapacitado['fecha_nac_persona_dis'];


    $stmtMontosPactados = $dbh->query("SELECT cod_bono,monto from bonos_personal_pactados where cod_estadoreferencial=1 and cod_personal =$codigo");
    while ($row = $stmtMontosPactados->fetch()){
        switch ($row['cod_bono']) {
            case 11:
                $noche_pactado=$row['monto'];
            break;
            case 12:
                $domingo_pactado=$row['monto'];
            break;
            case 13:
                $feriado_pactado=$row['monto'];
            break;
            case 14:
                $movilidad_pactado=$row['monto'];
            break;
            case 15:
                $refrigerio_pactado=$row['monto'];
            break;
            case 16:
                $refrigerio_pactado2=$row['monto'];
            break;
            case 18:
                $comision_ventas=$row['monto'];
            break;
            case 19:
                $fallo_caja=$row['monto'];
            break;
            case 100:
                $aporte_sindicato=$row['monto'];
            break;
   
        }
    }

    //IMAGEN
    $stmtIMG = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
    $stmtIMG->bindParam(':codigo',$codigo);
    $stmtIMG->execute();
    $resultIMG = $stmtIMG->fetch();    
    $archivo="";
    if(isset($resultIMG['imagen'])){
        $imagen = $resultIMG['imagen'];
        //$archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$imagen;//sale mal
        $archivo = "personal/imagenes/".$imagen;//sale mal
    }


}else{
    $codigo = 0;
    $cod_tipoIdentificacion = "";
    $tipo_identificacionOtro = "";
    $identificacion = "";
    $cod_lugar_emision = "";
    $lugar_emisionOtro = "";
    $fecha_nacimiento = "";
    $cod_cargo = "";
    $cod_unidadorganizacional = "";
    $cod_area = "";
    $jubilado = "";
    $cod_genero = "";
    $cod_tipopersonal = "";
    $haber_basico = "";
    $paterno = "";
    $materno = "";
    $apellido_casada = "";
    $primer_nombre = "";
    $otros_nombres = "";
    $nua_cua_asignado = "";
    $direccion = "";
    $cod_tipoafp = "";
    $cod_tipoaporteafp = "";
    $nro_seguro = "";
    $cod_estadopersonal = "";
    $telefono = "";
    $celular = "";
    $email = "";
    $persona_contacto = "";
    $celular_contacto = "";
    $created_at = "";
    $created_by = "";
    $modified_at = "";
    $modified_by = "";

    $cod_nacionalidad = "";
    $cod_estadocivil = "";
    $cod_pais = "";
    $cod_departamento = "";
    $cod_ciudad = "";
    $ciudadOtro = "";
    $cod_grado_academico = "";
    $ing_contr = "";
    $ing_planilla = "";
    $bandera = "";
    $nombre_grado_academico = "";
    $nombre_cargo = "";
    $nombre_uo = "";
    $nombre_area = "";
    $email_empresa = "";
    $personal_confianza = "";
    $cuenta_bancaria = "";
    $cuenta_habilitada=0;
    $cod_turno="";
    $cod_tipotrabajo="";
    $cod_cajasalud="";
    //personal discapacitado
    $cod_tipo_persona_discapacitado = "";
    $nro_carnet_discapacidad = "";
    $fecha_nac_persona_dis = "";
    //IMAGEN
    $stmtIMG = "";
    $resultIMG = "";
    $imagen = "";
    //$archivo = "";
    $archivo = "";

    //montos pactados
    $noche_pactado=0;
    $domingo_pactado=0;
    $feriado_pactado=0;
    $movilidad_pactado=0;
    $refrigerio_pactado=0;
    $refrigerio_pactado2=0;
    $comision_ventas=0;
    $fallo_caja=0;
    $aporte_sindicato=0;
}

//COMBOS...
$queryTPersonal = "SELECT codigo,nombre from tipos_personal where cod_estadoreferencial=1";
$statementTPersonal = $dbh->query($queryTPersonal);

$querytipos_afp = "SELECT codigo,nombre from tipos_afp where cod_estadoreferencial=1";
$statementtipos_afp = $dbh->query($querytipos_afp);

$querytipos_aporteafp = "SELECT codigo,nombre from tipos_aporteafp where cod_estadoreferencial=1";
$statementtipos_aporteafp = $dbh->query($querytipos_aporteafp);

$queryestados_personal = "SELECT codigo,nombre from estados_personal where cod_estadoreferencial=1";
$statementestados_personal = $dbh->query($queryestados_personal);


$querycajasalud = "SELECT codigo,nombre from tipos_caja_salud where cod_estadoreferencial=1";
$stmt_cajasalud = $dbh->query($querycajasalud);

?>

<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="form1" action="<?=$urlSavePersonal;?>" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularPersonal;?></h4>
                            </div>
                        </div>
                        <div class="card-body">                    
                            <h3 align="center">DATOS PERSONALES</h3>    
                            <!-- <div class="row">
                                <label class="col-sm-2 col-form-label">Código Personal</label>
                                <div class="col-sm-4">
                                    <div class="form-group"> -->
                                        <input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>" readonly="readonly"/>
                                    <!-- </div>
                                </div>                            
                            </div> --><!--fin campo codigo --> 
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo Identificación</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipoIdentificacion" id="cod_tipoIdentificacion" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <?php
                                            $queryUO = "SELECT codigo,nombre,abreviatura from tipos_identificacion_personal where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_tipoIdentificacion==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Tipo Identificación Otro</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="tipo_identificacionOtro" id="tipo_identificacionOtro" value="<?=$tipo_identificacionOtro?>"/>
                                    </div>
                                </div>                            
                            </div><!--fin campo tipo_identificacionOtro--> 
                                                   
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nro. Identificación</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="identificacion" id="identificacion" value="<?=$identificacion;?>" required="true" onChange="verificarExistenviaCI(<?=$codigo?>)" >
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label" >Lugar Emision</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_lugar_emision" id="cod_lugar_emision" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from personal_departamentos where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_lugar_emision==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>                            
                            </div><!--fin campo ci_lugar_emision -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Lugar Emisión Otro</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="lugar_emisionOtro" id="lugar_emisionOtro" value="<?=$lugar_emisionOtro;?>"/>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Nacionalidad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_nacionalidad" id="cod_nacionalidad" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from personal_pais where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_nacionalidad==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><!--fin campo Nacionalidad -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Pais</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_pais" id="cod_pais" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from personal_pais where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_pais==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Departamento</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                       <select name="cod_departamento" id="cod_departamento" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from personal_departamentos where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_departamento==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><!--fin campo pais y departamento -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Ciudad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_ciudad" id="cod_ciudad" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from personal_ciudad where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_ciudad==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Otra Ciudad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="ciudadOtro" id="ciudadOtro" value="<?=$ciudadOtro;?>" />
                                    </div>
                                </div>
                            </div><!--fin campo ciudad -->
                            <div class="row">                            
                                <label class="col-sm-2 col-form-label">Estado Civil</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_estadocivil" id="cod_estadocivil" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT * from tipos_estado_civil where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_estadocivil==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label">Genero</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_genero" id="cod_genero" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT * from tipos_genero where cod_estadoreferencial=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_genero==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    
                                    </div>
                                </div>
                            </div><!--fin genero y estadoCivil-->
                            <div class="row">                            
                                <label class="col-sm-2 col-form-label">Fecha Nacimiento</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="date" name="fecha_nacimiento" id="fecha_nacimiento"  value="<?=$fecha_nacimiento;?>"/>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Nombre</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="primer_nombre" id="primer_nombre"  value="<?=$primer_nombre;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>
                            </div><!--Fecha Nac-->
                            
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Paterno</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="paterno"  id="paterno"  value="<?=$paterno;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Materno</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="materno" id="materno"  value="<?=$materno;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>
                            </div><!--fin campo materno -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Apellido Casada</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="apellido_casada" id="apellido_casada" value="<?=$apellido_casada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Telefono</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" name="telefono" id="telefono" value="<?=$telefono;?>"/>
                                    </div>
                                </div>
                            </div><!--fin campo primer nombre y tel-->                        
                            <div class="row">                        
                                <label class="col-sm-2 col-form-label">Celular</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="celular" id="celular"  value="<?=$celular;?>" required="true"/>
                                </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="email" name="email" id="email" value="<?=$email;?>" required="true"/>
                                </div>
                                </div>
                            </div><!--fin campo celular y email -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Direccion</label>
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="direccion" id="direccion" value="<?=$direccion;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>                    
                                </div>                        
                            </div><!--fin campo direccion -->
                            <h3 align="center">DATOS DE LA EMPRESA</h3>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Estado</label>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <select name="cod_estadopersonal" class="selectpicker form-control form-control-sm " data-style="btn btn-info" required onChange="ajax_personal_estado(this);">
                                        <?php while ($row = $statementestados_personal->fetch()) { ?>
                                            <option <?php if($cod_estadopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                        </select>                  
                                    </div>
                                </div>

                                <label class="col-sm-1 col-form-label">Fecha de Ingreso</label>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input class="form-control" type="date" name="ing_contr" id="ing_contr" required="true" value="<?=$ing_contr;?>" />
                                    </div>
                                </div>
                                <label class="col-sm-1 col-form-label"></label>
                                <div class="col-sm-5" id="div_contenedor_fecha_retiro">
                                    
                                </div>
                                
                            </div> <!--fin campo ing contrato y ing planilla-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Turno</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="turno" id="turno"  class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                            <?php 
                                            $sql="SELECT codigo,nombre from personal_turno where cod_estadoreferencial=1 order by nombre";
                                            $statementTurno = $dbh->query($sql);
                                            while ($row = $statementTurno->fetch()) { ?>
                                                <option <?php if($cod_turno == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>           

                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label">Tipo Trabajo</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="tipo_trabajo" id="tipo_trabajo"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php 
                                            $sql="SELECT codigo,nombre from personal_tipotrabajo where cod_estadoreferencial=1 order by nombre";
                                            $statementTipoTrabajo = $dbh->query($sql);
                                            while ($row = $statementTipoTrabajo->fetch()) { ?>
                                                <option <?php if($cod_tipotrabajo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Personal de Confianza</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="personal_confianza" id="personal_confianza"  class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                            <option <?php if($personal_confianza == '0') echo "selected"; ?> value="0">NO</option>
                                            <option <?php if($personal_confianza == '1') echo "selected"; ?> value="1">SI</option>
                                        </select>           

                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label">Tipo Personal</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipopersonal"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php while ($row = $statementTPersonal->fetch()) { ?>
                                                <option <?php if($cod_tipopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>                                                                   
                                <input class="form-control" type="hidden" name="otros_nombres" id="otros_nombres" value="<?=$otros_nombres;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div><!--fin campo apellido casada y tipo personal-->
                            <?php
                            if($bandera==0)
                            { ?>
                                <div class="row">
                                  <label class="col-sm-2 col-form-label">Oficina</label>
                                  <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info"  data-show-subtext="true" data-live-search="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_unidadorganizacional==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                  </div>
                               
                                  <label class="col-sm-2 col-form-label">Area</label>
                                  <div class="col-sm-4">
                                    <div class="form-group" >
                                        <div id="div_contenedor_area">
                                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
                                                <option value=""></option>
                                                <?php 
                                                $queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1 order by nombre";
                                                $statementArea = $dbh->query($queryArea);
                                                while ($row = $statementArea->fetch()){ ?>
                                                    <option <?=($cod_area==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>                    
                                    </div>
                                  </div>
                                </div>              
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Cargo</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div id="div_contenedor_cargo">
                                                <select name="cod_cargo"  class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                                    <option value=""></option>
                                                    <?php 
                                                    $queryCargos = "SELECT codigo,nombre,abreviatura from cargos where cod_estadoreferencial=1";
                                                    $statementCargos = $dbh->query($queryCargos);
                                                    while ($row = $statementCargos->fetch()) { ?>
                                                        <option <?php if($cod_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                    <?php } ?>
                                                </select>    
                                            </div>
                                            
                                        </div>                    
                                    </div>                
                                    <label class="col-sm-2 col-form-label">Grado Académico</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <select name="grado_academico" id="grado_academico"  class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
                                                <?php 
                                                $querygrado_academico = "SELECT codigo,nombre from personal_grado_academico where codestadoreferencial=1";
                                                $statementgrado_academico = $dbh->query($querygrado_academico);
                                                while ($row = $statementgrado_academico->fetch()) { ?>
                                                    <option <?php if($cod_grado_academico == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>                                           
                                </div><!--fin campo cargo -->
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Haber Basico (Bs)</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number" step="any" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" required/>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Email Empresarial</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email_empresa" id="email_empresa" required="true" value="<?=$email_empresa;?>" />
                                        </div>
                                    </div> 
                                </div><!--haber basico-->
                                <?php 
                            }else{ ?>
                                <div class="row">
                                  <label class="col-sm-2 col-form-label">Oficina</label>
                                  <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="hidden" class="form-control"  name="cod_uo" id="cod_uo"  value="<?=$cod_unidadorganizacional;?>"/>
                                        <input class="form-control" readonly="readonly" value="<?=$nombre_uo;?>"/>
                                    </div>
                                  </div>
                               
                                  <label class="col-sm-2 col-form-label">Area</label>
                                  <div class="col-sm-4">
                                    <div class="form-group" >
                                        <div id="div_contenedor_area">
                                            <input type="hidden" class="form-control"  name="cod_area" id="cod_area"  value="<?=$cod_area;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_area;?>"/>
                                        </div>                    
                                    </div>
                                  </div>
                                </div>              
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Cargo</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control"  name="cod_cargo" id="cod_cargo"  value="<?=$cod_cargo;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_cargo;?>"/>
                                        </div>                    
                                    </div>                
                                    <label class="col-sm-2 col-form-label">Grado Académico</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control"  name="grado_academico" id="grado_academico"  value="<?=$cod_grado_academico;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_grado_academico;?>"/>
                                        </div>
                                    </div>                                           
                                </div><!--fin campo cargo -->
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Haber Basico (Bs)</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                             <input class="form-control" type="number" step="any" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" readonly="readonly"/>
                                        </div>
                                    </div> 
                                    <label class="col-sm-2 col-form-label">Email Empresarial</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email_empresa" id="email_empresa" required="true" value="<?=$email_empresa;?>" />
                                        </div>
                                    </div> 
                                </div><!--haber basico-->
                            <?php }
                            ?>                
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Jubilado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="jubilado"  class="selectpicker form-control form-control-sm " data-style="btn btn-info">
                                            <option <?php if($jubilado == '0') echo "selected"; ?> value="0">NO</option>
                                            <option <?php if($jubilado == '1') echo "selected"; ?> value="1">SI</option>
                                        </select>           

                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Nua / Cua Asignado</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="nua_cua_asignado" id="nua_cua_asignado" value="<?=$nua_cua_asignado;?>"/>
                                </div>
                                </div>
                            </div><!--fin campo nua_cua_asignado-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">AFP</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipoafp" id="cod_tipoafp"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php while ($row = $statementtipos_afp->fetch()) { ?>
                                                <option <?php if($cod_tipoafp == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Tipo de Aporte AFP</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipoaporteafp" id="cod_tipoaporteafp"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php while ($row = $statementtipos_aporteafp->fetch()) { ?>
                                                <option  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><!--fin campo cod_tipoaporteafp-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nro. Seguro</label>
                                <div class="col-sm-2">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nro_seguro" id="nro_seguro" required value="<?=$nro_seguro;?>"/>
                                </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="cod_cajasalud"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <option value=""></option>
                                        <?php while ($row = $stmt_cajasalud->fetch()) { ?>
                                            <option <?php if($cod_cajasalud == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                        </select>                  
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Cuenta Bancaria</label>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input class="form-control" type="number" name="cuenta_bancaria" id="cuenta_bancaria" required value="<?=$cuenta_bancaria;?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="cuenta_habilitada" id="cuenta_habilitada" class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <option value=""></option>
                                            <option <?php if($cuenta_habilitada == 0) echo "selected"; ?> value="0">NO HABILITADO</option>
                                            <option <?php if($cuenta_habilitada == 1) echo "selected"; ?> value="1">HABILITADO</option>
                                        </select> 
                                    </div>
                                </div>


                                
                            </div><!--fin campo cod_estadopersonal-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nombre Contacto</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="persona_contacto" id="persona_contacto" required value="<?=$persona_contacto;?>"/>
                                </div>
                                </div> 

                                <label class="col-sm-2 col-form-label">Celular Contacto</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="celular_contacto" id="celular_contacto" required value="<?=$celular_contacto;?>"/>
                                </div>
                                </div>                        
                            </div><!--fin campo persona_contacto -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo De Persona Con Discapacidad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="tipo_persona_discapacitado" id="tipo_persona_discapacitado"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" >
                                            <option <?php if($cod_tipo_persona_discapacitado == 0) echo "selected"; ?> value="0"> NINGUNO</option>
                                            <option <?php if($cod_tipo_persona_discapacitado == 1) echo "selected"; ?> value="1">PERSONA CON DISCAPACIDAD</option>
                                            <option <?php if($cod_tipo_persona_discapacitado == 2) echo "selected"; ?> value="2"> TUTOR DE PERSONA CON DISCAPACIDAD</option>                            
                                        </select>
                                    </div>
                                </div>                                
                            </div><!--fin campo persona discapacidad -->
                            <div id="contenedor_padre_discapacidad" >
                                <div id="div2">                                
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">Nro Carnet Discapacidad</label>
                                        <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number" name="nro_carnet_discapacidad" id="nro_carnet_discapacidad" value="<?=$nro_carnet_discapacidad;?>"/>
                                        </div>
                                        </div>

                                        <label class="col-sm-2 col-form-label">Fecha Nacimiento De Persona Con Discapacidad</label>
                                        <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="date" name="fecha_nac_persona_dis" id="fecha_nac_persona_dis" value="<?=$fecha_nac_persona_dis;?>" />  
                                        </div>
                                        </div>
                                    </div>
                                </div>    
                            </div>


                            <h3 align="center">MONTOS PACTADOS</h3>                        
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Noche Pactado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="noche_pactado" id="noche_pactado" required="true" value="<?=$noche_pactado;?>" />                               
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Domingo Pactado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="domingo_pactado" id="domingo_pactado" required="true" value="<?=$domingo_pactado;?>" />                               
                                    </div>
                                </div>
                            </div> 

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Feriado Pactado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="feriado_pactado" id="feriado_pactado" required="true" value="<?=$feriado_pactado;?>" />                               
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Movilidad Pactado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="movilidad_pactado" id="movilidad_pactado" required="true" value="<?=$movilidad_pactado;?>" />                               
                                    </div>
                                </div>
                            </div> 

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Refrigerio Pactado (LS)</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="refrigerio_pactado" id="refrigerio_pactado" required="true" value="<?=$refrigerio_pactado;?>" />                               
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Refrigerio Pactado (D)</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="refrigerio_pactado2" id="refrigerio_pactado2" required="true" value="<?=$refrigerio_pactado2;?>" />                               
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Comisión Ventas</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="comision_ventas" id="comision_ventas" required="true" value="<?=$comision_ventas;?>" />                               
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Fallo de Caja</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="fallo_caja" id="fallo_caja" required="true" value="<?=$fallo_caja;?>" />                               
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Aporte Sindicato</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" step="any" name="aporte_sindicato" id="aporte_sindicato" required="true" value="<?=$aporte_sindicato;?>" />                               
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>


                            <div class="row">
                                <label class="col-sm-2 col-form-label">Imagen</label>
                                <div class="col-md-7">
                                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                        <div class="fileinput-new img-raised">
                                            <img src="<?=$archivo;?>" alt="..." style="width:250px;">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail img-raised">
                                        </div>
                                        <div>
                                            <span class="btn btn-raised btn-round <?=$buttonNormal;?> btn-file">
                                            <span class="fileinput-new">Seleccionar Imagen</span>
                                            <span class="fileinput-exists">Cambiar</span>
                                            <input type="file" name="image" /><!-- ARCHHIVO -->
                                            </span>
                                            <a href="#" class="btn <?=$buttonNormal;?> btn-round fileinput-exists" data-dismiss="fileinput">
                                            <i class="fa fa-times"></i> Quitar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--fin campo imagen-->                    
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                            <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
                        </div>
                    </div>                            
                </form>
            </div>
        </div>
        
    </div>
</div>


