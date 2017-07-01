<?php
require 'includes/application_top.php';
//include_once("includes/templates/fiberstore/common/connect.php");
header("Content-Type: text/html; charset=utf-8");

global $db;
$result = array();

$q = strtolower($_GET["q"]);
if (!$q) return;

$count = $db->Execute("select count(*) from fs_search_words where fs_search_words like '$q%' limit 0,10");

if($count->RecordCount() > 0){
	$query =$db->Execute("select fs_search_words as categories_name,fs_search_link from fs_search_words where fs_search_words like '$q%' and language_id = '".(int)$_SESSION['languages_id']."' order by sort desc");
}
/*
else {
	$query =$db->Execute("select categories_name from categories_description where categories_name like '$q%' and language_id = ".$_SESSION['languages_id']." limit 0,10");
}
*/
while (!$query->EOF) {
if($query->fields['categories_name']){
    $search_url = $query->fields['fs_search_link'];
}else{
    $search_url = 'http://www.fs.com/index.php?main_page=advanced_search_result&keyword='.$query->fields['categories_name'];
}
	$result [] = array(
	    	$query->fields['categories_name'] => $search_url
		);
    $query->MoveNext();			
}
foreach ($result as $key=>$value) {
	foreach ($value as $i=>$arr){
		echo "$i|$arr\n";
	}
}
?>
