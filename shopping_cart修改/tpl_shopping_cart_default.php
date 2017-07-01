<!--  
<div class="login_new_01"><a href="<?php echo zen_href_link(FILENAME_DEFAULT,'','NONSSL')?>"><img src="../images/logo_fs_01.gif" border="0" class="aaa"></a><span class="login_new_02">Shopping Cart</span>
<?php
$FsCustomRelate = new classes\custom\FsCustomRelate();
$currency_symbol_left =  $currencies->currencies[$_SESSION['currency']]['symbol_left'];?>
<?php if (sizeof($productArray)){?>
	<div class="header_04 bbb">
    <table width="230" cellspacing="0" cellpadding="0" border="0" class="pay_lc_two">
      <tbody><tr>
        <td class="pay_liucheng" colspan="3"></td>
      </tr>
      <tr>
        <td width="20%"><div align="left" class="pay_lc_01">&nbsp;Cart</div></td>
        <td width="25%"><div align="center" class="pay_lc_03">&nbsp;&nbsp;&nbsp; Checkout</div></td>
        <td width="25%"><div align="right" class="pay_lc_04">Success</div></td>
      </tr>
    </tbody></table>
  </div>
  <?php } ?>
</div>
<style type="text/css">
.shopping_cart_03 span { color:#999; text-decoration:line-through; font-weight:normal; }
</style>
-->
<!--  春节提示
  <div class="spring_festival">
  <span><b>Happy New Year!</b></span>
  <p>Products from our global warehouse will be delayed due to our holiday (Jan. 23th - Feb. 2nd), but products from our our Seattle warehouse can be available with same-day delivery.<br />
  <em>FS.COM appreciate for your understanding and wish you a happy new year!</em></p>
</div>

-->
<?php if (sizeof($productArray)){?>

    <div class="cart_con_top">
        <div class="title_large aaa"><?php echo BOX_HEADING_SHOPPING_CART;?></div>
        <?php /*?><div class="cart_checkout_btn m_none">
    <!--<span class="shopping_cart_06"> <a id="continue_shop" href="<?php echo HTTP_SERVER;?>" class="button_11"><?php echo FS_CART_CONTINUE;?></a></span>-->
          <?php  // the tpl_ec_button template only displays EC option if cart contents >0 and value >0
if (defined('MODULE_PAYMENT_PAYPALWPP_STATUS') && MODULE_PAYMENT_PAYPALWPP_STATUS == 'True'){
  include(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/paypal/tpl_ec_button.php');
}
?>
    <?php if($check_status){   ?>
    <button type="button" class="contact_button_01" id="checkout_top" disabled value="Proceed to Checkout"><?php echo FS_CART_PROCESSING;?></button>
    <?php  }else{ ?>
    <?php  if($customer_records==0){ ?>
    <button type="button" class="button_02 bbb" id="checkout_checkout" onClick="location.href='<?php echo zen_href_link(address_book_edit,'','SSL');?>'" value="Proceed to Checkout">
    <?php echo FS_CART_CHECKOUT;?><i class="security_icon"></i>
    <?php }else{ ?>
    <button type="button" class="button_02 bbb" id="checkout_checkout" onClick="location.href='<?php echo zen_href_link(FILENAME_CHECKOUT,'','SSL');?>'" value="Proceed to Checkout"><?php echo FS_CART_CHECKOUT;?><i class="security_icon"></i>
    <?php }?>
    <?php }?>
    </button>
  </div><?php */?>
        <div class="ccc"></div>
        <div class="cart_shopping_icon"><input type="hidden" name="shopping_list" value='<?php echo $list_str;?>'>
            <a href="<?php echo zen_href_link(FILENAME_SAVE_SHOPPING_LIST,'&type=save&list='.$list_str);?>" id="save_list" class="cart_save"><span class="icon iconfont">&#xf040;</span> <?php echo FS_SAVE;?>
                <div class="cart_friend"><em><i></i></em><?php echo FS_SAVE_MESSAGE;?>.</div></a>
            <a href="<?php echo zen_href_link(FILENAME_SAVE_SHOPPING_LIST,'&type=share&list='.$list_str);?>" id="share_list" class="cart_share"><span class="icon iconfont">&#xf045;</span> <?php echo FS_SHOPPING_SHARE;?>
                <div class="cart_friend"><em><i></i></em><?php echo FS_SHARE_MESSAGE;?>.</div></a> <!--<?php echo FS_OR;?>-->
            <a href="<?php echo zen_href_link(FILENAME_PRINT_SHOPPING_LIST,'&list='.$list_str);?>"  class="cart_print"><span class="icon iconfont">&#xf100;</span> <?php echo FS_PRINT;?>
                <div class="cart_friend"><em><i></i></em><?php echo FS_PRINT_MESSAGE;?>.</div></a> <!--<?php echo FS_THIS;?>.--></div>
        <!-- <div class="title_small"><a href="#"><b>Share</b></a> this cart with a friend.</div> -->
        <?php if (sizeof($productArray)){?>
            <form name="cart_form" id="cart_form" action="<?php echo zen_href_link(FILENAME_SHOPPING_CART,'&action=update_product');?>" method="post">


                <div class="shopping_cart">

                    <div class="shopping_cart_pro_tit">
                        <div class="shopping_cp01">&nbsp;&nbsp;</div>
                        <div class="shopping_cp02"><b><?php echo FS_CART_YOUR_ITEM;?></b></div>
                        <div class="shopping_cp03 text_center"><b><?php echo FS_CART_PRICE;?></b></div>
                        <div class="shopping_cp04 text_center"><b><?php echo FS_CART_QTY;?></b></div>
                        <div class="shopping_cp05 text_center"><b><?php echo FS_CART_WEIGHT;?></b></div>
                        <div class="shopping_cp06 text_right"><b><?php echo FS_CART_TOTAL;?></b></div>
                    </div>

                    <?php   if($check_status){   ?>
                        <div class="shopping_cart_stock"><!-- <span class="products_in_stock">Unvailable </span> -->
                            &nbsp;&nbsp;<?php echo FS_CART_MOQ;?>
                        </div>
                    <?php	} ?>

                    <?php foreach ($productArray as $i => $product){
                        $img_src=  DIR_WS_IMAGES. (file_exists(DIR_WS_IMAGES.$product['productImageSrc']) ? $product['productImageSrc'] : 'no_picture.gif');
                        $image = zen_image($img_src,'',200,200,' border="0" ');
                        $link = zen_href_link(FILENAME_PRODUCT_INFO, '&products_id='.intval($product['id']),'NONSSL');
                        $min_order_qty = zen_get_products_min_order_by_productsid(intval($product['id']));


                        // 如果购买的数量大于  库存  显示提示语
                        echo zen_get_products_instock_total_qty_is_show_message($product['id'],(int)$product['quantity'],$product['attributes'] );

                        ?>


                        <div class="shopping_cart_pro_con">
                            <div class="shopping_cp01 shopping_cart_02"><a href="<?php echo $link;?>"><?php echo $image;?></a>
                                <?php if (sizeof($productArray) >= 1){?>
                                    <?php $product_is_id=$product['id']; $product_is_price = $product['productsPrice']; $product_is_qty = $_SESSION['cart']->contents[$product_is_id]['qty'];  ?>
                                    <input class="checkbox-id" id='pic_pro_<?php  echo $product['id'];?>' qty="<?php  echo $product_is_qty;?>"  rel="<?php echo  $product_is_price;?>" type="checkbox"  value="<?php echo $product['id']?>" name="products[]">
                                <?php }?></div>
                            <div class="shopping_cp02 shopping_cart_02"><a href="<?php echo $link;?>"><?php echo $product['productsName'];?></a>
                                <?php
                                echo $product['attributeHiddenField'];
                                if (isset($product['attributes']) && is_array($product['attributes'])) {
                                    echo '<div class="cartAttribsList">';
                                    echo '<ul>';
                                    reset($product['attributes']);
                                    $Length=$Attr='';
                                    foreach ($product['attributes'] as $option => $value) {
                                        if($option == 'length'){ $Length = $value['length'];
                                            ?>
                                            <li><?php echo $value['length'] ?>
                                                <?php
                                                echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$value['price_prefix'].$currencies->display_price($value['length_price'],0,1);
                                                ?>
                                            </li>
                                            <?php
                                        }else{  $Attr[] = $value['options_values_id']; ?>
                                            <li><?php echo $value['products_options_name'] . TEXT_OPTION_DIVIDER . nl2br($value['products_options_values_name']); ?>
                                                <?php
                                                if($value['options_values_price'] > 0){
                                                    echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$value['price_prefix'].$currencies->display_price($value['options_values_price'],0,1);
                                                }
                                                ?>
                                            </li>
                                        <?php }
                                    }
                                    echo '</ul>';
                                    echo '</div>';
                                }
                                ?>
                                <div class="shopping_cart_sku"><span class="product_sku">#<span><?php echo (int)$product['id'];?></span></span> <span class="products_in_stock"></span>
                                    <?php
                                    $ProductsID=$product['id'];
                                    if(is_array($Attr)&&sizeof($Attr)){
                                        $FsCustomRelate::$products_id = $product['id'];
                                        $FsCustomRelate::$optionAttr = $Attr;
                                        $FsCustomRelate::$length = $Length;
                                        $matchProducts = $FsCustomRelate->handle();
                                        if($matchProducts){
                                            $ProductsID = $matchProducts[0];
                                        }
                                    }
                                    $NowInstockQTY = zen_get_products_instock_total_qty_of_products_id((int)$ProductsID);
                                    if($NowInstockQTY!=='Available'){
                                        echo '
		<meta itemprop="availability" content="http://schema.org/InStock" />
		<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
		<span class="products_in_stock">'.$NowInstockQTY.','.'</span> '.zen_get_products_instock_shipping_date_of_products_id((int)$product['id'],$NowInstockQTY,$countries_code_2).

                                            '<div class="track_orders_wenhao">
		<div class="question_bg"></div>
		 <div class="question_text_01 leftjt"><div class="arrow"></div>
			<div class="popover-content">';

                                        echo FS_THEA_CTUAL_SHIPPING_TIME;

                                        echo	'</div>
		 </div>
      </div>'
                                        ;

                                    }else{
                                        echo '
		<meta itemprop="availability" content="http://schema.org/InStock" />
		<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
		<span class="products_in_stock">'.$NowInstockQTY.','.'</span> '.zen_get_products_instock_shipping_date_of_products_id((int)$product['id'],$NowInstockQTY,$countries_code_2).
                                            '<div class="track_orders_wenhao">
		<div class="question_bg"></div>
		 <div class="question_text_01 leftjt"><div class="arrow"></div>
			<div class="popover-content">';

                                        if($deliver_time == 1){
                                            $shipping_html= FS_CART_SHIPPING_HTML1;
                                        }else if($deliver_time == 2){
                                            $shipping_html= FS_CART_SHIPPING_HTML2;
                                        }else{
                                            $shipping_html= FS_CART_SHIPPING_HTML;
                                        }
                                        echo $shipping_html;

                                        echo	'</div>
		 </div>
      </div>'
                                        ;
                                    }
                                    ?>
                                </div>
                                <!--移动端文件 mark-->
                                <?php /*?><div class="shopping_cart_03 m_price">
            <?php // if(get_products_discount_products_id($product['id'])){?>
          <?php
				  if($_SESSION['member_level'] >1 && $currencies->display_price_rate(zen_round(($product['products_price']*$order->info['currency_value']),2),0,1) != $product['productsPriceEach']){
				  echo '<span>'.$currencies->display_price_rate(zen_round(($product['products_price']*$order->info['currency_value']),2),0,1).'</span>';
				   }
                   //$shopping_total += $product['products_price_total'];
				   echo $product['productsPriceEach'];?>
          </div>

          <div class="cart_basket_btn m_qty">

            Qty:
            <?php echo $product['quantity'];?>


            <a class="remove_cart" href="<?php echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_product&product_id='.$product['id']);?>" href="javascript:void(0)"><i></i></a> </div><?php */?>

                                <!--移动端文件结束-->

                            </div>
                            <div class="shopping_cp03 shopping_cart_03 text_center"  >
                                <div class="shopping_cp_cell"><?php // if(get_products_discount_products_id($product['id'])){?>
                                    <?php
                                    if($_SESSION['member_level'] >1 && $currencies->display_price_rate(zen_round(($product['products_price']*$order->info['currency_value']),2),0,1) != $product['productsPriceEach']){
                                        echo '<span>'.$currencies->display_price_rate(zen_round(($product['products_price']*$order->info['currency_value']),2),0,1).'</span><br/>';
                                    }
                                    $shopping_total += $product['products_price_total'];
                                    echo $product['productsPriceEach'];?></div></div>

                            <div  class="shopping_cp04 text_center">
                                <div class="shopping_cp_cell">
                                    <div class="cart_basket_btn">
                                        <?php /*?><a class="cart_qty_reduce"  <?php  $min_order = zen_get_products_min_order_by_productsid(intval($product['id']));
        if(isset($min_order) && $min_order >= 2) {}else{?> href="javascript:change_cart_num('<?php echo $product['id'];?>',0)" <?php }?> > </a><?php */?>
                                        <?php
                                        if ($product['flagShowFixedQuantity']) {
                                            //   echo $product['showFixedQuantityAmount'];         之前设置了最大购买数量 ，现已取消   melo
                                            echo $product['quantityField'];
                                        } else {
                                            echo $product['quantityField'];
                                        }
                                        ?>

                                        <div class="pro_mun">
                                            <a class="cart_qty_add"  href="javascript:change_cart_num('<?php echo $product['id'];?>',1);"> </a>
                                            <a class="cart_qty_reduce"  <?php  $min_order = zen_get_products_min_order_by_productsid(intval($product['id']));
                                            if(isset($min_order) && $min_order >= 2) {}else{?> href="javascript:change_cart_num('<?php echo $product['id'];?>',0)" <?php }?> > </a>
                                        </div>


                                        <?php /*?><a class="cart_qty_add"  href="javascript:change_cart_num('<?php echo $product['id'];?>',1);"> </a><?php */?>
                                        <a class="remove_cart" href="<?php echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_product&product_id='.$product['id']);?>" href="javascript:void(0)"><i></i></a> </div>
                                    <div class="ccc"></div>
                                    <div style="display: none"> <a id="update" href="javascript:void(0);" onClick="javascript: $('#cart_form').submit();"><?php ECHO FIBERSTORE_CART_ACTUALIZAR;?></a>
                                        <?php //echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_product&product_id='.$product['id']);?>
                                        <a class="remove_cart" href="<?php //echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_product&product_id='.$product['id']);?>">
                                            <?php //ECHO FIBERSTORE_CART_BORRAR;?>
                                        </a> </div></div>
                            </div>
                            <div class=" shopping_cp05 text_center"><div class="shopping_cp_cell"><?php echo $product['productsWeight'];?></div></div>

                            <div class="shopping_cp06 text_right"><div class="shopping_cp_cell"><?php echo  $product['productsPrice'];?></div></div>
                            <?php /*?><td><span class="remove_shopping_cart"><a id="remove_cart_<?php echo $product['id'];?>" class="remove_cart" href="<?php echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_product&product_id='.$product['id']);?>">X</a></span></td><?php */?>
                        </div>
                    <?php }?>
                </div>
            </form>
            <!-- ** BEGIN PAYPAL EXPRESS CHECKOUT ** -->
            <!-- ** END PAYPAL EXPRESS CHECKOUT ** -->
        <?php }else{?>
            <p style="text-align: center; color: #C50001; font-size: 14px; padding: 25px;font-weight: bold;"><?php echo FS_CART_EMPTY;?> </p>
        <?php }?>
        <!-- products price total  2014-11-24 -->
        <?php
        //$cart_price_total = $currencies->fs_format($shopping_total, true, $order->info['currency'], $order->info['currency_value']);
        // if($order_total_lists[1]['value']){
        // $ship_text = $order_total_lists[1]['text'];
        //  }else{
        $ship_text = $shipping_price;
        //   }

        if($order->info['currency'] == 'BRL'){
            $value1 = explode('R$',$ship_text);
            $value0 = explode('R$',$order_total_lists[0]['text']);
        }
        else if($order->info['currency'] == 'NOK' || $order->info['currency'] == 'DKK'){
            $value1 = explode('kr.',$ship_text);
            $value0 = explode('kr.',$order_total_lists[0]['text']);
        }else if($order->info['currency'] == 'SEK'){
            $value1 = explode('Kr.',$ship_text);
            $value0 = explode('Kr.',$order_total_lists[0]['text']);
        }
        else if($order->info['currency'] == 'MXN'){
            $value1 = explode('$',$ship_text);
            $value0 = explode('$',$order_total_lists[0]['text']);
        }
        else{
            $value1 = explode(';',$ship_text);
            $value0 = explode(';',$order_total_lists[0]['text']);
        }
        $value1 = str_replace(array(","), '', $value1);
        $value0 = str_replace(array(","), '', $value0);
        $value_total =end($value1) + end($value0) ;

        $cart_final_price = $currencies->fs_format($value_total/$order->info['currency_value'], true, $order->info['currency'], $order->info['currency_value']);

        if(($_SESSION['member_level']>1) && ($shopping_total - $order_total_lists[0]['value'] > 0) ){
            $off =  $currencies->fs_format(($shopping_total - $order_total_lists[0]['value']), true, $order->info['currency'], $order->info['currency_value']) ;
            $offers =   '<span><em class="products_in_stock"> <i class="business_icon"></i>'.FS_CART_BUSINESS.' </em> &nbsp; <span class="special_price bbb">'.FS_CART_SAVE.'&nbsp;&nbsp;-'.$currency_symbol_left.$off.'</span></span>';
            // if($order_total_lists[1]['value'] >0){
        }
        ?>
        <div class="shopping_cart_help">
            <?php if (sizeof($productArray) >= 1){?>
                <div class="shopping_cart_04_01">
                    <div class="shopping_cart_04_01_delete">
                        <label>
                            <input type="checkbox" id="checkbox_pro" name="chkAll">
                            <?php echo FS_CART_ALL;?></label>
                    </div>
                    <span class="wishlist_03 wishlist_04"><a href="javascript:;" class="" id="all_delete"><?php echo FS_CART_DELETE;?></a></span> </div>
            <?php }?>
            <div class="title_small"><?php echo FS_CART_HELP;?> <a href="<?php echo zen_href_link(FILENAME_LIVE_CHAT_SERVICE);?>"><?php echo FS_CART_CHAT;?></a></div>
            <!-- <div class="shopping_cart_star"><span class="p_star01"></span><img title="" alt="" src="/images/cart_google_icon.jpg"></div>-->
        </div>
        <!-- end  -->
        <div class="checkout_offset">
            <?php if($offers){ ?>
                <div class="checkout_price">
                    <ul>
                        <li>
                            <?php

                            echo $offers;
                            ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <div class="checkout_price" id="cart_total_info">
                <ul>
                    <li class="shopping_cart_04_03"><span id="true_price" style="display: none" class="bbb"><span id="coin"><?php echo $currency_symbol_left;?> 0.00</span></span>
	    <span class="bbb" id="all_money" style="display:block;"><?php echo $currency_symbol_left;?><?php echo
            $currencies->fs_format($order_total_lists[0]['value'], true, $order->info['currency'], $order->info['currency_value']);
            ?>
                </span>

                        <?php $cart_items = $_SESSION['cart']->count_contents();
                        if($cart_items >1){
                            echo '<span>'.FS_CART_CART_TOTAL.'</span>'.FS_CART_ITEMS.':';
                        }else{
                            echo '<span>'.FS_CART_CART_TOTAL.'</span>'.FS_CART_ITEM.':';
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <!-- shipping info -->
            <?php
            //print_r($order_total_lists);
            if($order_total_lists[1]['value'] >= 0){?>
                <div class="checkout_price" id="cart_shipping_info" style="display:none">
                    <ul>
                        <li class="price_width">

        <span id="shipping_cost">
        <?php
        if($order_total_lists[1]['value'] == 0){
            echo '<span class="text_color_05">Free</span>';
        }else{
            echo $order_total_lists[1]['text'];
        }
        ?>
        </span>

       <span class="aaa"> <?php echo FS_CART_ESTIMATED;?> (
           <?php
           echo  $shipping_choice;?>
           ):

        <div class="track_orders_wenhao">
            <div class="question_bg"></div>
            <div class="question_text_01 leftjt"><div class="arrow"></div>
                <div class="popover-content">
                    <?php if($shipping_choice == 'UPS 2DAYS'){ ?>
                        <b><?php echo FS_CART_HOW;?></b><br />
                        <?php echo FS_CART_YOUR;?>
                    <?php }else{ ?>
                        <?php echo FS_CART_YOU;?>
                    <?php } ?>
                </div>
            </div>
        </div>
        </span>

                        </li>
                    </ul>
                </div>
            <?php } ?>
            <!-- end shipping   2014-11-24  -->
            <div class="shopping_cart_04" id="cart_total_info" style="display:none">
                <dl>
                    <dd class="shopping_cart_04_03"><?php echo $currency_symbol_left;?><?php echo
                        $currencies->fs_format($order_total_lists[2]['value'], true, $order->info['currency'], $order->info['currency_value']);
                        ?></dd>
                    <dd class="shopping_cart_04_02">
                        <?php $cart_items = $_SESSION['cart']->count_contents();
                        if($cart_items >1){
                            echo '<span>'.FS_CART_AMOUNT;
                        }else{
                            echo '<span>'.FS_CART_AMOUNT;
                        }
                        ?>
                    </dd>
                </dl>
            </div>

            <div class="ccc"></div>
            <div class="shopping_cart_05">

                <!-- <div class="aaa"><?php //ECHO FIBERSTORE_REMOVE_ITEMS;?></div> -->
    <span class="shopping_cart_button">
   <?php if($check_status){   ?>
       <button type="button" class="contact_button_01" id="checkout_top" disabled value="Proceed to Checkout"><?php echo FS_CART_PROCESSING;?></button>
   <?php  }else{ ?>
        <?php  if($customer_records==0){ ?>
        <button type="button" class="button_02 " id="checkout"  value="Proceed to Checkout">
            <?php echo FS_CART_CHECKOUT;?><i class="security_icon"></i>
            <?php }else{ ?>
            <button type="button" class="button_02 " id="checkout"  value="Proceed to Checkout"><?php echo FS_CART_CHECKOUT;?><i class="security_icon"></i>
                <?php }?>
                <?php }?>
            </button>
    </span>

                <?php  // the tpl_ec_button template only displays EC option if cart contents >0 and value >0
                if (defined('MODULE_PAYMENT_PAYPALWPP_STATUS') && MODULE_PAYMENT_PAYPALWPP_STATUS == 'True') {
                    include(DIR_FS_CATALOG . DIR_WS_MODULES . 'payment/paypal/tpl_ec_button.php');
                }
                ?>

                <!--<span class="shopping_cart_06 bbb"> <a id="continue_shop" href="<?php echo HTTP_SERVER;?>" class="button_11"><?php echo FS_CART_CONTINUE;?></a></span>-->
                <div class="ccc"></div>
            </div>


        </div>
        <div class="ccc"></div>
    </div>
    <!--  <div class="text_right"><a href="#"><img src="images/pay.gif" border="0"></a></div>-->
    <div class="shopping_cart_methodsicon">
        <ul>
            <li><img alt="FiberStore diners.jpg" src="/images/diners_logo.jpg"></li>
            <li><img alt="FiberStore discover.jpg" src="/images/discover_logo.jpg"></li>
            <li><img alt="FiberStore index_visa01.jpg" src="/images/index_visa01.jpg"></li>
            <li><img src="/images/index_visa02.jpg" alt="FiberStore index_visa02.jpg"></li>
            <li><img src="/images/index_visa03.jpg" alt="FiberStore index_visa03.jpg"></li>
            <li><img src="/images/index_visa04.jpg" alt="FiberStore index_visa04.jpg"></li>
            <li><img src="/images/logo_Paypal.jpg" alt="FiberStore logo_Paypal.jpg" title="Paypal"></li>
            <li><img src="/images/logo_western.jpg" alt="FiberStore logo_western.jpg" title="western"></li>
            <li><img src="/images/logo_Wire_Transfer.jpg" alt="FiberStore logo_Wire_Transfer.jpg" title="Wire Transfer"></li>
        </ul>
    </div>
    </div>
    <?php  //require($template->get_template_dir('tpl_account_right_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/'.'tpl_account_right_default.php');?>
    <?php
    if(isset($_COOKIE['fs_views'])){

        $pIDs = explode("|", $_COOKIE['fs_views']);

        if(is_array($pIDs)&&!empty($pIDs)){

            echo ' <div class="p_hot_01">
	    	<b><span  title="fiberstore views">'.FIBERSTORE_DASHBOARD_HISTORY.'</span></b>
			
			</div>
			<div class="v_show">
			<span class="prev change_btn" >Prev</span>
	  		<div class="v_content recently_viewed">
			
	    	<div class="v_content_list">
			<ul>';
            //zen_href_link(FILENAME_ACCOUNT_HISTORY_INFO,'&orders_id='.$order['orders_id'],'NONSSL');
            foreach($pIDs as $value=>$pID){
                if(zen_get_products_name($pID,$_SESSION['langusges_id']) != null)
                    echo '<li><div class="list_10"><a target="_blank" href="'.zen_href_link(FILENAME_PRODUCT_INFO, '&products_id='.$pID,'NONSSL').'">'.zen_get_fs_products_image($pID,150,150).'</a></div><span><a target="_blank" href="'.zen_href_link(FILENAME_PRODUCT_INFO, '&products_id='.$pID,'NONSSL').'">'.zen_get_products_name($pID,$_SESSION['langusges_id']).'</a></span>'.'<p>'.$currencies->new_display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$pID)),0).'</p><div class="recently_viewed_btn">
		   <input type="submit" id="prod_add_'.(int)$pID.'" onclick="prodAddToCart('.(int)$pID.')" value="'.FS_CART_ADD_TO_CART.'" name="Add" class="button_02 button_10"></div></li>';
            }

            echo '</ul>
			</div>
			
			</div>
			<span class="next change_btn">Next</span>
			</div>';
        }
    }
    ?>
    <br class="ccc">
    <div id="overlayer" class="ui-widget-overlay" style="display:none;"></div>
    <div id="fs_loading"  style="display:none;"  class="processing">
        <div class="processing_sub">
            <img src="/images/loading.gif" />
            <div class="loader">
                <div class="loader-inner line-scale">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelector('main').className += 'loaded';
                });
            </script>
        </div>
        <?php echo FS_CART_PROCESSING;?></div>
<?php }else { ?>
    <div class="cart_no">
        <!--      <div class="login_title">Currently in your Shopping Cart</div>
      -->
        <dl>
            <dt><img src="/images/cart_no.png"></dt>
            <dd><?php echo FS_CART_EMPTY;?> </dd>
            <dd> <a href="<?php echo HTTP_SERVER;?>">
                    <input  class="button_02" type="button" value="<?php echo FS_CART_CONTINUE;?>">
                </a> </dd>
        </dl>
    </div>
    <div class="ccc"></div>
<?php } ?>
<!--
<div class="login_footer">
  <div class="login_new_09">Copyright © 2002-<?php echo date('Y',time());?> FS.COM  All Rights Reserved.</div>
  <img class="bbb" src="../images/footer_logo.jpg">
  <div class="ccc"></div>
</div>
-->
<script type="text/javascript">

    $('.checkbox-id').bind("change",function(){
        var oProductId = $(this).attr('id');
        var oProductPre = $(this).attr('rel');
        var oProductQty = $(this).attr('qty');
        var oAllPrice = $('#coin').text();
        oAllPrice =oAllPrice.replace(/[ ]/g,"")
        var oAllPriceInfo  = oAllPrice.match(/([-]?[1-9]((\d{0,2}$)|(\d{0,2}(\,\d{3})*$|((\d{0,2})(\,\d{3})*(\.\d{1,2}$)))))|(^[0](\.\d{1,2})?$)|(^[-][0]\.\d{1,2}$)/g);

        if($(this).attr('checked')){
            var url='ajax_checked_box.php?request_type=checked_up';
        }else{
            var url ='ajax_checked_box.php?request_type=checked_un';
        }
        $.ajax({
            url: url,
            data: {product_id:oProductId,price:oProductPre,oProductPre:oProductQty,number:oAllPriceInfo},
            type: "POST",
            dataType:"html",
            success: function(msg){
                $('#coin').html(msg);
                if(msg=="0"){
                    $('#all_money').show();
                    $('#true_price').hide();
                }else{
                    $('#all_money').hide();
                    $('#true_price').show();
                }
            },error: function(XMLHttpRequest,msg){alert("<?php echo FS_CART_JS_SORRY;?>");}
        });
    });

    $("#checkbox_pro").click(function(){
        if($(this).is(':checked')){
            $('input[name="products[]"]').each(function(){
                $(this).attr('checked','checked');
                $('#coin').text($('#all_money').text());
            });
        }else{
            $('input[name="products[]"]').each(function(){
                $(this).removeAttr('checked');
            });
        }
        if($(this).is(':checked')){
            var status='1';
        }else{
            var status='0';

        }
        $.ajax({
            url: 'ajax_checked_box.php?request_type=status',
            data: {status:status},
            type: "POST",
            dataType:"html",
            success: function(msg){
                if(msg==1){
                    $('#all_money').show();
                    $('#true_price').hide();
                }
            },error: function(XMLHttpRequest,msg){alert("<?php echo FS_CART_JS_SORRY;?>");}
        });
    });
    //PROCEED TO CHECKOUT  click disabled
    $('#checkout').click(function() {
        var input_pro_len = $('input[name="products[]"]').length;
        var products_id = new Array();

        for(var $i = 0;$i<input_pro_len;$i++){
            var if_true = $('input[name="products[]"]').eq($i).is(':checked');
            if(if_true == true){
                var pu_id = $('input[name="products[]"]').eq($i).val();
                products_id.push(pu_id);

            }
        }

        var  products_check_flag = true ;
        $('input[name="products[]"]').each(function(){
            if($(this).is(':checked')) products_check_flag = false ;
        });

//                return false;
        if($('#all_money').css('display') == 'block'){
            $.ajax({
                url: "ajax_shopping_pro_id.php?shopping_pro_id=pro_id",
                type: "POST",
                data: {'products_id':products_id},
                dataType: "json",
                success: function(data){
                    location.href='<?php echo zen_href_link(FILENAME_CHECKOUT,'','SSL');?>&get_all=ture';
                    //alert(data);
                },error:function(data){
                    location.href='<?php echo zen_href_link(FILENAME_CHECKOUT,'','SSL');?>&get_all=ture';
                }
            });
        }else{
            $.ajax({
                url: "ajax_shopping_pro_id.php?shopping_pro_id=pro_id",
                type: "POST",
                data: {'products_id':products_id},
                dataType: "json",
                success: function(data){
                    location.href='<?php echo zen_href_link(FILENAME_CHECKOUT,'','SSL');?>';
                    //alert(data);
                },error:function(data){
                    location.href='<?php echo zen_href_link(FILENAME_CHECKOUT,'','SSL');?>';
                }
            });
        }
    });

    //continue to shopping ******
    var his_href = '<?php echo $_SERVER['HTTP_HOST']?>';
    if (document.referrer == his_href || document.referrer != ""){
        $("#continue_shop").attr("href","javascript: history.go(-1)");

    }else $("#continue_shop").attr("href",'/');

    function defaultShippings(shipping_method,shipping_code){

        $.ajax({
            url: "ajax_process_other_requests.php?request_type=display_shipping",
            type: "POST",
            data: "&securityToken=<?php echo $_SESSION['securityToken'];?>&shipping=" + shipping_method+"&shipping_code=" + shipping_code,
            dataType: "json",
            beforeSend: function(){
            },
            success: function(data){
                //$("#shipping_cost").text(parseFloat(data.cost).toFixed(2));
                $("#total_fee").text((parseFloat(order_totals.subtotal) + parseFloat(data.cost)).toFixed(2));
                order_totals.shipping = data.cost;
            }
        });
    }

    setTimeout("defaultShippings('<?php echo $_SESSION['default_choice'];?>','<?php echo $_SESSION['_choice'];?>')",200);



    $('input[name="products[]"]').click(function(){
        $("#checkbox_pro").removeAttr('checked');
    });

    $('#all_delete').click(function(){
        var products = new Array(),$i=0;
        $("input[name='products[]']").each(function(){
            if($(this).is(':checked')){
                products[$i] = this.value;
                $i++;
            }
        });

        if(products.length > 0){
            if($('.question').remove() > 0)
                $('.question').remove();
            var div = $('<div class="question"></div>');
            var del = "<?php echo FS_CART_JS_DELETE?>";
            var yes = "<?php echo FS_CART_JS_YES?>";
            var no = "<?php echo FS_CART_JS_NO?>";
            div.css({'left':$(this).offset().left + $(this).width()/4 - 110,'top':$(this).offset().top+$(this).height()- 5}).html('<b></b><div class="question_01">'+del+' <br/> <span class="yes">'+yes+'</span><span class="cancel">'+no+'</span></div>').appendTo('body').show();

            $('.question').animate({opacity: 1}, 300);
            $('.yes').live('click', function(){
                var form = $('<form method="post" action="<?php echo zen_href_link(FILENAME_SHOPPING_CART,'&action=remove_all','NONSSL');?>" ></form>');
                for($i=0,$n = products.length; $i < $n; $i++){
                    form.append('<input name="products[]" value="'+products[$i]+'" />');
                }
                form.appendTo('body').submit();
            });
            $('.cancel').live('click', function(){
                $(this).parents('div.question').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }
    });




    function check_min_qty(evt){
        var min_qty=parseInt($(evt).siblings('input[name="product_min_qty"]').val());
        var qty = $(evt).val();
        if(qty<min_qty){
            $(evt).val(min_qty);
        }
    }

    function fslocking() {
        if($('bigbox').siblings(":contains('#overlayer')").length) {
            $('bigbox').siblings('#overlayer').show();
        }else
        //$('#overlayer').prependTo('html,body').show();
            $('#overlayer').show();
        if($('#window').is(':visible')) $('#window').hide();
        $('#fs_loading').show();
    }

    function fs_close_locking() {
        // $('#overlayer').removeClass('ui-widget-overlay');
        $('#overlayer').css('display','none');
        $('#fs_loading').hide();
    }

    function enterKey(evt){
        var min_qty=parseInt($(evt).siblings('input[name="product_min_qty"]').val());
        //if key is enter
        document.onkeydown = function (e) {
            var theEvent = window.event || e;
            var code = theEvent.keyCode || theEvent.which;
            var qty = $(evt).val();
            if (code == 13) {
                if(qty<min_qty){
                    $(evt).val(min_qty);
                }
                fslocking();
                $('#cart_form').submit();
            }
        }
    }

</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.remove_cart').click(function(e) {
            if($('.question').remove() > 0)
                $('.question').remove();
            var div = $('<div class="question"></div>');
            e.preventDefault();
            thisHref	= $(this).attr('href');
            var del = "<?php echo FS_CART_JS_DELETE?>";
            var yes = "<?php echo FS_CART_JS_YES?>";
            var no = "<?php echo FS_CART_JS_NO?>";
            div.css({'left':$(this).offset().left + $(this).width()/4 - 110,'top':$(this).offset().top+$(this).height()- 5}).html('<b></b><div class="question_01">'+del+' <br/> <span class="yes">'+yes+'</span><span class="cancel">'+no+'</span></div>').appendTo('body').show();

            $('.question').animate({opacity: 1}, 300);
            $('.yes').live('click', function(){
                window.location = thisHref;
            });
            $('.cancel').live('click', function(){
                $(this).parents('div.question').fadeOut(300, function() {
                    $(this).remove();
                });
            });
        });
    });

    function change_cart_num(products_id,add_or_reduce){
        //default is add quantuty
        var pAttr = products_id.split(":");
        if(pAttr.length == 1){
            pAttr[1] = "";
        }
        $min_order = $('#min_order_'+parseInt(products_id)+'_'+pAttr[1]).text();
        if($min_order != '' && $min_order >= $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val() ){
            $num = $min_order;
        }else $num = $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val();
        //alert($num);
        if(!isNaN($num)){
            if(add_or_reduce){
                quantity = parseInt($num)+1;
            }else {
                if($num > 1)
                    quantity = parseInt($num) - 1;
                else{ document.getElementById('remove_cart_'+products_id).click();}
            }
            var url ='<?php if($_SERVER['REQUEST_SCHEME']=='http'){$param = "NONSSL";}else{$param = "SSL";}
    echo html_entity_decode(zen_href_link(FILENAME_SHOPPING_CART,'&action=update_product',$param));?>';
            $.ajax({
                url: url,
                data: 'products_id[]='+products_id+'&cart_quantity[]='+quantity,
                type: "POST",
                beforeSend: function(){fslocking();},
                success: function(){
                    fs_close_locking();
                    $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val(quantity) ;
                    $('#cart_form').submit();
                },error: function(XMLHttpRequest,msg){alert("<?php echo FS_CART_JS_SORRY;?>");}
            });
        }else	alert("<?php echo FS_CART_ENTER;?>");

    }

    function submit_cart_num(products_id){
        var pAttr = products_id.split(":");
        if(pAttr.length == 1){
            pAttr[1] = "";
        }
        $min_order = $('#min_order_'+parseInt(products_id)+'_'+pAttr[1]).text();
        if($min_order != '' && $min_order >= $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val() ){
            $num = $min_order;
        }else $num = $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val();
        if(!isNaN($num)){
            if($num > 1){
                quantity = parseInt($num);
            }else {
                document.getElementById('remove_cart_'+products_id).click();
            }
            $.ajax({
                url: "<?php echo html_entity_decode(zen_href_link(FILENAME_SHOPPING_CART,'&action=update_product','NONSSL'));?>",
                data: 'products_id[]='+products_id+'&cart_quantity[]='+quantity,
                type: "POST",
                beforeSend: function(){fslocking();},
                success: function(){
                    fs_close_locking();
                    $('#quantity_'+parseInt(products_id)+'_'+pAttr[1]).val(quantity);
                    $('#cart_form').submit();
                },error: function(XMLHttpRequest,msg){alert("<?php echo FS_CART_JS_SORRY;?>");}
            });
        }else	alert("<?php echo FS_CART_ENTER;?>");

    }
    function prodAddToCart(id){
        var qty =  1;
        $.ajax({
            type:"POST",
            dataType:"html",
            url:"?modules=ajax&handler=products_quote_info&ajax_request_action=actionAddProduct",
            data:"products_id="+id+"&cart_quantity="+qty,
            beforeSend:function(){
                $("#prod_add_"+id).val('<?php echo FS_CART_PROCESSING;?>').attr('disabled',true);
            },
            success:function(data){
                $("#prod_add_"+id).val('<?php echo FS_CART_PROCESSING;?>');
                $("#ShoppingCartInfo").html(data);

                location.reload();
            }
        });
    }

</script>
<script type=text/javascript>
    var c_site = "<?php echo $_COOKIE['c_site']?>";

    if(c_site){

    }else{

        if($(window).width() < 480){
            $(".v_content").css("width","300px");
        }

        if($(window).width() < 960 && 480 < $(window).width()){
            $(".v_content").css("width","555px");
        }
    }

</script>

<script type="text/javascript" src="includes/templates/fiberstore/jscript/slider.js"></script>