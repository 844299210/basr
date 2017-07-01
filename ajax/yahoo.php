<?php
/**
 * yahoo.php
 *
 * @package yahoo product submit feeder
 * @copyright Copyright 2007 Numinix Technology http://www.numinix.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: yahoo.php, v 1.08a 04.23.2008 22:42:59 numinix $
 * @author Numinix Technology
 */
@define('YAHOO_VERSION', '1.08a 04.23.2008 23:42:59');
/*
Yahoo! Product Submit - Attribute List - http://searchmarketing.yahoo.com/shopsb/shpsb_specs.php
*/
	require('includes/application_top.php');

	@define('YAHOO_EXPIRATION_DAYS', 30);
	@define('YAHOO_EXPIRATION_BASE', 'now'); // now/product
	@define('YAHOO_OFFER_ID', 'id'); // id/model/false
	@define('YAHOO_DIRECTORY', 'feed/');
	@define('YAHOO_OUTPUT_BUFFER_MAXSIZE', 1024*1024);
	@define('YAHOO_CHECK_IMAGE', 'false');
	@define('YAHOO_STAT', false);
	$anti_timeout_counter = 0; //for timeout issues as well as counting number of products processed
	$max_limit = false;
	$today = date("Y-m-d");
	@define('YAHOO_USE_CPATH', 'false');
	@define('NL', "<br />\n");

	if (YAHOO_MAGIC_SEO_URLS == 'true') {
	  require_once(DIR_WS_CLASSES . 'msu_ao.php');
	  include(DIR_WS_INCLUDES . 'modules/msu_ao_1.php');
	}

	require(zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] .'/', 'yahoo.php', 'false'));

	$languages = $db->execute("select code, languages_id from " . TABLE_LANGUAGES . " where name='" . YAHOO_LANGUAGE . "' limit 1");

	$product_url_add = (YAHOO_LANGUAGE_DISPLAY == 'true' ? "&language=" . $languages->fields['code'] : '') . (YAHOO_CURRENCY_DISPLAY == 'true' ? "&currency=" . YAHOO_CURRENCY : '');

	echo TEXT_YAHOO_STARTED . NL;
	echo TEXT_YAHOO_FILE_LOCATION . DIR_FS_CATALOG . YAHOO_DIRECTORY . YAHOO_OUTPUT_FILENAME . NL;
	echo "Processing: Feed - " . (isset($_GET['feed']) && $_GET['feed'] == "yes" ? "Yes" : "No") . ", Upload - " . (isset($_GET['upload']) && $_GET['upload'] == "yes" ? "Yes" : "No") . NL;

if (isset($_GET['feed']) && $_GET['feed'] == "yes") {
	if (is_dir(DIR_FS_CATALOG . YAHOO_DIRECTORY)) {
		if (!is_writeable(DIR_FS_CATALOG . YAHOO_DIRECTORY)) {
			echo ERROR_YAHOO_DIRECTORY_NOT_WRITEABLE . NL;
			die;
		}
	} else {
		echo ERROR_YAHOO_DIRECTORY_DOES_NOT_EXIST . NL;
		die;
	}

	$stimer_feed = microtime_float();
	if (!get_cfg_var('safe_mode') && function_exists('safe_mode')) {
		set_time_limit(0);
	}

	$output_buffer = "";

	if(!zen_yahoo_fwrite()) {
		echo ERROR_YAHOO_OPEN_FILE . NL;
		die;
	}

		$output = array();

		/*$output["code"] = "code";
		$output["name"] = "name";
		$output["price"] = "price";
		$output["product-url"] = "product-url";
		$output["merchant-site-category"] = "merchant-site-category";
		$output["medium"] = "medium";
		$output["image-url"] = "image-url";
		$output["sale-price"] = "sale-price";
		$output["brand"] = YAHOO_BRAND;
		$output["model"] = YAHOO_MODEL;
		if (YAHOO_ASA == true) {
			$output["condition"] = "condition";
			$output["upc"] = "upc";
			$output["isbn"] = "isbn";
			if (YAHOO_SHIPPING == "fixed") $output["shipping-price"] = "shipping-price";
		}
		if (YAHOO_INSTOCK == true) $output["in-stock"] = "in-stock";
		if (YAHOO_AVAILABILITY_SWITCH == true) $output["availability"] = "availability";
		if (YAHOO_SHIPPING == "calculated") {
			$output["shipping-weight"] = "shipping-weight";
			$output["shipping-surcharge"] = "shipping-surcharge";
		}
		$output["description"] = "description";*/
		
		
		/******************************************************************/
		/*Required for pricegrabber*/
		$output['Unique-Retailer-SKU'] = 'Unique-Retailer-SKU';
		$output['Manufacturer-Part-Number'] = 'Manufacturer-Part-Number';
		$output['Condition'] = 'Condition';
		$output['Availability'] = 'Availability';
		$output['Selling-Price'] = 'Selling-Price';
		$output['Manufacturer-Name'] = 'Manufacturer-Name';
		$output['Product-Title'] = 'Product-Title';
		$output['Categorization'] = 'Categorization';
		$output['Product-URL'] = 'Product-URL';
		$output['Image-URL'] = 'Image-URL';
		$output['Detailed-Description'] = 'Detailed-Description';
		
		
		
		/* Sometimes Required for price grabber*/
		$output['Weight'] = 'Weight';
		$output['Shipping-Cost'] = 'Shipping-Cost';
		/******************************************************************/
		zen_yahoo_fwrite($output);


		$categories_array = zen_yahoo_category_tree();

		/*if (YAHOO_ASA == 'true') {
		$products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter, p.products_upc, p.products_isbn, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight, p.products_condition,p.products_sku
										 FROM " . TABLE_PRODUCTS . " p
											 LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
											 LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
											 LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
											 LEFT JOIN " . TABLE_SPECIALS . " s ON (s.products_id = p.products_id)
										 WHERE p.products_status = 1
											 AND p.product_is_call = 0
											 AND p.product_is_free = 0
											 AND pd.language_id = " . (int)$languages->fields['languages_id'] ."
                       GROUP BY p.products_id
										 ORDER BY p.products_id ASC";
		} else {
		$products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, p.products_weight,p.products_sku
										 FROM " . TABLE_PRODUCTS . " p
											 LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
											 LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
											 LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
											 LEFT JOIN " . TABLE_SPECIALS . " s ON (s.products_id = p.products_id)
										 WHERE p.products_status = 1
											 AND p.product_is_call = 0
											 AND p.product_is_free = 0
											 AND pd.language_id = " . (int)$languages->fields['languages_id'] ."
                     GROUP BY p.products_id
										 ORDER BY p.products_id ASC";
		}*/

		
		/*$products_array = array(
		67,60,63,10003,10017,10026,10027,10028,10029,10094,10133,10134,10278,10279,10268,10269,10274,10275,10297,10280,10281,10282,10479,10480,
		10482,10481,10484,10478,10485,10486,10508,10507,10505,10506,10602,10601,10260,10261,10237);*/
		
		//$top_path_array = array(9,112,135);
		
		//$second_path_array = array(27,22,61,83,82,118,98,89,100);
		
//		$get_second_paths = $db->Execute("select categories_id from " . TABLE_CATEGORIES . ' WHERE parent_id in (' . join(',',$top_path_array). ")") ;
		
		
		$second_path_array = $products_array = array();
		$get_second_paths = $db->Execute("select categories_id from " . TABLE_CATEGORIES . " WHERE parent_id  = 1") ;
		while (!$get_second_paths->EOF){
			
			$second_path_array[] = $get_second_paths->fields['categories_id'];
			$get_second_paths->MoveNext();
		}
		
		$get_products_list = $db->Execute("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . ' WHERE categories_id in (' . join(',',$second_path_array). " )") ;
		while (!$get_products_list->EOF){
			
			$products_array[] = $get_products_list->fields['products_id'];
			$get_products_list->MoveNext();
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
											 AND pd.language_id = " . (int)$languages->fields['languages_id'] .
											" AND p.products_id in (" . join(',',$products_array) . ")
                       GROUP BY p.products_id
										 ORDER BY p.products_id ASC ";
		
		
		$products = $db->Execute($products_query);
		$tax_rate = array();
			while (!$products->EOF && !$max_limit) { // run until end of file or until maximum number of products reached
				list($categories_list, $cPath) = zen_yahoo_get_category($products->fields['products_id']);
				if (numinix_categories_check((YAHOO_POS_CATEGORIES), $products->fields['products_id'], 1) == true && numinix_categories_check((YAHOO_NEG_CATEGORIES), $products->fields['products_id'], 2) == false) { // check to see if category limits are set.  If so, only process for those categories.
					if ($anti_timeout_counter == YAHOO_MAX_PRODUCTS && YAHOO_MAX_PRODUCTS != 0) { // if counter is greater than or equal to maximum products
						$max_limit = true; // then max products reached
					} else {
						$max_limit = false; // otherwise, max products not reached
					}
					//if ($products->fields['specials_id'] == $products->fields['products_id']) {
						//if ($today < $products->fields['expires_date']) {
							//$special_price = $products->fields['specials_new_products_price'];
						//}
					//}
					//if (PROJECT_VERSION_MINOR < 3.6) {
						//$price = zen_get_products_actual_price($products->fields['products_id']);
					//} else {
						//$price = $products->fields['products_price_sorter'];
					//}
					$price = $products->fields['products_price_sorter'];
					$special_price = zen_get_products_actual_price($products->fields['products_id']); // will use specials or salemaker if present
					if (PROJECT_VERSION_MINOR < 3.6) { // if older version of zen cart, use zen_get_products_actual_price
						$price = $special_price;
					}
					if ($price > 0) {
						$anti_timeout_counter++;
						if (!isset($tax_rate[$products->fields['products_tax_class_id']])) {
							$tax_rate[$products->fields['products_tax_class_id']] = zen_get_tax_rate($products->fields['products_tax_class_id']);
						}
						$price = zen_add_tax($price, $tax_rate[$products->fields['products_tax_class_id']]);
						$special_price = zen_add_tax($special_price, $tax_rate[$products->fields['products_tax_class_id']]);
						$price = $currencies->value($price, true, YAHOO_CURRENCY, $currencies->get_value(YAHOO_CURRENCY));
						$special_price = $currencies->value($special_price, true, YAHOO_CURRENCY, $currencies->get_value(YAHOO_CURRENCY));
						// BEGIN MAGIC SEO URLS
						if (YAHOO_MAGIC_SEO_URLS == 'true') {
							include(DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
						// END MAGIC SEO URLS
						} else {
							$link = ($products->fields['type_handler'] ? $products->fields['type_handler'] : 'product') . '_info';
							$cPath_href = (YAHOO_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
							$link = zen_href_link($link, $cPath_href . 'products_id=' . (int)$products->fields['products_id'] . $product_url_add, 'NONSSL', false);
						}
						$link = html_entity_decode($link);

						$output = array();
						/*$output["code"] = (YAHOO_CODE == "model" ? $products->fields['products_model'] : (int)$products->fields['products_id']);
						$output["name"] = zen_yahoo_cleaner($products->fields['products_name']);
						$output["price"] = $price;
						$output["product-url"] = $link;*/

						$bread_crumbs = zen_yahoo_get_category($products->fields['products_id']);
						array_pop($bread_crumbs);
						$bread_crumbs = implode(" > ", $bread_crumbs);
						$bread_crumbs  = str_replace(',',' >',$bread_crumbs);
						/*$bread_crumbs = htmlentities($bread_crumbs);*/

						/*$output["merchant-site-category"] = $bread_crumbs;
						$output["medium"] = ""; // will add function for determining medium of music/videos in later version.
						if (zen_yahoo_image_url($products->fields['products_image']) != '') {
							$output["image-url"] = zen_yahoo_image_url(zen_yahoo_cleaner($products->fields['products_image']));
						} else {
							$output["image-url"] = "";
						}
						$output["sale-price"] = ($special_price < $price ? $special_price : ''); // if a special price exists, use it or leave blank
						$output["brand"] = zen_yahoo_cleaner($products->fields['manufacturers_name']);
						$output["model"] = zen_yahoo_cleaner($products->fields['products_model'], true);
						$output["condition"] = (YAHOO_ASA == 'true' ? ($products->fields['products_condition'] != '' ? $products->fields['products_condition'] : YAHOO_CONDITION) : YAHOO_CONDITION);
						$output["upc"] = $products->fields['products_upc'];
						$output["isbn"] = $products->fields['products_isbn'];
						$output["shipping-price"] = $products->fields['products_sh_na'];

						$instock = ($products->fields['products_quantity'] > 0 ? "yes" : "no");
						$output["in-stock"] = $instock;

						$output["availability"] = YAHOO_AVAILABILITY;

						$weight = $products->fields['products_weight'];
						if (YAHOO_ASA == 'true'){
							if ($products->fields['products_weight_type'] == "kgs") {
								$weight = $weight * 2.20462262;
							}
						}
						$output["shipping-weight"] = $weight;
						$output["shipping-surcharge"] = YAHOO_SURCHARGE;
						$output["description"] = zen_yahoo_cleaner($products->fields['products_description']);*/
						
						/**for price grabber**/
						$products_id = $products->fields['products_id'] ? $products->fields['products_id'] : '';
						/*$products_sku = isset($products->fields['products_sku']) ? str_replace('SKU','SKU#',$products->fields['products_sku']) : 'NULL';*/
						$sku_or_mrn = mt_rand(10000,999999);
						$output['Unique-Retailer-SKU'] = $sku_or_mrn;
						$output['Manufacturer-Part-Number'] = $sku_or_mrn;
						$output['Condition'] = 'New';
						$output['Availability'] = 'Yes';
						$products_price = (isset($products->fields['products_price_sorter']) && is_numeric($products->fields['products_price_sorter'])) ? $products->fields['products_price_sorter'] : $products->fields['products_price'];
						if (!$products_price) $products_price = 'NULL';
						$output['Selling-Price'] = $products_price;
						$output['Manufacturer-Name'] = $products->fields['manufacturers_name'] ? $products->fields['manufacturers_name'] : 'NULL';
						$output['Product-Title'] = $products->fields['products_name'] ? $products->fields['products_name'] : 'NULL';
						/*$output['Categorization'] = $bread_crumbs ? $bread_crumbs : 'NULL';*/
						$output['Categorization'] = get_price_grabber_categorization($products_id);
						$output['Product-URL'] = $link ? $link : 'NULL';
						if (zen_yahoo_image_url($products->fields['products_image']) != '') {
							$output["Image-URL"] = zen_yahoo_image_url($products->fields['products_image']);
						} else {
							$output["Image-URL"] = "NULL";
						}
						$output['Detailed-Description'] = $products->fields['products_description'] ? preg_replace("/[\r|\n]+/i"," ",str_replace(array('•','&nbsp;'), '', strip_tags($products->fields['products_description']))) : 'NULL';
						
						$output['Detailed-Description'] = !empty($output['Detailed-Description']) ? substr($output['Detailed-Description'], 0 , (strpos($output['Detailed-Description'], 'etc.')+4)) : 'NULL';
						
						
						/* Sometimes Required for price grabber*/
						
						$output['Weight'] = (isset($products->fields['products_weight']) && is_numeric($products->fields['products_weight'])) ? $products->fields['products_weight'] : 'NULL';
						$output['Shipping-Cost'] = '';
						/**for price grabber**/
						
						
						
						zen_yahoo_fwrite($output);
						
					}
				}
				$products->MoveNext();
			}

	zen_yahoo_fwrite();

	$timer_feed = microtime_float()-$stimer_feed;

	echo TEXT_YAHOO_FEED_COMPLETE . ' ' . YAHOO_TIME_TAKEN . ' ' . sprintf("%f " . TEXT_YAHOO_FEED_SECONDS, number_format($timer_feed, 6) ) . ' ' . $anti_timeout_counter . TEXT_YAHOO_FEED_RECORDS . NL;
}

if (isset($_GET['upload']) && $_GET['upload'] == "yes") {
	echo TEXT_YAHOO_UPLOAD_STARTED . NL;
	if(ftp_file_upload(YAHOO_SERVER, YAHOO_USERNAME, YAHOO_PASSWORD, DIR_FS_CATALOG . YAHOO_DIRECTORY . YAHOO_OUTPUT_FILENAME, YAHOO_ACCOUNT_NUMBER)) {
		echo TEXT_YAHOO_UPLOAD_OK . NL;
		$db->execute("update " . TABLE_CONFIGURATION . " set configuration_value = '" . date("Y/m/d H:i:s") . "' where configuration_key='YAHOO_UPLOADED_DATE'");
	} else {
		echo TEXT_YAHOO_UPLOAD_FAILED . NL;
	}
}

	function zen_yahoo_fwrite($output='') {
		static $fp = false;
		static $output_buffer = "";
		static $title_row = false;
		if($output == '') {
			if(!$fp) {
				$retval = $fp = fopen(DIR_FS_CATALOG . YAHOO_DIRECTORY . YAHOO_OUTPUT_FILENAME, "wb");
			} else {
				if(strlen($output_buffer) > 0) {
					$retval = fwrite($fp, $output_buffer, strlen($output_buffer));
					$output_buffer = "";
				}
				fclose($fp);
			}
		} else {
			if(!$title_row) {
				$title_row = $output;
			}
			$buf = array();
			foreach($title_row as $key=>$val) {
				$buf[] = (isset($output[$key]) ? ltrim($output[$key]) : '');
			}
			$output = implode("\t", $buf);
			if(strlen($output_buffer) > YAHOO_OUTPUT_BUFFER_MAXSIZE) {
				$retval = fwrite($fp, $output_buffer, strlen($output_buffer));
				$output_buffer = "";
			}
			$output = rtrim($output) . "\n";
			/*if(strtolower(CHARSET) != 'utf-8')
				$output_buffer .= utf8_encode($output);
			else
				$output_buffer .= $output;*/
			$output_buffer .= $output;
		}
		return $retval;
	}

	function trim_array($x) {
   		if (is_array($x)) {
       		return array_map('trim_array', $x);
   		} else {
   			return trim($x);
		}
	}

  function numinix_categories_check($categories_list, $products_id, $charge) {
    $categories_array = split(',', $categories_list);
    if ($categories_list == '') {
      if ($charge == 1) {
        return true;
      } elseif ($charge == 2) {
        return false;
      }
    } else {
      $match = false;
      foreach($categories_array as $category_id) {
        if (zen_product_in_category($products_id, $category_id)) {
          $match = true;
          break;
        }
      }
      if ($match == true) {
        return true;
      } else {
        return false;
      }
    }
  }

	function zen_yahoo_get_category($products_id) {
		global $categories_array, $db;
		static $p2c;
		if(!$p2c) {
			$q = $db->Execute("SELECT *
												FROM " . TABLE_PRODUCTS_TO_CATEGORIES);
			while (!$q->EOF) {
				if(!isset($p2c[$q->fields['products_id']]))
					$p2c[$q->fields['products_id']] = $q->fields['categories_id'];
				$q->MoveNext();
			}
		}
		if(isset($p2c[$products_id])) {
			$retval = $categories_array[$p2c[$products_id]]['name'];
			$cPath = $categories_array[$p2c[$products_id]]['cPath'];
		} else {
			$cPath = $retval =  "";
		}
		return array($retval, $cPath);
	}

	function zen_yahoo_category_tree($id_parent=0, $cPath='', $cName='', $cats=array()){
		global $db, $languages;
		$cat = $db->Execute("SELECT c.categories_id, c.parent_id, cd.categories_name
												 FROM " . TABLE_CATEGORIES . " c
													 LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id
												 WHERE c.parent_id = '" . (int)$id_parent . "'
												 AND cd.language_id='" . (int)$languages->fields['languages_id'] . "'
												 AND c.categories_status= '1'",
												 '', false, 150);
		while (!$cat->EOF) {
			$cats[$cat->fields['categories_id']]['name'] = (zen_not_null($cName) ? $cName . ', ' : '') . trim($cat->fields['categories_name']); // previously used zen_yahoo_sanita instead of trim
			$cats[$cat->fields['categories_id']]['cPath'] = (zen_not_null($cPath) ? $cPath . '_' : '') . $cat->fields['categories_id'];
			if (zen_has_category_subcategories($cat->fields['categories_id'])) {
				$cats = zen_yahoo_category_tree($cat->fields['categories_id'], $cats[$cat->fields['categories_id']]['cPath'], $cats[$cat->fields['categories_id']]['name'], $cats);
			}
			$cat->MoveNext();
		}
		return $cats;
	}

	function zen_yahoo_sanita($str, $rt=false) { // currently using zen_yahoo_cleaner below instead of zen_yahoo_sanita
		$str = strip_tags($str);
		$str = str_replace(array("\t" , "\n", "\r"), ' ', $str);
		$str = preg_replace('/\s\s+/', ' ', $str);
//	$str = str_replace(array("&reg;", "�", "&copy;", "�", "&trade;", "�"), ' ', $str);
		$str = htmlentities(html_entity_decode($str));
		$in = $out = array();
		$in[] = "&reg;"; $out[] = '(r)';
		$in[] = "&copy;"; $out[] = '(c)';
		$in[] = "&trade;"; $out[] = '(tm)';
//		$str = str_replace($in, $out, $str);
		if($rt) {
			$str = str_replace(" ", "&nbsp;", $str);
			$str = str_replace("&nbsp;", "", $str);
		}
		$str = trim($str);
		return $str;
	}

	function zen_yahoo_cleaner($str) {
		$str = html_entity_decode($str);
		$_strip_search = array("![\t ]+$|^[\t ]+!m",'%[\r\n]+%m'); // remove CRs and newlines
		$_strip_replace = array('',' ');
		$_cleaner_array = array(">" => "> ", "&reg;" => "", "�" => "", "&trade;" => "", "�" => "", "\t" => "", "    " => "");
		$str = strtr($str, $_cleaner_array);
		$str = strip_tags($str);
		$str = preg_replace($_strip_search, $_strip_replace, $str);
		
		$str = preg_replace("[\t]+", '', $str);// fresh the tab
		return $str;
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

	function zen_yahoo_expiration_date($base_date) {
		if(YAHOO_EXPIRATION_BASE == 'now')
			$expiration_date = time();
		else
			$expiration_date = strtotime($base_date);
		$expiration_date += YAHOO_EXPIRATION_DAYS*24*60*60;
		$retval = (date('Y-m-d', $expiration_date));
		return $retval;
	}

	function ftp_file_upload($url, $login, $password, $local_file, $ftp_dir='', $ftp_file=false, $ssl=false, $ftp_mode=FTP_ASCII) {
		if(!is_callable('ftp_connect')) {
			echo FTP_FAILED . NL;
			return false;
		}
		if(!$ftp_file)
			$ftp_file = basename($local_file);
		ob_start();
		if($ssl)
			$cd = ftp_ssl_connect($url);
		else
			$cd = ftp_connect($url);
		if (!$cd) {
			$out = ftp_get_error_from_ob();
			echo FTP_CONNECTION_FAILED . ' ' . $url . NL;
			echo $out . NL;
			return false;
		}
		echo FTP_CONNECTION_OK . ' ' . $url . NL;
		$login_result = ftp_login($cd, $login, $password);
		if (!$login_result) {
			$out = ftp_get_error_from_ob();
//			echo FTP_LOGIN_FAILED . FTP_USERNAME . ' ' . $login . FTP_PASSWORD . ' ' . $password . NL;
			echo FTP_LOGIN_FAILED . NL;
			echo $out . NL;
			ftp_close($cd);
			return false;
		}
//		echo FTP_LOGIN_OK . FTP_USERNAME . ' ' . $login . FTP_PASSWORD . ' ' . $password . NL;
		echo FTP_LOGIN_OK . NL;
		if ($ftp_dir != "") {
			if (!ftp_chdir($cd, $ftp_dir)) {
				$out = ftp_get_error_from_ob();
				echo FTP_CANT_CHANGE_DIRECTORY . '&nbsp;' . $url . NL;
				echo $out . NL;
				ftp_close($cd);
				return false;
			}
		}
		echo FTP_CURRENT_DIRECTORY . '&nbsp;' . ftp_pwd($cd) . NL;
		ftp_pasv($cd, true);
		$upload = ftp_put($cd, $ftp_file, $local_file, $ftp_mode);
		$out = ftp_get_error_from_ob();
		$raw = ftp_rawlist($cd, $ftp_file, true);
		for($i=0,$n=sizeof($raw);$i<$n;$i++){
			$out .= $raw[$i] . '<br/>';
		}
		if (!$upload) {
			echo FTP_UPLOAD_FAILED . NL;
			if(isset($raw[0])) echo $raw[0] . NL;
			echo $out . NL;
			ftp_close($cd);
			return false;
		} else {
			echo FTP_UPLOAD_SUCCESS . NL;
			echo $raw[0] . NL;
			echo $out . NL;
		}
		ftp_close($cd);
		return true;
	}

	function ftp_get_error_from_ob() {
		$out = ob_get_contents();
		ob_end_clean();
		$out = str_replace(array('\\', '<!--error-->', '<br>', '<br />', "\n", 'in <b>'),array('/', '', '', '', '', ''),$out);
		if(strpos($out, DIR_FS_CATALOG) !== false){
			$out = substr($out, 0, strpos($out, DIR_FS_CATALOG));
		}
		return $out;
	}

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
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