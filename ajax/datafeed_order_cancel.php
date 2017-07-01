<?php

require 'includes/application_top.php';

require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';
require(DIR_WS_CLASSES . 'shipping.php');

$objPHPExcel = new PHPExcel ();

$objPHPExcel->setActiveSheetIndex(0);
$shipping_modules = new shipping;

//$objPHPExcel->getActiveSheet()->setTitle ( 'datafeed for common' );
/*
 *execl 的列为
 *A1,B1
 *A2,B2
 */
$exel_columns = array ('A', 'B');
$pricegrabber_title = array ('Order ID', 'Reason');

for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
	
	$objPHPExcel->getActiveSheet ()->setCellValue ($exel_columns[$i].'1', $pricegrabber_title [$i] );

}
$query = "select a.orders_id,b.reason from orders a,orders_cancel_request b where a.orders_id = b.orders_id and  a.orders_status = 5";
$orders = $db->getAll($query);
$index = 2;           //this is use to identify the row
foreach ($orders as $i => $order){		
		$output = array ();
		$output [] = trim($order['orders_id']);
		$output [] = trim($order['reason']);
		if (sizeof($output)){
			for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
				$objPHPExcel->getActiveSheet ()->setCellValue ( $exel_columns [$i].$index, $output [$i] );
			}
		}
		$index++;
}
//$file = 'google_shopping'.date('Ymdhis');
//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//$objWriter->save ( DIR_FS_CATALOG . 'feed/'.$file.'.xls');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
$filename="datafeed_order_cancel.xls"; 

header('Content-Type: application/vnd.ms-excel'); 

header('Content-Disposition: attachment;filename="'.$filename.'"');

header('Cache-Control: max-age=0'); 

$objWriter->save('php://output'); 