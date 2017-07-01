<?php 
require('includes/application_top.php');

$page = intval($_GET['page']);  
//$cID = $_GET['cID'];  

$start = $page * 15;
$arr = array();

$sql = "select in_the_news_id, in_the_news_title, in_the_news_source, in_the_news_url, in_the_news_time from ".TABLE_IN_THE_NEWS." LIMIT ".$start.",15";

$query = mysql_query($sql);

while ($row = mysql_fetch_array($query)) {
	
	$arr[] = array(
			'id' => $row['in_the_news_id'],
			'title' => $row['in_the_news_title'],
			'url' => $row['in_the_news_url'],
			'source' => $row['in_the_news_source'],
			'time' => $row['in_the_news_time']
	);
}

echo json_encode($arr,true);  //转换为json数据输出

?>