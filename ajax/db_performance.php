<?php
require 'includes/application_top.php';
$sql = "select products_id, products_price from products order by :products_id: limit 10";

// $test = array(array('target'=>':products_id:','replacement'=>'products_id','type'=>'passthru'));
// foreach ($test as $i => $t){
// 	var_dump($t);
// }
// exit;
$returns = zen_get_data_from_db($sql, array('products_id','products_price'),array(array('target'=>':products_id:','replacement'=>'products_id','type'=>'passthru')));

var_dump($returns);