<?php

require 'includes/application_top.php';
if($_GET['request_type']=="setCart"){

isset($_POST['product_id'])?$_POST['product_id']:false;

    if($_POST['product_id']!==false) {
        $remove_product = $_SESSION['cart']->remove_cart_product($_SESSION['customer_id'], $_POST['product_id']);
        if ($_SESSION['cart']->count_contents()&&$remove_product==1) {
            $num = 0;
            $str = "";
            $productsInfo = $_SESSION['cart']->get_products();
            $total_price = 0;$more_cart=false;
            $currencies_value = zen_get_currencies_value_of_code($_SESSION['currency']);
            foreach ($productsInfo as $i => $product) {
                $num++;
                $productsPriceDiscount= get_products_final_price_of_discount($product['products_price'],$product['id'],$product['quantity']);
                if(get_products_have_discount_of_categories((int)$product['id'])){
                    $total_price += round($product['final_price']*$currencies_value,2)*$product['quantity'];
                    $productsPriceEach =  $currencies->display_price($product['final_price'], zen_get_tax_rate($product['tax_class_id']), 1) . ($product['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($product['onetime_charges'], zen_get_tax_rate($product['tax_class_id']), 1) : '');
                }
                else{
                    $productsPriceEach =  $currencies->display_price($productsPriceDiscount, zen_get_tax_rate($product['tax_class_id']), 1) . ($product['onetime_charges'] != 0 ? '<br />' . $currencies->display_price($product['onetime_charges'], zen_get_tax_rate($product['tax_class_id']), 1) : '');
                }
                $link = zen_href_link(FILENAME_PRODUCT_INFO,'&products_id='.(int)$product['id']);
                //$name = substr($product['name'],0,41)."...";
                $name = $product['name'];
                $image_src = DIR_WS_IMAGES.(file_exists(DIR_WS_IMAGES.$product['image']) ? $product['image'] : 'no_picture.gif');
                $image = zen_image($image_src,$name,100,100,' border="0" title="'.$name.'"');
                $price = $currencies->display_price($product['price'], 0);
                $cart_price = $product['products_price'];
                $quantity = $product['quantity'];
                if($num > 4){
                    $cart_products_list_html .='';
                    $more_cart = true;
                }else{
                    $cart_products_list_html .= '<li id='.(int)$product['id'].'><a class="cart_image" href="'.$link.'">'.$image.' </a><p class="cart_name_pre"><a class="cart_name" href="'.$link.'">'.$name.'</a><b>'.$productsPriceEach.' * '.$quantity.'</b></p>
						<a class="cart_remove" href="javascript:remove_shopping_cart(\''.$product['id'].'\','.$quantity.','.$cart_price.');">'.FIBERSTORE_REMOVE.'</a>
						</li>';
                }

                if (count($productsInfo) < 1) {
                    $null = "<b class=no_add_cart>Your Shopping Cart is Empty.</b>";
                    echo $null;
                    die;
                }
            }
            if ($more_cart) {
                $cart_products_list_html .= '<a class="cart_more_21" href="'.zen_href_link(FILENAME_SHOPPING_CART).'"><b>'.FIBERSTORE_VIEW_MORE.'</b></a>';
            }

            $cart_items = $_SESSION['cart']->count_contents();

            if($cart_items==1){  $items =  F_BODY_HEADER_ITEM;}
            if($cart_items>=2 && $cart_items<=4 ){ $items =  F_BODY_HEADER_ITEM_TWO;}
            if($cart_items==0 || $cart_items>=5){ $items =  F_BODY_HEADER_ITEMS; }
            $cart_products_list_html .= '
			  					<div><b>'.$cart_items.' '.$items.'</b>, <b id="total_price">'.$currencies->display_price($total_price/$currencies_value,0).'</b>  <font style=" font-size:1em; color:#666">—</font>  <a class="top_edit_order" href="'.zen_href_link(FILENAME_SHOPPING_CART).'">'.FIBERSTORE_EDITE_ORDER.'</a> <br>
			                        <a class="button_04" href="'.zen_href_link('paypal_express.php', 'type=ec', 'SSL', true, true, true).'">'.FS_BUY_WITH.' &nbsp;<img src="/images/shopping_ec_paypal.png" alt="FiberStore shopping_ec_paypal.png" title="Paypal"></a>
			                         <a class="button_02" href="'.zen_href_link(FILENAME_CHECKOUT,'','SSL').'">'.FIBERSTORE_CHECK_YOU_ORDER.'<i class="security_icon"></i></a>
			                    </div>';
        } else {
            $cart_products_list_html .= '<b class="no_add_cart">' . FIBERSTORE_SHOPPING_HELP . '</b>';
        }
        echo $cart_products_list_html;
        die;
    }
}
?>