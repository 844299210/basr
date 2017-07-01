<?php
require 'includes/application_top.php';

function zen_is_set_category_en($cid)
{
	global $db;

	$result = $db->Execute("select count(categories_id) total from " .TABLE_CATEGORIES_DESCRIPTION . " where language_id = 1 and categories_id = ".$cid);
	return ($result->fields['total'] < 1) ? false : true;
} 
function zen_is_set_category_sp($cid)
{
	global $db;

	$result = $db->Execute("select count(categories_id) total from " .TABLE_CATEGORIES_DESCRIPTION . " where language_id = 2 and categories_id = ".$cid);
	return ($result->fields['total'] < 1) ? false : true;
}

// pid between 10886 and 14873

for ($i = 10886; $i <=14873; $i++) {
	if (zen_is_set_category_en($i) && !zen_is_set_category_sp($i)) {
		$result = $db->Execute("select categories_id,categories_name,categories_description,categories_introduction from " . TABLE_PRODUCTS_DESCRIPTION. " 
			WHERE categories_id = ".$i." AND language_id = 1;");
	
		$c_info = array(
		'categories_id' => $result->fields['categories_id'],
		'categories_name' => $result->fields['categories_name'],
		'categories_description' => $result->fields['categories_description'],
		'categories_introduction' => $result->fields['categories_introduction'],
		'language_id' => 2
		);
		
		zen_db_perform(TABLE_CATEGORIES_DESCRIPTION,$c_info);
		unset($c_info);
	}
}
/*
for ($index = 14411; $index <= 14873; $index++) {
	$result = $db->Execute("select products_name,products_description,products_url,products_viewed,products_specifications,
			products_short_description,products_overview from " . TABLE_PRODUCTS_DESCRIPTION. " 
			WHERE products_id = ".$index." AND language_id = 1;");
	
	while(!$result->EOF){
		$products_info = array(
		'products_name' => $result->fields['products_name'],
		'products_description' => $result->fields['products_description'],
		'products_url' => $result->fields['products_url'],
		'products_viewed' => $result->fields['products_viewed'],
		'products_specifications' => $result->fields['products_specifications'],
		'products_short_description' => $result->fields['products_short_description'],
		'products_overview' => $result->fields['products_overview'],
		'language_id' => 2
		);
		
		zen_db_perform(TABLE_PRODUCTS_DESCRIPTION,$products_info);
		unset($products_info);
	
	$result->MoveNext();
	}
}
*/
echo 'end..';