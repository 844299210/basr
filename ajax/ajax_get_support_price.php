<?php
require 'includes/application_top.php';


if($_POST['method'] == 'get_price_instock'){
    $products_id = explode(',',$_POST['products_id']);
    for($i=0;$i<sizeof($products_id);$i++){
/*        $price[] = $currencies->display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$products_id[$i])),0);*/
        $arr['price'][] = $currencies->new_display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$products_id[$i])),0);
        if($_POST['s_id']==754){
          $main_products_id = zen_get_products_related_model($products_id[$i]);
          $instock = fs_get_data_from_db_fields('instock_qty','products_instock','products_id='.(int)$seattle_instock.' and warehouse=3','');
        }else{
          $instock = zen_get_products_instock_total_qty_of_products_id((int)$products_id[$i]);
        }
        $arr['instock'][] = $instock;
    }
    echo json_encode($arr);exit;
}

if($_POST['method'] == 'get_price'){
    $products_id = explode(',',$_POST['products_id']);
    $price = array();
    for($i=0;$i<sizeof($products_id);$i++){
        /*        $price[] = $currencies->display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$products_id[$i])),0);*/
        $price[] = $currencies->new_display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$products_id[$i])),0);
    }
    echo json_encode($price);exit;
}
?>
