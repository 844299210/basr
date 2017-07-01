<?php
require 'includes/application_top.php';
require 'PHPExcel.php';
require 'PHPExcel/Writer/Excel5.php';

$objPHPExcel = new PHPExcel ();

$objPHPExcel->setActiveSheetIndex ( 0 );

$objPHPExcel->getActiveSheet ()->setTitle ( 'pricegrabber' );

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


$second_path_array = $products_array = array ();
$get_second_paths = $db->Execute ( "select categories_id from " . TABLE_CATEGORIES . " WHERE parent_id  = 1" );
while ( ! $get_second_paths->EOF ) {
	
	$second_path_array [] = $get_second_paths->fields ['categories_id'];
	$get_second_paths->MoveNext ();
}

$get_products_list = $db->Execute ( "select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE categories_id in (' . join ( ',', $second_path_array ) . " )" );

while ( ! $get_products_list->EOF ) {
	
	$products_array [] = $get_products_list->fields ['products_id'];
	$get_products_list->MoveNext ();
}

$products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight,p.products_sku
										 FROM " . TABLE_PRODUCTS . " p
											 LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
											 LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
											 LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
											 LEFT JOIN " . TABLE_SPECIALS . " s ON (s.products_id = p.products_id)
										 WHERE p.products_status = 1
											 AND p.product_is_call = 0
											 AND p.product_is_free = 0
											 AND pd.language_id = 1
											 AND p.products_id in (" . join(',',$products_array) . ")
                       GROUP BY p.products_id
										 ORDER BY p.products_id ASC ";
$products = $db->Execute ($products_query);

$index = 2;//this is use to identify the row
while ( ! $products->EOF ) {
	
	if (YAHOO_MAGIC_SEO_URLS == 'true') {
		include (DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
	
	// END MAGIC SEO URLS
	} else {
		//$link = ($products->fields ['type_handler'] ? $products->fields ['type_handler'] : 'product') . '_info';
		$cPath_href = (YAHOO_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
		$link = zen_href_link ( FILENAME_PRODUCT_INFO, $cPath_href . 'products_id=' . ( int ) $products->fields ['products_id'], 'NONSSL', false );
	}
	$link = html_entity_decode ( $link );
	
	if ($products->fields ['products_price_sorter'] > 0){
		// the price must > 0
	
			$output = array ();
			$products_id = $products->fields ['products_id'] ? $products->fields ['products_id'] : '';
			/*$products_sku = isset($products->fields['products_sku']) ? str_replace('SKU','SKU#',$products->fields['products_sku']) : 'NULL';*/
			$sku_or_mrn = mt_rand ( 10000, 999999 );
			$output [] = $sku_or_mrn;
			$output [] = $sku_or_mrn;
			$output [] = 'New';
			$output [] = 'Yes';
			$products_price = (isset ( $products->fields ['products_price_sorter'] ) && is_numeric ( $products->fields ['products_price_sorter'] )) ? $products->fields ['products_price_sorter'] : $products->fields ['products_price'];
			if (! $products_price)
				$products_price = 'NULL';
			$output [] = $products_price;
			$output [] = $products->fields ['manufacturers_name'] ? $products->fields ['manufacturers_name'] : 'NULL';
			$output [] = $products->fields ['products_name'] ? $products->fields ['products_name'] : 'NULL';
			/*$output['Categorization'] = $bread_crumbs ? $bread_crumbs : 'NULL';*/
			$output [] = get_price_grabber_categorization ( $products_id );
			$output [] = $link ? $link : 'NULL';
			$output [] = zen_yahoo_image_url ( $products->fields ['products_image'] );
			$products->fields ['products_description'] = $products->fields ['products_description'] ? preg_replace ( "/[\r|\n]+/i", " ", str_replace ( array ('•', '&nbsp;' ), '', strip_tags ( $products->fields ['products_description'] ) ) ) : 'NULL';
			
			$output [] = ! empty ( $products->fields ['products_description'] ) ? substr ( $products->fields ['products_description'], 0, (strpos ( $products->fields ['products_description'], 'etc.' ) + 4) ) : 'NULL';
			
			/* Sometimes Required for price grabber*/
			
			$output [] = (isset ( $products->fields ['products_weight'] ) && is_numeric ( $products->fields ['products_weight'] )) ? $products->fields ['products_weight'] : 'NULL';
			//$output['Shipping-Cost'] = '';
			/**for price grabber**/
		
			if (sizeof($output)){
			for($i = 0; $i < sizeof ( $exel_columns ); $i++) {
				$objPHPExcel->getActiveSheet ()->setCellValue ( $exel_columns [$i].$index, $output [$i] );
			}}
			$index++;
	}
	$products->MoveNext ();
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

function get_price_grabber_categorization($products_id){
		
		 /*global $db;
		 //$get_category = $db->Execute("select categories_name from " . TABLE_PRODUCTS_TO_CATEGORIES . " as ptc, ".TABLE_CATEGORIES_DESCRIPTION." as pd  WHERE ptc.categories_id = pd.categories_id and ptc.products_id = " . (int)$products_id);
		 $get_parent_id = $db->Execute("select parent_id from " . TABLE_PRODUCTS_TO_CATEGORIES ." as ptc , ".TABLE_CATEGORIES." AS c  WHERE c.categories_id = ptc.categories_id and  ptc.products_id = " . (int)$products_id);
		 if ($get_parent_id->RecordCount()){
		 	$parent_id = $get_parent_id->fields['parent_id'];
		 }
		 if (isset($parent_id) && $parent_id){/*get parent categories name according to parent_id* /
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
		 }*/
		 return 'Computers > Networking > Transceivers';
		 
	}
$objWriter = new PHPExcel_Writer_Excel5 ( $objPHPExcel );
$objWriter->save ( DIR_FS_CATALOG . 'feed/pricegrabber.xls');
echo ('over ........');