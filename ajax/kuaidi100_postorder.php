<?php
header("Content-Type:text/html;charset=utf-8");
//运单订阅接口
//注意：key经常会变，请与快递100联系获取最新key

//向快递100传递的参数
$post_data = array();
$post_data["schema"] = 'json';	//订阅后快递100返回的参数类型，json/xml

//$company = 'fedex';			//运单所选物流公司,如果使用了 "autoCom":"1" 这里的物流公司即可省略
$number = '673002478334';		//运单号
$key = 'ukdrjQpp6200';			//key秘钥		
$callbackurl = 'http://test.whgxwl.com:8000/kuaidi100_callback.php';	//订阅成功后，给快递100推送运单信息的返回接口url
$url='http://www.kuaidi100.com/poll';														//快递100的订阅接口地址

//将参数组装为一个json格式数据发送给快递100
//$post_data["param"] = '{"company":"'.$company.'",';
$post_data["param"] = '{"number":"'.$number.'",';
$post_data["param"] = $post_data["param"].'"key":"'.$key.'",';
$post_data["param"] = $post_data["param"].'"parameters":{"callbackurl":"'.$callbackurl.'","autoCom":"1"}}';	//这里设置的地址也就是推送过来的接受地址


//向快递100发送订阅请求
$o="";
foreach ($post_data as $k=>$v)
{
	$o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
}

$post_data=substr($o,0,-1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$result = curl_exec($ch);		//返回提交结果，格式与指定的格式一致（result=true代表成功）


//发送订阅请求后，快递100返回的参数
$data = json_decode($result);	

//返回的数据例1：stdClass Object ( [result] => 1 [returnCode] => 200 [message] => 提交成功 )
//返回的数据例2：stdClass Object ( [result] => [returnCode] => 501 [message] => POLL:重复订阅 )	
?>

<script>
var status="<?php echo $data->message?>";
alert(status);
</script>