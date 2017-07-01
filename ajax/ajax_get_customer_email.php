<?php 
require 'includes/application_top.php';

if($_POST['customer_number']){//获取邮箱
	$customer_number = zen_db_prepare_input($_POST['customer_number']);
	$online_email = fs_get_data_from_db_fields('customers_email_address','customers','customers_number_new='.$customer_number,'limit 1');
	$offline_email = fs_get_data_from_db_fields('customers_email_address','customers_offline','customers_number_new='.$customer_number,'limit 1');
	if($online_email){
		echo $online_email;
	}else if($offline_email){
		echo $offline_email;
	}else{
		echo '未查询到客户邮箱,请填写';
	}
	exit();
}
