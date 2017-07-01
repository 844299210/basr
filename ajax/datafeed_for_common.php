<?php

if (isset($_GET['datafeed']) && 'common' == $_GET['datafeed']){
	

require 'includes/application_top.php';

/*datafeed class*/
require 'includes/classes/datafeed_common.php';
$datafeed = new datafeed(array(1,7,132));

function get_price_grabber_categorization($products_id){
		
		 global $db;
		 //$get_category = $db->Execute("select categories_name from " . TABLE_PRODUCTS_TO_CATEGORIES . " as ptc, ".TABLE_CATEGORIES_DESCRIPTION." as pd  WHERE ptc.categories_id = pd.categories_id and ptc.products_id = " . (int)$products_id);
		 $get_parent_id = $db->Execute("select parent_id from " . TABLE_PRODUCTS_TO_CATEGORIES ." as ptc , ".TABLE_CATEGORIES." AS c  WHERE c.categories_id = ptc.categories_id and  ptc.products_id = " . (int)$products_id);
		 if ($get_parent_id->RecordCount()){
		 	$parent_id = $get_parent_id->fields['parent_id'];
		 }
		 if (isset($parent_id) && $parent_id){/*get parent categories name according to parent_id*/
		 	$get_category = $db->Execute("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION. " WHERE categories_id  =  ". (int)$parent_id);
		 
		 
		 if ($get_category->RecordCount()) $categories_name = $get_category->fields['categories_name'];
		 
		 if (isset($categories_name) && $categories_name){
		 	switch (true){
		 		case strpos(strtolower($categories_name),'adapter'):
		 			$categories_name = 'Computers > Networking > Bridges';
		 			break;
		 		case strpos(strtolower($categories_name),'firewall') or strpos(strtolower($categories_name),'security'):
		 			$categories_name = 'Computers > Networking > Firewall & Security Devices';
		 			break;
		 		case strpos(strtolower($categories_name),'hub'):
		 			$categories_name = 'Computers > Networking > Hubs';
		 			break;
		 		case strpos(strtolower($categories_name),'modem'):
		 			$categories_name = 'Computers > Networking > Modems';
		 			break;
		 		
		 		case strpos(strtolower($categories_name),'storage'):
		 			$categories_name = 'Computers > Networking > Network Storage Devices';
		 			break;
		 		case strpos(strtolower($categories_name),'print'):
		 			$categories_name = 'Computers > Networking > Print Servers';
		 			break;
		 		case strpos(strtolower($categories_name),'routers'):
		 			$categories_name = 'Computers > Networking > Routers';
		 			break;
		 		case strpos(strtolower($categories_name),'switches'):
		 			$categories_name = 'Computers > Networking > Switches';
		 			break;
		 		case strpos(strtolower($categories_name),'telephon'):
		 			$categories_name = 'Computers > Networking > Telephony';
		 			break;
		 		case strpos(strtolower($categories_name),'terminal'):
		 			$categories_name = 'Computers > Networking > Terminals';
		 			break;
		 		case strpos(strtolower($categories_name),'transceiver'):
		 			$categories_name = 'Computers > Networking > Transceivers';
		 			break;
		 		case strpos(strtolower($categories_name),'wireless'):
		 			$categories_name = 'Computers > Networking > Wireless Networking';
		 			break;
		 		default:
		 			$categories_name = 'Computers > Networking > Other';
		 			break;
		 		
		 	}
		 	
		 	return $categories_name;
		 }
		 }
		 return $categories_name;
		 
	}

function zen_yahoo_image_url($products_image) {
		if($products_image == "") return "";

		$products_image_extention = substr($products_image, strrpos($products_image, '.'));
		$products_image_base = ereg_replace($products_image_extention, '', $products_image);
		$products_image_medium = $products_image_base . IMAGE_SUFFIX_MEDIUM . $products_image_extention;
		$products_image_large = $products_image_base . IMAGE_SUFFIX_LARGE . $products_image_extention;

		// check for a medium image else use small
		if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
		  $products_image_medium = DIR_WS_IMAGES . $products_image;
		} else {
		  $products_image_medium = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
		}
		// check for a large image else use medium else use small
		if (!file_exists(DIR_WS_IMAGES . 'large/' . $products_image_large)) {
		  if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
		    $products_image_large = DIR_WS_IMAGES . $products_image;
		  } else {
		    $products_image_large = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
		  }
		} else {
		  $products_image_large = DIR_WS_IMAGES . 'large/' . $products_image_large;
		}
		//if (function_exists('handle_image')) {
			//$image_ih = handle_image($products_image_large, '', 300, 300, '');
			//$retval = (HTTP_SERVER . DIR_WS_CATALOG . $image_ih[0]);
		//} else {
			$retval = (HTTP_SERVER . DIR_WS_CATALOG . $products_image_large);
		//}
		return $retval;
	}
require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';

$objPHPExcel = new PHPExcel ();

$objPHPExcel->setActiveSheetIndex ( 0 );

$objPHPExcel->getActiveSheet ()->setTitle ( 'datafeed for common' );

/*
 *execl 的列为
 *A1,B1
 *A2,B2
 */

$exel_columns = array ('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L' );
$pricegrabber_title = array ('Unique-Retailer-SKU', 'Manufacturer-Part-Number', 'Condition', 'Availability', 'Selling-Price', 'Manufacturer-Name', 'Product-Title', 'Categorization', 'Product-URL', 'Image-URL', 'Detailed-Description', 'Weight' );

for($i = 0; $i < sizeof ( $exel_columns ); $i ++) {
	
	$objPHPExcel->getActiveSheet ()->setCellValue ($exel_columns[$i].'1', $pricegrabber_title [$i] );
}

$products = $datafeed->get_products_array();

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
		// the price must > 0
	
			$output = array ();
			$products_id = $product['products_id'] ? $product['products_id'] : '';
			/*$products_sku = isset($products->fields['products_sku']) ? str_replace('SKU','SKU#',$products->fields['products_sku']) : 'NULL';*/
			$sku_or_mrn = mt_rand ( 10000, 999999 );
			$output [] = $sku_or_mrn;
			$output [] = $sku_or_mrn;
			$output [] = 'New';
			$output [] = 'Yes';
			$products_price = (isset ( $product['products_price_sorter'] ) && is_numeric ( $product['products_price_sorter'] )) ? $product['products_price_sorter'] : $product['products_price'];
			if (! $products_price)
				$products_price = 'NULL';
			$output [] = $products_price;
			$output [] = $product['manufacturers_name'] ? $product['manufacturers_name'] : 'NULL';
			$output [] = $product['products_name'] ? $product['products_name'] : 'NULL';
			/*$output['Categorization'] = $bread_crumbs ? $bread_crumbs : 'NULL';*/
			$output [] = get_price_grabber_categorization ( $products_id );
			$output [] = $link ? $link : 'NULL';
			$output [] = zen_yahoo_image_url ( $product['products_image'] );
			//$product['products_description'] = $product['products_description'] ? preg_replace ( "/[\r|\n]+/i", " ", str_replace ( array ('•', '&nbsp;' ), '', strip_tags ( $product['products_description'] ) ) ) : 'NULL';
			
			//$output [] = ! empty ( $product['products_description'] ) ? substr ( $product['products_description'], 0, (strpos ( $product['products_description'], 'etc.' ) + 4) ) : 'NULL';
			$output [] = strip_tags($product['products_description']);
			/* Sometimes Required for price grabber*/
			
			$output [] = (isset ( $product['products_weight'] ) && is_numeric ( $product['products_weight'] )) ? $product['products_weight'] : 'NULL';
			//$output['Shipping-Cost'] = '';
			/**for price grabber**/
		
			if (sizeof($output)){
			for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
				$objPHPExcel->getActiveSheet ()->setCellValue ( $exel_columns [$i].$index, $output [$i] );
			}}
			$index++;
	}
}



$objWriter = new PHPExcel_Writer_Excel5 ( $objPHPExcel );
$objWriter->save ( DIR_FS_CATALOG . 'feed/datafeed_for_common.xls');
echo ('over ........');

}else echo '404';