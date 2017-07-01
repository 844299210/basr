<?php
if(isset($_GET['request_type'])){
	$debug = false;
	require 'includes/application_top.php';
	if (isset($_POST['securityToken']) && $_SESSION['securityToken'] == $_POST['securityToken']){
		switch ($_GET['request_type']){
			case 'fs_ajax_login':

  		        login_delCache(DIR_FS_CATALOG.'cache/products',1);

			 	$email_address = $_POST['email_address'];
                $email_address = zen_db_prepare_input($email_address);
			 	$password = $_POST['password'];

			 	$result = $db->Execute("select customers_id, customers_firstname, customers_lastname,customers_password as password,customers_default_address_id from customers where customers_email_address regexp'".$email_address."'");
			 	if ($result->RecordCount() && zen_validate_password($password,$result->fields['password']) && $result->fields['customers_id'] != 17377) {
			 	    $_SESSION['customer_id'] = $result->fields['customers_id'];
/*                    $cid = $_SESSION['customer_id'];
			 	    $sql="SELECT admin_id FROM admin_to_customers WHERE customers_id=".$_SESSION['customer_id']."";
			 	    $res = $db->Execute($sql);
			 	    $admin_id=$res->fields['admin_id'];
			 	    if($admin_id){//判断管理员是否存在
			 	        $admin_sql="SELECT admin_name FROM admin WHERE admin_id=".$admin_id."";
			 	        $res = $db->Execute($admin_sql);
			 	        if(!$res->fields['admin_name']){
			 	            unset($admin_id);
			 	        }
			 	    }
			 	    if(!$admin_id){
                        //分配判断代码应置于插入数据之前,$email_address上面已经定义
                        require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));

			 	        if($admin_id){//判断管理员是否存在
			 	            $admin_sql="SELECT admin_name FROM admin WHERE admin_id=".$admin_id."";
			 	            $res = $db->Execute($admin_sql);
			 	            if(!$res->fields['admin_name']){
			 	                $admin_id=null;
			 	            }
			 	        }

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

		    <td style=" padding:0 30px; line-height:26px; font-size:11px;" colspan="2">
		    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<p><b>'.zen_get_admin_name($sales).'无业务员客户'.$email.'已自动分配给你，请进入http://cn.fiberstore.com:8006/New_Fiberstore_Manager_2015/sale_admin_review_customer.php 查询</b></p>
		    </td>

		   </tr>
		  </tbody></table>
		 </td>
		</tr>';
			 	            $html_msg['EMAIL_FOTTER'] = EMAIL_FOOTER_INFO;
			 	            zen_mail($sales_email, $sales_email, '新分配信息', $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'contact_us');
			 	        }

			 	        //end
			 	    }*/
			 	    require_once DIR_WS_CLASSES . 'set_cookie.php';
		        	$Encryption = new Encryption;
		        	$cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
		        	setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*300 ,"/");

			 		$_SESSION['customer_first_name'] = $result->fields['customers_firstname'];
			 		$_SESSION['customers_email_address'] = $result->fields['customers_email_address'];
			 		$_SESSION['customer_default_address_id'] = $result->fields['customers_default_address_id'];
			 		exit('success');
			 	}else if ($result->RecordCount()){
			 		exit('error');
			 	}else {
			 		exit('noAccount') ;
			 	}
				break;
			case 'fs_ajax_regist':

				$email_address = $_POST['email_address_regist'];
                $email_address = zen_db_prepare_input($email_address);

				$passwords =$_POST['password_regist'];
				$password = zen_encrypt_password($passwords);


				//$telephone = mysql_real_escape_string ( $_POST ['phone_number'] );
				$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
				$user_ip_address = $_SERVER['REMOTE_ADDR'];
				$regist_from = mysql_real_escape_string($_POST['regist_from']);

				$email_format = (ACCOUNT_EMAIL_PREFERENCE == '1' ? 'HTML' : 'TEXT');
				$newsletter = (ACCOUNT_NEWSLETTER_STATUS == '1' || ACCOUNT_NEWSLETTER_STATUS == '0' ? false : true);
				$telephone = "";
				$nick = "";
				$fax = '';
				//add others fields in table costomers


			    if($_POST ['country']){
				$customer_country_id =mysql_real_escape_string ( $_POST ['country'] );
				}else{
				$customer_country_id = 223;

				}


				$customerDiscoveryTypeId = zen_db_prepare_input($_POST['customerDiscoveryTypeId']);
				$customer_newsletter = $_POST['customer_other'];


				//list($firstname, $lastname) = split ('[/. ]', $user_name);


				$firstname = mysql_real_escape_string  (trim($_POST ['customer_name'])) ;
				$lastname = mysql_real_escape_string  (trim($_POST ['last_name'])) ;

			    $customer_name = $firstname." ".$lastname;

/*
				if (strpos($customer_name, ' ')) {
					$lastname = substr($customer_name, strrpos($customer_name,' ')+1);
					$firstname =  substr($customer_name, 0,-strlen($lastname));
				}else{
					$firstname = $customer_name;
					$lastname = '';
				}
				*/

				//set customer name into session
				$_SESSION['name'] = $customer_name;




				$check_email_query = "select count(*) as total
				from " . TABLE_CUSTOMERS . "
				where customers_email_address = '" . zen_db_input ( $email_address ) . "'";
				$check_email = $db->Execute ( $check_email_query );
				if ($check_email->fields ['total'] > 0) {
					exit('error');
				}


            $customer_newsletter = 1;
            $customers_dob = date('Y-m-d H:i:s');
			$regist_sql = array(
						'customers_firstname' => $firstname,
						'customers_lastname' => $lastname,
						'customers_email_address' => $email_address,
						'customers_nick' => $nick,
						'customers_telephone' => $telephone,
						'customers_fax' => $fax,
						//'customers_newsletter' => ( int ) $newsletter,
						'customers_email_format' => $email_format,
						'customers_default_address_id' => 0,
						'customers_password' =>  $password ,
						'language_id' =>  (int)$_SESSION['languages_id'] ,
						'customers_newsletter' => $customer_newsletter ,
						'customers_company' => mysql_real_escape_string($customerDiscoveryTypeId),
						'customer_country_id' => $customer_country_id,
						'customers_dob' => $customers_dob ,
						'customers_authorization' => ( int ) CUSTOMERS_APPROVAL_AUTHORIZATION,
						'http_user_agent' => $http_user_agent,
						'user_ip_address' => $user_ip_address,
						'customers_regist_from' => $regist_from,

				);
                //分配判断代码应置于插入数据之前,$email_address上面已经定义
                require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));
                if($admin_id){
                    //邮箱匹配到了 标记老客户 用于统计
                    $regist_sql['is_old'] = $is_old;
                }

				zen_db_perform(TABLE_CUSTOMERS,$regist_sql);
				$_SESSION['customer_id'] = $db->Insert_ID();
		        $cid = $db->insert_ID();

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
					require_once DIR_WS_CLASSES . 'set_cookie.php';
						$Encryption = new Encryption;
						$cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
						setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*300 ,"/");

					$sql = "insert into " . TABLE_CUSTOMERS_INFO . "
							  (customers_info_id, customers_info_number_of_logons,
							   customers_info_date_account_created, customers_info_date_of_last_logon)
				  values ('" . ( int ) $_SESSION ['customer_id'] . "', '1', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";

					$db->Execute ($sql);


				if(isset($_GET['type'])){
					if($_GET['type'] == 'link'){

						$sql_data_array = array(
							'customers_id' => $_SESSION['customer_id'],
							'customers_name' => zen_get_customers_firstname($_SESSION['customer_id']) . ' ' . zen_get_customers_lastname($_SESSION['customer_id']),
							'customers_email_address' =>zen_get_customer_name_email($_SESSION['customer_id'])
						);
						$orders_id = $_GET['orders_id'];
						$result = $db->getAll("select create_orders_id from create_order_to_customer where customers_email = '$email_address' and  orders_id = '$orders_id'");
						if($result){
							if($orders_id){
								zen_db_perform(TABLE_ORDERS, $sql_data_array,'update','orders_id='.$orders_id);
							}
						}
					}
				}



					echo 'success';exit;


				break;

			case 'fs_review_like_or_not':
				$type = $_POST['type'];
				$rID = $_POST['rID'];
				$ip = $_SERVER['REMOTE_ADDR'];
				$count = 1;

				require ('fs_ajax/functions_fs_reviews.php');
				if(!empty($ip)){
					$ip_sql = "select id,ip from reviews_geust_ip where source_type=0 and comments_id=".$rID." and ip='".$ip."' and type!=".$type." and add_time=curdate() and language_id=".(int)$_SESSION['languages_id'];
					$result = $db->Execute($ip_sql);
					if(!$result->EOF){
						return false;
					}else{
						$ip_query = "select id,ip from reviews_geust_ip where source_type=0 and comments_id=".$rID." and ip='".$ip."' and type=".$type." and add_time=curdate() and language_id=".(int)$_SESSION['languages_id'];
						$res = $db->Execute($ip_query);
						if(!$res->EOF){
							if (is_exist_reviews_valuation($rID)){
								$like_bad = $db->Execute("select r_like,r_bad from reviews_like_or_not where reviews_id=".$rID);
								$r_like = $like_bad ->fields['r_like'];
								$r_bad = $like_bad ->fields['r_bad'];
								if (1 == $type){
									if($r_like>0){
										$fs_update_column_sql = ' r_like = r_like-1 ';
										
									}else{
										$fs_update_column_sql = ' r_like = 0 ';
									}
								}else if (0 == $type){
									if($r_bad>0){
										$fs_update_column_sql = ' r_bad = r_bad-1 ';
										
									}else{
										$fs_update_column_sql = ' r_bad = 0 ';
									}
								}
								$fs_query = "update reviews_like_or_not set " . $fs_update_column_sql. " where reviews_id = ".(int)$rID;
								$db->Execute($fs_query);
								$db->Execute("delete from reviews_geust_ip where id=".$res->fields['id']);
								$count = get_reviews_count($rID,$type);
								echo '{"rID":"'.$rID.'","num":"'.$count.'","type":"0"}';
							}else{
								
								echo '{"rID":"0","num":"0","type":"0"}';
							}
						}else{
							
							$ip_array=array('comments_id'=>$rID,'ip'=>$ip,'type'=>$type,'source_type'=>0,'add_time'=>'now()','language_id'=>(int)$_SESSION['languages_id']);
							zen_db_perform('reviews_geust_ip',$ip_array);
						
							if (is_exist_reviews_valuation($rID)){
								if (1 == $type) {
									$fs_update_column_sql = ' r_like = r_like+1 ';
								}else if (0 == $type){
									$fs_update_column_sql = ' r_bad = r_bad+1 ';
								}
								$fs_query = "update reviews_like_or_not set " . $fs_update_column_sql. " where reviews_id = ".(int)$rID;
								$db->Execute($fs_query);
								$count = get_reviews_count($rID,$type);
								echo '{"rID":"'.$rID.'","num":"'.$count.'","type":"1"}';
							}else {
								$arr1 = array('reviews_id' => $rID);
								if (1 == $type) {
									$arr2 = array('r_like'=> 1,'r_bad'=> 0);
								}else if (0 == $type){
									$arr2 = array('r_like'=> 0,'r_bad'=> 1);
								}
								zen_db_perform('reviews_like_or_not',array_merge($arr1,$arr2));
								echo '{"rID":"'.$rID.'","num":"'.$count.'","type":"1"}';
							}
						}
					}
				}
			break;
				
			case 'fs_comments_like_or_not':
				$type = $_POST['type'];
				$cID = $_POST['cID'];
				$ip = $_SERVER['REMOTE_ADDR']; 
				$count = 1;
				
				require ('fs_ajax/functions_fs_reviews.php');
				if(!empty($ip)){
					$ip_sql = "select id from reviews_geust_ip where source_type=1 and comments_id=".$cID." and ip='".$ip."' and type!=".$type." and add_time=curdate() and language_id=".(int)$_SESSION['languages_id'];
					$result = $db->Execute($ip_sql);
					if(!$result->EOF){
						return false;
					}else{
						$ip_query = "select id from reviews_geust_ip where source_type=1 and comments_id=".$cID." and ip='".$ip."' and type=".$type." and add_time=curdate() and language_id=".(int)$_SESSION['languages_id'];
						$res = $db->Execute($ip_query);
						if(!$res->EOF){
							if (is_exist_comments_valuation($cID)){
								$like_bad = $db->Execute("select r_like,r_bad from reviews_comments_like_or_not where comments_id=".$cID);
								$r_like = $like_bad ->fields['r_like'];
								$r_bad = $like_bad ->fields['r_bad'];
								if (1 == $type){
									if($r_like>0){
										$fs_update_column_sql = ' r_like = r_like-1 ';
										
									}else{
										$fs_update_column_sql = ' r_like = 0 ';
									}
								}else if (0 == $type){
									if($r_bad>0){
										$fs_update_column_sql = ' r_bad = r_bad-1 ';
									}else{
										$fs_update_column_sql = ' r_bad = 0 ';
									}
								}
								$fs_query = "update reviews_comments_like_or_not set " . $fs_update_column_sql. " where comments_id = ".(int)$cID;
								$db->Execute($fs_query);
								$db->Execute("delete from reviews_geust_ip where id=".$res->fields['id']);
								$count = get_comments_count($cID,$type);
								echo '{"cID":"'.$cID.'","num":"'.$count.'","type":"0"}';
							}else{
								echo '{"cID":"0","num":"0","type":"0"}';
							}
						}else{
						
							$ip_array=array('comments_id'=>$cID,'ip'=>$ip,'type'=>$type,'source_type'=>1,'add_time'=>'now()','language_id'=>(int)$_SESSION['languages_id']);
							zen_db_perform('reviews_geust_ip',$ip_array);

							if (is_exist_comments_valuation($cID)){
								if (1 == $type) {
									$fs_update_column_sql = ' r_like = r_like+1 ';
								}else if (0 == $type){
									$fs_update_column_sql = ' r_bad = r_bad+1 ';
								}
								$fs_query = "update reviews_comments_like_or_not set " . $fs_update_column_sql. " where comments_id = ".(int)$cID;
								$db->Execute($fs_query);
			
								//get revies'count
								$count = get_comments_count($cID,$type);
								//echo $count;
			
								echo '{"cID":"'.$cID.'","num":"'.$count.'","type":"1"}';
							}else {
								$arr1 = array('comments_id' => $cID);
								if (1 == $type) {
									$arr2 = array('r_like'=> 1,'r_bad'=> 0);
								}else if (0 == $type){
									$arr2 = array('r_like'=> 0,'r_bad'=> 1);
								}
								zen_db_perform('reviews_comments_like_or_not',array_merge($arr1,$arr2));
								echo '{"cID":"'.$cID.'","num":"'.$count.'","type":"1"}';
							}
						}
					}
				}
			break;	

			case 'cart_num':
					$type = $_POST['type'];
					$p_id = $_POST['p_id'];
					$p_num = $_POST['p_num'];

					$num = 1;
					require ('fs_ajax/functions_fs_reviews.php');
					if (1 == $type) {
						$cart_num = 'customers_basket_quantity = customers_basket_quantity+1';
					}else if (0 == $type){
						$cart_num = 'customers_basket_quantity = customers_basket_quantity-1';
					}
					$sql = "update " . TABLE_CUSTOMERS_BASKET . " set ".$cart_num." where products_id = '" . (int)$p_id . "'";
					$db->Execute($sql);

					$num = get_customer_quantity($p_id,$type);

					echo '{"p_id":"'.$p_id.'","count":"'.$num.'"}';
					//exit('success');
			break;

		}
	}
}