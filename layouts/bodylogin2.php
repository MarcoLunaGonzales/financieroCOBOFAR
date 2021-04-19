<?php 
//header('Content-Type: text/html; charset=iso-8859-1');
?>

<!DOCTYPE html>
<html lang="en">
<!--ESTE ES EL DOCUMENTO DEL BODYLOGIN -->

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    iFinanciero - COBOFAR
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
  <link href="../assets/autocomplete/awesomplete.css" rel="stylesheet" />
  <link href="../assets/autocomplete/autocomplete/autocomplete-img.css" rel="stylesheet" />
  <link href="../assets/css/style.css" rel="stylesheet" />
</head>

<body class="off-canvas-sidebar">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
    <div class="container">
      <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
        <span class="sr-only">Toggle navigation</span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
        <span class="navbar-toggler-icon icon-bar"></span>
      </button>
      
    </div>
  </nav>
  <!-- End Navbar -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
  <script>
    $("#con_fac").mask("AA-AA-AA-AA-AA-AA-AA");
  </script>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  
  <!-- Forms Validations Plugin -->
  <script src="../assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="../assets/js/plugins/moment.min.js"></script>
 <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="../assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="../assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="../assets/js/plugins/sweetalert2.js"></script>
  <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
  <script src="../assets/js/plugins/dataTables.fixedHeader.min.js"></script>

  <script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <script src="../assets/js/material-dashboard.js?v=2.1.0" type="text/javascript"></script>
  <script src="../assets/js/mousetrap.min.js"></script>
   
  <script src="../assets/autocomplete/awesomplete.min.js"></script>
  <script src="../assets/autocomplete/autocomplete/autocomplete-img.js"></script>
  <!--CHART GOOGLE-->
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  
  <script src="../assets/alerts/alerts.js"></script>
  <script src="../assets/alerts/functionsGeneral.js"></script>

  <script>
    var imageLogo="data:image/jpeg;base64,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";
  </script>
  

  <!--ESTE ES EL DOCUMENTO DEL BODYLOGIN -->
  <script>
    $(document).ready(function() {
      // Initialise Sweet Alert library
      alerts.showSwal();
    });
  </script>

  <script>
    $(document).ready(function() {
      // Initialise the wizard
      demo.initMaterialWizard();
      setTimeout(function() {
        $('.card.card-wizard').addClass('active');
      }, 600);
    });
  </script>

  <script type="text/javascript">
    $(document).ready(function() {

        // Setup - add a text input to each footer cell
        $('#libreta_bancaria_reporte_modal tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="'+title+'" />' );
        } );
     
        // DataTable
        var table = $('#libreta_bancaria_reporte_modal').DataTable({
            initComplete: function () {
                // Apply the search
                this.api().columns().every( function () {
                    var that = this;
                    $( 'input', this.footer() ).on( 'keyup change clear', function () {
                        if ( that.search() !== this.value ) {
                            that
                                .search( this.value )
                                .draw();
                        }
                    });
                });
            },
            "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
                  header: false,
                  footer: false
            },
            "order": false,
            "paging":   false,
            "info":     false,          
            "scrollY":        "400px",
            "scrollCollapse": true
        });
        
      $('#tablePaginator').DataTable( {
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "ordering": false
        } );
      
        $('#example').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
          
        });

        $('#tablePaginatorReport ').DataTable({
            "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            },
            "order": false

        } );
        if($("#tableCuentasBuscar").length){
          $('#tableCuentasBuscar tfoot th').each( function () {
               var title = $(this).text();
               $(this).html( '<input type="text" class="form-control" placeholder="Buscar '+title+'" />' );
           } );
 
            // DataTable
            var table = $('#tableCuentasBuscar').DataTable({
              /*"paging":false});*/
                "processing": true,
                "serverSide": true,
          "ajax":{
            url :"../comprobantes/cuentasDatos.php", // json datasource
            type: "post",  // method  , by default get
            error: function(){  // error handling
              $(".employee-grid-error").html("");
              $("#tableCuentasBuscar").append('<tbody class="employee-grid-error"><tr><th colspan="3">No hay datos en el Servidor</th></tr></tbody>');
              $("#tableCuentasBuscar_processing").css("display","none");
              
            }
          }
          ,"language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            }
              });
             // Apply the search
             table.columns().every( function () {
                 var that = this;
 
                 $( 'input', this.footer() ).on( 'keyup change clear', function (e) {
                  if(e.keyCode == 13) {
                   
                  }
                     if ( that.search() !== this.value ) {
                         that
                             .search( this.value )
                             .draw();
                     }
                 } );
             } );
             var r = $('#tableCuentasBuscar tfoot tr');
               r.find('th').each(function(){
                    $(this).css('padding', 8);
                });
             $('#tableCuentasBuscar thead').append(r);
             $('#search_0').css('text-align', 'center');

          /*$('#tableCuentasBuscar').DataTable({
            "paging":   false,
            "info":     false,
            "order": false,
            "searching": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            fixedHeader: {
              header: true,
              footer: true
            }
          } );*/
        }
        
    } );
    //<!--FUNCIONES DE VALIDACION-->
    $(document).ready(function() {
       /*setFormValidation('#form1');
        jQuery.extend(jQuery.validator.messages, {
        required: "El campo es requerido."
      });*/
    //campo input autocomplete
     if ($('#cuenta_auto').length) {
       autocompletar("cuenta_auto","cuenta_auto_id",array_cuenta);
     }
     if ($('#cuenta_auto_num').length) {
       autocomplete("cuenta_auto_num","cuenta_auto_num_id", array_cuenta, imagen_cuenta);
     }
     if ($('#nro_cuenta').length) {
       //autocomplete("nro_cuenta","nro_cuenta_id", array_cuenta_numeros, imagen_cuenta);
      // autocomplete("cuenta","cuenta_id", array_cuenta_nombres, imagen_cuenta);
     }
    // $("#formRegFactCajaChica").submit(function(e) {
    //   $('<input />').attr('type', 'hidden')
    //         .attr('name', 'facturas')
    //         .attr('value', JSON.stringify(itemFacturasDCC))
    //         .appendTo('#formRegFactCajaChica');
      
    // });

    $("#formSoliFactNormas").submit(function(e) {
        if($("#total_monto_bob_a_tipopago").val()){//existe array de objetos tipopago          
          var tipo_solicitud=$("#tipo_solicitud").val();          
          if(tipo_solicitud==2){            
            var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
          }else{
            var montoTotalItems=$("#monto_total_a").val();  
          }
          var montoTotalItems=parseFloat(Math.round(montoTotalItems * 100) / 100).toFixed(2);
          var monto_modal_por_tipopago=$("#total_monto_bob_a_tipopago").val();
          var monto_modal_por_tipopago=parseFloat(Math.round(monto_modal_por_tipopago * 100) / 100).toFixed(2);
          //si existe array de objetos transformarlo a json
          $('<input />').attr('type', 'hidden')
            .attr('name', 'tiposPago_facturacion')
            .attr('value', JSON.stringify(itemTipoPagos_facturacion))
            .appendTo('#formSoliFactNormas');
          // validamos que obligue insertar archivos en caso de forma de pago deposito
          var cod_defecto_deposito_cuenta=$("#cod_defecto_deposito_cuenta").val();
          for(var j = 0; j < itemTipoPagos_facturacion[0].length; j++){
            var dato = Object.values(itemTipoPagos_facturacion[0][j]);
            var cod_tipopago_x=dato[0];
            if(cod_tipopago_x==cod_defecto_deposito_cuenta){
              if($("#cantidad_archivosadjuntos").val()==0){
                var msg = "Por favor agregue Archivo Adjunto.";
                $('#msgError').html(msg);
                $('#modalAlert').modal('show'); 
                return false;  
              }
            }            
          }
          if(monto_modal_por_tipopago!=0){
            if(montoTotalItems!=monto_modal_por_tipopago){
              var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Formas de Pago...</p>";
              $('#msgError').html(mensaje);
              $('#modalAlert').modal('show'); 
              return false;  
            }else{
              if($("#total_monto_bob_a_areas").val()){
                var tipo_solicitud=$("#tipo_solicitud").val();          
                if(tipo_solicitud==2){            
                  var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
                }else{
                  var montoTotalItems=$("#monto_total_a").val();
                }
                var montoTotalItems=parseFloat(Math.round(montoTotalItems * 100) / 100).toFixed(2);
                var monto_modal_por_area=$("#total_monto_bob_a_areas").val();
                var monto_modal_por_area=parseFloat(Math.round(monto_modal_por_area * 100) / 100).toFixed(2);
                var sw_x=true;//para ver la cantidad de las unidades
                var mensaje='<p>Por favor verifique los montos de la distribución de porcentajes en Unidades...<p>';
                //si existe array de objetos areas
                $('<input />').attr('type', 'hidden')
                .attr('name', 'areas_facturacion')
                .attr('value', JSON.stringify(itemAreas_facturacion))
                .appendTo('#formSoliFactNormas');
                 //si existe array de objetos unidades//falta hacer sus alertas
                $('<input />').attr('type', 'hidden')
                .attr('name', 'unidades_facturacion')
                .attr('value', JSON.stringify(itemUnidades_facturacion))
                .appendTo('#formSoliFactNormas');
                for (var i =0;i < itemUnidades_facturacion.length; i++) {              
                  var dato = Object.values(itemUnidades_facturacion[i]);
                  if(dato!=''){                
                    var monto_total_unidades=0;              
                    var datoArea = Object.values(itemAreas_facturacion[0][i]);                
                    var monto_area=datoArea[2];              
                    var monto_area=parseFloat(Math.round(monto_area * 100) / 100).toFixed(2);
                    for(var j = 0; j < itemUnidades_facturacion[i].length; j++){
                      var dato2 = Object.values(itemUnidades_facturacion[i][j]);
                      monto_total_unidades=monto_total_unidades+parseFloat(dato2[2]);
                    }
                    var monto_total_unidades=parseFloat(Math.round(monto_total_unidades * 100) / 100).toFixed(2);
                    if(monto_area!=monto_total_unidades){
                      // alert(monto_area+"-"+monto_total_unidades);
                      sw_x=false;
                    }

                  }      
                }
                if(!sw_x){ 
                  $('#msgError').html(mensaje);
                  $('#modalAlert').modal('show');               
                  return false;    
                }          
                if(monto_modal_por_tipopago!=0){
                  if(montoTotalItems!=monto_modal_por_area){
                    var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Areas...</p>";
                    $('#msgError').html(mensaje);
                    $('#modalAlert').modal('show'); 
                    return false;
                  }
                }
              }
            }
          }          
        }else{          
          if($("#total_monto_bob_a_areas").val()){            
            var tipo_solicitud=$("#tipo_solicitud").val();          
            if(tipo_solicitud==2){            
              var montoTotalItems=$("#modal_totalmontoserv_costo_a").val();
            }else{
              var montoTotalItems=$("#monto_total_a").val();
            }
            var monto_modal_por_area=$("#total_monto_bob_a_areas").val();
            var sw_x=true;//para ver la cantidad de las unidades
            var mensaje='<p>Por favor verifique los montos de la distribución de porcentajes en Unidades...<p>';
            //si existe array de objetos areas
            $('<input />').attr('type', 'hidden')
            .attr('name', 'areas_facturacion')
            .attr('value', JSON.stringify(itemAreas_facturacion))
            .appendTo('#formSoliFactNormas');
            //si existe array de objetos unidades
            $('<input />').attr('type', 'hidden')
            .attr('name', 'unidades_facturacion')
            .attr('value', JSON.stringify(itemUnidades_facturacion))
            .appendTo('#formSoliFactNormas');
            
            for (var i =0;i < itemUnidades_facturacion.length; i++) {              
              var dato = Object.values(itemUnidades_facturacion[i]);
              if(dato!=''){                
                var monto_total_unidades=0;              
                var datoArea = Object.values(itemAreas_facturacion[0][i]);                
                var monto_area=datoArea[2];              
                for(var j = 0; j < itemUnidades_facturacion[i].length; j++){
                  var dato2 = Object.values(itemUnidades_facturacion[i][j]);
                  monto_total_unidades=monto_total_unidades+parseFloat(dato2[2]);
                }
                if(monto_area!=monto_total_unidades){
                  // alert(monto_area+"-"+monto_total_unidades);
                  sw_x=false;
                }

              }      
            }
            if(!sw_x){ 
              $('#msgError').html(mensaje);
              $('#modalAlert').modal('show');               
              return false;    
            }
            
            if(monto_modal_por_tipopago!=0){
              if(montoTotalItems!=monto_modal_por_area){
                var mensaje="<p>Por favor verifique los montos de la distribución de porcentajes en Areas...</p>";
                $('#msgError').html(mensaje);
                $('#modalAlert').modal('show'); 
                return false;
              }

            }

          }
        }
      });
    $("#formRegComp").submit(function(e) {
      var envio=0;
      var mensaje=""; var debehaber=0;
      var debeIVA=0;
      var haberIVA=0;
      var banderaDebeHaberIVA=0;
      $("#boton_enviar_formulario").attr("disabled",true);
      $("#boton_enviar_formulario").html("Enviando...");
      numFilas=$("#cantidad_filas").val();
      if(numFilas==0){
        mensaje+="<p>Debe tener registrado al menos una cuenta en detalle</p>";
      }
      if($("#nro_correlativo").val()==""){
        mensaje+="<p>Debe seleccionar un tipo de comprobante</p>";
      }     
      if (numFilas == 0 ||$("#nro_correlativo").val()==""){
         $('#msgError').html(mensaje);
         $('#modalAlert').modal('show'); 
         envio=1;
      }else{
          if($("#totaldeb").val()==""||$("#totalhab").val()==""){
               mensaje+="<p>La suma total no puede ser 0 (Debe - Haber)</p>";
               $('#msgError').html(mensaje);
               $('#modalAlert').modal('show'); 
               envio=1;
          }else{
              if($("#totaldeb").val()!=$("#totalhab").val()){
                  mensaje+="<p>El total del DEBE y EL HABER no coinciden</p>";
                  $('#msgError').html(mensaje);
                  $('#modalAlert').modal('show'); 
                  envio=1;
              }else{
                  var cont=0; var contcuenta=0;var contcuentaIva=0;
                  for (var i = 0; i < numFilas; i++) {
                    if($('select[name=area'+(i+1)+']').length>0&&$('select[name=unidad'+(i+1)+']').length>0){
                     if($('select[name=area'+(i+1)+']').val()==null||$('select[name=unidad'+(i+1)+']').val()==null){
                        cont++;
                     }                  
                    }
                  }
                  if(cont!=0){
                    mensaje+="<p>Debe seleccionar la Unidad y el Area</p>";
                    $('#msgError').html(mensaje);
                    $('#modalAlert').modal('show');
                    envio=1;
                  }else{
                   for (var i = 0; i < numFilas; i++) {
                    if($("#debe"+(i+1)).length>0&&$("#haber"+(i+1)).length>0){                                         
                      if(($("#debe"+(i+1)).val()==""&&$("#haber"+(i+1)).val()=="")||$("#debe"+(i+1)).val()==0&&$("#haber"+(i+1)).val()==0){
                        mensaje+="<p>Todas las filas deben tener al menos un DEBE ó un HABER.</p>";
                        $('#msgError').html(mensaje);
                        $('#modalAlert').modal('show');
                        debehaber=1;
                      }
                      if($('#glosa_detalle'+(i+1)).val()==""){
                        mensaje+="<p>Fila "+(i+1)+". Debe registar la Glosa.</p>";
                        $('#msgError').html(mensaje);
                        $('#modalAlert').modal('show');
                        envio=1; 
                      }
                      if($('#cuenta'+(i+1)).val()==""||$('#cuenta'+(i+1)).val()==null||$('#cuenta'+(i+1)).val()==0){
                        contcuenta++;
                      } 
                      var cod_confi_iva=document.getElementById('cod_cuenta_configuracion_iva').value;
                     
                      //VALIDA LAS FACTURAS EN EL HABER PARA QUE NO SE REGISTREN
                      if($('#cuenta'+(i+1)).val()==cod_confi_iva){//para facturas
                        contcuentaIva++;
                        debeIVA=parseFloat($("#debe"+(i+1)).val());
                        haberIVA=parseFloat($("#haber"+(i+1)).val());
                        console.log("haberIVA: "+haberIVA);
                        if(haberIVA>0){
                          banderaDebeHaberIVA=1;
                        }
                      }           
                     }
                    }
                    console.log("numero de ivas: "+contcuentaIva+" "+debeIVA+" "+haberIVA+" banderaIVADH: "+banderaDebeHaberIVA);

                    if( contcuentaIva>0 && banderaDebeHaberIVA==0 ){
                      var cantiFacturas = itemFacturas.length;                        
                      var contadorFacturas=0;//var sumaTotalFactura=0;
                      var sumaTotalFactura=0;  
                      for (var i = 0; i < cantiFacturas; i++){
                        var factura=itemFacturas[i];                          
                        if(itemFacturas[i]==null || itemFacturas[i]==''){
                          contadorFacturas++;
                        }else{//existe facturas                                                 
                          for(var j = 0; j < itemFacturas[i].length; j++){
                            var dato = Object.values(itemFacturas[i][j]);
                            if(dato[4]==""){  dato[4]=0;}
                            if(dato[7]==""){  dato[7]=0;}
                            if(dato[8]==""){  dato[8]=0;}
                            sumaTotalFactura=sumaTotalFactura+parseFloat(dato[4]);//+parseFloat(dato[7])+parseFloat(dato[8]);
                          }                                                                                 
                        }                
                      }
                      var monto_debe_total_comprobante = $("#totaldeb").val();  
                      /*if(sumaTotalFactura!=monto_debe_total_comprobante){
                        mensaje+="<p>El Monto registrado en las facturas difiere del total!</p>";
                        $('#msgError').html(mensaje);
                        $('#modalAlert').modal('show');
                        envio=1; 
                      }*/
                      console.log("SUMA FACTURAS: "+sumaTotalFactura+" "+monto_debe_total_comprobante);
                      if(contadorFacturas==cantiFacturas){
                        mensaje+="<p>No puede existir Facturas vacías!</p>";
                        $('#msgError').html(mensaje);
                        $('#modalAlert').modal('show');
                        envio=1; 
                      } 
                    }
                    if(contcuenta!=0){
                      mensaje+="<p>No puede existir cuentas vacías!</p>";
                      $('#msgError').html(mensaje);
                      $('#modalAlert').modal('show');
                      envio=1; 
                    }else{
                      if(debehaber==1){
                        envio=1;
                      }else{
                        var contEstadoDebito=0;
                        for (var i = 0; i < numFilas; i++){
                          console.log("entro al detalle");
                          var debeZ=parseFloat($("#debe"+(i+1)).val());
                          var haberZ=parseFloat($("#haber"+(i+1)).val());
                          var tipoComprobante=parseFloat($("#tipo_comprobante").val());
                          var tipoEstadoCuenta=$("#tipo_estadocuentas"+(i+1)).val();//1 DEBE; 2 HABER
                          var tipoECCasoespecial=$("#tipo_estadocuentas_casoespecial"+(i+1)).val();
                          var cuentaAuxiliar=$("#cuenta_auxiliar"+(i+1)).val();  
                          var estadoCuentaSelect=$("#nestado"+(i+1)).hasClass("estado");

                          var detalleLibretaSelect=$("#nestadolib"+(i+1)).hasClass("estado");
                          var libretasBancarias=$("#libretas_bancarias"+(i+1)).hasClass("d-none"); 
                          var fechaComprobante=$("#fecha").val().split("-"); 
                          var d = new Date();
                          var mesActual = 7;//parseInt($("#global_mes").val());//d.getMonth()+1;
                          var anioActual = 2020;//parseInt($("#global_gestion").val());//d.getFullYear();
                          var habilitarValidacionLibreta=$("#validacion_libretas").val(); 
                        if($("#debe"+(i+1)).length>0){
                          //VALIDAMOS CUANDO LA CUENTA TENGA EC LA CUENTA AUXILIAR SIEMPRE ESTE SELECCIONADA.
                          if(tipoEstadoCuenta>0 && cuentaAuxiliar==0){  
                            $('#msgError').html("La fila "+(i+1)+" debe estar asociada a una CUENTA AUXILIAR, ya que está configurada para llevar Estados de Cuenta.");
                            $('#modalAlert').modal('show');
                            $("#boton_enviar_formulario").removeAttr("disabled");
                            $("#boton_enviar_formulario").html("Guardar");
                            return false;
                            //CONSULTAMOS SI EN EL CASO ESPECIAL ESTA MATANDO LA CUENTA
                          }else{
                            if( (tipoEstadoCuenta==1 && haberZ>0) || (tipoEstadoCuenta==2 && debeZ>0) ){
                              if( estadoCuentaSelect==false ){
                                $('#msgError').html("Fila "+(i+1)+" Debe seleccionar un Estado de Cuenta para Cerrar.");
                                $('#modalAlert').modal('show');
                                $("#boton_enviar_formulario").removeAttr("disabled");
                                $("#boton_enviar_formulario").html("Guardar");
                                return false;
                              }
                            }
                          }
                          if($("#debe"+(i+1)).val()>0&&$("#haber"+(i+1)).val()>0){
                                  $('#msgError').html("No puede existir montos en DEBE y en HABER en la Fila "+(i+1)+"!");
                                  $('#modalAlert').modal('show');
                                  $("#boton_enviar_formulario").removeAttr("disabled");
                                  $("#boton_enviar_formulario").html("Guardar");
                                  return false;
                          }
                          //Validar las cuentas que esten relacionadads al estado de cuentas los montos deben ser iguales
                          if( (tipoEstadoCuenta==1 && haberZ>0) ){
                            for (var f = 0; f < itemEstadosCuentas[i].length; f++) {
                              if(itemEstadosCuentas[i][f].monto!=haberZ){
                                 $('#msgError').html("Fila "+(i+1)+" El Monto del Estado de Cuenta no iguala al Haber.");
                                 $('#modalAlert').modal('show');
                                 $("#boton_enviar_formulario").removeAttr("disabled");
                                 $("#boton_enviar_formulario").html("Guardar");
                                 return false;
                              }
                            }  
                          }
                          if(parseInt(habilitarValidacionLibreta)>0){
                            //LA LIBRETA DEBE ESTAR RELACIONADA A LA CUENTA DE LA LIBRETA BANCARIA 
                            if(detalleLibretaSelect==false && libretasBancarias==false && $("#tipo_comprobante").val()!=4 && parseInt(fechaComprobante[1])>=parseInt(mesActual)&&parseInt(fechaComprobante[0])>=parseInt(anioActual)){
                                $('#msgError').html("Fila "+(i+1)+" Debe seleccionar un detalle de la Libreta Bancaria para Cerrar.");
                                $('#modalAlert').modal('show');
                                $("#boton_enviar_formulario").removeAttr("disabled");
                                $("#boton_enviar_formulario").html("Guardar");
                                return false;
                            }        
                          }
                          if( (tipoEstadoCuenta==2 && debeZ>0) ){
                            for (var f = 0; f < itemEstadosCuentas[i].length; f++) {
                              if(itemEstadosCuentas[i][f].monto!=debeZ){
                                 $('#msgError').html("Fila "+(i+1)+" El Monto del Estado de Cuenta no iguala al Debe.");
                                 $('#modalAlert').modal('show');
                                 $("#boton_enviar_formulario").removeAttr("disabled");
                                 $("#boton_enviar_formulario").html("Guardar");
                                 return false;
                              }
                            }  
                          }
                         }//fin if si existe
                        }
                        if(contEstadoDebito==1){
                          envio=1;
                        }else{
                          for (var i = 0; i < numFilas; i++) {
                              if($("#debe"+(i+1)).val()==""){
                                $("#debe"+(i+1)).val("0");
                              }
                              if($("#haber"+(i+1)).val()==""){
                                $("#haber"+(i+1)).val("0");
                              }
                          }     
                        }
                      }               
                    } 
                  }
              }
          }
        }
        if(envio==1){
          $("#boton_enviar_formulario").removeAttr("disabled");
          $("#boton_enviar_formulario").html("Guardar");
          return false;
        }else{
          //verificar archivos obligatorios
             var contArchOblig=0;
           for (var i = 0; i < $("#cantidad_archivosadjuntos").val(); i++) {
            if($('#obligatorio_file'+(i+1)).length>0){
              if($('#obligatorio_file'+(i+1)).val()==1){
                if($('#documentos_cabecera'+(i+1)).length>0){
                  if($('#documentos_cabecera'+(i+1)).val()==""&&!($("#existe_archivo_cabecera"+(i+1)).length>0)){
                     contArchOblig++; 
                     break;
                  }
                }    
               }
             }                  
            }
           if(contArchOblig!=0){
              $('#msgError').html("Debe cargar los archivos obligatorios");
              $('#modalAlert').modal('show');
              $("#boton_enviar_formulario").removeAttr("disabled");
              $("#boton_enviar_formulario").html("Guardar");
             return false;
           }else{ 
            $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturas))
            .appendTo('#formRegComp');
            $('<input />').attr('type', 'hidden')
            .attr('name', 'estados_cuentas')
            .attr('value', JSON.stringify(itemEstadosCuentas))
            .appendTo('#formRegComp');
           }
          
        }
    });


    $("#formRegDet").submit(function(e) {
      var mensaje="";
      if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un grupo en el DETALLE", "warning");
        return false;
      }else{
        if($("#cantidad_personal").length>0){
           if($("#cantidad_personal").text()>0){
             $('<input />').attr('type', 'hidden')
            .attr('name', 'detalles')
            .attr('value', JSON.stringify(itemDetalle))
            .appendTo('#formRegDet');
           }else{
             mensaje+="<p></p>";
             Swal.fire("Informativo!", "Debe registrar al menos un Personal", "warning");
             return false;
           }
        }else{
          $('<input />').attr('type', 'hidden')
            .attr('name', 'detalles')
            .attr('value', JSON.stringify(itemDetalle))
            .appendTo('#formRegDet');       
        }
      }     
    });


    $("#formDetTcp").submit(function(e) {
      var mensaje="";
      if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un detalle", "warning");
        return false;
      }else{
        var cont=0;
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if($('#cuenta_plantilladetalle'+(i+1)).val()==""||$('#cuenta_plantilladetalle'+(i+1)).val()==null){
             cont++; 
             break;
           }                  
        }
        if(cont!=0){
           Swal.fire("Informativo!", "No esta asignada la cuenta en uno o m&aacute; detalles <a href='#' class='btn btn-just-icon btn-primary btn-link'><i class='material-icons'>view_list</i><span class='bg-danger estado2'></span></a>", "warning"); 
           return false;
        }
      }     
    });

    $("#buttonSubmitFalse").on("click",function(){
          swal({
        title: '¿Estás Seguro Guardar?',
        text: "El Monto Solicitado es Mayor al Presupuestado",
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        buttonsStyling: false
      }).then((result) => {
          if (result.value) {
            $( "#buttonSubmit" ).click();
            return(true);
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            return(false);
          }
        })
    });

    $("#formSolDet").submit(function(e) {
      var mensaje="";
      /*if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        alertaModal('Debe registrar al menos un grupo','bg-secondary','text-white');
        return false;*/
      //}else{
      var tipoSolicitudRecurso=$("#tipo_solicitud").val();
      var cuentaHonorarios=$("#cuenta_honorarios_docente").val();

    if($("#cantidad_filas").val()==0){
        mensaje+="<p></p>";
        Swal.fire("Informativo!", "Debe registrar al menos un detalle", "warning");
        return false;
      }else{  //    primer else

        var cont=0;
        var mensajeRet="La Retencion IVA debe tener al menos una factura registrada";
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if(parseInt($('#cod_retencion'+(i+1)).val())==parseInt($('#cod_configuracioniva').val())){
             /*if(itemFacturas[i].length==0){
              cont++; 
              break;
             }*/      
           }else{
             if($('#cod_retencion'+(i+1)).val()==0||$('#cod_retencion'+(i+1)).val()==""){
              cont++;
              mensajeRet="Debe seleccionar una Retención <a href='#' class='btn btn-sm btn-fab btn-warning'><i class='material-icons text-dark'>ballot</i></a>";
              break;
             }
           }                  
        }
        //validacion SERVICIO DETALLE SR
        for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if(parseInt($('#cod_obligar_servicio_registro').val())==1){
            //alert(verificarAreaServicioDetalleSolicitud(i+1));
             if(($('#cod_servicio'+(i+1)).val()==0||$('#cod_servicio'+(i+1)).val()=="")&&verificarAreaServicioDetalleSolicitud(i+1)==1){
              cont++;
              mensajeRet="Debe seleccionar un Servicio  Relacionado al Gasto en <a href='#' class='btn btn-sm btn-fab btn-default'><i class='material-icons text-dark'>flag</i></a>";
              break;
             }     
           }                  
        }
        
        //verificar descripcion archivos
           for (var i = 0; i < $("#cantidad_archivosadjuntos").val(); i++) {
            if($('#nombre_archivo'+(i+1)).length>0){
              if($('#nombre_archivo'+(i+1)).val()==""){
                //cont++;
                //mensajeRet="Todos los archivos adjuntos deben tener una descripción"; 
                //break;
               }
             }                  
            }

        if(cont!=0){
           Swal.fire("Informativo!", mensajeRet, "warning"); 
           return false;
        }else{  //2do else
          //
          var cont=0;var sumaFactura=0;
          for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if(parseInt($('#cod_retencion'+(i+1)).val())==parseInt($('#cod_configuracioniva').val())){
            for (var d = 0; d < itemFacturas[i].length; d++) {
              sumaFactura+=parseFloat(itemFacturas[i][d].impFac);//-exeFac-iceFac-tazaFac;
              console.log("fac monto:"+itemFacturas[i][d].impFac);
            };    
           }                  
          }
          console.log("SUMATORIA FACTURAS:"+sumaFactura);
          console.log("TOTAL SOLICITUD:"+parseFloat($("#total_solicitado").val()));
          var restaIva=parseFloat($("#total_solicitado").val())-sumaFactura;
          if(sumaFactura>0){
            if((sumaFactura+restaIva)!=parseFloat($("#total_solicitado").val())){
              cont++; 
            }
          } 
          //cont=0; //para quitar la valicacion momentanea de la factura 
          //
        if(cont!=0){
          Swal.fire("Informativo!", "El monto total de las facturas es distinto al solicitado", "warning"); 
          return false;
        }else{
          var cont2=0;
          for (var i = 0; i < $("#cantidad_filas").val(); i++) {
           if(!($('#partida_cuenta_id'+(i+1)).val()>0)){
              cont2++; 
              break;    
           }                  
          }
          if(cont2!=0){
           Swal.fire("Informativo!", "Hay filas que no estan relacionadas a una cuenta!", "warning"); 
           return false;
          }else{  //3er else
            var contAct=0;
            for (var i = 0; i < $("#cantidad_filas").val(); i++) {
             
             if($('#unidad_fila'+(i+1)).val()==3000){
              if($('#cod_actividadproyecto'+(i+1)).val()==0||$('#cod_actividadproyecto'+(i+1)).val()==""){ //no estan relacionados a una actividad
                 contAct++; 
                 break;
              }       
             }                  
            }
           if(contAct!=0){
             Swal.fire("Informativo!", "Hay filas que no estan relacionadas a una actividad - PROYECTO SIS!", "warning"); 
             return false;
           }else{   //4to else
            //verificar archivos obligatorios
             var contArchOblig=0;
           for (var i = 0; i < $("#cantidad_archivosadjuntos").val(); i++) {
            if($('#obligatorio_file'+(i+1)).length>0){
              if($('#obligatorio_file'+(i+1)).val()==1){
                if($('#documentos_cabecera'+(i+1)).length>0){
                  if($('#documentos_cabecera'+(i+1)).val()==""&&!($("#existe_archivo_cabecera"+(i+1)).length>0)){
                     contArchOblig++; 
                     break;
                  }
                }    
               }
             }                  
            }
           if(contArchOblig!=0){
             Swal.fire("Informativo!", "Debe cargar los archivos obligatorios", "warning"); 
             return false;
           }else{  
                //quinto else
            var hayContraro=0;  var mensajeContrato="";
            if(tipoSolicitudRecurso==1&&$("#validacion_contrato").val()==1){
              var simulacionCodigo=$("#simulaciones").val().split("$$$")[0];
             for (var i = 0; i < $("#cantidad_filas").val(); i++) {
              if($('#partida_cuenta_id'+(i+1)).val()==cuentaHonorarios){
                var proveedorFila=$("#proveedor"+(i+1)).val();
                var montoFila=$("#importe"+(i+1)).val();
                var datosResp=verificarContratoDatosDesdeSolicitud(simulacionCodigo,proveedorFila,montoFila).split("#####");
                hayContraro=parseInt(datosResp[0]);
                mensajeContrato=datosResp[1];
                break;
              }
             }     
            }//fin tipo solicitud  
            if(hayContraro>0){
              Swal.fire("Informativo!", mensajeContrato, "warning"); 
             return false; 
            }else{
               //para poner la retencion iva si tiene al menos una factura..
           for (var i = 0; i < $("#cantidad_filas").val(); i++) {
            if($('#cod_retencion'+(i+1)).val()==0||$('#cod_retencion'+(i+1)).val()==""){
              $('#cod_retencion'+(i+1)).val(6); //agregar retenciones sin gasto;
            }else{
              if(itemFacturas[i].length!=0){
              $('#cod_retencion'+(i+1)).val($('#cod_configuracioniva').val()); 
              }                        
            }
             
            //asignar codigo 0 a division detalle, los que están con el boton oculto
            if($('#boton_division'+(i+1)).hasClass("d-none")){
              $('#cod_divisionpago'+(i+1)).val(0); //agregar codigo 0 de division (sin división);
            } 
           }

           $('<input />').attr('type', 'hidden')
            .attr('name', 'facturas')
            .attr('value', JSON.stringify(itemFacturas))
            .appendTo('#formSolDet');
           $('<input />').attr('type', 'hidden')
            .attr('name', 'd_oficinas')
            .attr('value', JSON.stringify(itemDistOficina))
            .appendTo('#formSolDet');
           $('<input />').attr('type', 'hidden')
            .attr('name', 'd_areas')
            .attr('value', JSON.stringify(itemDistArea))
            .appendTo('#formSolDet');
            
            $('<input />').attr('type', 'hidden')
            .attr('name', 'd_oficinas_global')
            .attr('value', JSON.stringify(itemDistOficinaGeneral))
            .appendTo('#formSolDet');
           $('<input />').attr('type', 'hidden')
            .attr('name', 'd_areas_global')
            .attr('value', JSON.stringify(itemDistAreaGlobal))
            .appendTo('#formSolDet');

            // documentos cabecera
            $('<input />').attr('type', 'hidden')
            .attr('name', 'archivos_cabecera')
            .attr('value', JSON.stringify(itemDocumentos))
            .appendTo('#formSolDet');

            $('<input />').attr('type', 'hidden')
            .attr('name', 'archivos_detalle')
            .attr('value', JSON.stringify(itemDocumentosDetalle))
            .appendTo('#formSolDet');
             }//else
             //formSolDet
          } 
         }      
        }
      }
     }  
    }
      //}    
    });
   document.getElementById('qrquincho').addEventListener('change', readSingleFile, false);
   document.getElementById('archivos').addEventListener('change', archivosPreview, false);
   document.getElementById('archivosDetalle').addEventListener('change', archivosPreviewDetalle, false);
  });
  </script>

 <script>
    $(document).ready(function() {
      // initialise Datetimepicker and Sliders 
      md.initFormExtendedDatetimepickers();
      if($("#boton_solicitudbuscar").length){
        addSolicitudDetalleSearch(); //
      }
      if($("#formRegComp")){
        Mousetrap.bind('alt+t', function(){ $("#tipo_comprobante").focus(); return false; });

        Mousetrap.bind('alt+a', function(){ addCuentaContable(); return false; });
        Mousetrap.bind('alt+q', function(){ minusCuentaContable(numFilas); return false; });
        Mousetrap.bind('shift+u', function(){ $('#modalCopySel').modal('show'); return false; });
        
        Mousetrap.bind('shift+g', function(){ $('#modalCopy').modal('show'); return false; });
        Mousetrap.bind('shift+r', function(){ $('#modalFile').modal('show'); return false; });
        Mousetrap.bind('shift+p', function(){ cargarPlantillas(); return false; });
        Mousetrap.bind('shift+s', function(){ modalPlantilla(); return false; });
        //salir de los modals con escape
        Mousetrap.bind('esc', function(){ $(".modal").modal("hide"); return false; });
        Mousetrap.bind('alt+enter', function(){ $(".modal").modal("hide"); return false; });
      }
      if($("#formSolDet")){
        if($("#simulacion").length){
          var tipo_s=1;
           Mousetrap.bind('alt+a', function(){ addSolicitudDetalle(null,tipo_s); return false; });
        }else{
          Mousetrap.bind('alt+s', function(){ addSolicitudDetalleSearch(); return false; });
        }
       
        Mousetrap.bind('alt+q', function(){ minusDetalleSolicitud(numFilas); return false; });
        //salir de los modals con escape
        Mousetrap.bind('esc', function(){ $(".modal").modal("hide"); return false; });
        Mousetrap.bind('alt+enter', function(){ $(".modal").modal("hide"); return false; });
      }
      
    });
  </script>
  <script type="text/javascript">
    $(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#libreta_bancaria_reporte tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" />' );
    } );
 
    // DataTable
    var table = $('#libreta_bancaria_reporte').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                });
            });
        },
        "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        },
        fixedHeader: {
              header: true,
              footer: true
        },
        "order": false,
        "paging":   false,
        "info":     false
        //"searching": false
    });
   
    $('#minus_tabla_lib').on( 'click', function (e) {
        e.preventDefault();
        for (var i = 8; i < 14; i++) {
          var column = table.column(i);
          column.visible( ! column.visible() );
        };
    } );
     

     $('#reporte_datos_busqueda tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" />' );
    } );
 
    // DataTable
    var table = $('#reporte_datos_busqueda').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every( function () {
                var that = this;
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                  var sumaBruto=0;
                    if ( that.search() !== this.value ) {
                        that.search( this.value ).draw();
                    }
                });
            });
        },
        footerCallback: function ( row, data, start, end, display ) {
          if($("#importe_bruto").length>0){
            var api = this.api();
            var pageTotal = api.column(11,{page:'current'}).data().reduce( function (a,b) {
                return parseFloat(a) + parseFloat(b);
               },0);
            //alert(pageTotal)
            $("#importe_bruto").val((new Intl.NumberFormat('de-DE').format(pageTotal.toFixed(2))).replace('.',' ').replace(',','.').replace(' ',','));
          }
        },
        "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        },
        fixedHeader: {
              header: true,
              footer: true
        },
        //"order": true,
        "paging":   false,
        "info":     false
        //"searching": false
    });
} );

    $(document).ready(function() {
      var table_mayor=$('#libro_mayor_rep').DataTable(
      {
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:'<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -100, 0, 50 ],
                    alignment: 'left',
                    image: imageLogo,
                    width:50,
                    height:50 
                } );
               doc.content.splice( 1, 0, {
                    margin: [ 0, 0, 0, 12 ],
                    text: [{
                      text: 'Periodo: '+periodo_mayor+' \n Cuenta: '+cuenta_mayor+' \n Unidad: '+unidad_mayor,
                      bold: true,
                      fontSize: 9,
                      alignment: 'left'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title:'Reporte Libro Mayor',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });
        var table_diario=$('#libro_diario_rep').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:      '<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title: 'Reporte Libro Diario',
                //messageTop:'Reporte Libro Diario',
                exportOptions: {
                        columns: ':visible'
                },
              customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -80, 0, 12 ],
                    alignment: 'left',
                    image: imageLogo,
                    width:50,
                    height:50, 
                } );
                doc.content.splice( 1, 0, {
                    margin: [ 100, 0, 0, 12 ],
                    text: [{
                      text: 'Unidad: '+unidad_reporte_diario+' \n Fecha: '+fecha_reporte_diario+' \n Tipo: '+tipo_reporte_diario,
                      bold: true,
                      fontSize: 9,
                      alignment: 'right'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title: 'Reporte Libro Diario',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });
 var table_diario=$('#reporte_sr').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": true,
            fixedHeader: {
              header: true,
              footer: true
            },
        });
        var table_diario=$('#libro_compras_rep').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title: 'Reporte Libro Compras',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title: 'Reporte Libro Compras',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title: 'Reporte Libro Compras',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:      '<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title: 'Reporte Libro Compras',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                        columns: ':visible'
                },
              customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -80, 0, 12 ],
                    alignment: 'left',
                    image: imageLogo,
                    width:50,
                    height:50, 
                } );
                doc.content.splice( 1, 0, {
                    margin: [ 100, 0, 0, 12 ],
                    text: [{
                      text: 'Unidad: '+unidad_reporte+' \n Fecha: '+fecha_reporte+' \n ',
                      bold: true,
                      fontSize: 9,
                      alignment: 'right'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title: 'Reporte Libro Compras',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });
        var table_diario=$('#libro_ventas_rep').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[

            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title: 'Reporte Libro Ventas',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title: 'Reporte Libro Ventas',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title: 'Reporte Libro Ventas',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text:      '<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title: 'Reporte Libro Ventas',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                //messageTop:'Reporte Libro Ventas',
                exportOptions: {
                        columns: ':visible'
                },
              customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -80, 0, 12 ],
                    alignment: 'left',
                    image: imageLogo,
                    width:50,
                    height:50, 
                } );
                doc.content.splice( 1, 0, {
                    margin: [ 100, 0, 0, 12 ],
                    text: [{
                      text: 'Gestión: '+gestion_reporte+' \n Mes: '+mes_reporte+' \n ',
                      bold: true,
                      fontSize: 9,
                      alignment: 'right'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title: 'Reporte Libro Ventas',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });
        var table_diario=$('#reporte_solicitud_facturacion').DataTable({
          "paging":   false,
            "info":     false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
            },
            "order": false,
            "searching": false,
            fixedHeader: {
              header: true,
              footer: true
            },
            dom: 'Bfrtip',
            buttons:[
            {
                extend: 'copy',
                text:      '<i class="material-icons">file_copy</i>',
                titleAttr: 'Copiar',
                title: 'Reporte Solicitud Facturación',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text:      '<i class="material-icons">list_alt</i>',
                titleAttr: 'CSV',
                title: 'Reporte Solicitud Facturación',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text:      '<i class="material-icons">assessment</i>',
                titleAttr: 'Excel',
                title: 'Reporte Solicitud Facturación',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                extend: 'pdfHtml5',
                text:      '<i class="material-icons">picture_as_pdf</i>',
                titleAttr: 'Pdf',
                title: 'Reporte Solicitud Facturación',
                //messageTop:'Reporte Solicitud Facturación',
                exportOptions: {
                        columns: ':visible'
                },
              customize: function ( doc) {
                   doc['footer']=(function(page, pages) { return {
                         columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                              text: page.toString(), italics: true 
                             },' de ',
                             { text: pages.toString(), italics: true }]
                          }],
                         margin: [10, 5]
                        }
                   });
                doc.content.splice( 1, 0, {
                    margin: [ 0, -80, 0, 12 ],
                    alignment: 'left',
                    image: imageLogo,
                    width:50,
                    height:50, 
                } );
                doc.content.splice( 1, 0, {
                    margin: [ 100, 0, 0, 12 ],
                    text: [{
                      //text: 'Unidad: '+unidad_reporte_diario+' \n Fecha: '+fecha_reporte_diario+' \n Tipo: '+tipo_reporte_diario,
                      bold: true,
                      fontSize: 9,
                      alignment: 'right'
                   }]        
                } );
              }
            },
            {
                extend: 'print',
                text:      '<i class="material-icons">print</i>',
                titleAttr: 'Imprimir',
                title: 'Reporte Solicitud Facturación',
                exportOptions: {
                    columns: ':visible'
                }
            }
          ]
        });

      var table_af=$('#tablePaginatorFixed2 ').DataTable({
            "paging":   false,
              "info":     false,
              "language": {
                  "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
              },
              "order": false,
              "searching": false,
              fixedHeader: {
                header: true,
                footer: true
              },
              dom: 'Bfrtip',
              buttons:[

              {
                  extend: 'copy',
                  text:      '<i class="material-icons">file_copy</i>',
                  titleAttr: 'Copiar',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'csv',
                  text:      '<i class="material-icons">list_alt</i>',
                  titleAttr: 'CSV',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'excel',
                  text:      '<i class="material-icons">assessment</i>',
                  titleAttr: 'Excel',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdf',
                  text:      '<i class="material-icons">picture_as_pdf</i>',
                  titleAttr: 'Pdf',
                  title: 'Reporte De Activos Fijos',
                  //messageTop:'Reporte Libro Diario',
                  exportOptions: {
                          columns: ':visible'
                  },
                customize: function ( doc) {
                     doc['footer']=(function(page, pages) { return {
                           columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                text: page.toString(), italics: true 
                               },' de ',
                               { text: pages.toString(), italics: true }]
                            }],
                           margin: [10, 5]
                          }
                     });
                  doc.content.splice( 1, 0, {
                      margin: [ 0, -50, 0, 12 ],
                      alignment: 'left',
                      image: imageLogo,
                      width:60,
                      height:60 
                  } );
                }
              },
              {
                  extend: 'print',
                  text:      '<i class="material-icons">print</i>',
                  titleAttr: 'Imprimir',
                  title: 'Reporte De Activos Fijos',
                  exportOptions: {
                      columns: ':visible'
                  }
              }
            ]
          });
    
      var table_afxU=$('#tablePaginatorFixed').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                      doc['footer']=(function(page, pages) { return {
                               columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: imageLogo,
                          width:50,
                          height:50 
                      } );
                      doc.defaultStyle.fontSize = 7;
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Reporte De Activos Fijos Por Unidad',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });

      var table_afxU=$('#tablePaginatorFixed1').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: imageLogo,
                          width:50,
                          height:50 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Depreciación De Activos Fijos Por Mes Y Gestión',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });
      var table_afxU=$('#tablePaginatorFixed3').DataTable({
                      "paging":   false,
                        "info":     false,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                        },
                        "order": false,
                        "searching": false,
                        fixedHeader: {
                          header: true,
                          footer: true
                        },
                        dom: 'Bfrtip',
                        buttons:[

                        {
                            extend: 'copy',
                            text:      '<i class="material-icons">file_copy</i>',
                            titleAttr: 'Copiar',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            text:      '<i class="material-icons">list_alt</i>',
                            titleAttr: 'CSV',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            text:      '<i class="material-icons">assessment</i>',
                            titleAttr: 'Excel',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdf',
                            text:      '<i class="material-icons">picture_as_pdf</i>',
                            titleAttr: 'Pdf',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            //messageTop:'Reporte Libro Diario',
                            exportOptions: {
                                    columns: ':visible'
                            },
                          customize: function ( doc) {
                               doc['footer']=(function(page, pages) { return {
                                     columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                          text: page.toString(), italics: true 
                                         },' de ',
                                         { text: pages.toString(), italics: true }]
                                      }],
                                     margin: [10, 5]
                                    }
                               });
                            doc.content.splice( 1, 0, {
                                margin: [ 0, -50, 0, 12 ],
                                alignment: 'left',
                                image: imageLogo,
                                width: 50,
                                height:50 
                            } );
                          }
                        },
                        {
                            extend: 'print',
                            text:      '<i class="material-icons">print</i>',
                            titleAttr: 'Imprimir',
                            title: 'Depreciación De Activos Fijos Por Rubro Por Mes',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                      ]
                    });
      var table_afxU=$('#tablePaginatorFixedAsignacion').DataTable({
        "paging":   false,
          "info":     false,
          "language": {
              "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
          },
          "order": false,
          "searching": false,
          fixedHeader: {
            header: true,
            footer: true
          },
          dom: 'Bfrtip',
          buttons:[

          {
              extend: 'copy',
              text:      '<i class="material-icons">file_copy</i>',
              titleAttr: 'Copiar',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'csv',
              text:      '<i class="material-icons">list_alt</i>',
              titleAttr: 'CSV',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'excel',
              text:      '<i class="material-icons">assessment</i>',
              titleAttr: 'Excel',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          },
          {
              extend: 'pdf',
              text:      '<i class="material-icons">picture_as_pdf</i>',
              titleAttr: 'Pdf',
              title: 'Reporte De Activos Fijos Asignados',
              //messageTop:'Reporte Libro Diario',
              exportOptions: {
                      columns: ':visible'
              },
            customize: function ( doc) {
                 doc['footer']=(function(page, pages) { return {
                       columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                            text: page.toString(), italics: true 
                           },' de ',
                           { text: pages.toString(), italics: true }]
                        }],
                       margin: [10, 5]
                      }
                 });
              doc.content.splice( 1, 0, {
                  margin: [ 0, -50, 0, 12 ],
                  alignment: 'left',
                  image: imageLogo,
                  width:50,
                  height:50 
              } );
            }
          },
          {
              extend: 'print',
              text:      '<i class="material-icons">print</i>',
              titleAttr: 'Imprimir',
              title: 'Reporte De Activos Fijos Asignados',
              exportOptions: {
                  columns: ':visible'
              }
          }
        ]
      });
      

      var table_afxU=$('#tablePaginatorFixedPlanillaSueldo').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Planilla Sueldos Personal',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: imageLogo,
                          width: 50,
                          height: 50 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Planilla Sueldos Personal',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });

    var table_afxU=$('#tablePaginatorFixedTributaria').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: false,
                    footer: false
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Planilla Tributaria',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: imageLogo,
                          width:50,
                          height:50 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Planilla Tributaria',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });
    var table_afxU=$('#tablePaginatorHeaderFooter').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[                 
                ]
              });

    var table_afx=$('#tablePaginatorFixedEstadoCuentas').DataTable({
                "paging":   false,
                  "info":     false,
                  "language": {
                      "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                  },
                  "order": false,
                  "searching": false,
                  fixedHeader: {
                    header: true,
                    footer: true
                  },
                  dom: 'Bfrtip',
                  buttons:[

                  {
                      extend: 'copy',
                      text:      '<i class="material-icons">file_copy</i>',
                      titleAttr: 'Copiar',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'csv',
                      text:      '<i class="material-icons">list_alt</i>',
                      titleAttr: 'CSV',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'excel',
                      text:      '<i class="material-icons">assessment</i>',
                      titleAttr: 'Excel',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  },
                  {
                      extend: 'pdf',
                      text:      '<i class="material-icons">picture_as_pdf</i>',
                      titleAttr: 'Pdf',
                      title: 'Reporte Estado De Cuentas',
                      //messageTop:'Reporte Libro Diario',
                      exportOptions: {
                              columns: ':visible'
                      },
                    customize: function ( doc) {
                         doc['footer']=(function(page, pages) { return {
                               columns: ['COBOFAR - REPORTES',{alignment: 'right',text: [{ 
                                    text: page.toString(), italics: true 
                                   },' de ',
                                   { text: pages.toString(), italics: true }]
                                }],
                               margin: [10, 5]
                              }
                         });
                      doc.content.splice( 1, 0, {
                          margin: [ 0, -50, 0, 12 ],
                          alignment: 'left',
                          image: imageLogo,
                          width:60,
                          height:60 
                      } );
                    }
                  },
                  {
                      extend: 'print',
                      text:      '<i class="material-icons">print</i>',
                      titleAttr: 'Imprimir',
                      title: 'Reporte Estado De Cuentas',
                      exportOptions: {
                          columns: ':visible'
                      }
                  }
                ]
              });

    });

  </script>
</body>
</html>