<?php


require 'includes/application_top.php';

require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';
function get_categories_name($cid,$c=0) {
	global $db,$array;
	if($c == 1){
		$array = array();
	}
	$result = $db->getAll("select categories_id,parent_id from categories where categories_id = '$cid' limit 1");
	if($result){
		$array[] = $result[0]['categories_id'];
		get_categories_name($result[0]['parent_id']);
	}
	return $array;
}
function get_cate($cid){
	global $db;
	$cate_arr = array_reverse(get_categories_name($cid,1));
	if($cate_arr){
		$str = 'HOME';
		foreach($cate_arr as $key=>$v){
			$res = $db->getAll("select categories_name from categories_description where categories_id = '$v' and language_id =1  limit 1");
			if($res){
				$str .= ' > '.$res[0]['categories_name'];
			}
		}
		return $str;
	}else{
		
		return "";
	}
}

$objPHPExcel = new PHPExcel ();

$objPHPExcel->setActiveSheetIndex(0);

//$objPHPExcel->getActiveSheet()->setTitle ( 'datafeed for common' );
/*
 *execl 的列为
 *A1,B1
 *A2,B2
 */

$exel_columns = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z' );
$pricegrabber_title = array ('id', 'title', 'description', 'condition', 'price', 'availability', 'link', 'image link', 'gtin', 'mpn', 'brand', 'google','genter','age group','size','color','material','pattern','item group id','tax','shipping','shipping weight','sale price','sale price affective date','addtional','product type');

for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
	
	$objPHPExcel->getActiveSheet ()->setCellValue ($exel_columns[$i].'1', $pricegrabber_title [$i] );

}
$query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter,ptc.categories_id, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight,p.products_SKU
										 FROM " . TABLE_PRODUCTS . " p
											 LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
											 LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
											 LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
											 LEFT JOIN " . TABLE_SPECIALS . " s ON (s.products_id = p.products_id) 
											 LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON (p.products_id = ptc.products_id)
										 WHERE p.products_status = 1
											 AND p.product_is_call = 0
											 AND p.product_is_free = 0
											 AND pd.language_id = 1
											 AND p.status = 0
                       GROUP BY p.products_id
										 ORDER BY p.products_id ASC limit 1000";
//AND p.products_id >= 22785
//AND p.products_id <= 24043
//AND p.products_id >= 24044
//AND p.products_id <= 24587
//AND p.products_id >= 24588
//AND p.products_id <= 25018
//AND p.products_id >= 25019
//AND p.products_id <= 25049
$products = $db->getAll($query);

$index = 2;//this is use to identify the row
foreach ($products as $i => $product){
	if (YAHOO_MAGIC_SEO_URLS == 'true') {
		include (DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
	
		// END MAGIC SEO URLS
	} else {
		//$link = ($product['type_handler'] ? $product['type_handler'] : 'product') . '_info';
		$cPath_href = (YAHOO_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
		$link = zen_href_link ( FILENAME_PRODUCT_INFO, $cPath_href . 'products_id=' . ( int ) $product['products_id'], 'NONSSL', false );
	}
	$link = html_entity_decode ( $link );
	if ($product['products_price_sorter'] > 0){
		$output = array ();
		$output [] = trim($product['products_SKU']);
		$output [] = trim($product['products_name']);
		$products_description = strip_tags(trim($product['products_description']));
		$output [] = str_replace('&nbsp;',' ',$products_description);
		$output [] = 'New';
		$products_price = (isset ( $product['products_price_sorter'] ) && is_numeric ( $product['products_price_sorter'] )) ? $product['products_price_sorter'] : $product['products_price'];
		if (!$products_price){
			$products_price = '';
		}else{
			$products_price = $products_price."  USD";
		}
		$output [] = $products_price;
		$output [] = 'In stock';
		$output [] = $link ? $link : '';
		if($product['products_image']){
			$images = HTTP_SERVER."/images/".$product['products_image'];
		}else{
			$images = "";
		}
		$output [] = $images;
		$output [] = '';
		$output [] = '';
		$output [] = 'FiberStore';
		$output [] =  get_cate($product['categories_id']);
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = '::0:';
		$usd_to_cny_rate = $currencies->currencies['CNY']['value'];
		$shipping_cost = ($product['products_weight'] > 0 ) ?	number_format( ( (($product['products_weight'] * 110) + 15) / $usd_to_cny_rate),2,'.','') : 0;
		$output [] = 'US:::'.$shipping_cost;
		$output [] = $product['products_weight'] ? trim($product['products_weight'])." kg" :'NULL';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = get_cate($product['categories_id']);
		
		
		
		if (sizeof($output)){
			for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
				$objPHPExcel->getActiveSheet ()->setCellValue ( $exel_columns [$i].$index, $output [$i] );
			}
		}
		$db->query("update products set status = 1 where products_id = '".$product['products_id']."'");
		$index++;
	}
}
//$file = 'google_shopping'.date('Ymdhis');
//$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
//$objWriter->save ( DIR_FS_CATALOG . 'feed/'.$file.'.xls');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
$filename="datafeed.xls"; 

header('Content-Type: application/vnd.ms-excel'); 

header('Content-Disposition: attachment;filename="'.$filename.'"');

header('Cache-Control: max-age=0'); 

$objWriter->save('php://output'); 