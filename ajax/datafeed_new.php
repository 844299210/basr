<?php


require 'includes/application_top.php';

require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';
require(DIR_WS_CLASSES . 'shipping.php');
function get_categories_name($cid,$c=0){
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
function get_categories_son_ids($cid,$c=0){
	global $db,$array_1;
	if($c == 1){
		$array_1 = array();
	}
	$result = $db->getAll("select categories_id,parent_id from categories where parent_id = '$cid'");
	foreach($result as $key=>$v){
		$array_1[] = $v['categories_id'];
		get_categories_son_ids($v['categories_id']);
	}
	return $array_1;
}

$cateArr1 = array_unique(get_categories_son_ids(573,1));
$cateArr2 = array_unique(get_categories_son_ids(9,1));
$cateArr3 = array_unique(get_categories_son_ids(918,1));
$str1 = implode(',',$cateArr1);
$str2 = implode(',',$cateArr2);
$str3 = implode(',',$cateArr3);
$str = $str1.",".$str2.",".$str3;
$objPHPExcel = new PHPExcel ();

$objPHPExcel->setActiveSheetIndex(0);
$shipping_modules = new shipping;
$countries_code_2 = 'US';
$code = 'USD';
if(isset($_GET['act']) && $_GET['act']){
	if($_GET['act'] == 'gb'){
		$countries_code_2 = 'GB';
		$code = 'GBP';
	}
	if($_GET['act'] == 'au'){
		$countries_code_2 = 'AU';
		$code = 'AUD';
	}
}
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
$query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter,ptc.categories_id, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight, p.products_weight_for_view,p.products_SKU
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
											 AND p.products_id not in (select products_id from products_to_categories where categories_id in ($str))
                       GROUP BY p.products_id
										 ORDER BY p.products_id ASC limit 100";
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
	$db->query("update products set status = 1 where products_id = '".$product['products_id']."'");
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
		$products_description = strip_tags(trim($product['products_name']));
		$output [] = str_replace('&nbsp;',' ',$products_description);
		$output [] = 'New';
		$products_price = (isset ( $product['products_price_sorter'] ) && is_numeric ( $product['products_price_sorter'] )) ? $product['products_price_sorter'] : $product['products_price'];
		if (!$products_price){
			$products_price = '';
		}else{
			$products_price = sprintf("%01.2f",$currencies->value($products_price,true,$code))." ".$code;
			//echo $products_price;exit;
			//$products_price = round($products_price,2)."  USD";
		}
		$output [] = $products_price;
		$output [] = 'In stock';
		if($link){
			$link_arr = explode('-p-',$link);
			if($link_arr){
				$link = HTTP_SERVER.'/'.'-p-'.$link_arr[count($link_arr)-1];
			}
		}else{
			$link = "";
		}
		$output [] = $link ? $link."?currency=".$code : '';
		if($product['products_image']){
			$images = HTTP_SERVER."/images/".$product['products_image'];
		}else{
			$images = "";
		}
		$images_url = DIR_FS_CATALOG."images/".$product['products_image'];
		if(!$images || !file_exists($images_url))continue;
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
		$total_weight = $product['products_weight'];
		$usd_to_cny_rate = $currencies->currencies['CNY']['value'];
		$length_array = array('length'=>'1m','products_id'=>$product['products_id'],'qty'=>1);
		$shipping = $shipping_modules->quotes('','',true,$countries_code_2,$length_array);
   		$shipping = get_sort($shipping);
		if($shipping){
			$cost = $shipping[0]['methods'][0]['cost'];
		}else{
			$cost = 0;
		}
		$shipping_cost = number_format($cost,2,'.','');
		//$shipping_cost = ($product['products_weight'] > 0 ) ?	number_format( ( (($product['products_weight'] * 110) + 15) / $usd_to_cny_rate),2,'.','') : 0;
		$shipping_cost = $countries_code_2.':::'.sprintf("%01.2f",$currencies->value($shipping_cost,true,$code));
		$output [] = $shipping_cost;
		$output [] = $product['products_weight_for_view'] ? trim($product['products_weight_for_view'])." kg" :'NULL';
		$output [] = '';
		$output [] = '';
		$output [] = '';
		$output [] = get_cate($product['categories_id']);
		
		
		
		if (sizeof($output)){
			for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
				$objPHPExcel->getActiveSheet ()->setCellValue ( $exel_columns [$i].$index, $output [$i] );
			}
		}
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