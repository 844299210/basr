<?php
require 'includes/application_top.php';
$p_ids = array();
$sql = "select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " group by products_id order by products_id";
// echo $sql;
$get_pids = $db->Execute($sql);

while (!$get_pids->EOF){
	$p_ids [] = $get_pids->fields['products_id'];
	$get_pids->MoveNext();
}

 echo implode(',', $p_ids) ."<br/>";

$sql = "select products_id from " . TABLE_PRODUCTS . " where products_id not in (".implode(',', $p_ids).")";
// echo $sql;
$p_ids = array();
$get_pids = $db->Execute($sql);

while (!$get_pids->EOF){
	$p_ids [] = $get_pids->fields['products_id'];
	$get_pids->MoveNext();
}

echo implode(',', $p_ids);