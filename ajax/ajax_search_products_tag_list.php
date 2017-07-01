<?php
require 'includes/application_top.php';
//include_once("includes/templates/fiberstore/common/connect.php");
$host="localhost";
$db_user="fiberstoredb";
$db_pass="yUxuan3507";
$db_name="fiberstore_beta";

$link=mysql_connect($host,$db_user,$db_pass);
mysql_select_db($db_name,$link);
mysql_query("SET names UTF8");
header("Content-Type: text/html; charset=utf-8");

global $db;
$result = array();

$q = strtolower($_GET["q"]);
if (!$q) return;

$count = $db->Execute("select count(*) from meta_tags_of_search_products where tag_name like '$q%' limit 0,10");

if($count->RecordCount() > 0){
$query = mysql_query("select products_id,tag_name from meta_tags_of_search_products where tag_name like '$q%' limit 0,10");
}

while ($row = mysql_fetch_array($query)) {

$row_url = zen_href_link(FILENAME_PRODUCT_INFO, '&products_id='.$row['products_id'],'NONSSL');

//if($row['tag_categories_id']){
// $row_url = $row['tag_name'];
//}else{
// $row_url = $search_url;
//}

	$result [] = array(
	    	$row['tag_name'] => $row_url
		);
}
foreach ($result as $key=>$value) {
	foreach ($value as $i=>$arr){
		echo "$i|$arr\n";
	}
}
?>
