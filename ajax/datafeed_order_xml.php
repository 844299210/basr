<?PHP
require "includes/application_top.php";

$xml = "<?xml version=\"1.0\"?>\n";
$xml .= "<rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\">\n";
$xml .= "<channel>\n";
$xml .= "<title>datafeed order</title>\n";
$xml .= "<link>http://www.fiberstore.com</link>\n";
$xml .= "<description>datafeed orders</description>\n";

$query = "select a.*,b.tracking_number,b.shipping_method from orders a left join order_tracking_info b on a.orders_id = b.orders_id where a.orders_status = 2";
$orders = $db->getAll($query);
foreach ($orders as $v) {
	$xml .= create_item($v);
}
//  创建XML单项
function create_item($n)
{
	$shipping_array = array('FEDEX','DHL','UPS');
	if(in_array(trim(strtoupper(str_replace('zones','',$n['shipping_module_code']))),$shipping_array)){
		$shipping = trim(strtoupper(str_replace('zones','',$n['shipping_module_code'])));
	}else{
		$shipping = 'Other';
	}
    $item = "<item>\n";
    $item .= "<g:merchant_order_id>" . $n['orders_id'] . "</g:merchant_order_id>\n";
	$item .= "<g:tracking_number>" . $n['tracking_number'] . "</g:tracking_number>\n";
	$item .= "<g:carrier_code>" . $shipping . "</g:carrier_code>\n";
	$item .= "<g:ship_date>" . date('c',strtotime($n['date_purchased'])) . "</g:ship_date>\n";
    $item .= "</item>\n";

    return $item;
}

$xml .= "</channel>\n";
$xml .= "</rss>\n";
file_put_contents("./feed/datafeed_order.xml",$xml);
echo "更新成功！！！";
?> 