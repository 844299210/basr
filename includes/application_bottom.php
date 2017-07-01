<?php
/**
 * application_bottom.php
 * Common actions carried out at the end of each page invocation.
 *
 * @package initSystem
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

/*bof after place an order , free variable*/
if (isset($_GET['main_page']) && $_GET['main_page'] == 'place_order_success') {
    
	/*foreach ($_SESSION as $key=>$value)
	{
		if ($key != 'customer_id' || $key != 'name' || $key != 'customer_first_name' || $key != 'customer_country_id' || $key != 'customer_zone_id' || $key != 'customers_authorization') {
			unset($_SESSION[$key]);
		}
	}*/
}
/*eof after place an order , free variable*/


// enable ob cache
if(!isset($_SESSION['customer_id'])){
	if (defined('OB_CACHE_ENABLE') && 'true' == OB_CACHE_ENABLE &&   in_array($_GET['main_page'], $ob_cache_pages)){
		
		$page_contents = ob_get_flush();
	
		//store cache files
		if (FILENAME_DEFAULT == $_GET['main_page'] && $this_is_home_page || FILENAME_DEFAULT != $_GET['main_page']){
			//only caching home page and other pages except categories pages
			cacheFactory::save_caching_file_contents(DIR_WS_CATALOG.'cache/htmls/page_'.$_GET['main_page'].'.html', DIR_FS_CATALOG.'cache/htmls/page_'.$_GET['main_page'].'.html', $page_contents);
		}
		//output buffering contents and stop buffering
		ob_end_flush();
	}
}
if (defined('OB_CACHE_ENABLE') && 'true' == OB_CACHE_ENABLE &&   in_array($_GET['main_page'],$cache_array)){
	$page_contents = ob_get_flush(); 
	
	foreach($cache_array_info as $key=>$v){
		if($_GET['main_page'] == $key){
			if(empty($v)){
				if(isset($_GET['page'])){
					$file = $key."_".$_GET['page'];
				}else{
					$file = $key;
				}
			}else{
				if(isset($_GET['page'])){
						$file = md5($v."_".$_GET[$v]."_".$_GET['page']);
				}else{
						$file = md5($v."_".$_GET[$v]);
				}
			}
			if($_GET['main_page']=='product_info'){
				if(isset($_SESSION['customer_id']) && intval($_SESSION['customer_id'])>0){
				}else{
					if($_SESSION['currency'] == 'USD'){
						
						cacheFactory::save_caching_file_contents(DIR_WS_CATALOG.'cache/products/'.$file.'.html', DIR_FS_CATALOG.'cache/products/'.$file.'.html', $page_contents);
					}
				}
				
			}else{
				cacheFactory::save_caching_file_contents(DIR_WS_CATALOG.'cache/products/'.$file.'.html', DIR_FS_CATALOG.'cache/products/'.$file.'.html', $page_contents);
			}
			
			break;
		}
	}
	ob_end_flush();

}


// close session (store variables)
session_write_close();

