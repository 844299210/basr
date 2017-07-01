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

var_dump('test');

global $db;
$result = array();

$count = $db->Execute("select count(*) from seo_tag_popular  order by id desc limit 0,10");

if($count->RecordCount() > 0){
	$query = mysql_query("select DISTINCT keyword from seo_tag_popular order by id desc limit 0,10");
}
while ($row = mysql_fetch_array($query)) {

//$search_url = zen_href_link(FILENAME_ADVANCED_SEARCH_RESULT,'keyword='.$row['categories_name']);

$search_url = 'http://beta.fiberstore.com/index.php?main_page=advanced_search_result&keyword='.$row['keyword'];

	$result [] = array(
	    	$row['keyword'] => $search_url
		);
}
foreach ($result as $key=>$value) {
	foreach ($value as $i=>$arr){
		echo "$i|$arr\n";
		var_dump('test');
	}
}
?>
