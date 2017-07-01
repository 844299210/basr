<?php

if(isset($_GET['request_type'])){

	require 'includes/application_top.php';

	if($_GET['request_type'] == 'custom_shipping'){

		if($_POST['method'] && $_POST['acount']){
			$_SESSION['method_shppings'] = $_POST['method'];
			$_SESSION['method_acounts'] = $_POST['acount'];
		}

	}elseif($_GET['request_type'] == 'wholesale_price'){
		$qty =  intval($_POST['qty']) ? $_POST['qty']:0;
		$products_id = intval($_POST['products_id']);

		$result = $db->Execute("select products_price from products where products_id = '".$products_id."'");
		$products_price = $result->fields['products_price'];
		$productsPriceDiscount= get_products_final_price_of_discount($products_price,$products_id,$qty);
		
		
		if(!get_products_have_discount_of_categories((int)$products_id)){

				$products_price = $productsPriceDiscount;
		 }
		 echo  $currencies->format($products_price);exit;
	}elseif($_GET['request_type'] == 'products_instock'){
		$products_id = (int)$_POST['products_id'];
		$country_code = $_POST['country_code'];
		$countries_code_2 = strtoupper($country_code);
		$shipping_html = '<span class="product_03_02">'.FS_AVAILABILTY.':</span>';
 
       $NowInstockQTY = zen_get_products_instock_total_qty_of_products_id($products_id);
       $deliver_time = zen_get_products_instock_shipping_date_of_products_id($products_id,$NowInstockQTY,$countries_code_2);              
 		$shipping_html .='<link itemprop="itemCondition" href="http://schema.org/NewCondition" />
		<span class="products_in_stock">
		'. $NowInstockQTY.'<em>,</em>'.'</span> '.$deliver_time;
		if($deliver_time == '<b>'.FS_SHIP_SAME_DAY.'</b>'){
			$shipping_html .= '<link itemprop="availability" href="http://schema.org/InStock"/>';
		}
 		if($deliver_time != '<b>'.FS_SHIP_SAME_DAY.'</b>'){
 		$shipping_html .= '<div class="track_orders_wenhao">
		<div class="question_bg"></div>
		 <div class="question_text_01 leftjt"><div class="arrow"></div>
			<div class="popover-content">';
			if($deliver_time == '<b>'.FS_SHIP_NEXT_DAY.'</b>'){
			$shipping_html .=FS_PRODUCTS_ORDERS_RECEIVED.'<br/>'.FS_PRODUCTS_ACTUAL_TIME;
			}else{
			 $shipping_html .=FS_PRODUCTS_ACTUAL_TIME;
			}
             $shipping_html .='<link itemprop="availability" href="http://schema.org/OnlineOnly"/>';
		 $shipping_html .='</div></div></div>';
 		}
		$shipping_html .='<div class="ccc"></div>';
		echo $shipping_html;
	}elseif($_GET['request_type'] == 'custom_weekend_up'){
		$shipping_method = $_POST['shipping_method'];
		$week_price =  $currencies->value($_POST['week_price']);
		$_SESSION['o_arrive'] = $week_price;
					
		require(DIR_WS_CLASSES . 'order.php');
				
		$order = new order();
				
		$total_weight = $_SESSION['cart']->show_weight();
				
		require(DIR_WS_CLASSES . 'shipping.php');
				
		$shipping = new shipping();
		$init_quote = $shipping->quote($shipping_method,$shipping_method);
			exit('{"cost":"'.$currencies->new_value($init_quote[$shipping_method]['methods'][0]['cost']+$_POST['week_price']).'"}');
	}
}