<?php
require('includes/application_top.php');

$action = (isset($_POST['action']) ? $_POST['action'] : '');
if ($action!='') {
  switch ($action) {

//-------------------------------------------------------------------------------------------------------------------------

    case 'insert':

    if ( ($action == 'insert') ) {
        $customer_name = $_POST['customer_name'];

        $customers_country_id = $_POST['customers_country_id'];
        $customers_email = zen_db_prepare_input($_POST['customers_email']);
        $order_number = zen_db_prepare_input($_POST['order_number']);
        if (!$customers_email && !$order_number) {
            //die('邮箱和订单号不能同时为空！');
        }

        $customers_tel = $_POST['customers_tel'];
        $customers_from = $_POST['customers_from'];
        $customers_other = $_POST['customers_other'];
        $customers_import = $_POST['customers_import'];
        $insadmin_id = $_SESSION['admin_id'];
        $category_id = $_POST['category'];
        $customer_level = $_POST['customer_level'];
        /*新增客服标签*/
        //售前或售后
        $service_class = $_POST['service_class'];
        $service_tag = $_POST['service_tag'];


        //通过输入的订单号定位业务员
        if ($order_number) {
            $result = $db->Execute("select orders_id,customers_email_address from orders where 	orders_number = '" . $order_number . "'");

            if ($result->fields['orders_id']) {
                $result2 = $db->Execute("select admin_id from order_to_admin where orders_id='" . $result->fields['orders_id'] . "'");
                $admin_id = $result2->fields['admin_id'];
                $c_email = $result->fields['customers_email_address'];
            }
            if (!$admin_id) {
                $result = $db->Execute("select sales_admin,customers_emails from products_instock_shipping where orders_num = '" . $order_number . "' or order_invoice = '" . $order_number . "' or order_number = '" . $order_number . "' order by products_instock_id desc limit 1 ");
                $admin_id = $result->fields['sales_admin'];
                if ($result->fields['customers_emails']) {
                    $c_email = $result->fields['customers_emails'];
                }
            }

            if ($_POST['admin_id'] && $admin_id && $_POST['admin_id'] != $admin_id) {
                die('你选择的业务员和订单号对应的业务员不符!');
            }
            //没有填写客户邮箱,通过订单号得到邮箱
            if (!$customers_email && $c_email) {
                $customers_email = $c_email;
            }
        }

        if (!$admin_id && $customers_email) {
            $enquiry_id = $db->Execute("select customers_id from customers_enquiry where customers_email='" . $customers_email . "'");
            if ($enquiry_id->fields['customers_id']) {
                // die('此客户已分给其他销售');
                $enquiry_admin_id = $db->Execute("SELECT admin_id FROM `admin_to_enquiry` WHERE customers_id=" . $enquiry_id->fields['customers_id'] . " ");
                if ($_POST['admin_id'] && $enquiry_admin_id->fields['admin_id']) {
                    if ($enquiry_admin_id->fields['admin_id'] != $_POST['admin_id']) {
                        die('此客户已分给其他销售');
                    }
                }
            };
        }

        if ($_POST['admin_id'] != 0) {
            $admin_id = $_POST['admin_id'];
        }

        //  记录    统计      `stats_enquiry`  '1表示注册的      2未注册'
        $customer_online = $db->Execute("SELECT customers_id FROM customers WHERE customers_email_address='" . $customers_email . "'  LIMIT 1");
        if ($customer_online->RecordCount() && $customer_online->fields['customers_id']) {
            $stats_enquiry = 1;
        } else {
            $stats_enquiry = 2;
        }

        //注册7天内询盘的标记为注册询盘客户 用于统计
        $customer_online = $db->Execute("SELECT customers_id FROM customers c left join customers_info ci on c.customers_id = ci.customers_info_id WHERE customers_email_address='" . $customers_email . "'  and DATE_ADD(customers_info_date_account_created,INTERVAL 7 DAY)>curdate()");
        if ($customer_online->RecordCount() && $customer_online->fields['customers_id']) {
            $db->Execute("update customers set stats_enquiry=1 where customers_id=".$customer_online->fields['customers_id']);
        }

        //录入人所代表的客服类型
        $customer_service = fs_get_data_from_db_fields('is_customer_service','admin','admin_id='.$_SESSION['admin_id'],'');
        //limit 3,先插入数据也没关系
        $sql = 'INSERT INTO customers_enquiry(customers_name,customers_country_id,customers_email,customers_tel,customers_from,update_time,customers_other,customers_import,admin_id,category_id,order_number,stats_enquiry,customer_level,customer_service,service_type)
              VALUE("' . $customer_name . '","' . $customers_country_id . '","' . $customers_email . '","' . $customers_tel . '","' . $customers_from . '",now(),"' . $customers_other . '","' . $customers_import . '","' . $insadmin_id . '","' . $category_id . '","' . $order_number . '","' . $stats_enquiry . '","' . $customer_level . '","' . $customer_service . '","'.$service_class.'")';
        $db->Execute($sql);
        $cid = $db->insert_ID();
        /**
         * 写入数据
         */
        //如果新增成功,并且是售前服务
        if($cid && $service_class == 1){
            //die(json_encode('可以的！'));
            $insert_tag_id = $cid;
            //循环插入附表
            foreach ($service_tag as $key => $value){
                die(json_encode($value));
                $insert_sql = "INSERT INTO `customers_enquiry_tag` (`customers_id`,`tag`) VALUES  ($insert_tag_id, $value)";
                $db->Execute($insert_sql);
            }
        }
        //die(json_encode('可以的！'));


        if (!$admin_id) {
            $customer_id = $cid;
            $_POST['type'] = 'customer_enquiry';
            $is_old = 0;
            require('semi_automatic_allot.php');
            // $is_old 在 semi_automatic_allot.php文件中定义 标记客户是否是老客户 用于统计
            if($is_old){
                $db->Execute("update customers_enquiry set is_old =".$is_old." where customers_id=".$cid);
            }
        }else{
            //指定销售 或 指定 订单号 都标记为老客户
            $db->Execute("update customers_enquiry set is_old =1 where customers_id=".$cid);
        }


        if ($admin_id) {
            $customers_id = $cid;
            $sel_sql = "select admin_id from admin_to_enquiry where customers_id =" . (int)$customers_id . "";
            $get_admin = $db->Execute($sel_sql);
            if (!$get_admin->fields['admin_id']) {
                $sql = 'INSERT INTO admin_to_enquiry(admin_id,customers_id,add_time) VALUE("' . $admin_id . '","' . $customers_id . '",now())';
                $db->Execute($sql);
            } else {
                $sql = 'UPDATE admin_to_enquiry SET admin_id=' . $admin_id . ' WHERE customers_id=' . $customers_id . '';
                $db->Execute($sql);
            }
            //发送提醒邮件
            $disp_order = '';
            switch ($customers_import) {
                case 1:
                    $disp_order = 'not urgent';
                    break;
                case 2:
                    $disp_order = 'slight urgent';
                    break;
                case 3:
                    $disp_order = 'urgent';
                    break;
                case 4:
                    $disp_order = 'very urgent';
                    break;

            }
            switch ($customers_from) {
                case 1:
                    $c_from = 'linkedin';
                    break;
                case 2:
                    $c_from = 'google+';
                    break;
                case 3:
                    $c_from = 'facebook';
                    break;
                case 4:
                    $c_from = 'twiter';
                    break;
                case 5:
                    $c_from = 'youtobe';
                    break;
                case 6:
                    $c_from = '谷歌广告';
                    break;
                case 7:
                    $c_from = '其他';
                    break;
                case 8:
                    $c_from = '电话';
                    break;
                case 9:
                    $c_from = 'LIVE CHAT';
                    break;
                case 10:
                    $c_from = '客户介绍';
                    break;
                case 11:
                    $c_from = 'sales邮箱';
                    break;
                default:
                    $c_from = '其他';
                    break;
            }
            $sales_email = zen_admin_email_of_id($admin_id);

            $html=zen_get_corresponding_languages_email_common('admin');
					 $html_msg['EMAIL_HEADER'] = $html['html_header'];
					 $html_msg['EMAIL_FOOTER'] = $html['html_footer'];
					 $html_msg['SOURCE'] = $c_from;
					 $html_msg['CUSTOMER_NAME'] = $_POST['customer_name'];
					 $html_msg['COUNTRY'] = zen_get_country_name($customers_country_id);
					 $html_msg['EMAIL_ADDRESS'] = $customers_email;
					 $html_msg['PHONE_NUMBER'] = $customers_tel;
					 $html_msg['URGENCE_LEVEL'] = $disp_order;
					 $html_msg['CONTENTS'] = $customers_other;	
            $title = "You have a new inquiry from " . $customer_name . "";

            //if ($customers_from != '11') {
                zen_mail($sales_email, $sales_email, $title, $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'insert_enquiry_to_us');
            //}
        }
        echo 'ok';
        exit;
    }
    break;


    case 'is_core_product':
        $category_id = $_POST['category_id'];
        $new_product = zen_get_categories_of_new_product($category_id);
        //新品以外的产品都当核心产品处理
        if($new_product){
            echo 'new';
        }else{
            echo 'core';
        }
        exit;
    break;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
     case 'delete':

    if ( ($action == 'delete') ){
    	$customers_id=$_POST['customers_id'];

		$sql='DELETE FROM customers_enquiry WHERE customers_id='.$customers_id.'';
		$db->Execute($sql);
		$sql_a='DELETE FROM admin_to_enquiry WHERE customers_id='.$customers_id.'';
		$db->Execute($sql_a);
		echo 'ok';
    }


    break;
	 case 'select':

    if ( ($action == 'select') ){
    	$customers_id=$_POST['customers_id'];

		$sql='SELECT * FROM customers_enquiry WHERE customers_id='.$customers_id.'';
		$res=$db->getAll($sql);

		echo json_encode($res);
    }


    break;
	case 'update':

    if ( ($action == 'update') ){
		$customers_id=$_POST['customers_id'];
    	$customer_name=$_POST['customer_name'];
		$customers_country_id=$_POST['customers_country_id'];
		$customers_email=$_POST['customers_email'];
		$enquiry_id = $db->Execute("select customers_id from customers_enquiry where customers_email='" . $customers_email . "'");

		$customers_tel=$_POST['customers_tel'];
		$customers_from=$_POST['customers_from'];
		$customers_other=$_POST['customers_other'];
		$customers_import=$_POST['customers_import'];
        $category_id=$_POST['category'];
        $order_number=$_POST['order_number'];
		$sql='UPDATE  customers_enquiry set customers_name="'.$customer_name.'",customers_country_id="'.$customers_country_id.'",customers_email="'.$customers_email.'",	customers_tel="'.$customers_tel.'",customers_from="'.$customers_from.'",customers_other="'.$customers_other.'",customers_import="'.$customers_import.'",category_id="'.$category_id.'",order_number="'.$order_number.'"  WHERE customers_id='.$customers_id.'';
		$db->Execute($sql);
		 $enquiry_admin_id=$db->Execute("SELECT admin_id FROM `admin_to_enquiry` WHERE customers_id=".$customers_id."  ");
		 $admin_id=$enquiry_admin_id->fields['admin_id'];
    	$disp_order='';
					   switch ($customers_import) {
	              case 1:
	              $disp_order='not urgent';
	              break;
	              case 2:
	              $disp_order= 'slight urgent';
	              break;
	              case 3:
	              $disp_order= 'urgent';
	              break;
	              case 4:
	              $disp_order= 'very urgent';
	              break;

		      }
			  switch ($customers_from){
					case 1:
					$c_from= 'linkedin';
					break;
					case 2:
					$c_from= 'google+';
					break;
					case 3:
					$c_from='facebook';
					break;
					case 4:
					$c_from= 'twiter';
					break;
					case 5:
					$c_from= 'youtobe';
					break;
					case 6:
					$c_from= '谷歌广告';
					break;
                  case 7:
                      $c_from= '其他';
                      break;
					case 8:
					$c_from= '电话';
					break;
					case 9:
					$c_from= 'LIVE CHAT';
					break;
					case 10:
					$c_from= '客户介绍';
					break;
					default:
                        $c_from= '其他';
					break;
				}
					  $sales_email = zen_admin_email_of_id($admin_id);
					  $html=zen_get_corresponding_languages_email_common('admin');
					 $html_msg['EMAIL_HEADER'] = $html['html_header'];
					 $html_msg['EMAIL_FOOTER'] = $html['html_footer'];
					 $html_msg['SOURCE'] = $c_from;
					 $html_msg['CUSTOMER_NAME'] = $_POST['customer_name'];
					 $html_msg['COUNTRY'] = zen_get_country_name($customers_country_id);
					 $html_msg['EMAIL_ADDRESS'] = $customers_email;
					 $html_msg['PHONE_NUMBER'] = $customers_tel;
					 $html_msg['URGENCE_LEVEL'] = $disp_order;
					 $html_msg['CONTENTS'] = $customers_other;
		 $title="You have a new inquiry from ".$customer_name."";
		 if($customers_from!='11'){
		 zen_mail($sales_email, $sales_email, $title, $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'insert_enquiry_to_us');
		 }
		echo 'ok';
    }
	break;
	case 'insert_saler':

    if ( ($action == 'insert_saler') ){
		$customers_id=$_POST['customers_id'];
    	$admin_id=$_POST['admin_id'];
		$customers_email=$_POST['customers_email'];
		$sel_sql="select admin_id from admin_to_enquiry where customers_id =".(int)$customers_id."";
		$get_admin = $db->Execute($sel_sql);

        if($get_admin->fields['admin_id']!=$admin_id){
            $sales_email = zen_admin_email_of_id($admin_id);
            $html=zen_get_corresponding_languages_email_common('admin');
					 $html_msg['EMAIL_HEADER'] = $html['html_header'];
					 $html_msg['EMAIL_FOOTER'] = $html['html_footer'];
            $html_msg['EMAIL_BODY'] = '
	     <tr>
		    <td style="background:#ffffff;"><table width="100%" border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td width="30%">E-mail address：</td>
    <td width="70%">'.$customers_email.'</td>
  </tr>

</table>
		 </td>
		</tr>';
            
            $title="You have a new inquiry from ".$customer_name."";

            zen_mail($sales_email, $sales_email, $title, $text_message, 'service@fiberstore.net', 'service@fiberstore.net', $html_msg, 'default');

        }

		if(!$get_admin->fields['admin_id']){

		$sql='INSERT INTO admin_to_enquiry(admin_id,customers_id,add_time) VALUE("'.$admin_id.'","'.$customers_id.'",now())';
		$db->Execute($sql);
		echo '分配成功';
		}else{
		$sql='UPDATE admin_to_enquiry SET admin_id='.$admin_id.' WHERE customers_id='.$customers_id.'';
		$db->Execute($sql);
		zen_update_customers_admin($admin_id,$customers_email);
		zen_update_live_chat_admin($admin_id,$customers_email);
		echo '分配成功';
		}
    }
	break;
	case 'update_sort':

	    if ( ($action == 'update_sort') ){
	        $sort_id = $_POST['sort_id'];
	        $sort_order = $_POST['sort_order'];
	        $sql='UPDATE  categories set sort_order="'.$sort_order.'" WHERE categories_id = '.$sort_id.'';
	        $db->Execute($sql);
	       echo "更新成功";
	    }
	    break;
	    case 'set_american_admin':
	        if($_POST['id']){
	            $id=$_POST['id'];

	            $db->Execute("UPDATE live_chat_admin set american_id='1' where id = '" . (int)$id . "'");
	            echo ok;
	            die;
	        }
	        break;
	    case 'cancel_american_admin':
	        if($_POST['id']){
	            $id=$_POST['id'];

	            $db->Execute("UPDATE live_chat_admin set american_id='0' where id = '" . (int)$id . "'");
	            echo ok;
	            die;
	        }
	        break;
	        // 批量更新分类排序
	        case 'onekey_update_sort':


	            $categories_sort_array=array();

	            if($_POST['categories_array']){
	                $categories_sort_array=$_POST['categories_array'];
	                $categories_sort_array = array_filter($categories_sort_array);

	                $ids = implode(',', array_keys($categories_sort_array));
	                $sql = "UPDATE categories SET sort_order = CASE categories_id ";
	                foreach ($categories_sort_array as $id => $ordinal) {
	                    $sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
	                }
	                $sql .= "END WHERE categories_id IN ($ids)";
	                $db->Execute($sql);
	                echo 'success';
	            }else{
	                echo "无分类排序";
	            }

	            break;
	            case 'insert_hot':
	                if($_POST['model_id']){
	                    $db->Execute("UPDATE products_instock_add_model set is_hot='1' where model_id = '".$_POST['model_id']."'");
	                    echo 'ok';
	                }else{
	                    echo 'error';
	                }
	                break;
	                case 'insert_multiple':
	                    if($_POST['multiple']){
	                        $db->Execute("UPDATE now_instock_multiple set instock_config_value=".$_POST['multiple']." where instock_config_name = 'instock_multiple'");
	                        echo 'ok';
	                    }else{
	                        echo 'error';
	                    }
	                    break;
	                    case 'insert_alert':
	                        if($_POST['alert']){
	                            $db->Execute("UPDATE now_instock_multiple set instock_config_value=".$_POST['alert']." where instock_config_name = 'instock_alert'");
	                            echo 'ok';
	                        }else{
	                            echo 'error';
	                        }
	                        break;

//-------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------
  } // end switch
} // end zen_not_null


?>