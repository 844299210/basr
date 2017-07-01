<?php 
require 'includes/application_top.php';

//接收推送信息接口

//订阅成功后，收到首次推送信息是在5~10分钟之间，在能被5分钟整除的时间点上，0分..5分..10分..15分....
$param = $_POST['param'];

try{
	//$param包含了文档指定的信息，...这里保存您的快递信息,$param的格式与订阅时指定的格式一致
	if(!empty($param)){
		$newparam = json_decode($param);
		$tracking_number = $newparam->lastResult->nu;

		$sql = "UPDATE `fs_kuaidi100` SET `shipping_info`='{$param}',`update_time`=now() WHERE tracking_number='{$tracking_number}'";
		$db->Execute($sql);
	}
	echo  '{"result":"true","returnCode":"200","message":"成功"}';
	//要返回成功（格式与订阅时指定的格式一致），不返回成功就代表失败，没有这个30分钟以后会重推
}catch(Exception $e){
	echo  '{"result":"false","returnCode":"500","message":"失败"}';
	//保存失败，返回失败信息，30分钟以后会重推
}

?>