<?php $per_page_list = array(array('id'=>'24','text'=>'24'),array('id'=>'36','text'=>'36'),array('id'=>'48','text'=>'48'));?>
<?php   require (DIR_WS_CLASSES.'fs_reviews.php');
$fs_reviews = new fs_reviews();
?>
<div class="search_01">
    <span><?php echo "“".$final_keyword."“"?></span> <?php ECHO FIBERSTORE_SEARCH_FOUND;?>
    <span><?php echo $fs_result->number_of_rows;?></span><?php ECHO FIBERSTORE_SEARCH_ITEM;?>
</div>
<div class="Menubox">
    <ul>
        <li <?php  echo  $style == 'images' ? 'class="hover"' : '' ;?>  onmousedown="setTab('one',2,2)" id="one2" class=""><a><i class="list_icon02"></i><?php ECHO FIBERSTORE_IMAGES;?></a></li>
        <li <?php  echo  $style == 'list' ? 'class="hover"' : '' ;?>  onmousedown="setTab('one',1,2)" id="one1"><a><i class="list_icon01"></i><?php echo FIBERSTORE_DETAILS;?></a></li>
    </ul>
</div>

<div class="list_content"  id="con_one_1"   <?php  echo $style == 'list' ? '' : 'style="display:none;"' ;?>>
    <div class="list_clearfix">
  <span class="list_clearfix_block01"><?php echo FIBERSTORE_SHOWING;?>
      <?php
      echo  zen_draw_pull_down_menu('itemPageSize',$per_page_list,$count,'class="login_country"  onchange="change_show_list_num(this.value,\'list\')" ' ) ;
      ?>  <?php echo FIBERSTORE_OF;?>  <?php  echo $fs_result->number_of_rows.FIBERSTORE_RESULTS_BY;?>
  </span>
  <span class="list_clearfix_block02">
     <?php echo preg_replace('/style=(list|images|matrix)/', 'style=list', $page_top_links );?>
  </span>
    </div>
    <div class="product_allrow">
        <?php
        if(is_array($products)){
            foreach ($products as $product){
                ?>
                <div class="product_list_item">
                    <hr />
                    <div class="product_list_row">
                        <div class="product_list_img">
                            <a  href="<?php echo $product['href'];?>" class="thumbnail">
                                <?php echo $product['image'];?></a>
                            <span class="product_sku">#<span><?php echo $product['id'] ;?></span></span>
                        </div>
                        <div class="product_list_col">
                            <h3><a  href="<?php echo $product['href'];?>"><?php echo  $product['name'];?></a></h3>
                            <div class="product_list_info">
                                <div class=""><?php

                                    $reviews = $fs_reviews->get_all_reviews_of_product_products_info($product['id']);
                                    $content_of_reviews = sizeof($reviews);
                                    $reviews_score=$fs_reviews->get_reviews_score($product['id']);
                                    $stars_level = $fs_reviews->get_reviews_star_level($product['id']);
                                    $stars_rand_level = $fs_reviews->get_reviews_star_level_of_review_num($product['id']);
                                    $stars_num= $fs_reviews->get_all_rating_of_level($product['id']);
                                    $ratings = $fs_reviews->get_all_reviews_of_rating($product['id']);

                                    $content_of_ratings = sizeof($ratings);
                                    $stars_matcher = array( 1 => 'p_star05', 2 => 'p_star04',3 => 'p_star03', 4 => 'p_star02', 5 => 'p_star01', );
                                    if ($content_of_reviews){
                                        $reviews_nums=substr($reviews_score,-1);
                                        $reviews_sums=substr($reviews_score,0,1);
                                        if($reviews_nums==0){
                                            $reviews_width=100;
                                        }else{
                                            $reviews_width=$reviews_nums*10;
                                        }
                                        $products_list_info = fs_product_reviews_level_show($reviews_score,$reviews_width,$reviews_sums);
                                    }else {
                                        $products_list_info = '<span class="p_star11" ></span>';
                                    }
                                    echo $products_list_info;
                                    ?>
                                    <span class="products_in_stock">
                            <?php
                            $NowInstock = zen_get_products_instock_total_qty_of_products_ids($product['id']);
                            echo $NowInstock;  ?>,
                            </span>
                                    <?php
                                    $deliver_time = zen_get_products_instock_shipping_date_of_products_id($product['id'],$NowInstockQTY,$countries_code_2);
                                    echo $deliver_time;
                                    if($deliver_time != '<b>'.FS_SHIP_SAME_DAY.'</b>'){ ?>
                                        <div class="track_orders_wenhao">
                                            <div class="question_bg"></div>
                                            <div class="question_text_01 leftjt"><div class="arrow"></div>
                                                <div class="popover-content">
                                                    <?php
                                                    if($deliver_time == '<b>'.FS_SHIP_NEXT_DAY.'</b>'){
                                                        $shipping_html=FS_PRODUCTS_ORDERS_RECEIVED.'<br/>'.FS_PRODUCTS_ACTUAL_TIME;
                                                    }else{
                                                        $shipping_html=FS_PRODUCTS_ACTUAL_TIME;
                                                    }

                                                    echo  $shipping_html ;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                                <h4><?php echo _zen_get_products_name_infomation($product['id']);  ?></h4>
                            </div>
                        </div>
                        <div class="product_list_form">
                            <div class="product_list_price">
                                <label><?php echo FIBERSTORE_YOUR_PRICE;?>:</label>
              <span class="price">
		      <?php
              if('1' != $product['is_inquiry'] ){
                  echo  $product['price'];
              }else{  echo '-';  }
              $add_to_cart =FIBERSTORE_ADD_TO_CART;
              ?>
              </span>
                            </div>
                            <div class="product_list_text">
                                <label><?php echo FIBERSTORE_QUANTITY;?>:</label>
              <span>
            <input type="text" id="detail_quantity_<?php echo $product['id'];?>" name="cart_quantity"   maxlength = "4"  min="1"
                   value="<?php echo (zen_not_null($product['is_min_order_qty']) ? $product['is_min_order_qty'] : '1');?>"  autocomplete="off" class="p_07 product_list_qty"><div class="pro_mun">
                      <a href="javascript:void(list_cart_quantity_change(1,'<?php echo $product['id'];?>'));" class="cart_qty_add"></a>
                      <a href="javascript:void(list_cart_quantity_change(0,'<?php echo $product['id'];?>'));" class="cart_qty_reduce cart_reduce"></a>
                  </div>
              </span>
                            </div>
                            <div class="product_list_btn">
                                <a class="button_02"  id=""
                                   href="<?php echo zen_href_link(FILENAME_PRODUCT_INFO,'products_id=' . $product['id']);?>" >
                                    <?php echo $add_to_cart;?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

    </div>
    <div class="line01"></div>
    <div class="new_page">
        <?php
        echo preg_replace('/style=(list|images|matrix)/', 'style=list', $page_links );
        ?>
    </div>
</div>

<div class="list_content"  id="con_one_2" <?php  echo $style == 'images' ? '' : 'style="display:none;"' ;?>>
    <div class="list_clearfix">
  <span class="list_clearfix_block01"><?php echo FIBERSTORE_SHOWING;?>
      <?php echo zen_draw_pull_down_menu('itemPageSize',$per_page_list,$count,'class="login_country"  onchange="change_show_list_num(this.value,\'images\')" ' ) ;
      ?>   <?php echo FIBERSTORE_OF;?>  <?php  echo $fs_result->number_of_rows.FIBERSTORE_RESULTS_BY;?>
  </span>
    <span class="list_clearfix_block02">
       <?php
       echo preg_replace('/style=(list|images|matrix)/', 'style=images', $page_top_links );
       ?>
    </span>
    </div>

    <div class="product_allrow">

        <?php
        $count=count($products);
        if(is_array($products)){
            foreach ($products as $product){
                ?>
                <div class="product_list_item product_grid" >
                    <hr>
                    <div class="product_list_row">
                        <div class="product_list_img">
                            <a  href="<?php echo $product['href'];?>" class="thumbnail">
                                <?php echo $product['image'];?></a>
                        </div>
                        <div class="product_list_col">
                            <h3><a  href="<?php echo $product['href'];?>"><?php echo $product['name'];?></a></h3>
                            <div class="product_list_info">
                                <div class=""><span class="product_sku">#<span><?php echo $product['id'] ;?></span></span>
                                    <?php

                                    $reviews = $fs_reviews->get_all_reviews_of_product_products_info($product['id']);
                                    $content_of_reviews = sizeof($reviews);
                                    $reviews_score=$fs_reviews->get_reviews_score($product['id']);
                                    $stars_level = $fs_reviews->get_reviews_star_level($product['id']);
                                    $stars_rand_level = $fs_reviews->get_reviews_star_level_of_review_num($product['id']);
                                    $stars_num= $fs_reviews->get_all_rating_of_level($product['id']);
                                    $ratings = $fs_reviews->get_all_reviews_of_rating($product['id']);

                                    $content_of_ratings = sizeof($ratings);
                                    $stars_matcher = array( 1 => 'p_star05', 2 => 'p_star04',3 => 'p_star03', 4 => 'p_star02', 5 => 'p_star01', );
                                    if ($content_of_reviews){
                                        $reviews_nums=substr($reviews_score,-1);
                                        $reviews_sums=substr($reviews_score,0,1);
                                        if($reviews_nums==0){
                                            $reviews_width=100;
                                        }else{
                                            $reviews_width=$reviews_nums*10;
                                        }
                                        $products_list_info = fs_product_reviews_level_show($reviews_score,$reviews_width,$reviews_sums);
                                    }else {
                                        $products_list_info = '<span class="p_star11" ></span>';
                                    }
                                    echo $products_list_info;
                                    ?></div>
                                <div class="product_grid_stock">
                     <span class="products_in_stock">
                     <?php
                     if($count>1){
                         $NowInstockQTYS = zen_get_products_instock_total_qty_of_products_id($product['id']);
                         echo $NowInstockQTYS;
                     }else{
                         echo $NowInstock;
                     }
                     ?>,
                     </span>
                                    <?php
                                    $deliver_time = zen_get_products_instock_shipping_date_of_products_id($product['id'],$NowInstockQTY,$countries_code_2);
                                    echo $deliver_time;
                                    if($deliver_time != '<b>'.FS_SHIP_SAME_DAY.'</b>'){ ?>
                                        <div class="track_orders_wenhao">
                                            <div class="question_bg"></div>
                                            <div class="question_text_01 leftjt"><div class="arrow"></div>
                                                <div class="popover-content">
                                                    <?php
                                                    if($deliver_time == '<b>'.FS_SHIP_NEXT_DAY.'</b>'){
                                                        $shipping_html=FS_PRODUCTS_ORDERS_RECEIVED.'<br/>'.FS_PRODUCTS_ACTUAL_TIME;
                                                    }else{
                                                        $shipping_html=FS_PRODUCTS_ACTUAL_TIME;
                                                    }

                                                    echo  $shipping_html ;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <div class="product_list_form">
                            <div class="product_list_price">
                                <label><?php echo FIBERSTORE_YOUR_PRICE;?>:</label>
              <span class="price">
		      <?php
              if('1' != $product['is_inquiry'] ){
                  echo $product['price'];
              }else{  echo '-';
              }
              $add_to_cart =FIBERSTORE_ADD_TO_CART;
              ?>
              </span>
                            </div>
                            <div class="product_list_text">
                                <label><?php echo FIBERSTORE_QUANTITY;?>:</label>
              <span>
                   <input type="text" id="cart_quantity_<?php echo $product['id'];?>" name="cart_quantity"   maxlength = "4"  min="1"
                          value="<?php echo (zen_not_null($product['is_min_order_qty']) ? $product['is_min_order_qty'] : '1');?>"  autocomplete="off" class="p_07 product_list_qty"><div class="pro_mun">
                      <a href="javascript:void(q_cart_quantity_change(1,'<?php echo $product['id'];?>'));" class="cart_qty_add"></a>
                      <a href="javascript:void(q_cart_quantity_change(0,'<?php echo $product['id'];?>'));" class="cart_qty_reduce cart_reduce"></a>
                  </div>
              </span>
                            </div>
                            <div class="product_list_btn">

                                <a class="button_02"  id=""
                                   href="<?php echo zen_href_link(FILENAME_PRODUCT_INFO,'products_id=' . $product['id']);?>" >
                                    <?php echo $add_to_cart;?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <div class="line01"></div>
    <div class="new_page">
        <?php
        echo preg_replace('/style=(list|images|matrix)/', 'style=images', $page_links );
        ?>
    </div>
</div>
<script type="text/javascript">
function list_cart_quantity_change(type,id){
	var qty = parseInt($("#detail_quantity_"+id).val());
	if(!isNaN(qty)){
		switch(type){
			case 0:
					if(qty >=2){
					$("#detail_quantity_"+id).val(qty-1);
					$("#img_quantity_"+id).val(qty-1);
					}else{
					}
				break;
			case 1:
				$("#detail_quantity_"+id).val(qty+1);
				$("#img_quantity_"+id).val(qty+1);
				break;
		}
	}else{
		$("#detail_quantity_"+id).val(1);
		$("#img_quantity_"+id).val(1);
		return false;
	}
}
function q_cart_quantity_change(type,id){
	var qty = parseInt($("#cart_quantity_"+id).val());
	if(!isNaN(qty)){
		switch(type){
			case 0:
					if(qty >=2){
					$("#cart_quantity_"+id).val(qty-1);
					}else{
					}
				break;
			case 1:
				$("#cart_quantity_"+id).val(qty+1);
				break;
		}
	}else{
		$("#cart_quantity_"+id).val(1);
		return false;
	}
}

function change_show_list_num(list_num,tab){
	 var  list_num_page =  '<?php echo $page_jump_links;?>' ;
	 var pattern= /count=(12|24|36|48)/;
	 var jump_page = list_num_page.replace(pattern, 'count='+list_num)
	      pattern= /style=(list|images)/;
	     jump_to_page = jump_page.replace(pattern, 'style='+tab)
	 window.location = jump_to_page  ;
}
</script>