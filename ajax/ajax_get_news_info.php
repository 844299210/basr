<?php 
require 'includes/application_top.php';

$page = intval($_POST['pageNum']);
$event = intval($_POST['event']);  
$Nyear = intval($_POST['Nyear']);  

if ($Nyear != null){
	//$count_sql = "where news_or_event = ".$event." AND date_format(in_the_news_time,'%Y') = '".$Nyear."'";
	$where_sql = "where news_or_event = ".$event." AND date_format(in_the_news_time,'%Y') = '".$Nyear."'";
}
$result = mysql_query("select in_the_news_id from ".TABLE_IN_THE_NEWS." ".$where_sql."");
$total = mysql_num_rows($result); 
$pageSize = 8; 
$totalPage = ceil($total/$pageSize); 
 
$startPage = $page * $pageSize; 
$arr['total'] = $total; 
$arr['pageSize'] = $pageSize; 
$arr['totalPage'] = $totalPage; 

$sql = "select in_the_news_id as nid, in_the_news_title as title, in_the_news_source as source, in_the_news_url as url, in_the_news_time as time from ".TABLE_IN_THE_NEWS."
		".$where_sql." order by in_the_news_time desc limit $startPage,$pageSize";

//echo $sql;
$query = mysql_query($sql); 

while($row=mysql_fetch_array($query)){ 
     $arr['list'][] = array( 
        'nid' => $row['nid'], 
      	'title' => $row['title'], 
     	'source' => $row['source'],
        'url' => $row['url'],
        'time' => date('F j,Y',strtotime($row['time']))
     ); 
} 
echo json_encode($arr); 
