<?php

require "../assets/libraries/PHP_EXCEL/PHPExcel.php";
require "../assets/libraries/PHP_EXCEL/PHPExcel/Writer/Excel5.php"; 







// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Create a first sheet, representing sales data
$objPHPExcel->setActiveSheetIndex(0);


$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Nro. Pedido');
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Fecha');
$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Días');
$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Observaciones');
$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Proveedor');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Lineas');
$objPHPExcel->getActiveSheet()->setCellValue('G1', 'F. Prom Venta');
$objPHPExcel->getActiveSheet()->setCellValue('H1', 'F. Saldo');;

$objPHPExcel->getActiveSheet()->SetCellValue('A2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('C2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('D2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('E2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('F2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('G2', '');
$objPHPExcel->getActiveSheet()->SetCellValue('H2', '');


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Name of Sheet 1');

// Create a new worksheet, after the default sheet
$objPHPExcel->createSheet();

// Add some data to the second sheet, resembling some different data types
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'More data');

// Rename 2nd sheet
$objPHPExcel->getActiveSheet()->setTitle('Second sheet');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-Excel');
header('Content-Disposition: attachment;filename="name_of_file.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
?>