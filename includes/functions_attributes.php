<?php
define('ATTRIBUTE_FIBER_TYPE', 'Fiber Type');
define('ATTRIBUTE_FIBER_COUNT', 'Fiber Count');
define('ATTRIBUTE_OUTER_JACKET', 'Custom Outer Jacket');
define('ATTRIBUTE_Package', 'Package');
define('ATTRIBUTE_REMARK', 'Label of Package');
define('ATTRIBUTE_TUBE_COLOR', 'Inner Tube Color');
define('ATTRIBUTE_FIBER_COLOR', 'Fiber Color');


//Fiber Optic Network
define('EXPRESS_PORT', 'Express Port Connector');
define('LINGNMPORT', '1310nm Port Connector');
define('MONITORPORT', 'Monitor Port Connector');
define('WUNMPORT', '1550nm Port Connector');
define('SPECIALSERVICE', 'Special Service');
define('HOUSING', 'Housing');
define('FIBERDIAMETER', 'Pigtail Fiber Diameter');
define('PORTCONNECTORS', 'Channels Port Connectors');
define('OPTIONVALUENAME', 'None···（optional）');

define('MONITORTAPPER', 'Monitor Tap Percentage');
define('COMPORTCONNECTOR', 'Com Port Connector');
define('EQUIPMENTMODEL', 'Equipment Model');
define('BRAND', 'Compatible Brands');
define('BREAKOUTLEG', 'Breakout Leg Length');
define('POWERSUPPLYA', 'Power Supply for Grade A');
define('POWERSUPPLYB', 'Power Supply for Grade B');
define('CATVEDFATYPE', 'CATV EDFA Type');
define('ADDITIONALFUNCTION', 'Additional Function');
define('DATAPORT', 'Data Port');
define('SDHEDFATYPE', 'SDH EDFA Type');
define('WAVELENGTHRANGE', 'Wavelength Range');
define('RJPORT', 'RJ45 Port');
define('FIBERPORTA', 'Fiber Port A');
define('FIBERPORTB', 'Fiber Port B');
//定制属性 Others
function get_attributes_others($products_options_name, $attr_value = "")
{
    global $db;
    if (in_array(trim($products_options_name), array(ATTRIBUTE_FIBER_TYPE, ATTRIBUTE_FIBER_COUNT, ATTRIBUTE_OUTER_JACKET, ATTRIBUTE_Package))) return false;
    $result1 = $db->getAll("select products_options_id from products_options where products_options_name = '" . trim($products_options_name) . "' and products_options_type=0 limit 1");
    $result2 = $db->getAll("select products_options_id from products_options where products_options_name = '" . trim($products_options_name) . "' and products_options_type=1 limit 1");
    if (!empty($result1) && !empty($result2)) {
        if ($attr_value) {
            if ($attr_value == 'Others') {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    } else {
        return false;
    }
}

//HP 根据产品价格 加上它的60%的价格
function set_hp_option_price($products_id)
{
    global $db, $cPath_array;
    $products_base_price = zen_get_products_base_price((int)$_GET['products_id']);
    $add_option_price = $products_base_price * 0.6;
    $add_option_huawei_price = $add_option_htc_price = 3;
    $special_products_id = array(29838, 29849, 20057, 11589, 11590, 11591);
    $htc_list = $db->getAll("SELECT products_attributes_id FROM products_attributes WHERE products_id = '$products_id' AND options_values_id=1096 limit 1");
    if ($htc_list) {
        $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='0' WHERE products_attributes_id = '" . $htc_list[0]['products_attributes_id'] . "'");
        if (isset($cPath_array[2])) {
            if (in_array($cPath_array[2], array(81, 83, 90, 87, 89))) {
                $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='$add_option_htc_price' WHERE products_attributes_id = '" . $htc_list[0]['products_attributes_id'] . "'");
            }
        }
        if (in_array($products_id, array(20040, 20041))) {
            $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='$add_option_htc_price' WHERE products_attributes_id = '" . $htc_list[0]['products_attributes_id'] . "'");
        }
    }
    $huawei_list = $db->getAll("SELECT products_attributes_id FROM products_attributes WHERE products_id = '$products_id' AND options_values_id=1097 limit 1");
    if ($huawei_list) {
        if (isset($cPath_array[2])) {
            if (in_array($cPath_array[2], array(81, 83, 90, 87, 89))) {
                $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='$add_option_huawei_price' WHERE products_attributes_id = '" . $huawei_list[0]['products_attributes_id'] . "'");
            }
        }
        if (in_array($products_id, array(20040, 20041))) {
            $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='$add_option_huawei_price' WHERE products_attributes_id = '" . $huawei_list[0]['products_attributes_id'] . "'");
        }
    }
    if (!in_array($products_id, $special_products_id)) {
        $list = $db->getAll("SELECT products_attributes_id FROM products_attributes WHERE products_id = '$products_id' AND options_values_id=1082 limit 1");
        if ($list) {
            $db->query("UPDATE products_attributes SET price_prefix='+',options_values_price='$add_option_price' WHERE products_attributes_id = '" . $list[0]['products_attributes_id'] . "'");
        }
    }
}

//详情  显示(eg,Cisco Nexus 7000)
function option_name_detail($products_options_name)
{
    if ($products_options_name == 'Compatible Brands') {
        return true;
    } else {
        return false;
    }
}

//对 Custom 设置是否为BLOCK  inline
function set_block_inline($products_options_name)
{
    if ($products_options_name == 'Compatible Brands') {
        return true;
    } else {
        return false;
    }
}

function get_brand_option_ids()
{
    global $db;
    return $db->getAll("select products_options_id from products_options where language_id =1 and products_options_name = '" . EQUIPMENTMODEL . "' and products_options_type = 1 limit 1");
}

function get_brand_option_ids_status($products_options_name = "")
{
    global $db;
    if (EQUIPMENTMODEL == trim($products_options_name)) {
        return $db->getAll("select products_options_id from products_options where language_id =1 and products_options_name = '" . EQUIPMENTMODEL . "' and products_options_type = 1 limit 1");
    } else {
        return false;
    }
}

function get_brand_option_values_description($option_id)
{
    global $db;
    $res = $db->getAll("select a.products_options_values_comment from products_options_values a,products_options_values_to_products_options b where a.products_options_values_id = b.products_options_values_id and b.products_options_id = '" . $option_id . "' and a.language_id = 1 order by  a.products_options_values_sort_order ASC limit 1");
    return $res[0]['products_options_values_comment'];
}

function zend_option_brand_set($option_name)
{
    if ($option_name == BRAND) {
        return true;
    } else {
        return false;
    }
}

/*
//Csutom
function option_custom($option_name,$a){
    $option_name = trim(strip_tags($option_name));
    if($a == 1){
        if($option_name == 'option_test1' || $option_name == 'pic'){
            return false;
        }else{
            return true;
        }
    }elseif($a == -1){
        if($option_name == 'option_test1' || $option_name == 'pic'){
            return true;
        }else{
            return false;
        }
    }
}
*/
function option_custom($option_name, $a, $products_id)
{
    $option_name = trim(strip_tags(str_replace('*', '', $option_name)));
    global $db;
    $list = $db->getAll("select b.is_custom from products_options a,products_attributes b where a.products_options_name = '" . $option_name . "' and a.products_options_id = b.options_id and b.products_id = '" . $products_id . "'");
    $status = false;
    if ($list) {
        foreach ($list as $key => $v) {
            if ($v['is_custom'] == 1) {
                $status = true;
            }
        }
    }
    if ($a == 1) {
        if ($status) {
            return false;
        } else {
            return true;
        }
    } elseif ($a == -1) {
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}

function option_custom_status($products_id)
{
    global $db;
    $list = $db->getAll("select b.is_custom from products_options a,products_attributes b where  a.products_options_id = b.options_id and b.products_id = '" . $products_id . "'");
    $status = false;
    if ($list) {
        foreach ($list as $key => $v) {
            if ($v['is_custom'] == 1) {
                $status = true;
            }
        }
    }
    return $status;
}

function category_custom($products_id)
{
    global $db;
    $result = $db->getAll("select * from products_to_categories where products_id = '" . $products_id . "' limit 1");
    $res = "";
    if ($result) {
        if (count($result) == 1) {
            $cate_id = $result[0]['categories_id'];
            $cate_arr = get_category_parent_id($cate_id, array());
            foreach ($cate_arr as $key => $v) {
                $res = $db->getAll("select * from categories_image where categories_id = '" . $v . "' limit 1");
                if ($res) {
                    $res = $res[0];
                    break;
                }
            }

        }
    }
    return $res;
}

function get_category_parent_id($cate_id, $cate_arr)
{
    global $db;
    $act_info = $db->getAll("select categories_id,parent_id from categories where categories_id = '" . $cate_id . "' limit 1");
    if ($act_info) {
        $cate_arr[] = $act_info[0]['categories_id'];

        $cate_arr = get_category_parent_id($act_info[0]['parent_id'], $cate_arr);

    }
    return $cate_arr;

}

function get_category_sons_id($cate_id, $cate_arr)
{
    global $db;
    $act_info = $db->getAll("select categories_id,parent_id from categories where parent_id = '" . $cate_id . "'");
    if ($act_info) {
        foreach ($act_info as $key => $v) {
            $cate_arr[] = $v['categories_id'];

            $cate_arr = get_category_sons_id($v['categories_id'], $cate_arr);
        }

    }
    return $cate_arr;
}

function transceivers_categories($cid)
{
    $transceivers_arr = get_category_sons_id(9, array());
    $array = array(80, 62, 110, 132, 139, 155);
    foreach ($transceivers_arr as $key => $v) {
        foreach ($array as $k) {
            if ($k == $v) {
                unset($transceivers_arr[$key]);
            }
        }
    }
    if (in_array($cid, $transceivers_arr)) {
        return true;
    } else {
        return false;
    }
}

//设置Fiber Count 属性价格进行设置
function get_fiber_count_status($name)
{
    $a = "";
    if ($name == ATTRIBUTE_FIBER_COUNT) {
        $a = 'fiber_count';
    } elseif ($name == ATTRIBUTE_FIBER_TYPE) {
        $a = 'fiber_type';
    } else {
        $a = "";
    }
    return $a;
}

function fiber_output_html($products_options_name)
{
    $html = "";
    if ($products_options_name == ATTRIBUTE_FIBER_COUNT) {
        $html .= "<span id='fiber_count_span'></span>";
    }
    return $html;
}

//判断是否存在Fiber Type，Fiber Count属性
function attribute_type_count($attribute)
{
    global $db;
    $fiber_type_s = "";
    $fiber_count_s = "";
    if ($attribute) {
        foreach ($attribute as $key => $v) {
            if (intval($key) > 0) {
                $attribute_list = $db->getAll("select * from products_options where products_options_id = '$key' limit 1");
                $name = $attribute_list[0]['products_options_name'];
                if ($name == ATTRIBUTE_FIBER_TYPE || $name == ATTRIBUTE_FIBER_COUNT) {
                    $a = $db->getAll("select * from products_options_values where products_options_values_id = '$v' and language_id = 1 limit 1");
                    if ($a) {
                        if ($name == ATTRIBUTE_FIBER_TYPE) {
                            $fiber_type_s = $a[0]['products_options_values_name'];
                        } elseif ($name == ATTRIBUTE_FIBER_COUNT) {
                            $fiber_count_s = $a[0]['products_options_values_name'];
                        }
                    }
                }
            }
        }
    }
    if (!empty($fiber_type_s) && !empty($fiber_count_s)) {
        return true;
    } else {
        return false;
    }
}

function set_fibers_count_attribute($products_id, $attribute)
{
    global $db;
    $fiber_type = ATTRIBUTE_FIBER_TYPE;
    $fiber_count = ATTRIBUTE_FIBER_COUNT;
    $sm = 'SM';
    $mm = 'MM';
    $str_replace = 'Fibers';
    $attibute_arr = array();
    $length_s = "";
    $fiber_type_s = "";
    $fiber_count_s = "";
    if ($attribute) {
        foreach ($attribute as $key => $v) {
            if ($key == 'length') {
                $length_id = intval($v);
                $list = $db->getAll("select length from products_length where id= '$length_id' limit 1");
                if ($list) {
                    $length = $list[0]['length'];
                    if (stripos($length, 'km')) {
                        $length = substr(trim($length), 0, -2);
                        if ($length >= 1) {
                            $length_s = $length;
                        }
                    }
                }
            } else {
                if (intval($key) > 0) {
                    $attribute_list = $db->getAll("select * from products_options where products_options_id = '$key' limit 1");
                    $name = $attribute_list[0]['products_options_name'];
                    if ($name == $fiber_type || $name == $fiber_count) {
                        $a = $db->getAll("select * from products_options_values where products_options_values_id = '$v' and language_id = 1 limit 1");
                        if ($a) {
                            if ($name == $fiber_type) {
                                $fiber_type_s = $a[0]['products_options_values_name'];
                            } elseif ($name == $fiber_count) {
                                $fiber_count_s = $a[0]['products_options_values_name'];
                                $option_id = $key;
                            }
                        }
                    }
                }
            }
        }
    }
    if (!empty($length_s) && !empty($fiber_type_s) && !empty($fiber_count_s)) {

        $products_options_values_name = trim(str_replace($str_replace, '', $fiber_count_s));
        $fiber_cable = optical_cable_price($products_id, $fiber_type_s, $products_options_values_name);
        $price = $fiber_cable['price'] * $length_s;
        $weight = $fiber_cable['weight'] * $length_s;
        $product_price = zen_get_products_base_price((int)$products_id);
        $price = $price - $product_price;
        /*
        if($fiber_type_s == $sm){
            if($products_options_values_name >= 4){
                $price = (473+($products_options_values_name-4)/2*40.5)*$length_s;
            }
        }elseif($fiber_type_s == $mm){
            if($products_options_values_name >= 4){
                $price = (675+($products_options_values_name-4)/2*146.25)*$length_s;
            }
        }
        
        if($products_options_values_name >= 4 && $products_options_values_name <=24){
            $weight = 116;
        }elseif($products_options_values_name == 36){
            $weight = 129;
        }elseif($products_options_values_name == 48){
            $weight = 141;
        }elseif($products_options_values_name == 64 || $products_options_values_name == 72){
            $weight = 159;
        }elseif($products_options_values_name == 96){
            $weight = 209;
        }elseif($products_options_values_name == 122 || $products_options_values_name == 144){
            $weight = 280;
        }
        $weight = $weight*$length_s;*/
        $attibute_arr = array('price_prefix' => '+', 'option_id' => $option_id, 'options_values_price' => $price, 'products_attributes_weight' => $weight);

    }
    return $attibute_arr;
}

function optical_cable_price($products_id, $type_name, $strand)
{
    global $db;
    $info = array();
    if (in_array($type_name, array('OS1(G.652D)', 'OS1(G.657A1)', 'OS1(G.657A2)', 'OS1(G.657B1)', 'OS1(G.657B2)'))) {
        $type_name = 'SM';
    } elseif (in_array($type_name, array('OM1', 'OM2'))) {
        $type_name = 'MM';
    }
    $reuslt = $db->getAll("select b.price_1_9,b.weight from products a,products_attributes_fibers b,products_attributes_fibers_type c where a.model_id = b.model_id and b.type_id = c.id and a.products_id = '$products_id' and c.type_name = '$type_name' and b.strand = '$strand' limit 1");
    if ($reuslt) {
        $info = array('price' => $reuslt[0]['price_1_9'], 'weight' => $reuslt[0]['weight']);
    }
    return $info;
}

function get_fiber_count_weight($fiber_count)
{

    if ($fiber_count >= 4 && $fiber_count <= 24) {
        $weights = 116;
    } elseif ($fiber_count == 36) {
        $weights = 129;
    } elseif ($fiber_count == 48) {
        $weights = 141;
    } elseif ($fiber_count == 64 || $fiber_count == 72) {
        $weights = 159;
    } elseif ($fiber_count == 96) {
        $weights = 209;
    } elseif ($fiber_count == 122 || $fiber_count == 144) {
        $weights = 280;
    } else {
        $weights = 0;
    }
    return $weights;

}

//设置outer jacket   +金额/KM
function get_outer_jacket_status($name)
{
    $b = "";
    if ($name == ATTRIBUTE_OUTER_JACKET) {
        $b = 'outer_jacket';
    } else {
        $b = "";
    }
    return $b;
}

function get_outer_jacket_length($length_id)
{
    global $db;
    $length_arr = $db->getAll("select length from products_length where id='" . $length_id . "' limit 1");
    $length_s = 1;
    if ($length_arr) {
        $length_q = $length_arr[0]['length'];
        if (stripos($length_q, 'km')) {
            $length_s = substr(trim($length_q), 0, -2);
        }
    }
    return $length_s;
}

function get_outer_jacket_options_values_price($option, $options_values_price, $length_s)
{
    global $db;
    $option_list = $db->getAll("select products_options_name from products_options where products_options_id = '$option' limit 1");
    if ($option_list) {
        if ($option_list[0]['products_options_name'] == ATTRIBUTE_OUTER_JACKET) {

            $options_values_price = $options_values_price * $length_s;
        }
    }
    return $options_values_price;
}

//对Remark  颜色块进行设置
function get_remark_status($products_options_id)
{
    global $db;
    $a = $db->getAll("select products_options_name from products_options where products_options_id = '$products_options_id' limit 1");
    if ($a) {
        //if($a[0]['products_options_name'] == ATTRIBUTE_REMARK || $a[0]['products_options_name'] == ATTRIBUTE_TUBE_COLOR || $a[0]['products_options_name'] == ATTRIBUTE_FIBER_COLOR){

        //if($a[0]['products_options_name'] == ATTRIBUTE_REMARK || $a[0]['products_options_name'] == ATTRIBUTE_TUBE_COLOR || $a[0]['products_options_name'] == ATTRIBUTE_FIBER_COLOR){
        if ($a[0]['products_options_name'] == ATTRIBUTE_TUBE_COLOR || $a[0]['products_options_name'] == ATTRIBUTE_FIBER_COLOR) {
            if ($a[0]['products_options_name'] == ATTRIBUTE_TUBE_COLOR) {
                return 1;
            } elseif ($a[0]['products_options_name'] == ATTRIBUTE_FIBER_COLOR) {
                return 2;
            } else {
                return true;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function fiber_optic_network_status($name, $a = 0)
{
    $b = "";
    if ($name == EXPRESS_PORT) {
        $b = 'express_port';
    } elseif ($name == LINGNMPORT) {
        $b = '1310nm_port';
    } elseif ($name == MONITORPORT) {
        $b = 'monitor_port';
    } elseif ($name == WUNMPORT) {
        $b = '1550nm_port';
    } elseif ($name == WUNMPORT) {
        $b = '1550nm_port';
        //}elseif($name == FIBERDIAMETER){
        //$b = "fiber_diameter";
    } elseif ($name == MONITORTAPPER) {
        $b = "fiber_monitor_tap";
    } elseif ($name == POWERSUPPLYA) {  //2014/8/13添加
        $b = 'powersupplya';
    } elseif ($name == POWERSUPPLYB) {  //2014/8/13添加
        $b = 'powersupplyb';
    } elseif ($name == FIBERPORTA) {  //2014/8/13添加
        $b = 'fiberporta';
    } elseif ($name == FIBERPORTB) {  //2014/8/13添加
        $b = 'fiberportb';
    } elseif ($name == DATAPORT) {
        if ($_GET['products_id'] == 32555) {
            $b = 'dataport';
        }
    } else {
        $b = "";
    }
    if ($a == 0) {
        if ($name == HOUSING) {
            $b = 'housing';
        }
        if ($name == PORTCONNECTORS) {
            $b = 'channels_port_connectors';
        }
        if ($name == COMPORTCONNECTOR) {
            $b = 'com_port_connector';
        }
        if ($name == BREAKOUTLEG) {
            $b = 'breakout_leg_length';
        }
        if ($name == CATVEDFATYPE) {
            $b = 'catvedfatype';
        }
        if ($name == SDHEDFATYPE) {
            $b = 'sdhedfatype';
        }
        if ($name == WAVELENGTHRANGE) {
            $b = 'wavelengthrandge';
        }
        if ($name == RJPORT) {
            $b = 'rjport';
        }
    }
    return $b;
}

function fiber_optic_network($option_name)
{
    $option_name = trim(strip_tags(str_replace('*', '', $option_name)));
    $result = fiber_optic_network_status($option_name, 1);
    return $result;
}

//对fiber service属性进行设置
function specical_service_status($products_options_id)
{
    global $db;
    $a = $db->getAll("select products_options_name from products_options where products_options_id = '$products_options_id' limit 1");
    if ($a) {
        if (in_array($a[0]['products_options_name'], array(SPECIALSERVICE, ADDITIONALFUNCTION))) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }

}

function Channels_Port_option_value_id($products_options_id, $products_options_name)
{
    global $db;
    $info = array();
    if ($products_options_name == PORTCONNECTORS) {
        $info = $db->getAll("select * from products_options_values_to_products_options where products_options_id = '$products_options_id'");
    }
    return $info;
}

?>