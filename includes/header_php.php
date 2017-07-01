<?php
/**
 * header for manage profile
 */

if (!$_SESSION['customer_id']) {
    $_SESSION['navigation']->set_snapshot();
    zen_redirect(zen_href_link(FILENAME_LOGIN, '', 'SSL'));
}

require DIR_WS_CLASSES . 'customer_account_info.php';
$customer_info = new customer_account_info();

$customers_type = $customer_info->get_customers_types();

///* need action and securyToken params to validate*/
//if (isset($_POST['action']) && 'update_profile' == $_POST['action'] && isset($_POST['securityToken']) && $_POST['securityToken'] == $_SESSION['securityToken']){
//
//	$customers_gender = mysql_real_escape_string($_POST['customers_gender']);
//	$customers_firstname = mysql_real_escape_string($_POST['customers_firstname']);
//	$customers_lastname = mysql_real_escape_string($_POST['customers_lastname']);
//	$customers_type_id = intval($_POST['customers_type_id']);
//	$customers_company = mysql_real_escape_string($_POST['customers_company']);
//	
//	$account_info = array(
//	'customers_gender' => $customers_gender,
//	'customers_firstname' => $customers_firstname,
//	'customers_lastname' => $customers_lastname,
//	'customers_type_id' => $customers_type_id,
//	'customers_company' => $customers_company
//	);
//	
//	$customer_info->update_customer_profile($account_info);
//	
//	$account_info = $customer_info->get_customer_profile();
//	
//	/*bof getnarate the customers type string*/	
//	$size_of_type = sizeof($customers_type);
//	if ($size_of_type){
//		$type_string = '[';
//		for($i =0; $i< $size_of_type;$i++){
//			$type_string .= '{"id":"'.$customers_type[$i]['id'].'","text":"'.$customers_type[$i]['text'].'"}, ';
//			/*if ($i < ($size_of_type -1)) $type_string.= ",";*/
//		}
//		$type_string = substr($type_string, 0, (strlen($type_string) - 2)) . "]";
//			
//	}
//	/*eof getnarate the customers type string*/
//	
//	/*bof use json to output data for javascript object*/
//	
//	
//	echo '{"customers_gender":"'.$account_info['customers_gender'].'",
//		   "customers_firstname":"'.$account_info['customers_firstname'].'",
//		   "customers_lastname":"'.$account_info['customers_lastname'].'",
//		   "customers_type_id":"'.$account_info['customers_type_id'].'",
//		   "customers_company":"'.$account_info['customers_company'].'",
//		   "customers_types":'.$type_string.'}';
//	//exit(0);
//	
//	/*eof use json to output data for javascript object*/
//}

/*get current customer info*/
$account_info = $customer_info->get_customer_profile();

//var_dump($account_info);exit;