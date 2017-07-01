<?php
	require 'includes/application_top.php';
ini_set("display_errors", "On");
error_reporting(E_ERROR);
//todo:test
$_GET['ajax_request_action'] = 'facebook';
$_POST['name'] = "Daye Chen";
$_POST['id'] = 123456789;
$_POST ['email'] = 'chendage78945@gmail.ocm';
//$_POST ['email'] = 'hknetworking@Gmail.com';

if (isset($_GET['ajax_request_action'])){
	$action = $_GET['ajax_request_action'];

		switch($action){

            case 'google':
                if($_POST['email'] != null){

                    $google_plus_id = mysql_real_escape_string($_POST['gid']);
                    $first_name = mysql_real_escape_string($_POST['fName']);
                    $last_name = mysql_real_escape_string($_POST['gName']);
                    $name = $first_name." ".$last_name;
                    $email = mysql_real_escape_string( $_POST ['email'] );
                    $gender = mysql_real_escape_string($_POST['gender']);
                    $result = $db->Execute("select customers_id,customers_email_address,customers_firstname,customers_password from customers where customers_email_address = '".$email."'");
                    if ($result->RecordCount()){
                        $_SESSION ['customer_id'] = $result->fields['customers_id'];
                        $_SESSION ['customer_first_name'] = $result->fields['customers_firstname'];
                        $_SESSION['customers_email_address'] = $result->fields['customers_email_address'];

                        //set cookie for customer_id ******************************
                        require_once DIR_WS_CLASSES . 'set_cookie.php';
                        $Encryption = new Encryption;
                        $cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
                        setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");

                    }else{
                        $now_time = date('Y-m-d H:i:s');
                        $customer = array(
                            'customers_firstname' => $first_name,
                            'customers_lastname' => $last_name,
                            'customers_email_address' => $email,
                            'customers_dob' => $now_time,
                            'social_media_id' => 4
                        );

                        //分配判断代码应置于插入数据之前,自动分配文件中叫$email_address而不是$email
                        $email_address = $email;
                        //require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));

                        if($admin_id){
                            //邮箱匹配到了 标记老客户 用于统计
                            $customer['is_old'] = $is_old;
                        }

                        zen_db_perform(TABLE_CUSTOMERS, $customer);
                        $_SESSION ['customer_id'] = $db->Insert_ID();
                        $customer_info = array(
                            'customers_info_id' => $db->Insert_ID() ,
                            'customers_info_date_of_last_logon' =>$now_time ,
                            'customers_info_number_of_logons'=> 1,
                            'customers_info_date_account_created'=>$now_time,
                        );

                        zen_db_perform(TABLE_CUSTOMERS_INFO, $customer_info);

                        $cid = $_SESSION ['customer_id'];

                        if($admin_id){
                            $customers_id=$cid;

                            $sql='INSERT INTO admin_to_customers(admin_id,customers_id,add_time) VALUE("'.$admin_id.'","'.$customers_id.'",now())';
                            $db->Execute($sql);
                            $sales_email = zen_admin_email_of_id($admin_id);
                            $html_msg['EMAIL_HEADER'] = EMAIL_HEADER_INFO;
                            $html_msg['EMAIL_BODY'] = '
	     <tr>
		    <td><table width="650" border="0" align="center" cellspacing="0" cellpadding="0" style=" font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; line-height:18px; border:0;">
		  <tbody><tr>
		    <td width="10" bgcolor="#f4f4f4" rowspan="2">&nbsp;</td>
		    <td style="border-right:1px solid #d2d2d2; padding:0 30px; line-height:26px; font-size:11px;" colspan="2">
		    <span style="color:#666666; line-height:18px;"><br>This message comes from the <b>fiberstore administrator</b>, please review!</span>
		            <br>
	            <span style="  font-size:12px; font-weight:bold; display:block; padding-bottom:10px;">Customer Information</span>

	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Customer Name:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($firstname.$lastname ? $firstname.$lastname : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Phone Number:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($telephone ? $telephone : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">E-mail address:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($email_address ? $email_address : 'not set yet').'</span>
	            </div>


		            <div style="clear:both;"><br></div>
		</td>

		  </tr>
		  </tbody></table>
		</td>
		</tr>';
                            $html_msg['EMAIL_FOTTER'] = EMAIL_FOOTER_INFO;
                            zen_mail($sales_email, $sales_email, 'Customer Info', $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'contact_us');

                        }

                        //end
                        //set cookie for customer_id ******************************
                        require_once DIR_WS_CLASSES . 'set_cookie.php';
                        $Encryption = new Encryption;
                        $cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
                        setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");


                        $_SESSION ['customer_first_name'] = $first_name;
                        $_SESSION['customers_email_address'] = $email;

                        $google_plus_info = array(
                            'google_plus_id' => $google_plus_id,
                            'google_plus_email' => $email,
                            'google_plus_name' => $name,
                            'google_plus_gender' => $gender,
                            'customers_id' => $_SESSION['customer_id'],
                        );

                        zen_db_perform(TABLE_CUSTOMERS_SOCIAL_MEDIA_GOOGLE_INFO, $google_plus_info);
                    }
                    exit('ok');
                    //header('Location: http://www.fiberstore.com');
                }
                break;


			case 'paypal':
			if($_POST['email'] != null){
				 $first_name = mysql_real_escape_string($_POST['fName']);
				 $last_name = mysql_real_escape_string($_POST['gName']);
				 $name = $first_name." ".$last_name;
				 $email = mysql_real_escape_string( $_POST ['email'] );
				 $zoneinfo = mysql_real_escape_string($_POST['zoneinfo']);
				 $result = $db->Execute("select customers_id,customers_email_address,customers_firstname,customers_password from customers where customers_email_address = '".$email."'");

                if ($result->RecordCount()){
					$_SESSION ['customer_id'] = $result->fields['customers_id'];
					$_SESSION ['customer_first_name'] = $result->fields['customers_firstname'];
					$_SESSION['customers_email_address'] = $result->fields['customers_email_address'];

					                    //set cookie for customer_id ******************************
					require_once DIR_WS_CLASSES . 'set_cookie.php';
					$Encryption = new Encryption;
		        	$cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
		        	setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");


				 }else{
                     //分配判断代码应置于插入数据之前,自动分配文件中叫$email_address而不是$email
                     $email_address = $email;
                     require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));
                     $now_time = date('Y-m-d H:i:s');

				 $customer = array(
			        'customers_firstname' => $first_name,
			 		'customers_lastname' => $last_name,
			 		'customers_email_address' => $email,
	 				'customers_dob' => $now_time,
	 				'social_media_id' => 5
		 			);
                //E($customer);
                     if($admin_id){
                         //邮箱匹配到了 标记老客户 用于统计
                         $customer['is_old'] = $is_old;
                     }
				  zen_db_perform(TABLE_CUSTOMERS, $customer);
				  $_SESSION ['customer_id'] = $db->Insert_ID();

				    $customer_info = array(
						   'customers_info_id' => $db->Insert_ID() ,
						   'customers_info_date_of_last_logon' =>$now_time ,
						   'customers_info_number_of_logons'=> 1,
						   'customers_info_date_account_created'=>$now_time,
						 );
                    //E($customer_info);
					zen_db_perform(TABLE_CUSTOMERS_INFO, $customer_info);

					$cid = $_SESSION ['customer_id'];

		if($admin_id){
		    $customers_id=$cid;

		    $sql='INSERT INTO admin_to_customers(admin_id,customers_id,add_time) VALUE("'.$admin_id.'","'.$customers_id.'",now())';
		    $db->Execute($sql);
		    $sales_email = zen_admin_email_of_id($admin_id);
		    $html_msg['EMAIL_HEADER'] = EMAIL_HEADER_INFO;
		    $html_msg['EMAIL_BODY'] = '
	     <tr>
		    <td><table width="650" border="0" align="center" cellspacing="0" cellpadding="0" style=" font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; line-height:18px; border:0;">
		  <tbody><tr>
		    <td width="10" bgcolor="#f4f4f4" rowspan="2">&nbsp;</td>
		    <td style="border-right:1px solid #d2d2d2; padding:0 30px; line-height:26px; font-size:11px;" colspan="2">
		    <span style="color:#666666; line-height:18px;"><br>This message comes from the <b>fiberstore administrator</b>, please review!</span>
		            <br>
	            <span style="  font-size:12px; font-weight:bold; display:block; padding-bottom:10px;">Customer Information</span>

	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Customer Name:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($firstname.$lastname ? $firstname.$lastname : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Phone Number:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($telephone ? $telephone : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">E-mail address:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($email_address ? $email_address : 'not set yet').'</span>
	            </div>


		            <div style="clear:both;"><br></div>
		</td>

		  </tr>
		  </tbody></table>
		</td>
		</tr>';
		    $html_msg['EMAIL_FOTTER'] = EMAIL_FOOTER_INFO;
		    zen_mail($sales_email, $sales_email, 'Customer Info', $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'contact_us');

		}

		//end
                    //set cookie for customer_id ******************************
					require_once DIR_WS_CLASSES . 'set_cookie.php';
					$Encryption = new Encryption;
		        	$cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
		        	setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");


                    $_SESSION ['customer_first_name'] = $first_name;
					$_SESSION['customers_email_address'] = $email;

                        $paypal_info = array(
				        'paypal_email' => $email,
                        'paypal_family_name' => $fName,
                        'paypal_given_name' => $gName,
				 		'paypal_zoneinfo' => $zoneinfo,
                            'customers_id' => $_SESSION['customer_id'],
                        );
                    //E($paypal_info);
                        zen_db_perform(TABLE_CUSTOMERS_SOCIAL_MEDIA_PAYPAL_INFO, $paypal_info);
				 }
				 exit('ok');
				 header('Location: http://www.fiberstore.com');
			}
			break;

            case 'facebook':
//exit(json_encode('ok'));
                if($_POST['email'] != null){
                    //todo:获取用户数据
                    $name = mysql_real_escape_string($_POST['name']);
                    $id = mysql_real_escape_string($_POST['id']);
                    $email = mysql_real_escape_string( $_POST ['email'] );
                    //把用戶名拆成名和姓
                    $name_spr = explode(" ",$name);
                    $first_name = $name_spr[0];
                    $last_name = $name_spr[1];

                    //todo:根据邮箱匹配用户
                    $result = $db->Execute("select customers_id,customers_email_address,customers_firstname,customers_password from customers where customers_email_address = '".$email."'");
                    //如果非空，说明用户已经注册
                    if ($result->RecordCount() && 1==2){
                        //设置session
                        $_SESSION ['customer_id'] = $result->fields['customers_id'];
                        $_SESSION ['customer_first_name'] = $result->fields['customers_firstname'];
                        $_SESSION['customers_email_address'] = $result->fields['customers_email_address'];

                        //设置cookie
                        require_once DIR_WS_CLASSES . 'set_cookie.php';
                        $Encryption = new Encryption;
                        $cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
                        setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");

                    //如果为空，用户没有注册
                    }else{
                        //分配判断代码应置于插入数据之前,自动分配文件中叫$email_address而不是$email
                        $email_address = $email;
                        //require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));
                        //require('includes/modules/auto_given.php');
                        $now_time = date('Y-m-d H:i:s');
                        //用户数据
                        $customer = array(
                            'customers_firstname' => $first_name,
                            'customers_lastname' => $last_name,
                            'customers_email_address' => $email,
                            'customers_dob' => $now_time,
                            'social_media_id' => 1 //Facebook
                        );

                        if($admin_id){
                            //邮箱匹配到了 标记老客户 用于统计
                            $customer['is_old'] = $is_old;    //1是老用户  0是新用户
                        }
                        //数据写入用户表
                        //E($customer);
                        //zen_db_perform(TABLE_CUSTOMERS, $customer);
                        $_SESSION ['customer_id'] = $db->Insert_ID();   //最新生成的用户id,存入session

                        $customer_info = array(
                            'customers_info_id' => $db->Insert_ID() ,
                            'customers_info_date_of_last_logon' =>$now_time ,
                            'customers_info_number_of_logons'=> 1,
                            'customers_info_date_account_created'=>$now_time,
                        );
                        //数据写入用户信息附表
                        //E($customer_info);
                        //zen_db_perform(TABLE_CUSTOMERS_INFO, $customer_info);

                        $cid = $_SESSION ['customer_id'];

                        if($admin_id){
                            $customers_id=$cid;
                            //todo:分配客户
                            $sql='INSERT INTO admin_to_customers(admin_id,customers_id,add_time) VALUE("'.$admin_id.'","'.$customers_id.'",now())';
                            $db->Execute($sql);
                            $sales_email = zen_admin_email_of_id($admin_id);
                            $html_msg['EMAIL_HEADER'] = EMAIL_HEADER_INFO;
                            $html_msg['EMAIL_BODY'] = '
	     <tr>
		    <td><table width="650" border="0" align="center" cellspacing="0" cellpadding="0" style=" font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; line-height:18px; border:0;">
		  <tbody><tr>
		    <td width="10" bgcolor="#f4f4f4" rowspan="2">&nbsp;</td>
		    <td style="border-right:1px solid #d2d2d2; padding:0 30px; line-height:26px; font-size:11px;" colspan="2">
		    <span style="color:#666666; line-height:18px;"><br>This message comes from the <b>fiberstore administrator</b>, please review!</span>
		            <br>
	            <span style="  font-size:12px; font-weight:bold; display:block; padding-bottom:10px;">Customer Information</span>

	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Customer Name:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($firstname.$lastname ? $firstname.$lastname : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">Phone Number:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($telephone ? $telephone : 'not set yet').'</span>
	            </div>
	            <div style="clear:both;">
	              <span style="width:30%; float:left; text-align:right;">E-mail address:</span>
	              <span style="width:68%; float:right; text-align:left;">'.($email_address ? $email_address : 'not set yet').'</span>
	            </div>


		            <div style="clear:both;"><br></div>
		</td>

		  </tr>
		  </tbody></table>
		</td>
		</tr>';
                            $html_msg['EMAIL_FOTTER'] = EMAIL_FOOTER_INFO;
                            //zen_mail($sales_email, $sales_email, 'Customer Info', $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'contact_us');

                        }

                        //end
                        //set cookie for customer_id ******************************

                        require_once DIR_WS_CLASSES . 'set_cookie.php';

                        $Encryption = new Encryption;
                        //设置登陆session cookie
                        $cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
                        setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*365 ,"/");


                        $_SESSION ['customer_first_name'] = $first_name;
                        $_SESSION['customers_email_address'] = $email;

                        $facebook_info = array(
                            'facebook_email' => $email,
                            'facebook_name' => $name,
                            'facebook_id' => $id,
                            'customers_id' => $_SESSION['customer_id'],
                        );
                        //E($facebook_info);
                       // zen_db_perform('customers_social_media_facebook_info', $facebook_info);
                    }
                    exit(json_encode('ok'));
                    //header('Location: http://www.fiberstore.com');
                }
                break;

		}
}
?>