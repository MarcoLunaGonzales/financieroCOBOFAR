<?php
ob_start();
require 'imp_solicitud_recursos.php';
$html = ob_get_clean();
descargarPDFSolicitudesRecursos("COBOFAR - Solicitud Recursos",$html);