<?php
class shopping_cart_help {
    function shopping_cart_help(){
        $this->cart = $_SESSION['cart'];
    }
    /**
     * @todo show block of current cart products list info
     */
    function show_cart_products_block(){
        global $currencies;
        $cart_products_list_html = '<dd id="shopping_cart">';
        if($this->cart->count_contents()){
            $cart_products = $this->cart->get_products() ;
            $num =0;
            $total_price = 0;$more_cart=false;
            $currencies_value = zen_get_currencies_value_of_code($_SESSION['currency']);
            foreach ($cart_products as $i => $product){
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
            }
            if ($more_cart) {
                $cart_products_list_html .= '<a class="cart_more_21" href="'.zen_href_link(FILENAME_SHOPPING_CART).'"><b>'.FIBERSTORE_VIEW_MORE.'</b></a>';
            }

            unset($_SESSION['paypal_ec_temp']);
            unset($_SESSION['paypal_ec_token']);
            unset($_SESSION['paypal_ec_payer_id']);
            unset($_SESSION['paypal_ec_payer_info']);

            $cart_items = $_SESSION['cart']->count_contents();

            if($cart_items==1){  $items =  F_BODY_HEADER_ITEM;}
            if($cart_items>=2 && $cart_items<=4 ){ $items =  F_BODY_HEADER_ITEM_TWO; }
            if($cart_items==0 || $cart_items>=5){ $items =  F_BODY_HEADER_ITEMS; }
            $cart_products_list_html .= '
			  					<div><b>'.$cart_items.' '.$items.'</b>, <b id="total_price">'.$currencies->display_price($total_price/$currencies_value,0).'</b>  <font style=" font-size:1em; color:#666">—</font>  <a class="top_edit_order" href="'.zen_href_link(FILENAME_SHOPPING_CART).'">'.FIBERSTORE_EDITE_ORDER.'</a> <br>
			                        <a class="button_04" href="'.zen_href_link('paypal_express.php', 'type=ec', 'SSL', true, true, true).'">'.FS_BUY_WITH.' &nbsp;<img src="/images/shopping_ec_paypal.png" alt="FiberStore shopping_ec_paypal.png" title="Paypal"></a>
			                         <a class="button_02" href="'.zen_href_link(FILENAME_CHECKOUT,'','SSL').'&get_all=ture">'.FIBERSTORE_CHECK_YOU_ORDER.'<i class="security_icon"></i></a>
			                    </div>';
        }else{
            $cart_products_list_html .= '<b class="no_add_cart">'.FIBERSTORE_SHOPPING_HELP.'</b>';
        }
        $cart_products_list_html .= '</dd>';
        return $cart_products_list_html;
    }

    function show_add_to_cart_block(){
        global $currencies;
        $cart_products_list_html = '<dd id="shopping_cart"><ul class="add_cart_03">';
        if($this->cart->count_contents()){
            $cart_products = $this->cart->get_products() ;
            $num =0;
            foreach ($cart_products as $i => $product){
                $link = zen_href_link(FILENAME_PRODUCT_INFO,'&products_id='.(int)$product['id']);
                $name = substr($product['name'],0,70)."...";
                $image_src = DIR_WS_IMAGES.(file_exists(DIR_WS_IMAGES.$product['image']) ? $product['image'] : 'no_picture.gif');
                $image = zen_image($image_src,$name,100,100,' border="0" title="'.$name.'"');
                $price = $currencies->display_price($product['price'], 0);
                $cart_price = $product['price'];
                $quantity = $product['quantity'];
                $cart_products_list_html .= '<li id="cart_'.(int)$product['id'].'"><a class="add_cart_04" href="'.$link.'">'.$name.'</a><b>'.$price.' * '.$quantity.'</b>
						<a class="add_cart_05" href="javascript:remove_shopping_cart(\''.$product['id'].'\','.$quantity.','.$cart_price.');">'.FIBERSTORE_REMOVE.'</a>
						</li>';
            }
            $cart_products_list_html .= '</ul>
				<div class="add_cart_06">'.FIBERSTORE_CART_TOTAL.'('.$cart_items = $_SESSION['cart']->count_contents().FS_ITEMS.')
				<b id="total_price">'.$currencies->display_price($_SESSION['cart']->show_total(),0).'</b>
			    <a class="button_02" href="'.zen_href_link(FILENAME_CHECKOUT).'">'.FS_PROCEED_TO_CHECKOUT.'</a>
			    <div class="ccc"></div></div>';

        }else $cart_products_list_html .= '<b class="no_add_cart">'.FIBERSTORE_SHOPPING_HELP.'</b>';
        return $cart_products_list_html;
    }
}

?>

<script type="text/javascript">
    function remove_shopping_cart(products_id,products_num,price){
        var number = parseInt($('.header_cart_href em').text()) - products_num;
        if(number<0){
            number=0;
        }
        $.ajax({
            url:  "ajax_shopping_cart.php?request_type=setCart",
            data: {product_id:products_id},
            type: "POST",
            dataType:'html',
            success: function(msg){
                if(msg){
                    $('#shopping_cart').html(msg);
                    $('.header_cart_href em').text(number);
                    $(".m_cart").find(".icon").find("i").text(number);
                }
            },error: function(XMLHttpRequest,msg){alert('Sorry, try again please !');}
        });

    }
</script>