<?php
require 'includes/application_top.php';
if(isset($_POST)){
$pay_from_email  =   $_POST['pay_from_email'];
$merchant_id  =   $_POST['merchant_id'];
$customer_id  =   $_POST['customer_id'];
//$transaction_id  =   $_POST['transaction_id'];
$transaction_id  =   $_POST['mb_transaction_id'];
$mb_amount  =   $_POST['mb_amount'];
$mb_currency  =   $_POST['mb_currency'];
$status  =   $_POST['status'];
$failed_reason_code  =   $_POST['failed_reason_code'];
$md5sig  =   $_POST['md5sig'];
$sha2sig  =   $_POST['sha2sig'];
$amount  =   $_POST['amount'];
$currency  =   $_POST['currency'];
$payment_type  =   $_POST['payment_type'];
$order_id  =   $_POST['order_id'];
/*save transaction details in database*/
$db->query("INSERT INTO `skrill_payment_details` (`order_id`,`pay_from_email`,`merchant_id`,`customer_id`,`transaction_id`,`mb_amount`,`mb_currency`,`status`,`failed_reason_code`,`md5sig`,`sha2sig`,`amount`,`currency`,`payment_type`) VALUES ('".$order_id."', '".$pay_from_email."', '".$merchant_id."', '".$customer_id."', '".$transaction_id."', '".$mb_amount."', '".$mb_currency."', '".$status."', '".$failed_reason_code."', '".$md5sig."', '".$sha2sig."', '".$amount."', '".$currency."', '".$payment_type."') ");
}
?>