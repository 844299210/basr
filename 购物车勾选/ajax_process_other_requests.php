<?php
if(isset($_GET['request_type'])){
	$debug = false;
	require 'includes/application_top.php';
	
	switch ($_GET['request_type']){
		case 'check_inquiry_mail':
			$get_count = $db->Execute("select count(products_price_inquiry_id) as total from " . TABLE_PRICE_INQUIRY . " WHERE
				products_price_inquiry_email = '".mysql_real_escape_string($_POST['email'])."' and language_id = ".(int)$_SESSION['languages_id']."
				and is_blacklist = 1       ");
			if ($get_count->RecordCount() && $get_count->fields['total']){
			exit('ok');
			}else exit('error');
		break;
		
		case 'save_customer_select':
			 require_once DIR_WS_CLASSES .'set_cookie.php';
             $Encryption = new Encryption;
			 $countryCode_encrypt = $Encryption->_encrypt(($_POST['country']));
	         setcookie("countries_iso_code",$countryCode_encrypt,time()+86400 ,"/");
			  $_SESSION['currency'] = $_POST['currency'];
			  $_SESSION['choice_language'] = $_POST['choice_language'];
			  $_SESSION['countries_iso_code'] = $_POST['country'];
			  $_SESSION['ship_country'] = fs_get_country_id_of_code($_POST['country']);
			break;	
		
		case 'subscribe';
			$get_count = $db->Execute("select count(customers_subscribe_id) as total from " . TABLE_SUBSCRIBE . " WHERE 
				customers_email_address = '".mysql_real_escape_string($_POST['customers_email_address'])."' and language_id = ".(int)$_SESSION['languages_id']."       ");		
			if ($get_count->RecordCount() && $get_count->fields['total']){
				$res = $db->getAll("select * from unsubscribe_vistor where email_address = '".mysql_real_escape_string($_POST['customers_email_address'])."'");
				if($res){
					$db->Execute("delete from unsubscribe_vistor where email_address = '".$_POST['customers_email_address']."'");
					$_SESSION['newsletter_customers_email_address'] = mysql_real_escape_string($_POST['customers_email_address']);
					exit('subscribeOk');
				}else{
					exit('haveSubscribed');
				}
			}else{
				$_SESSION['newsletter_customers_email_address'] = mysql_real_escape_string($_POST['customers_email_address']);
				$customers_subscribe_data = array(
					'customers_name' => mysql_real_escape_string($_POST['customers_lastname']),
					'customers_email_address' => mysql_real_escape_string($_POST['customers_email_address']),
					'language_id' => (int)$_SESSION['languages_id'],
					'timeline'=>time()
			);
			zen_db_perform(TABLE_SUBSCRIBE, $customers_subscribe_data);
			if (0 < $db->insert_ID())exit('subscribeOk'); else exit('error');
			}
		break;
		case 'save_custoemr_visted':
			$new_customer_visited_page=$_SERVER['HTTP_REFERER'];
			$REMOTE_ADDR_ip=$_SERVER['REMOTE_ADDR'];
			$pd_id= isset($_POST['pd_id']) ? (int)$_POST['pd_id'] : 0;			
			
		    $ACCEPT_LANGUAGE_type=$_SERVER['HTTP_ACCEPT_LANGUAGE'];		
			if (preg_match("/[zh]{2}\-[cn|CN]{2}/", $ACCEPT_LANGUAGE_type)) {
                exit();
			}
			
			//  59.173.240.134  F3楼的ip
			if ($REMOTE_ADDR_ip) {		
			
//			require DIR_WS_CLASSES . 'customer_visited_pages.php';
//			$customer_visited = new customers_visited_pages();
//			$is_url = customers_visited_pages::store_customers_visited_pages($new_customer_visited_page);

			if ($pd_id > 0) {
			$sql="SELECT customers_visited_pages_id,visited_total,customers_id 
			      FROM customers_visited_pages 
			      WHERE use_ip ='".$REMOTE_ADDR_ip."' AND DATE(`visited_time`)=DATE(NOW()) AND products_id='". $pd_id ."' LIMIT 1" ;
			}else{
			$sql="SELECT customers_visited_pages_id,visited_total,customers_id 
			      FROM customers_visited_pages 
			      WHERE use_ip ='".$REMOTE_ADDR_ip."' AND DATE(`visited_time`)=DATE(NOW()) AND visited_page_url='". $new_customer_visited_page ."' LIMIT 1" ;
			}
			
		    $visited_result = $db->Execute($sql);	 			
		    if ($visited_result->RecordCount()) {
		    
			$visited_total=$visited_result->fields['visited_total'];
			$visited_id= $visited_result->fields['customers_visited_pages_id'];     
    		$customers_id = $visited_result->fields['customers_id'];   
		    
    		if ($customers_id) {
    		  $d_category = array('visited_total' =>$visited_total+1);
    		}else{
    		  $d_category = array('customers_id' => isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '','visited_total' =>$visited_total+1);
    		}
		    zen_db_perform(TABLE_CUSTOMERS_VISITED_PAGES, $d_category,'update','customers_visited_pages_id='.$visited_id); 
		    	
		    }else{
                  $d_category = array(   
		 				'customers_id' => isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : '',
		 				'visited_page_url' => $new_customer_visited_page,
		                'language_id' => $_SESSION['languages_id'],
		                'visited_time' => 'now()',
		                'use_ip' => $REMOTE_ADDR_ip,
		                'products_id' => $pd_id,
		 			);
		       zen_db_perform(TABLE_CUSTOMERS_VISITED_PAGES, $d_category);
		    }
			}
          exit();
		break;
		
		case 'postComment':
			$products_id = (int)$_POST['pid'];
			$reviews_id =(int)$_POST['rid'];
			$comment_cotent = $_POST['post_reply'];
			$customers_id = (isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : 0);
			if($customers_id){
			//$customers_name = (isset($_SESSION['name']) ? $_SESSION['name'] : '');
			$customers_name = (isset($_SESSION['customer_first_name']) ? $_SESSION['customer_first_name'] : '');
			$comment = array(
					'reviews_id' => $reviews_id,
					'products_id'=> $products_id,
					'status' => 1
					);
			zen_db_perform(TABLE_REVIEWS_COMMENTS, $comment);
			$cid = $db->insert_ID();
			$comment_description = array(
					'comments_id' => $cid,
					'customers_id' => $customers_id,
					'customers_name' => $customers_name,
					'comments_content'=>$comment_cotent,
					'date_added'=>'now()',
					'last_modified'=>'now()'
				);
			zen_db_perform(TABLE_REVIEWS_COMMENTS_DESCRIPTION, $comment_description);
			echo '{"rid":"'.$reviews_id.'","content":"'.$comment_cotent.'","name":"'.$customers_name.'","time":"'.date('F j, Y',time()).'"}';
			}

		break;
		
		case 'get_reviews_comments_desc':
			$products_id = (int)$_POST['pid'];
			$reviews_id =(int)$_POST['rid'];
			$sql = "select rc.comments_id,rcd.customers_name,rcd.comments_content,rcd.date_added from ".TABLE_REVIEWS_COMMENTS." as rc join ".TABLE_REVIEWS_COMMENTS_DESCRIPTION." as rcd on rc.comments_id = rcd.comments_id where rc.reviews_id = ".$reviews_id." AND rc.products_id = ".$products_id." ";
			$result = $db->Execute($sql);
		    if ($result->RecordCount()){
				while (!$result->EOF){
					//echo '{"rid":"'.$result->fields['comments_id'].'","content":"'.$result->fields['comments_content'].'","name":"'.$result->fields['customers_name'].'","time":"'.date('F j, Y',$result->fields['date_added']).'"}';
					$arr['list'][] = array(
						'rid' => $result->fields['comments_id'],
						'name' => $result->fields['customers_name'],
						'content' => $result->fields['comments_content'],
						'time' => date('F j, Y',strtotime($result->fields['date_added']))
					);
					$result->MoveNext();
				}
			}
			echo json_encode($arr);
		break;

		case 'get_two_categroy':
			if(isset($_POST['pid']) && !empty($_POST['pid'])){
				$pid = (int)$_POST['pid'];
                //优先调用自定义二级分类数据
                $custom_category = $db->getAll("select cid as id,categories_id,categories_name as name,categories_url as url from categories_left_display where parent_id=".$pid." and level_id=2 and language_id = ".$_SESSION['languages_id']." order by sort");
                if($custom_category){
                    $category_level2 = $custom_category;
                }else{
                    //$category_level2 = fs_get_subcategories($pid);
                }
				echo json_encode($category_level2);
			}
		break;

		case 'get_three_categroy':
			if(isset($_POST['pid']) && !empty($_POST['pid'])){
				$pid = (int)$_POST['pid'];

                //优先调用自定义三级分类数据
                $custom_category = $db->getAll("select cid as id,categories_id,categories_name as name,categories_url as url from categories_left_display where parent_id=".$pid." and level_id=3 and language_id = ".$_SESSION['languages_id']." order by sort");
                if($custom_category){
                    $category_level3 = $custom_category;
                }else{
                    $custom_category = $db->getAll("select categories_id from categories_left_display where cid=".$pid." and level_id=2 and language_id = ".$_SESSION['languages_id']." limit 1");
                    if($custom_category[0]['categories_id']){
                        $category_level3 = fs_get_subcategories($custom_category[0]['categories_id']);
                    }
/*                    if($pid == 3079){
                        $id = '3079,56,1114,2689';
                        $category_level3 = fs_get_subcategories_by_id($id);
                    }elseif($pid == 1071){
                        $id = '1071,889,1117,2691';
                        $category_level3 = fs_get_subcategories_by_id($id);
                    }elseif($pid == 2960){
                        $id = '2960,1037,1181';
                        $category_level3 = fs_get_subcategories_by_id($id);
                    }else{
                        $category_level3 = fs_get_subcategories($pid);
                    }*/
                }
				echo json_encode($category_level3);
			}
		break;
		
		case 'set_review':		//solution添加评论
			if(isset($_POST['data']) && !empty($_POST['data']) && !empty($_POST['solution_id'])){
				$review_content = $_POST['data'];
				$solution_id = (int)$_POST['solution_id'];
				$review_content = fliter_escape($review_content);
				$user_id = $_SESSION['customer_id'];
				$time = time();
				$language_id = $_SESSION['languages_id'];
				$review_array = array(
					'solution_id' =>$solution_id,
					'user_id'=>$user_id,
					'review_content'=>$review_content,
					'create_time'=>$time,
					'language_id'=>$language_id
				);																
				zen_db_perform('solutions_reviews',$review_array);
				$get_review_sql = "SELECT sr.review_id,sr.reply_id ,sr.solution_id,sr.user_id,c.customers_firstname as user_name,sr.praise_num,sr.review_content,sr.create_time FROM `solutions_reviews` as sr 
								left join `customers` as c on c.customers_id = sr.user_id
								WHERE sr.solution_id = $solution_id and sr.user_id = $user_id and sr.language_id = $language_id and sr.create_time = $time";
				$res = $db->Execute($get_review_sql);
				$reviews_nums = sizeof(get_solution_all_reviews($solution_id));
				while (!$res->EOF){
					$review_res = array(
						'r_id'=>$res->fields['review_id'],
						're_id'=>$res->fields['reply_id'],
						'so_id'=>$res->fields['solution_id'],
						'u_id'=>$res->fields['user_id'],
						'user_name'=>$res->fields['user_name'],
						'p_num'=>$res->fields['praise_num'],
						'r_content'=>$res->fields['review_content'],
						'time'=>date('M j,Y',$res->fields['create_time']),
						'reviews_nums'=>$reviews_nums
					);
					$res->MoveNext();
				}								
				echo json_encode($review_res);
			}
		break;
		
		case 'del_review':		//solution删除评论
			if(isset($_POST['data1']) && !empty($_POST['data1']) && !empty($_POST['data2'])){
				$review_id = (int)$_POST['data1'];
				$solution_id = (int)$_POST['data2'];
				$user_id = $_SESSION['customer_id'];
				$language_id = $_SESSION['languages_id'];
				$sql = "delete from `solutions_reviews` where review_id = ".$review_id." and solution_id = ".$solution_id." and user_id = ".$user_id." and language_id = ".$language_id;
				if($db->Execute($sql)){
					echo json_encode('Delete success');
				}else{
					echo json_encode('Delete failed');
				}
			}
		break;
		
		case 'reply_review':	//solution回复评论
			$review_content = fliter_escape($_POST['data1']);
			$reply_id = (int)$_POST['data2'];
			$solution_id = (int)$_POST['data3'];
			$user_id = (int)$_POST['data4'];
			$reply_name = fliter_escape($_POST['data5']);
			$time = time();
			$language_id = $_SESSION['languages_id'];
			if(isset($user_id) && !empty($review_content) && !empty($reply_id) && !empty($solution_id) && !empty($user_id) && !empty($reply_name)){
				$reply_array = array(
					'reply_id'=>$reply_id,
					'solution_id' =>$solution_id,
					'user_id'=>$user_id,
					'review_content'=>$review_content,
					'create_time'=>$time,
					'language_id'=>$language_id
				);
				zen_db_perform('solutions_reviews',$reply_array);				
				$get_reply_sql = "SELECT sr.review_id,sr.reply_id ,sr.solution_id,sr.user_id,c.customers_firstname as user_name,sr.praise_num,sr.review_content,sr.create_time FROM `solutions_reviews` as sr 
								left join `customers` as c on c.customers_id = sr.user_id
								WHERE sr.solution_id = $solution_id and sr.user_id = $user_id and sr.language_id = $language_id and sr.create_time = $time";
				$res = $db->Execute($get_reply_sql);												
				while (!$res->EOF){
					$review_res = array(
						'r_id'=>$res->fields['review_id'],
						're_id'=>$res->fields['reply_id'],
						'so_id'=>$res->fields['solution_id'],
						'u_id'=>$res->fields['user_id'],
						'user_name'=>$res->fields['user_name'],
						'reply_name'=>$reply_name,
						'p_num'=>$res->fields['praise_num'],
						'r_content'=>$res->fields['review_content'],
						'time'=>date('M j,Y',$res->fields['create_time']),
					);
					$res->MoveNext();
				}								
				echo json_encode($review_res);
			}
		break;
		
		case 'click_praise':	//solution评论点赞
			$review_id = (int)$_POST['data'];
			if(isset($review_id) && !empty($review_id)){
				$language_id = $_SESSION['languages_id'];
				$sql = "UPDATE `solutions_reviews` SET `praise_num`= `praise_num` + 1 WHERE `review_id` = {$review_id} and `language_id` = {$language_id}";	
				$db->Execute($sql);
				$get_new_praise = "SELECT `praise_num` FROM `solutions_reviews` WHERE `review_id` = {$review_id} and `language_id` = {$language_id}";
				$res = $db->Execute($get_new_praise);
				if($res->RecordCount()){
					$num = $res->fields['praise_num'];
					echo json_encode($num);
				}																			
			}
		break;
		
		case 'click_thank':		//solution点like
			$solution_id = (int)$_POST['data'];
			if(isset($solution_id) && !empty($solution_id)){
				$language_id = $_SESSION['languages_id'];
				$add_thank_sql = "UPDATE `solution_method` SET `total_thanks`=`total_thanks`+1 WHERE `solution_id` = {$solution_id} and `language_id` = {$language_id}";	
				$res = $db->Execute($add_thank_sql);
				$get_new_thank = "SELECT `total_thanks` FROM `solution_method` WHERE `solution_id` = {$solution_id} and `language_id` = {$language_id}";
				$res = $db->Execute($get_new_thank);
				if($res->RecordCount()){
					$thank_num = $res->fields['total_thanks'];
					echo json_encode($thank_num);
				}
			}
		break;
		
		case 'n_page':			//solution下一页
			$solution_id = (int)$_POST['data'];
			$last_page_num = (int)$_POST['page'];
			if(isset($solution_id) && !empty($solution_id) && !empty($last_page_num)){
				$all_reviews = get_solution_all_reviews($solution_id);
				$all_reviews_num = sizeof($all_reviews);//总共有多少条数据
				$how_page = ceil($all_reviews_num/10);	//总共有多少页
				if($all_reviews_num > 10){
					$total_page_data = array();
					for($i=0;$i<$how_page;$i++){
						$total_page_data['review_page'.$i] = array_slice($all_reviews,$i*10,10,true);
					}
					foreach($total_page_data as $key=>$val){
						foreach($val as $k=>$v){
							if($k == ($last_page_num+1)){
								$curPage_array = $val;	//当前页的评论数据
								break;
							}
						}
					}
					
					foreach($curPage_array as $k=>$v){
						$time = date('M j,Y',$v['create_time']);
						$curPage_array[$k]['create_time'] = $time;
					}
					
					$curPage_arr['solution_id'] = $solution_id;
					$curPage_arr['total_page_num'] = $how_page;
					$new_page = array();
					$new_page[] = $curPage_array;
					$new_page[] = $curPage_arr;
					echo json_encode($new_page);
				}else{
					echo json_encode('Not next page!');
				}
			}
		break;
		
		case 'p_page':			//solution上一页
			$solution_id = (int)$_POST['data'];
			$first_page_num = (int)$_POST['page'];
			if(isset($solution_id) && !empty($solution_id) && !empty($first_page_num) && $first_page_num >= 10){
				$all_reviews = get_solution_all_reviews($solution_id);
				$all_reviews_num = sizeof($all_reviews);//总共有多少条数据
				$how_page = ceil($all_reviews_num/10);	//总共有多少页
				if($all_reviews_num > 10){
					$total_page_data = array();
					for($i=0;$i<$how_page;$i++){
						$total_page_data['review_page'.$i] = array_slice($all_reviews,$i*10,10,true);
					}
					foreach($total_page_data as $key=>$val){
						foreach($val as $k=>$v){
							if($k == ($first_page_num-1)){
								$curPage_array = $val;	//当前页的评论数据
								break;
							}
						}
					}
					
					foreach($curPage_array as $k=>$v){
						$time = date('M j,Y',$v['create_time']);
						$curPage_array[$k]['create_time'] = $time;
					}
					
					$curPage_arr['solution_id'] = $solution_id;
					$curPage_arr['total_page_num'] = $how_page;
					$new_page = array();
					$new_page[] = $curPage_array;
					$new_page[] = $curPage_arr;
					echo json_encode($new_page);
				}else{
					echo json_encode('Not prev page!');
				}
			}
		break;
		
		case 'view_dialog':		//solution查看对话
			$review_id = (int)$_POST['data'];
			if(isset($review_id) && !empty($review_id)){
				$view_dialog_arr = getViewDialog($review_id,$review_id);
				sort($view_dialog_arr);
				foreach($view_dialog_arr as $k=>$v){
					$time = date('M j,Y',$v['create_time']);
					$view_dialog_arr[$k]['create_time'] = $time;
				}
				echo json_encode($view_dialog_arr);
			}else{
				echo json_encode('Not dialog!');
			}
		break;
		
		case 'show_shara':		//保存banner图内容
			if(!empty($_POST['data']) && !empty($_POST['data2'])){
				$banner_content = $_POST['data'];
				$a_id = $_POST['data2'];
				$banner_content_arr = array(
					'banner_content'=>$banner_content,
				);															
				zen_db_perform('support_articles_description', $banner_content_arr,'update','support_articles_id='.$a_id.' and language_id = '.$_SESSION['languages_id']);
			}
		break;
		
		case 'google_ads':
			//fallwind 2016.10.14  如果是通过Google广告进来的，就保存ip
			if(!empty($_SESSION['google_ads']) && isset($_SESSION['google_ads']) ){
				//$customer_come_ip = getCustomersIP();
				//setComeIp($customer_come_ip,2);
				switch($_GET['type']){
					case 'livechat_online':
						$customer_come_ip = getCustomersIP();
						setComeIpByLivechatOnline($customer_come_ip);
					break;
					case 'livechat_email':
						$name = fliter_escape($_GET['name']);
						$email = fliter_escape($_GET['email']);
						$number = fliter_escape($_GET['number']);
						$customer_come_ip = getCustomersIP();
						setComeIpByLivechatEmail($customer_come_ip,$name,$email,$number);
					break;
					case 'livechat_phone':
						$name = fliter_escape($_GET['name']);
						$email = fliter_escape($_GET['email']);
						$number = fliter_escape($_GET['number']);
						$customer_come_ip = getCustomersIP();
						setComeIpByLivechatPhone($customer_come_ip,$name,$email,$number);
					break;
				}
			}
		break;
	}
	
	
	if (isset($_POST['securityToken']) && $_SESSION['securityToken'] == $_POST['securityToken']){
		switch ($_GET['request_type']){
			case 'send_email':
				if ($debug){
					$file = DIR_FS_SQL_CACHE.'/ajax-send-mail-'.time().'.log';
					$handle = fopen($file,'a+');
					@chmod($file, 777);
				}
				function zen_check_order_exist($orders_id){
					global $db;
					$get_info = $db->Execute("select count(orders_id) as total from " . TABLE_ORDERS ." where orders_id = " .(int)$orders_id);
					return  ($get_info->fields['total'] ? true : false);
				}
				$orders_id = $_POST['orders_id'];
				if ($debug) fwrite($handle, $orders_id."\n");
				$complete_mail = false;
				if (isset($orders_id) && zen_check_order_exist($orders_id)){
					require (DIR_WS_CLASSES.'order.php');
					$order = new order($orders_id);/*for paypal load shipping */
					//require 'includes/languages/english/checkout_process.php';
					if ($debug) fwrite($handle, 'before send mail'."\n");
					if(isset($_GET['type']) && $_GET['type'] == 'gc'){
						$order->send_fs_credit_card_order_email(false);
					}elseif($_GET['type'] == 'bpay' || $_GET['type'] == 'eNETS' || $_GET['type'] == 'iDEAL' || $_GET['type'] == 'SOFORT'){
						$order->send_fs_gc_order_email($_GET['type']);
				    }else{
						$order->send_fs_order_email($complete_mail);
					}
					if ($debug){ fwrite($handle, 'after send mail'."\n");
						fclose($handle);
					}
				}

				break;

			case 'save_customer_po':
                $_SESSION['customer_po'] = $_POST['customer_po'];
                $_SESSION['customer_remarks'] = $_POST['customer_remarks'] ;
				$_SESSION['products_custom'] = $_POST['products_custom'];
				$_SESSION['purchase_order_num'] = $_POST['purchase_order_num'];
			    break;		
				
			case 'create_order':
				if ($debug){
					$file = DIR_FS_SQL_CACHE.'/ajax-create-order-'.time().'.log';
					$handle = fopen($file,'a+');
					@chmod($file, 777);
				}
				require (DIR_WS_CLASSES.'shipping.php');
				$shipping = new shipping();
				require (DIR_WS_CLASSES . 'payment.php');
				$payment = new payment();
				require (DIR_WS_CLASSES.'order.php');
				$order = new order();/*for paypal load shipping */
				if ($debug)fwrite($handle, ' init order - '.time()." \n");
				if(empty($_SESSION['shipping'])){
					echo '{"error":"err"}';exit;
				}				
				$shipping = new shipping($_SESSION['shipping']);
				if ($debug)fwrite($handle, ' init shipping - '.time()." \n");
				require (DIR_WS_CLASSES . 'order_total.php');
				$order_total_modules = new order_total();
				$order_totals = $order_total_modules->process();
				if ($debug)fwrite($handle, ' init order totals - '.time()." \n");
				$_SESSION['payment'] = isset($_SESSION['payment']) ? $_SESSION['payment'] : 'paypal';
				$_SESSION['payment'] = $_SESSION['payment'] ? $_SESSION['payment'] : 'paypal';
				$payment = new payment($_SESSION['payment']);
				if ($debug)fwrite($handle, ' init payment - '.time()." \n");
				if($_SESSION['cart']->get_products()){
				$order_id = $order->create($order_totals);
		        $customers_po = $_POST['customer_po'];
		        if($customers_po){
				$sql= " update orders set customers_po = '". $customers_po ."' where orders_id =".$order_id;
				$db->Execute($sql);
				}
				$client_type = $_POST['client_type'];
				if(!empty($client_type) && $client_type === 'phone'){
					$sql= " update orders set client_type = '". $client_type ."' where orders_id =".$order_id;
					$db->Execute($sql);
				}
				//订单备注保存
				$customer_remarks=zen_db_input($_POST['customer_remarks']);
				if($customer_remarks){
				  $sql='update orders set customers_remarks = "'.$customer_remarks.'" where orders_id ='.$order_id;
				  $db->Execute($sql);
				}
				$products_custom = $_POST['products_custom'];
				if($products_custom){
				  $sql='update orders set products_custom = "'.$products_custom.'" where orders_id ='.$order_id;
				  $db->Execute($sql);
				}
				$_SESSION['order_id'] = $order_id;
				$_SESSION['req_qreoid'] = $order_id;
				if ($debug)fwrite($handle, ' create  order -order number is '.$invoice.' - '.time()." \n");
				$order->create_add_products($order_id);
				if ($debug)fwrite($handle, ' add products to  order  - '.time().'\n');
				$get_orders_number = $db->Execute("select orders_number from " . TABLE_ORDERS . " where orders_id = ". $order_id);
				$invoice = $get_orders_number->fields['orders_number'];
				if ($debug)fwrite($handle, ' send order email - '.time()." \n");
				$action = $process_string = '';
			    $_SESSION['cart']->reset(true);
			    
			if ('paypal' == $_SESSION['payment']){
					if ($debug)fwrite($handle, ' process paypal action - '.time()." \n");
					$class = & $_SESSION['payment'];
					$action = $GLOBALS[$class]->form_action_url;
					if ($debug)fwrite($handle, 'action url: '.$action.' - '.time()." \n");
					$process_string = $GLOBALS[$class]->process_string();
					$process_string .= '::invoice--'.$invoice;

					if ($debug)fwrite($handle, '$process_string: '.$process_string.' - '.time()." \n"); ;

				if($debug){ fclose($handle);@chmod($file, 777);}

				echo '{"type":"'.$_SESSION['payment'].'","url":"'.$action.'","params":"'.$process_string.'","o_id":"'.(int)$_SESSION['order_id'].'"}';

				}elseif(in_array($_SESSION['payment'],array('bpay','eNETS','iDEAL','SOFORT'))){

					$order = new order($order_id);

					$class = & $_SESSION['payment'];

					$action = $GLOBALS[$class]->form_action_url;

					if ($debug)fwrite($handle, 'action url: '.$action.' - '.time()." \n");

					$process_string = $GLOBALS[$class]->process_string();
					echo '{"params":"'.$process_string.'","o_id":"'.(int)$order_id.'"}';
				}elseif('globalcollect' == $_SESSION['payment']){
					unset($_SESSION['sendto']);
					unset($_SESSION['billto']);
					unset($_SESSION['shipping']);
					unset($_SESSION['payment']);
					unset($_SESSION['comments']);
					//unset($_SESSION['cart']);
					echo $order_id;exit;
				}
				}else{
						if('paypal' == $_SESSION['payment']){
							echo '{"error":"err"}';exit;
						}	
				}
				
						//fallwind 2016.10.14	下单成功时，判断$_SESSION['google_ads']是否有值，有值就记录其ip，同时记录该值
				if(!empty($_SESSION['google_ads']) && isset($_SESSION['google_ads']) ){
					$customer_come_ip = getCustomersIP();
					setComeIpByOrders($customer_come_ip,$_SESSION['order_id']);
				}
				
				break;

			case  'select_address':	
					if(isset($_POST['address_book_id'])){
						$book_id=$_POST['address_book_id'];
						require DIR_WS_CLASSES . 'customer_account_info.php';
                         $customer_info = new customer_account_info();
						 $use_address=$customer_info->get_select_address($book_id);
						echo $use_address_string.= '{"address_book_id":"'.$use_address['address_book_id'].'",
						"entry_firstname":"'.$use_address['entry_firstname'].'",
						"entry_lastname":"'.$use_address['entry_lastname'].'",
						"entry_street_address":"'.$use_address['entry_street_address'].'",
						"entry_suburb":"'.$use_address['entry_suburb'].'",
						"entry_city":"'.$use_address['entry_city'].'",
						"entry_country":{"entry_country_id":"'.$use_address['entry_country']['entry_country_id'].'",
						"entry_country_name":"'.$use_address['entry_country']['entry_country_name'].'"},
						"entry_state":"'.$use_address['entry_state'].'",
						"entry_zone_id":"'.$use_address['entry_zone_id'].'",
						"entry_postcode":"'.$use_address['entry_postcode'].'",
						"entry_telephone":"'.$use_address['entry_telephone'].'"}';
					}
	          break;
				
			case 'paypal_submit':

				$orders_id = $_POST['orders_id'];

				$_SESSION['req_qreoid'] = $orders_id;

				require (DIR_WS_CLASSES . 'payment.php');

				$payment = new payment('paypal');

				require (DIR_WS_CLASSES.'order.php');

				$order = new order($orders_id);

				$action = $GLOBALS['paypal']->form_action_url;

				$process_string = $GLOBALS['paypal']->process_account_paypal_submit();

				$process_string .= '&invoice--'.$order->info['orders_number'];

				echo '{"url":"'.$action.'","params":"'.$process_string.'","o_id":"'.(int)$orders_id.'"}';

				break;

	case 'set_create_account':


										$entry_firstname = ($_POST['billing_firstname']);

										$entry_lastname = ($_POST['billing_lastname']);

										$entry_company = ($_POST['billing_company']);

										$entry_street_address = ($_POST['billing_street_address']);

										$entry_suburb = ($_POST['billing_suburb']);

										$entry_city = ($_POST['billing_city']);

										$entry_country_id = ($_POST['billing_country_id']);

										$entry_state = ($_POST['billing_state']);

										$billing_us_state = ($_POST['billing_us_state']);

										if($_POST['billing_country_id'] == 223){

											$entry_state =  $billing_us_state;

										}

										$entry_postcode = ($_POST['billing_postcode']);

										$entry_telephone = ($_POST['billing_telephone']);

										$email_address = ($_POST['email_address']);

										$password1 = ($_POST['password1']);
										$password2 = ($_POST['password2']);

										

				$email_format = (ACCOUNT_EMAIL_PREFERENCE == '1' ? 'HTML' : 'TEXT');
				$http_user_agent =$_SERVER["HTTP_USER_AGENT"];
				$user_ip_address =$_SERVER["REMOTE_ADDR"];
				
				$billto = $_SESSION['billto'] > 0  ? $_SESSION['billto']:$_SESSION['billtoG'];
				$sql_data_array = array (
		        'customers_firstname' => mysql_real_escape_string($entry_firstname),
				'customers_lastname' =>  mysql_real_escape_string($entry_lastname),
				'customers_email_address' => $email_address,
				'customers_telephone' => mysql_real_escape_string($entry_telephone),
				'customers_newsletter' => 1,
				'customers_email_format' => $email_format,
				'customers_default_address_id' => 0,
				'customers_password' => zen_encrypt_password ( $password1 ),
				'customers_authorization' => ( int ) CUSTOMERS_APPROVAL_AUTHORIZATION,
				'language_id' => (int)$_SESSION['languages_id'],
		        'customer_country_id'=>$entry_country_id,
		        'hear_us' =>'',
				'customers_default_billing_address_id' => $billto,
		        'customer_other_content'=>'',
		        'http_user_agent' =>$http_user_agent,
		        'user_ip_address' =>$user_ip_address,
		        'customers_regist_from' => 'guest',
					);

				
	if (strlen ( $email_address ) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
					$error = true;
					//$messageStack->add_session (FILENAME_REGIST, ENTRY_EMAIL_ADDRESS_ERROR );
					echo ENTRY_EMAIL_ADDRESS_ERROR;exit;
				} else if (zen_validate_email ( $email_address ) == false) {
					$error = true;
					//$messageStack->add_session (FILENAME_REGIST, ENTRY_EMAIL_ADDRESS_CHECK_ERROR );
					echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR;exit;
				} else {
					$check_email_query = "select count(customers_id) as total         from " . TABLE_CUSTOMERS . "
							 where customers_email_address = '" .  $email_address . "'";
					$check_email = $db->Execute ( $check_email_query );
					if ($check_email->fields['total'] > 0){
						$error = true;
						//echo 'Our system already has a record of that email address - please try logging in with that email address,If you do not use that address any longer you can correct it in the My Account area.';exit;
						//$messageStack->add_session ( FILENAME_REGIST,'<div id="fiberstore_message" class="tishi_02 display_none">Our system already has a record of that email address - please try logging in with that email address.<br /> If you do not use that address any longer you can correct it in the My Account area.</div>' );
																		$email_address = zen_db_prepare_input($email_address);
																	  $password = zen_db_prepare_input($password1);

																	
																		// Check if email exists
																		$check_customer_query = "SELECT customers_id, customers_firstname, customers_lastname, customers_password,
																										customers_email_address, customers_default_address_id,
																										customers_authorization, customers_referral
																							   FROM " . TABLE_CUSTOMERS . "
																							   WHERE customers_email_address = :emailAddress";

																		$check_customer_query  =$db->bindVars($check_customer_query, ':emailAddress', $email_address, 'string');
																		$check_customer = $db->Execute($check_customer_query);


																		if ($check_customer->RecordCount() < 1) {

																			exit(FS_LOGIN_EMAIL_ERROR);
																		}
																		 elseif ($check_customer->fields['customers_authorization'] == '4') {
																			exit(TEXT_LOGIN_BANNED);
																		}else {
																		  // Check that password is good
																		  if (!zen_validate_password($password, $check_customer->fields['customers_password'])) {
																			   exit('Password input error');
																		  }else {
																			if (SESSION_RECREATE == 'True') {
																			  zen_session_recreate();
																			}

																			$_SESSION['customer_id'] = $check_customer->fields['customers_id'];
																			$_SESSION['customer_default_address_id'] = $check_customer->fields['customers_default_address_id'];
																			$_SESSION['customers_authorization'] = $check_customer->fields['customers_authorization'];
																			$_SESSION['customer_first_name'] = $check_customer->fields['customers_firstname'];
																			$_SESSION['customer_last_name'] = $check_customer->fields['customers_lastname'];
																			$_SESSION['customers_email_address'] = $check_customer->fields['customers_email_address'];

																			get_customers_member_level();

																			$LoginRember = zen_db_prepare_input($_POST['LoginRember']);
																			
																			$_SESSION['name'] = $check_customer->fields['customers_firstname'] .' '. $check_customer->fields['customers_lastname'];
																			
																	$last_address=='';
																			$sql = "UPDATE " . TABLE_CUSTOMERS_INFO . "
																				  SET customers_info_date_of_last_logon = now(),
																				customers_info_address_of_last_logon = '".$last_address."',
																					  customers_info_number_of_logons = customers_info_number_of_logons+1
																				  WHERE customers_info_id = :customersID";

																			$sql = $db->bindVars($sql, ':customersID',  $_SESSION['customer_id'], 'integer');
																			$db->Execute($sql);


																			 $db->Execute("update address_book set customers_id = '".$_SESSION['customer_id']."' where address_book_id='".$_SESSION['billto']."'");
																				$list = $db->getAll("select guest_id from customer_of_guest where email_address = '".$email_address."' order by guest_id DESC limit 1");
																			if($list){
																						 $db->Execute("update address_book set customers_id = '".$_SESSION['customer_id']."' where customers_guest_id='".$list[0]['guest_id']."'");
																						 $db->Execute("update orders set customers_id = '".$_SESSION['customer_id']."' where guest_id='".$list[0]['guest_id']."'");
																			}

																			if (SHOW_SHOPPING_CART_COMBINED > 0) {
																			  $zc_check_basket_before = $_SESSION['cart']->count_contents();
																			}

																			$_SESSION['cart']->restore_contents();

																			 }
																		}
																	  
																	
					}else{

				require(DIR_WS_MODULES . zen_get_module_directory('auto_given.php'));
                if($admin_id){
                    //邮箱匹配到了 标记老客户 用于统计
                    $sql_data_array ['is_old'] = $is_old;
                }
                zen_db_perform (TABLE_CUSTOMERS, $sql_data_array );
                $_SESSION['customer_id'] = $db->Insert_ID();
                $cid = $db->insert_ID();
				if($admin_id){
					$customers_id=$cid;

					$sql='INSERT INTO admin_to_customers(admin_id,customers_id,add_time) VALUE("'.$admin_id.'","'.$customers_id.'",now())';
					$db->Execute($sql);
					 $sales_email = zen_admin_email_of_id($admin_id);
					 $html=zen_get_corresponding_languages_email_common();
					 $html_msg['EMAIL_HEADER'] = $html['html_header'];					 
		       $html_msg['EMAIL_FOOTER'] = $html['html_footer'];
		       $html_msg['CUSTOMER_NAME'] = $entry_firstname.$entry_lastname ? $entry_firstname.$entry_lastname : 'not set yet';
		       $html_msg['NUMBER'] = $telephone ? $telephone : 'not set yet';
		       $html_msg['EMAIL_ADDRESS'] = $email_address ? $email_address : 'not set yet';
		 zen_mail($sales_email, $sales_email, 'Customer Info', $text_message, 'service@fiberstore.net', EMAIL_FROM, $html_msg, 'regist_to_us');

					}
				 $db->Execute("update address_book set customers_id = '".$_SESSION['customer_id']."' where address_book_id='".$_SESSION['billto']."'");
				$list = $db->getAll("select guest_id from customer_of_guest where email_address = '".$email_address."' order by guest_id DESC limit 1");
				if($list){
					 $db->Execute("update address_book set customers_id = '".$_SESSION['customer_id']."' where customers_guest_id='".$list[0]['guest_id']."'");
					 $db->Execute("update orders set customers_id = '".$_SESSION['customer_id']."' where guest_id='".$list[0]['guest_id']."'");
				}

					require_once DIR_WS_CLASSES .'set_cookie.php';
					$Encryption = new Encryption;
					$cookie_customer_encrypt = $Encryption->_encrypt($_SESSION['customer_id']);
					//$cookie_customer_decrypt = $Encryption->_decrypt($cookie_customer_encrypt);
					setcookie("fs_login_cookie",$cookie_customer_encrypt,time()+86400*300 ,"/");

					$_SESSION['cart']->restore_contents();

						$sql = "insert into " . TABLE_CUSTOMERS_INFO . "
                          (customers_info_id, customers_info_number_of_logons,
                           customers_info_date_account_created, customers_info_date_of_last_logon)
								values ('" . ( int ) $_SESSION['customer_id'] . "', '1', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";

									$db->Execute ( $sql );

									if (SESSION_RECREATE == 'True') {
										zen_session_recreate ();
									}

									$_SESSION ['customer_first_name'] = $entry_firstname;
									$_SESSION ['customer_default_address_id'] = $address_id;
									$_SESSION ['customer_country_id'] = $country;
									$_SESSION ['customer_zone_id'] = $zone_id;
									$_SESSION ['customers_authorization'] = $customers_authorization;
									$_SESSION ['name'] = $entry_firstname;

									$_SESSION['customers_email_address'] = $email_address;

									$html_msg = array();
									$html=zen_get_corresponding_languages_email_common();
									$html_msg['EMAIL_HEADER'] = $html['html_header'];
									$html_msg['EMAIL_FOOTER'] = $html['html_footer'];
									$html_msg['EMAIL_BODY_COMMON_DEAR'] = EMAIL_BODY_COMMON_DEAR;
									$html_msg ['EMAIL_FIRST_NAME'] = $entry_firstname;
									$html_msg ['EMAIL_LAST_NAME'] = $entry_lastname;
									$html_msg['EMAIL_REGIST_TO_CUSTOMER_TEXT1'] = EMAIL_REGIST_TO_CUSTOMER_TEXT1;
									$html_msg['EMAIL_REGIST_TO_CUSTOMER_TEXT2'] = EMAIL_REGIST_TO_CUSTOMER_TEXT2;
									$html_msg['EMAIL_REGIST_TO_CUSTOMER_TEXT3'] = EMAIL_REGIST_TO_CUSTOMER_TEXT3;

									$email_text .= EMAIL_WELCOME;
									$email_text .=' Fiberstore ';

									

									$email_text .= "\n\n" . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_GV_CLOSURE;

									$email_text .= "\n\n" . sprintf ( EMAIL_DISCLAIMER_NEW_CUSTOMER, STORE_OWNER_EMAIL_ADDRESS ) . "\n\n";
							// 		$html_msg ['EMAIL_DISCLAIMER'] = sprintf ( EMAIL_DISCLAIMER_NEW_CUSTOMER, '<a href="mailto:' . STORE_OWNER_EMAIL_ADDRESS . '">' . STORE_OWNER_EMAIL_ADDRESS . ' </a>' );


									if (!defined('EMAIL_SUBJECT')) {
										//define('EMAIL_SUBJECT', 'Congratulations, you have a new account on FiberStore.com');
									}
									// send welcome email
									if (trim ( EMAIL_SUBJECT ) != 'n/a')
										//zen_mail ( $customer_name, $email_address, EMAIL_SUBJECT, $email_text, STORE_NAME, EMAIL_FROM, $html_msg, 'welcome' );
									$send_to_email = 'support@fiberstore.com';
									zen_mail_contact_us_or_bulk_order_inquiry($customer_name, $email_address, EMAIL_REGIST_TO_CUSTOMER_SUBJECT, $email_text, STORE_NAME, $send_to_email, $html_msg,'regist_to_customer');

									$_SESSION['regist_success'] = rand(0,1000);


					}
				}
				exit('ok');
				break;

				case 'checkpassword':

						$email_address = ($_POST['email_address']);

										$password = ($_POST['password1']);

						$check_customer_query = "SELECT customers_id, customers_firstname, customers_lastname, customers_password,
																										customers_email_address, customers_default_address_id,
																										customers_authorization, customers_referral
																							   FROM " . TABLE_CUSTOMERS . "
																							   WHERE customers_email_address = :emailAddress";

																		$check_customer_query  =$db->bindVars($check_customer_query, ':emailAddress', $email_address, 'string');
																		$check_customer = $db->Execute($check_customer_query);


														
																		  if (!zen_validate_password($password, $check_customer->fields['customers_password'])) {
																			   exit('Password input error');
																		  }else{
																			   exit('ok');
																		  }

				break;
			case 'set_address':

				
			if (!isset($customer_info) || !is_object($customer_info)){

											require DIR_WS_CLASSES . 'customer_account_info.php';

									    	$customer_info = new customer_account_info();

										}

				if (isset($_POST['tag'])){

					/* set shipping address*/

					if (3 == intval($_POST['tag'])){

						$_SESSION['sendto'] = $_POST['address_book_id'];

					}elseif(10 == intval($_POST['tag'])){
						$customer_info->set_new_shipping_address_bill($_SESSION['billto']);
						$_SESSION['sendto'] = $_SESSION['billto'];
						exit;
				    }else{ 

										$entry_firstname = ($_POST['entry_firstname']);

										$entry_lastname = ($_POST['entry_lastname']);

										$entry_company = ($_POST['entry_company']);

										$entry_street_address = ($_POST['entry_street_address']);

										$entry_suburb = ($_POST['entry_suburb']);

										$entry_city = ($_POST['entry_city']);

										$entry_country_id = ($_POST['entry_country_id']);

										$entry_state = ($_POST['entry_state']);

											if($entry_country_id == 223){
											$entry_state = ($_POST['shipping_us_state']);
										}

										$entry_postcode = ($_POST['entry_postcode']);

										$entry_telephone = ($_POST['entry_telephone']);

										

										$shipping_address = array(

											'entry_company' => mysql_real_escape_string($entry_company),

											'entry_firstname' => mysql_real_escape_string($entry_firstname),

											'entry_lastname' => mysql_real_escape_string($entry_lastname),

											'entry_street_address' => mysql_real_escape_string($entry_street_address),

											'entry_suburb' => mysql_real_escape_string($entry_suburb),

											'entry_postcode' => mysql_real_escape_string($entry_postcode),

											'entry_state' => mysql_real_escape_string($entry_state),

											'entry_city' =>  mysql_real_escape_string($entry_city),

											'entry_country_id' => (int)$entry_country_id,

											'entry_zone_id' => (int)$entry_zone_id,

											'entry_telephone' => mysql_real_escape_string($entry_telephone)

										);

								switch (intval($_POST['tag'])){

									case 1:					

										if ($customer_info->get_address_records()){

											$_SESSION['sendto'] = $address_id = $customer_info->add_new_shipping_address($shipping_address);

										}else{

											$_SESSION['sendto'] = $address_id = $customer_info->add_new_shipping_address($shipping_address);

											$_SESSION['billto'] = $customer_info->add_new_billing_address($shipping_address);

										}

										$shipping_addresses = $customer_info->get_customers_shipping_address();

										if (sizeof($shipping_addresses)){

							              $address_string = '';

							              foreach ($shipping_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }

							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';

						              }
										$addrss_content =  '"type":"insert","aid": "'.$address_id.'","addresses":'.$address_string.'';

										break;

									case 2:
										/*update exist shipping address*/

										$_SESSION['sendto'] = intval($_POST['address_book_id']);

										zen_db_perform(TABLE_ADDRESS_BOOK, $shipping_address,'update','address_book_id='.intval($_POST['address_book_id']));

										$shipping_addresses = $customer_info->get_customers_shipping_address();

										if (sizeof($shipping_addresses)){

							              $address_string = '';

							              foreach ($shipping_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }
							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';
						               }
										$addrss_content =  '"type":"update","aid":"'.intval($_POST['address_book_id']).'","addresses":'.$address_string.'';
										break;
								}

					}

								$total_weight = $_SESSION['cart']->show_weight();

				                require DIR_WS_CLASSES.'order.php';

				                $order = new order();

				                require DIR_WS_CLASSES.'shipping.php';

				                $shipping = new shipping($_SESSION['shipping']);

				                $order = new order();				                

				                require (DIR_WS_CLASSES.'order_total.php');

								$order_total_modules = new order_total();

								$order_totals = $order_total_modules->process();

				                $shipping = new shipping();

				                $quotes = $shipping->quote();

				                $fedex_cost = $currencies->value($quotes['fedexzones']['methods'][0]['cost']);

				                $dhl_cost = $currencies->value($quotes['dhlzones']['methods'][0]['cost']);

				                $airmail_cost = $currencies->value($quotes['airmailzones']['methods'][0]['cost']);
				                
				                $subtotal = $currencies->value($order->info['subtotal']);

				                switch ($_SESSION['shipping']['id']){

				                	case 'fedexzones_fedexzones':

				                		if ($fedex_cost){

				                		$current_shipping_cost = $fedex_cost;

				                		$_SESSION['shipping'] = array('id' => 'fedexzones_fedexzones',

										                                'title' => 'Fedex Rates',

										                                'cost' => $quotes['fedexzones']['methods'][0]['cost']);

				                		}

				                		break;

				                	case 'dhlzones_dhlzones':

				                		if ($dhl_cost){

				                			$current_shipping_cost = $dhl_cost;

				                			$_SESSION['shipping'] = array('id' => 'dhlzones_dhlzones',

				                					'title' => 'DHL Rates',

				                					'cost' => $quotes['dhlzones']['methods'][0]['cost']);

				                		}

				                		break;

				                	case 'airmailzones_airmailzones':

				                		if ($airmail_cost){

				                			$current_shipping_cost = $airmail_cost;
				                			$_SESSION['shipping'] = array('id' => 'airmailzones_airmailzones',
				                					'title' => 'Airmail Rates',
				                					'cost' => $quotes['airmailzones']['methods'][0]['cost']);
				                		}

				                		break;
				                }

				                if (!$current_shipping_cost)
				                	$all_fee = '"all_fee":{"error":"No shipping available to the selected country",';
				                else{ 
				                	$all_fee = '"all_fee":{"current_shipping":"'.$_SESSION['shipping']['id'].'","current_fee":"'.$current_shipping_cost.'",';
					                if ($fedex_cost) $all_fee .= '"fedex":"'.$fedex_cost.'",';
					                if ($dhl_cost) $all_fee  .= '"dhl":"'.$dhl_cost.'",';
					               	if ($airmail_cost) $all_fee .= '"airmail":"'.$airmail_cost.'",';
				                }

				               	$all_fee = substr($all_fee,0,strlen($all_fee)-1).'}';

				                if (isset($addrss_content) && $addrss_content){

				                	echo '{'.$addrss_content.','.$all_fee.'}';

				                }else {

				                	echo '{'.$all_fee.'}';

				                }
				}
				break;

	case 'set_guest_address':
				
			if (!isset($customer_info) || !is_object($customer_info)){

					require DIR_WS_CLASSES . 'customer_account_info.php';

					$customer_info = new customer_account_info();

			}

				if (isset($_POST['tag'])){

					/* set shipping address*/

					if (3 == intval($_POST['tag'])){

						$_SESSION['sendto'] = $_POST['address_book_id'];

					}elseif(10 == intval($_POST['tag'])){
						$customer_info->set_guest_shipping_address_bill($_SESSION['billtoG']);
						$_SESSION['sendtoG'] = $_SESSION['billtoG'];
						exit;
				    }else{ 

										$entry_firstname = ($_POST['entry_firstname']);

										$entry_lastname = ($_POST['entry_lastname']);
										
										$entry_company = ($_POST['entry_company']);

										$entry_street_address = ($_POST['entry_street_address']);

										$entry_suburb = ($_POST['entry_suburb']);

										$entry_city = ($_POST['entry_city']);

										$entry_country_id = ($_POST['entry_country_id']);

										$entry_state = ($_POST['entry_state']);

										if($entry_country_id == 223){
											$entry_state = ($_POST['shipping_us_state']);
										}

										$entry_postcode = ($_POST['entry_postcode']);

										$entry_telephone = ($_POST['entry_telephone']);

										

										$shipping_address = array(

											'entry_company' => mysql_real_escape_string($entry_company),

											'entry_firstname' => mysql_real_escape_string($entry_firstname),

											'entry_lastname' => mysql_real_escape_string($entry_lastname),

											'entry_street_address' => mysql_real_escape_string($entry_street_address),
												

											'entry_suburb' => mysql_real_escape_string($entry_suburb),

											'entry_postcode' => mysql_real_escape_string($entry_postcode),

											'entry_state' => mysql_real_escape_string($entry_state),

											'entry_city' =>  mysql_real_escape_string($entry_city),

											'entry_country_id' => (int)$entry_country_id,

											'entry_zone_id' => (int)$entry_zone_id,

											'entry_telephone' => mysql_real_escape_string($entry_telephone)

										);

								switch (intval($_POST['tag'])){

									case 1:					

										
											$_SESSION['sendtoG'] = $address_id = $customer_info->add_guest_shipping_address($shipping_address);


										//$db->Execute("update " .TABLE_CUSTOMERS . " set default_address_id = ".$address_id . " where customers_id = " .intval($_SESSION['customer_id']));

										

										$shipping_addresses = $customer_info->get_customers_shipping_address();


										break;

								

								}

					}

								

				               

				               

				                

					

				}

				break;


				case 'set_billing_address':

				if (isset($_POST['tag'])){
					/* set shipping address*/
					if (3 == intval($_POST['tag'])){

						$_SESSION['billto'] = $_POST['address_book_id'];

					}else{ 
								/*add new shipping address*/

					if (!isset($customer_info) || !is_object($customer_info)){

											require DIR_WS_CLASSES . 'customer_account_info.php';

									    	$customer_info = new customer_account_info();

					}
										
					$sql = "select customers_default_billing_address_id as id from customers where customers_id = ".(int)$_SESSION['customer_id'];
					$default_billing = $db->Execute($sql);
					if($default_billing->fields['id']){
					$customer_info->update_billing_address_type($default_billing->fields['id']);
					}
								
										$entry_firstname = ($_POST['billing_firstname']);

										$entry_lastname = ($_POST['billing_lastname']);

										$entry_company = ($_POST['billing_company']);

										$entry_street_address = ($_POST['billing_street_address']);

										$entry_suburb = ($_POST['billing_suburb']);

										$entry_city = ($_POST['billing_city']);

										$entry_country_id = ($_POST['billing_country_id']);

										$entry_state = ($_POST['billing_state']);
										$billing_us_state = ($_POST['billing_us_state']);

										if($_POST['billing_country_id'] == 223){

											$entry_state =  $billing_us_state;

										}

										$entry_postcode = ($_POST['billing_postcode']);

										$entry_telephone = ($_POST['billing_telephone']);


										$billing_address = array(
                                            'address_type' => 2,
											'entry_company' => mysql_real_escape_string($entry_company),

											'entry_firstname' => mysql_real_escape_string($entry_firstname),

											'entry_lastname' => mysql_real_escape_string($entry_lastname),
											
											

											'entry_street_address' => mysql_real_escape_string($entry_street_address),

											'entry_suburb' => mysql_real_escape_string($entry_suburb),

											'entry_postcode' => mysql_real_escape_string($entry_postcode),

											'entry_state' => mysql_real_escape_string($entry_state),

											'entry_city' =>  mysql_real_escape_string($entry_city),

											'entry_country_id' => (int)$entry_country_id,

											'entry_zone_id' => (int)$entry_zone_id,

											'entry_telephone' => mysql_real_escape_string($entry_telephone)

										);

								switch (intval($_POST['tag'])){

									case 1:					
										if ($customer_info->get_address_records()){

											$_SESSION['billto'] = $address_id = $customer_info->add_new_billing_address($billing_address);

										}else{

											//$_SESSION['sendto'] = $address_id = $customer_info->add_new_shipping_address($billing_address);

											$_SESSION['billto'] = $address_id = $customer_info->add_new_billing_address($billing_address);

										}
										echo "ok";exit;
										$billing_addresses = $customer_info->get_customers_billing_address();

										if (sizeof($billing_addresses)){

							              $address_string = '';

							              foreach ($billing_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_company":"'.$address['entry_company'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }

							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';

						                 }

										$addrss_content =  '"type":"insert","aid": "'.$address_id.'","addresses":'.$address_string.'';

										break;

									case 2:
/*update exist shipping address*/

										$_SESSION['billto'] = intval($_POST['address_book_id']);

										zen_db_perform(TABLE_ADDRESS_BOOK, $billing_address,'update','address_book_id='.intval($_POST['address_book_id']));

										$billing_addresses = $customer_info->get_customers_billing_address();

										if (sizeof($billing_addresses)){

							              $address_string = '';

							              foreach ($billing_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_company":"'.$address['entry_company'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }

							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';

						               }

										$addrss_content =  '"type":"update","aid":"'.intval($_POST['address_book_id']).'","addresses":'.$address_string.'';

										break;

								}

					       }

				    }

				break;	


				case 'set_guest_billing_address':

		if (isset($_POST['tag'])){
				
				/*add new shipping address*/

					if (!isset($customer_info) || !is_object($customer_info)){

											require DIR_WS_CLASSES . 'customer_account_info.php';

									    	$customer_info = new customer_account_info();

					}
					/*					
					$sql = "select customers_default_billing_address_id as id from customers where customers_id = ".(int)$_SESSION['customer_id'];
					$default_billing = $db->Execute($sql);
					if($default_billing->fields['id']){
					$customer_info->update_billing_address_type($default_billing->fields['id']);
					}
					*/
								
										$entry_firstname = ($_POST['billing_firstname']);

										$entry_lastname = ($_POST['billing_lastname']);

										$entry_company = ($_POST['billing_company']);

										$entry_street_address = ($_POST['billing_street_address']);

										$entry_suburb = ($_POST['billing_suburb']);

										$entry_city = ($_POST['billing_city']);

										$entry_country_id = ($_POST['billing_country_id']);

										$entry_state = ($_POST['billing_state']);

										$billing_us_state = ($_POST['billing_us_state']);

										if($_POST['billing_country_id'] == 223){

											$entry_state =  $billing_us_state;

										}

									$entry_postcode = ($_POST['billing_postcode']);

										$entry_telephone = ($_POST['billing_telephone']);


										$email_address = ($_POST['email_address']);


										if (strlen ( $email_address ) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
											$error = true;
											//$messageStack->add_session (FILENAME_REGIST, ENTRY_EMAIL_ADDRESS_ERROR );
											echo ENTRY_EMAIL_ADDRESS_ERROR;exit;
										} else if (zen_validate_email ( $email_address ) == false) {
											$error = true;
											//$messageStack->add_session (FILENAME_REGIST, ENTRY_EMAIL_ADDRESS_CHECK_ERROR );
											echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR;exit;
										} else {
											$check_email_query = "select count(customers_id) as total         from " . TABLE_CUSTOMERS . "
													 where customers_email_address = '" .  $email_address . "'";
											$check_email = $db->Execute ( $check_email_query );
											if ($check_email->fields['total'] > 0){
												$error = true;
												$login_in = "   <a href='/login.html'>Login in »</a>";
												echo 'Our system already has a record of that email address . Please try logging in with that email address . &nbsp;&nbsp;&nbsp;&nbsp;'.$login_in;exit;
												//$messageStack->add_session ( FILENAME_REGIST,'<div id="fiberstore_message" class="tishi_02 display_none">Our system already has a record of that email address - please try logging in with that email address.<br /> If you do not use that address any longer you can correct it in the My Account area.</div>' );
											}
										}
										$billing_address = array(

                                            'address_type' => 2,

											'entry_company' => mysql_real_escape_string($entry_company),

											'entry_firstname' => mysql_real_escape_string($entry_firstname),

											'entry_lastname' => mysql_real_escape_string($entry_lastname),																						

											'entry_street_address' => mysql_real_escape_string($entry_street_address),

											'entry_suburb' => mysql_real_escape_string($entry_suburb),

											'entry_postcode' => mysql_real_escape_string($entry_postcode),

											'entry_state' => mysql_real_escape_string($entry_state),

											'entry_city' =>  mysql_real_escape_string($entry_city),

											'entry_country_id' => (int)$entry_country_id,

											'entry_zone_id' => (int)$entry_zone_id,

											'entry_telephone' => mysql_real_escape_string($entry_telephone)

										);

										$customer_guest = array(
											
											'email_address' => $email_address,

											'first_name' => $entry_firstname,

											'last_name' => $entry_lastname,

											'customer_country_id' => (int)$entry_country_id,

											'add_time' => date('Y-m-d H:i:s')
										
										);
									
								switch (intval($_POST['tag'])){

									case 1:					
						
										$_SESSION['billtoG'] = $address_id = $customer_info->add_guest_billing_address($billing_address,$customer_guest);

											echo "ok";exit;
										$billing_addresses = $customer_info->get_customers_billing_address();

										if (sizeof($billing_addresses)){

							              $address_string = '';

							              foreach ($billing_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_company":"'.$address['entry_company'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }

							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';

						                 }

										$addrss_content =  '"type":"insert","aid": "'.$address_id.'","addresses":'.$address_string.'';

										break;

									case 2:

										$_SESSION['billtoG'] = intval($_POST['address_book_id']);

										zen_db_perform(TABLE_ADDRESS_BOOK, $billing_address,'update','address_book_id='.intval($_POST['address_book_id']));

										$billing_addresses = $customer_info->get_customers_billing_address();

										if (sizeof($billing_addresses)){

							              $address_string = '';

							              foreach ($billing_addresses as $i => $address){

							               	$address_string .= '"'.$address['address_book_id'].'":{"address_book_id":"'.$address['address_book_id'].'","entry_firstname":"'.$address['entry_firstname'].'","entry_lastname":"'.$address['entry_lastname'].'","entry_street_address":"'.$address['entry_street_address'].'","entry_suburb":"'.$address['entry_suburb'].'","entry_city":"'.$address['entry_city'].'","entry_country":{"entry_country_id":"'.$address['entry_country']['entry_country_id'].'","entry_country_name":"'.$address['entry_country']['entry_country_name'].'"},"entry_state":"'.$address['entry_state'].'","entry_zone_id":"'.$address['entry_zone_id'].'","entry_postcode":"'.$address['entry_postcode'].'","entry_telephone":"'.$address['entry_telephone'].'"},';

							               }

							               $address_string = '{"data":{'.substr($address_string, 0, (strlen($address_string)-1)).'}}';

						               }

										$addrss_content =  '"type":"update","aid":"'.intval($_POST['address_book_id']).'","addresses":'.$address_string.'';

										break;
								}
				    }
				break;	

			case 'add_shipping_address':

				if (!isset($customer_info) || !is_object($customer_info)){

					require DIR_WS_CLASSES . 'customer_account_info.php';

			    	$customer_info = new customer_account_info();

				}
				$entry_firstname = ($_POST['entry_firstname']);

				$entry_lastname = ($_POST['entry_lastname']);

				$entry_street_addresss = ($_POST['entry_street_addresss']);

				$entry_suburb = ($_POST['entry_suburb']);

				$entry_city = ($_POST['entry_city']);

				$entry_country_id = ($_POST['entry_country_id']);

				$entry_state = ($_POST['entry_state']);

				$entry_postcode = ($_POST['entry_postcode']);

				$entry_telephone = ($_POST['entry_telephone']);

				

				$shipping_address = array(

					'entry_company' => mysql_real_escape_string($entry_company),

					'entry_firstname' => mysql_real_escape_string($entry_firstname),

					'entry_lastname' => mysql_real_escape_string($entry_lastname),

					'entry_street_address' => mysql_real_escape_string($entry_street_address),

					'entry_suburb' => mysql_real_escape_string($entry_suburb),

					'entry_postcode' => mysql_real_escape_string($entry_postcode),

					'entry_state' => mysql_real_escape_string($entry_state),

					'entry_city' =>  mysql_real_escape_string($entry_city),

					'entry_country_id' => (int)$entry_country_id,

					'entry_zone_id' => (int)$entry_zone_id,

					'entry_telephone' => mysql_real_escape_string($entry_telephone)

				);

				$customer_info->add_new_shipping_address($shipping_address);				

				break;

			case 'change_shipping':

				$shipping_method = $_POST['shipping'];

				$shipping_code = $_POST['shipping_code'];

				require (DIR_WS_CLASSES.'order.php');

				$order = new order();
				$sessionIds = $_SESSION['shopping_pro_id'][0];
				if(!empty($sessionIds)){
					$sessionCart = $_SESSION['cart'];

					$countProId = count($sessionIds);
					if(empty($_SESSION['shortCart'])){
						$shortCart =  $_SESSION['shortCart'] = $_SESSION['cart']->contents;
					}

					foreach ($sessionCart->contents as $k=>$v){
						for($i=0;$i<$countProId;$i++){
							if($k==$sessionIds[$i]){
								$a=$k;
							}
						}
						if($a!=$k){
							unset($sessionCart->contents[$k]);
						}
					}
				}

				$total_weight = $sessionCart->show_weight();

				require (DIR_WS_CLASSES.'shipping.php');
				
				$shipping = new shipping();
				
				$init_quote = $shipping->quote($shipping_method,$shipping_method);
		
				
				$_SESSION['shipping'] = array('id' => $shipping_method.'_'.$shipping_method,

                                'title' => $init_quote[$shipping_method]['methods'][0]['title'],

                                'cost' => $init_quote[$shipping_method]['methods'][0]['cost']);
				$_SESSION['_choices'] =$currencies->new_value($init_quote[$shipping_method]['methods'][0]['cost']);
                $_SESSION['_choice'] = $shipping_code;
				exit('{"cost":"'.$currencies->new_value($init_quote[$shipping_method]['methods'][0]['cost']).'"}');

				break;

			case 'display_shipping':

				$shipping_method = $_POST['shipping'];
				
                $shipping_code = $_POST['shipping_code'];
				
				require (DIR_WS_CLASSES.'order.php');
				
				$order = new order();
				
				$total_weight = $_SESSION['cart']->show_weight();
				
				require (DIR_WS_CLASSES.'shipping.php');
				
				$shipping = new shipping();
				
				$init_quote = $shipping->quote($shipping_method,$shipping_method);
				
				$_SESSION['shipping'] = array('id' => $shipping_method.'_'.$shipping_method,

                                'title' => $init_quote[$shipping_method]['methods'][0]['title'],

                                'cost' => $init_quote[$shipping_method]['methods'][0]['cost']);

				exit('{"cost":"'.$currencies->value($init_quote[$shipping_method]['methods'][0]['cost']).'"}');

				break;	

			case 'shipping_insurance':
				if (isset($_POST['s'])){

					require (DIR_WS_CLASSES.'order.php');

					$order = new order();

					switch ($_POST['s']){

						case 1:

							$order->info['shipping_insurance'] = 1.99;

							$order->info['total'] = $order->info['total']+ 1.99;

							break;

						case 0:

							$order->info['shipping_insurance'] = 0;

							$order->info['total'] = $order->info['total'] - 1.99;

							break;
					}
				}

				break;

			case 'setPayment':

				$_SESSION['payment'] = $_POST['payment'];

				break;

			case 'set_credit_card_address':
				$req_qreoid = $_POST['req_qreoid'];
				$consignee_name = $_POST['consignee_name'];
				$billing_lastname = $_POST['lastname'];
				$Address =$_POST['Address'];
				$entry_country_id = $_POST['country'];
				$billing_country = get_countries_name($entry_country_id);
				$entry_state =$_POST['entry_state'];
				$entry_city =$_POST['entry_city'];
				$entry_postcode =$_POST['entry_postcode'];
				$entry_telephone =$_POST['entry_telephone']; 
				$s_tel_prefix_email1 = $_POST['s_tel_prefix_email1'];
				$db->execute("update orders set billing_name = '$consignee_name',billing_lastname='$billing_lastname',billing_street_address='$Address',billing_country='$billing_country',billing_state='$entry_state',billing_city='$entry_city',billing_postcode='$entry_postcode',billing_telephone='$entry_telephone',b_tel_prefix = '$s_tel_prefix_email1' where orders_id = '$req_qreoid'");
				exit('ok');
				break;

			case 'globalcollect_submit':
				$orders_id = $_POST['orders_id'];
				$_SESSION['req_qreoid'] = $orders_id;
				$paymentproductid = $_POST['paymentproductid'];

				$_SESSION['url_eroor'] = 'payment_billing';

				if(isset($_GET['act'])){

					$_SESSION['url_eroor'] = 'payment_against';
				}
				require (DIR_WS_CLASSES . 'payment.php');

				$payment = new payment('globalcollect');

				require (DIR_WS_CLASSES.'order.php');

				$order = new order($orders_id);

				$action = $GLOBALS['globalcollect']->form_action_url;

				$process_string = $GLOBALS['globalcollect']->process_button();

				echo json_encode(array('url'=>$action,'params'=>$process_string));exit;
				break;
	            
				
				case 'switch_payment_method':

				$orders_id = $_POST['orders_id'];

				$_SESSION['req_qreoid'] = $orders_id;

				$payment_method_code = $_POST['payment_method'];

				switch ($payment_method_code){
					case 'paypal':
						$payment_method = 'PayPal';
					break;
					case 'hsbc':
						$payment_method = 'HSBC Order';
					break;
					case 'globalcollect':
						$payment_method = 'Globalcollect';
					break;
				}
				if(isset($payment_method)){
					if($orders_id){
						$re = $db->Execute("select payment_method,payment_module_code from orders where orders_id = '".$orders_id."'");
						if($re){
							 $d_payment = array(   
									'orders_id' => $orders_id,
									'payment_module_code' =>$re->fields['payment_module_code'],
									'payment_method' => $re->fields['payment_method'],
									'date_added' => 'now()'
		 					);
							zen_db_perform('orders_payment_history', $d_payment);

							$d_payment = array(   
									'payment_module_code' =>$payment_method_code,
									'payment_method' => $payment_method,
									'last_modified' => 'now()'
		 					);
							zen_db_perform('orders', $d_payment,'update','orders_id='.$orders_id);
							echo "ok";exit;
						}
					}
				}else{
					echo "err";exit;
				}
			break;
			
			case 'get_specification':
				$cpath = trim($_POST['cpath']);
				$products_id = (int)$_POST['product_id'];
				$specification_content = fs_get_data_from_db_fields('specification','categories_products_specification_info',"cPath='{$cpath}' and products_id=".$products_id,'');
				echo $specification_content;
				exit;
			break;
			
		}
	}
}