<?php
require 'includes/application_top.php';
$currencies_value = zen_get_currencies_value_of_code($_SESSION['currency']);
if($_GET['type'] == 'custom'){
	$products_id = $_POST['products_id'];
	$custom_length = $_POST['custom_length'];
	if($products_id){
		if(is_numeric($custom_length) && $custom_length>0){
			$list = $db->getAll("select * from  products_count_length where products_id = '".$_POST['products_id']."' limit 1");
			if($list){
				$key = get_product_category_key($_POST['products_id']);
				$retail = get_retail_status($_POST['products_id']);
				if(($_POST['custom_length'])>1){
					if($key==0){
						/*
						if($_POST['custom_length']>1 && $_POST['custom_length']<=2){
							$length_price = 0.1;
						}elseif($_POST['custom_length']>2 && $_POST['custom_length']<=3){
							$length_price = 0.3;
						}else{
							$length_price = 0.3+($_POST['custom_length']-3)*$list[0]['unit_price'];
						}*/
						$length_price = ($_POST['custom_length']-1)*$list[0]['unit_price'];
				    }elseif($key==1){
						if($retail == 1){
							$length_price = ($_POST['custom_length']-1)*$list[0]['unit_price'];
						}else{
							$length_price = ($_POST['custom_length']*1000-1)*$list[0]['unit_price'];
						}
					}elseif($key==2){
						$length_price = ($_POST['custom_length']*1000-1)*$list[0]['unit_price'];
					}elseif($key==4){
						if($_POST['custom_length']-10<0){
							$length_price = 0;
						}else{
							$length_price = ($_POST['custom_length']-10)*$list[0]['unit_price'];
						}
					}elseif($key==5){
						if($_POST['custom_length']-100<0){
							$length_price = 0;
						}else{
							$length_price = ($_POST['custom_length']-100)*$list[0]['unit_price'];
						}
					}else{
						$length_price = ($_POST['custom_length']-1)*$list[0]['unit_price'];
					}
					$total_price = ($length_price*$currencies_value+get_products_all_currency_final_price(zen_get_products_base_price((int)$products_id)*$currencies_value))/$currencies_value;
					echo json_encode(array('type'=>'1','length_price'=>"(+".$currencies->format($length_price).")",'totle_price'=>$currencies->format($total_price)));
				}else{
					//$length_price = (3-$_POST['custom_length'])*$list[0]['unit_price'];
					if($key==2){
						$length_price = (1000-1)*$list[0]['unit_price'];
					}else{
						if($key==1 && $retail == 0){
							$length_price = (1000-1)*$list[0]['unit_price'];
						}else{
							$length_price = 0;
						}
					}
					
					$total_price = ($length_price*$currencies_value+get_products_all_currency_final_price(zen_get_products_base_price((int)$products_id)*$currencies_value))/$currencies_value;
					echo json_encode(array('type'=>'1','length_price'=>"(+".$currencies->format($length_price).")",'totle_price'=>$currencies->format($total_price)));

				}
			}
		}else{
			echo json_encode(array('type'=>'-1'));
		}
	}else{
		echo json_encode(array('type'=>'-2'));
	}
}elseif($_GET['type'] == 'length_update'){
	$products_id = (int)$_POST['products_id'];
	$length = (int)$_POST['length'];
	if($products_id>0 && $length>0){
		$list = $db->getAll("select * from products_length where id = '".$length."' and product_id = '".$products_id."' limit 1");
		if($list){
			//$new_price = $currencies->format(get_products_all_currency_final_price(zen_get_products_base_price((int)$products_id))+$list[0]['length_price']);
			$new_price = $currencies->format(($list[0]['length_price']*$currencies_value+get_products_all_currency_final_price(zen_get_products_base_price((int)$products_id)*$currencies_value))/$currencies_value);

            $NowInstockQTY = zen_get_products_instock_total_qty_of_products_id($products_id,$list[0]['length']);

			//echo $new_price.'#'.$NowInstockQTY;
			echo $new_price;
		}else{
			echo "err";
		}
	}else{
		echo "err";
	}
}
?>