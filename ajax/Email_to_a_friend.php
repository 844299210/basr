<?php
/*include the load files*/
require("includes/application_top.php");
/*if the user doesn't exist go to login page*/
if (!isset($_SESSION['customer_id'])) zen_redirect(zen_href_link("login",'','SSL'));

// if message have sent , go to a different page
if ($_POST && $_POST['to_email_address'])
{
	require("includes/languages/english/tell_a_friend.php");
	/*bof processing post email to his friend*/
	  $to_email_address = zen_db_prepare_input($_POST['to_email_address']);
	  $to_name = zen_db_prepare_input($_POST['to_name']);
	  $from_email_address = zen_db_prepare_input($_POST['from_email_address']);
	  $from_name = zen_db_prepare_input($_POST['from_name']);
	  $message = zen_db_prepare_input($_POST['message']);
  
	$email_subject = sprintf(EMAIL_TEXT_SUBJECT, $from_name, STORE_NAME);
    $email_body = sprintf(EMAIL_TEXT_GREET, $to_name);
    $email_body .= sprintf(EMAIL_TEXT_INTRO,$from_name, $product_info->fields['products_name'], STORE_NAME) . "\n\n";
    $html_msg['EMAIL_GREET'] = str_replace('\n','',sprintf(EMAIL_TEXT_GREET, $to_name));
    $html_msg['EMAIL_INTRO'] = sprintf(EMAIL_TEXT_INTRO,$from_name, $product_info->fields['products_name'], STORE_NAME);

    if (zen_not_null($message)) {
      $email_body .= sprintf(EMAIL_TELL_A_FRIEND_MESSAGE, $from_name)  . "\n\n";
      $email_body .= strip_tags($message) . "\n\n" . EMAIL_SEPARATOR . "\n\n";
      $html_msg['EMAIL_MESSAGE_HTML'] = sprintf(EMAIL_TELL_A_FRIEND_MESSAGE, $from_name).'<br />';
      $html_msg['EMAIL_MESSAGE_HTML'] .= strip_tags($message);
    } else {
      $email_body .= '';
      $html_msg['EMAIL_MESSAGE_HTML'] = '';
    }

    $email_body .= sprintf(EMAIL_TEXT_LINK, zen_href_link(zen_get_info_page($_GET['products_id']), 'products_id=' . $_GET['products_id']), '', false) . "\n\n" . sprintf(EMAIL_TEXT_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

    $html_msg['EMAIL_TEXT_HEADER'] = EMAIL_TEXT_HEADER;
    $html_msg['EMAIL_PRODUCT_LINK'] = sprintf(str_replace('\n\n','<br />',EMAIL_TEXT_LINK), '<a href="'.zen_href_link(zen_get_info_page($_GET['products_id']), 'products_id=' . $_GET['products_id']).'">'.$product_info->fields['products_name'].'</a>' , '', false);
    $html_msg['EMAIL_TEXT_SIGNATURE'] = sprintf(str_replace('\n','',EMAIL_TEXT_SIGNATURE), '' );

    // include disclaimer
    $email_body .= "\n\n" . EMAIL_ADVISORY . "\n\n";

    
    /*bof SET custome information*/
    
    if (isset($_POST['products_id'])) {
    	$get_products_information = $db->Execute("select * from " . TABLE_PRODUCTS . " as p , " . TABLE_PRODUCTS_DESCRIPTION . " as pd where p.products_id = pd.products_id and p.products_id = " . (int)$_POST['products_id']);
    	
    	if ($get_products_information->RecordCount() > 0) {
    		$products_name = $get_products_information->fields['products_name'];
    		$products_price = $get_products_information->fields['products_price'];
    		$products_id = $get_products_information->fields['products_id'];
    		$products_image = $get_products_information->fields['products_image'];    
    		$products_description = $get_products_information->fields['products_description'];    
    				
    	}
    }
    
    $html_msg['HTTP_SERVER'] = HTTP_SERVER;
    $html_msg['TO_NAME'] = $to_name;
    $html_msg['FROM_NAME'] = $from_name;
    $html_msg['PRODUCTS_NAME'] = isset($products_name) ? $products_name : NULL;
    $html_msg['PRODUCTS_PRICE'] = isset($products_price) ? $currencies->display_price($products_price) : NULL;
    $html_msg['PRODUCTS_IMAGE'] = isset($products_image) ? DIR_WS_IMAGES.$products_image : DIR_WS_IMAGES.'no_picture.gif';
    $html_msg['PRODUCTS_DESCRIPTION'] = isset($products_description) ? $products_description : NULL;
    $html_msg['PRODUCTS_LINK'] = zen_href_link(zen_get_info_page($products_id), 'products_id=' . $products_id);;
    
    /*eof SET custome information*/
    
    //send the email
    zen_mail($to_name, $to_email_address, $email_subject, $email_body, $from_name, $from_email_address, $html_msg, 'tell_a_friend');

    // limit spam/slamming
    $_SESSION['tell_friend_timeout'] = time();
    $_SESSION['tell_friend_boot']++;

    // send additional emails
    if (SEND_EXTRA_TELL_A_FRIEND_EMAILS_TO_STATUS == '1' and SEND_EXTRA_TELL_A_FRIEND_EMAILS_TO !='') {
      if ($_SESSION['customer_id']) {
        $account_query = "SELECT customers_firstname, customers_lastname, customers_email_address
                          FROM " . TABLE_CUSTOMERS . "
                          WHERE customers_id = :customersID";

        $account_query = $db->bindVars($account_query, ':customersID', $_SESSION['customer_id'], 'integer');
        $account = $db->Execute($account_query);
      }
      $extra_info=email_collect_extra_info($from_name,$from_email_address, $account->fields['customers_firstname'] . ' ' . $account->fields['customers_lastname'] , $account->fields['customers_email_address'] );

      $html_msg['EXTRA_INFO'] = $extra_info['HTML'];
      zen_mail('', SEND_EXTRA_TELL_A_FRIEND_EMAILS_TO, SEND_EXTRA_TELL_A_FRIEND_EMAILS_TO_SUBJECT . ' ' . $email_subject,
      $email_body . $extra_info['TEXT'], STORE_NAME, EMAIL_FROM, $html_msg, 'tell_a_friend_extra');
    }
/*eof processing post email to his friend*/



?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="ECoptical" />
	<meta name="keywords" content="ECoptical" />
	<META http-equiv="Refresh" content="3; URL=<?php echo HTTP_SERVER;?>">
	<title>Email to a friend - ECoptical</title>
	<link href="includes/templates/ecoptical/css/stylesheet_layout.css" rel="stylesheet" type="text/css" />
	<link href="includes/templates/ecoptical/css/stylesheet_common.css" rel="stylesheet" type="text/css" />
	<link href="includes/templates/ecoptical/css/stylesheet_product.css" rel="stylesheet" type="text/css" />
	</head>
	<body style="background:#FFF;">
		<br/><br/><br/>
		<center>Thank you so much ! You'll go to our website in 3 seconds ... (or <label onclick="winodw.close();">Close the window</label>)</center>
	</body>
	</html>
<?php 	
}else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="ECoptical" />
<meta name="keywords" content="ECoptical" />
<title>Email to a friend - ECoptical</title>
<link href="includes/templates/ecoptical/css/stylesheet_layout.css" rel="stylesheet" type="text/css" />
<link href="includes/templates/ecoptical/css/stylesheet_common.css" rel="stylesheet" type="text/css" />
<link href="includes/templates/ecoptical/css/stylesheet_product.css" rel="stylesheet" type="text/css" />
</head>
<body style="background:#FFF;">
<div class="openBox">
    <div class="logo"><a href="<?php echo HTTP_SERVER;?>"><img src="images/logo.jpg" width="187" height="62" alt="logo" /></a></div>
	<h1>Tell Your Friend About This Product</h1>
 <script type="text/javascript">
		function tell_a_friend_check(form1) {
			if (form1.elements['from_name'].value.length == 0) {
				alert("Please fill in your full name");
				form1.elements['from_name'].focus();
				return false;
				
			}
			if (form1.elements['from_email_address'].value.length == 0) {
				alert("Please fill in your email address");
				form1.elements['from_email_address'].focus();
				return false;
			}
			if (form1.elements['to_name'].value.length == 0) {
				alert("Please fill in your friend's name");
				form1.elements['to_name'].focus();
				return false;
			}
			if (form1.elements['to_email_address'].value.length == 0) {
				alert("Please fill in your friend's email address !");
				form1.elements['to_email_address'].focus();
				return false;
			}
			if (form1.elements['message'].value.length == 0) {
				alert("Please fill in the message to  your friend !");
				form1.elements['message'].focus();
				return false;
			}

			return true;
		}
	</script>   
    <form method="post" id="tell_a_friend" name="tell_a_friend" action="Email_to_a_friend.php" onsubmit="return tell_a_friend_check(this);">
    <table width="599" border="0" cellspacing="0" cellpadding="0" class="EmailTab">
      <tr>
        <td width="192"><p>&nbsp;</p></td>
        <td width="407" align="right">Fields in <b>bold</b> are required </td>
      </tr>
      <tr>
        <td align="right"><b>Full Name</b></td>
        <td><input type="text" name="from_name" class="input"/></td>
      </tr>
      <tr>
        <td align="right"><b>Email Address</b></td>
        <td><input type="text" name="from_email_address" class="input"/></td>
      </tr>
      <tr>
        <td align="right"><b>Friend Name</b></td>
        <td><input type="text" name="to_name" class="input"/></td>
      </tr>
      <tr>
        <td align="right"><b>Friend Email Address</b></td>
        <td><input type="text" name="to_email_address" class="input"/></td>
      </tr>
      <tr>
        <td align="right" valign="top"><strong>Message</strong></td>
        <td><textarea name="message" cols="" rows="5" style="width:260px; height:100px;"></textarea></td>
      </tr>
      <tr>
        <td align="right">&nbsp;</td>
        <td><input type="hidden" name="products_id" value="<?php echo $_GET['products_id'];?>"/><input name="" type="submit" value="Send Mail" class="btn5" /> <input name="" type="button" onclick="window.close();" value="Close" class="btn1" /></td>
      </tr>
     </table>
	</form>
</div>
</body>
</html>
<?php }?>