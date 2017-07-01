<?php 
require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  $admin_level = zen_get_admin_level($_SESSION['admin_id']);
  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if(!function_exists('judgeOrder')){
    /**
     * 通过退单号判断该订单是否为退单
     */
    function judgeOrder($products_instock_id){
      global $db;
      //在退单表中查找是否有此退单
      $sql = "SELECT `products_instock_id` FROM `products_instock_shipping_sales_after` WHERE `products_instock_id` = $products_instock_id";
      $ret = $db->getAll($sql);
      if($ret){
        return true;
      }else{
        return false;
      }
    }
  }
  
  switch ($action){
	  case 'change_ready_statu':
	  $is_ready=$_POST['is_ready'];
	  $products_instock_id=$_POST['products_instock_id'];
	  if (empty($products_instock_id)) {
	  	 exit();
	  }	  
	  	  $update_instock = array(
                        'is_ready' => $is_ready ,
		 			);		
			
	  zen_db_perform('products_instock_shipping',$update_instock,'update','products_instock_id='.$products_instock_id);
	 	  
	 $messageStack->add_session('更新成功', 'success');
     zen_redirect(zen_href_link('products_instock_shipping_declarant.php','','NONSSL'));
	  break;

	  case 'products_net_weight':

		  $products_net_weight = $_POST['products_net_weight'];
		  if($products_net_weight){
			  foreach($products_net_weight as $key=>$v){
				  if(!empty($key) && !empty($v)){
					  //$db->Execute("update products set products_net_weight = '".$v."',products_weight='".$v."',products_net_admin='".$_SESSION['admin_id']."',products_net_time= now() where products_id = $key");
					  $db->Execute("update products set products_net_weight = '".$v."',products_net_admin='".$_SESSION['admin_id']."',products_net_time= now() where products_id = $key");
				  }
			  }
		  }
	  $messageStack->add_session('更新成功', 'success');
     zen_redirect(zen_href_link('products_instock_shipping_declarant.php','','NONSSL'));
	  break;
      //A类和B类订单类型切换
      case 'change_order_type':
      $declare_type_change = $_POST['declare_type'];
      $products_instock_id=$_POST['products_instock_id'];
      if (empty($products_instock_id)) {
          exit();
      }
      $update_instock = [
          'declare_type' => $declare_type_change ,
      ];
      zen_db_perform('products_instock_shipping',$update_instock,'update','products_instock_id='.$products_instock_id);
      $messageStack->add_session('更新成功', 'success');
      zen_redirect(zen_href_link('products_instock_shipping_declarant.php','','NONSSL'));
      break;    
  }
  
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
        
<div class="fs_middle">
  <div class="fs_middle_con">
  	<div class="link_title">
        <span><a href="index.php">管理首页</a></span> / 
        <span><a href="products_instock_shipping_declarant.php">订单流程跟踪</a></span>
  </div>
<h2>订单流程跟踪</h2>

<div class="total_screening">
        
        <span class="left"><i class="icon-download-alt"></i>&nbsp;<a href="help/products_instock_shipping.doc">功能使用说明</a>&nbsp;&nbsp; 
             <img src="images/on_line.png"/>：线上订单  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
             <img src="images/lower_line.png"/> :  线下订单</span>
        <span class="right">
     <?php 
      if(isset($_GET['search']) && zen_not_null($_GET['search'])){
        $_GET['is_billing']='-1';
      }
      if(!isset($_GET['is_billing']) || !zen_not_null($_GET['is_billing'])){
        $_GET['is_billing']='1';
      }
     ?>
         报关/开票: <?php echo zen_draw_form('billing', 'products_instock_shipping_declarant.php', zen_get_all_get_params(array('billing')), 'get') .  
			zen_draw_pull_down_menu("is_billing", array( '0' => array('id' => '-1', 'text' => '所有'), 
                                                      '1' => array('id' => '1', 'text' => '开专票'),
			                                           '2' => array('id' => '2', 'text' => '不开票'),
                                                       '3' => array('id' => '3', 'text' => '开普票'),  
			                                           '4' => array('id' => '4', 'text' => 'A类订单'),
			                                           '5' => array('id' => '5', 'text' => 'B类订单'),
			), $_GET['is_billing'] ,'onChange="this.form.submit();" class="input-small"'); ?>
          </form>
          &nbsp;&nbsp;  
                
          快速搜索： <?php echo zen_draw_form('search','products_instock_shipping_declarant.php', '', 'get', '', true);
	echo '<input type="text" class="input-medium" name="search" placeholder="订单号/发票/运单号"> ' . ' ' . zen_hide_session_id();
	echo  '<button class="btn btn-info">Search</button>' ;
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
      $keywords = zen_db_prepare_input($_GET['search']);    
    }
?>
          </form>                  
          </span>
    </div>

  <table class="table table-hover products_instock_list" style="width:100%; ">
      
      <thead>
        <tr class="dataTableHeadingRow">
          <th class="dataTableHeadingContent" align="left">&nbsp;&nbsp;&nbsp;录入时间</th>
          <th class="dataTableHeadingContent" align="left">订单编号</th>
          <!-- <th class="dataTableHeadingContent" align="left">到款方式/操作人</th>   -->             
          <th class="dataTableHeadingContent" align="left">业务助理/业务员/填单助理</th>
          <th class="dataTableHeadingContent" align="left">id/产品型号/数量</th>
          <th class="dataTableHeadingContent" align="left">是否开票</th>
          <th class="dataTableHeadingContent" align="left">复检状态</th>
          <th class="dataTableHeadingContent" align="left">产品信息</th>      
          <th class="dataTableHeadingContent" align="left">发货区/转运员</th>
          <th class="dataTableHeadingContent" align="left">运输方式/单号</th>
          <th class="dataTableHeadingContent" align="left">物流员</th>                 
          <th class="dataTableHeadingContent" align="left">ready</th>                 
          <th class="dataTableHeadingContent" align="left">订单类型切换</th>
          <th class="dataTableHeadingContent" align="left">下载</th>
        </tr>
      </thead>

      <?php
     
//    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
//      $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
//      $search .= "AND (pi.order_number like '%" . $keywords . "%' 
//				      or pi.orders_num like '%" . $keywords . "%' 
//				      or pi.order_invoice like '%" . $keywords . "%'
//				      or pss.products_serial_number = '" . $keywords . "'
//                      or pis.products_first_serial_num = '" . $keywords . "'
//				      )";
//      $search = $db->bindVars($search, ':keywords:', $keywords, 'regexp');
//    }
  
   
//$sql = "select  distinct  pi.products_instock_id,
//pi.orders_id,pi.order_number,pi.order_invoice,
//pi.sales_admin,pi.delete_orders_payment,
//pi.sales_assistant,
//pi.finance_admin,
//pi.orders_num,
//pi.order_payment,
//pi.finance_time,pi.logistics_admin,
//pi.shipping_number,
//pi.shipping_method,pi.is_ready,
//pi.product_transport_admin,
//pis.products_model,pis.products_name
//       from products_instock_shipping  as pi  
//       left join products_instock_shipping_info as pis on(pi.products_instock_id = pis.products_instock_id) 
//       left join products_instock_shipping_info_serial as pss on(pis.products_shipping_info_id = pss.products_shipping_info_id)     
//       where  pi.is_billing= 1  
//       " . $search . "
//       GROUP BY pi.products_instock_id
//       ORDER BY  pi.products_instock_id desc
//       ";



    $search = '';
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
        $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
        $result= $db->Execute("select products_instock_id from order_tracking_info where tracking_number like '%".$keywords."%' limit 1");
        if($result->fields['products_instock_id']){
            $search .= "AND pi.products_instock_id=".$result->fields['products_instock_id'];
        }else{
            $search .= "AND (pi.order_number like '%" . $keywords . "%'
                  or pi.orders_num like '%" . $keywords . "%'
                  or pi.order_invoice like '%" . $keywords . "%'
                  or pi.shipping_number like '%" . $keywords . "%'
            )";
        }
    }

    $billing = '';
    $billingStatus = array('','0','1','2','3');
    if (isset($_GET['is_billing']) && zen_not_null($_GET['is_billing'])) {
      $is_billing = zen_db_input(zen_db_prepare_input($_GET['is_billing']));
      if($is_billing == '1'){
        $billing .= "AND (pis.is_billing = 1)";
        $billingStatus = array('1');
      }elseif($is_billing == '2'){
        $billing .= "AND (pis.is_billing = 2 or pis.is_billing is null)";
        $billingStatus = array('2','0','');
      }elseif($is_billing == '3'){
          $billing .= "AND (pis.is_billing = 3)";
          $billingStatus = array('3');
      }elseif($is_billing == 4){
          $billing .= "AND (pi.declare_type = 0)";
      }elseif($is_billing == 5){
          $billing .= "AND (pi.declare_type = 1)";
      }
    }
    
	/*
    if($_GET['payment_unconfirm'] !='' && $_GET['payment_unconfirm'] == 0){
    $get_confirm = zen_db_input(zen_db_prepare_input($_GET['payment_unconfirm']));
     $search1 = "AND (pi.payment_status = 0 )";
     //$search  = $db->bindVars($search, ':get_confirm:', $get_confirm, 'regexp');
    }
    */
    
//    if(isset($_GET['order_payment']) && is_numeric($_GET['order_payment'])){
//     $order_payment = zen_db_input(zen_db_prepare_input($_GET['order_payment']));
//      $search .= "AND (pi.order_payment = " . $order_payment . " )";
//      $search = $db->bindVars($search, ':order_payment:', $order_payment, 'regexp');
//    }



/* $sql = "select  pi.products_instock_id,pi.orders_id,pi.order_number,pi.is_billing,
                pi.order_invoice,pi.symbol_left,pi.order_price,pi.sales_admin,
        pi.sales_assistant,pi.sales_add_time,pi.finance_admin,
        pi.payment_status,pi.order_payment,pi.amount_recived,pi.price_symbol,pi.amount_date,pi.finance_time,
        pi.orders_num,pi.shipping_method,pi.shipping_number,pi.is_ready,pi.product_transport_admin,pi.logistics_admin
       from products_instock_shipping  as pi  
       where pi.products_instock_id > 0
       ". $search ."
       and pi.delete_orders_payment = 0
       ORDER BY  pi.products_instock_id desc
       "; */
//  pi.is_billing > 0  and 

    $sql = "select  pi.products_instock_id,pi.orders_id,pi.order_number,pi.declare_type,pis.is_billing,
                pi.order_invoice,pi.symbol_left,pi.order_price,pi.sales_admin,
        pi.sales_assistant,pi.sales_add_time,pi.finance_admin,
        pi.payment_status,pi.order_payment,pi.amount_recived,pi.price_symbol,pi.amount_date,pi.finance_time,
        pi.orders_num,pi.shipping_method,pi.shipping_number,pi.is_ready,pi.product_transport_admin,pi.logistics_admin,pi.assistant_id,is_free_shipping
       from products_instock_shipping  as pi  left join products_instock_shipping_info as pis using(products_instock_id)
       where pi.products_instock_id > 0
       ". $search . $billing ."
       and pi.delete_orders_payment = 0 and pi.is_seattle=0 and pi.cancel_order_status=0
       group by (pis.products_instock_id)
       ORDER BY  pi.products_instock_id desc
      ";
    
$split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $sql, $instock_query_numrows);
$instock = $db->Execute($sql);


$order_type =$declare= '';
$orders_id = '';
while(!$instock->EOF){
$orders_id = $instock->fields['orders_id'];

   $is_not_products = zen_get_all_products_shipping_info_id_of_instock($instock->fields['products_instock_id']);

   	$products_info_list = zen_get_products_model_of_order_shipping($instock->fields['products_instock_id']);
   	$now_inspection = zen_get_order_products_is_inspection($instock->fields['products_instock_id']);
   	
   	$Important_css ='';
   	if($now_inspection == 1 && $instock->fields['shipping_number'] =='' && in_array($admin_level,array(2,5,13))){
   	$Important_css = 'class="pro_list_bg"';
   	}
   	$order_olck = false;
   	if($now_inspection == 1 && $instock->fields['shipping_number']){
   	$order_olck = true;
   	}
    
   	//不报关订单
   	if($instock->fields['declare_type']==1){
   	$declare ='<span class="label label-warning">B类订单</span>';
   	}else{
   	$declare='';
   	}
      if($instock->fields['is_free_shipping']){
          $is_free_shipping=' <span class="label label-info">免运费</span>';
      }else{
          $is_free_shipping='';
      }
    //if ($instock->fields['delete_orders_payment']) echo "<tr class='cwbg_color'>";elseif ($instock->fields['delete_orders_payment']!=1) echo"<tr>";
    $delete_order_css =""; 
    if ($instock->fields['delete_orders_payment']){
    $delete_order_css =' class="cwbg_color"';
    }else{
    $delete_order_css ="";
    }
?>
  <tr <?php echo $Important_css.$delete_order_css;?>>
   <?php 
   	 
   switch ($instock->fields['order_payment']){
          			   
          case '1': 			  
          $payment ='Credit card';  			
          break;			    
          case '2': 			
          $payment ='Paypal';  			
          break;			 
          case '3': 		
          $payment ='Wire Transfer';			 
          break;			    
          case '4':			    
          $payment ='Net 30'; 			    
          break;
          case '5':
          $payment ='未付款，先发货';
          break;

          case '6':
		     $payment ='Credit card(800)'; 
		    break;
		    case '7':
		     $payment ='Net 30(已到款)'; 
		    break;
		    case '8':
		     $payment ='Western Union'; 
		    break;
		    case '9':
		     $payment ='货到付款'; 
		    break;
		    default:
		     $payment ='未到款';
		 break;
          
   	   }      
   	  
   	   if ($instock->fields['order_payment']=='5' || $instock->fields['order_payment']=='4' || $instock->fields['order_payment']=='6') {
   	          $payment_icon ='<img title="未付款，先发货" alt="未付款，先发货" src="./images/yellowfs_icon.png">';	
   	   }else{  	      	    	  		    
		      $payment_icon ='<img title="已付款" alt="已付款" src="./images/greenfs_icon.png">';		        	   
	}   	   
   	   
   	    
   if($orders_id){
   $order_type='<img src="images/on_line.png"/>';
   }else{
   $order_type='<img src="images/lower_line.png"/>';
   }   
   
 
    echo "
	    <td>".$order_type.'&nbsp;<a href="####"><span>'.date("Y.m.d",strtotime($instock->fields['finance_time'])).
	    '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date("H:i",strtotime($instock->fields['finance_time']))."</span></a>";	
	echo "</td>";
	
	echo "<td>".$declare.$is_free_shipping.'<br/>'.$instock->fields['orders_num']."<br />";
	if($instock->fields['order_number']){
	echo $instock->fields['order_number'];
	}else{
	echo $instock->fields['order_invoice'];
	}
	echo "</td> ";

/* 	echo "<td>";
      if (empty($instock->fields['finance_admin'])){
      	echo $payment_icon.$payment;
      }else{
	    
		echo $payment_icon.$payment."<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".zen_get_admin_name($instock->fields['finance_admin']);
	  }	 
	echo "</td>"; */
 
	echo "<td>";
  echo  empty($instock->fields['assistant_id']) ? '--' : zen_get_admin_name($instock->fields['assistant_id']);
  echo '/';echo '<br/>';
  echo  empty($instock->fields['sales_admin']) ? '--' : zen_get_admin_name($instock->fields['sales_admin']);
  echo '/';echo '<br/>';
  echo  empty($instock->fields['sales_assistant']) ? '--' : zen_get_admin_name($instock->fields['sales_assistant']);
  echo "</td>";
  
 ?>   
 
       <?php 
         echo "<td>";
         if(sizeof($products_info_list)!==0){
      	/**
    		 * 订单所包含的产品
    		 */
    
    	  for($r=0;$r<sizeof($products_info_list);$r++){
          if(in_array($products_info_list[$r]['is_billing'],$billingStatus)){
        	  if($products_info_list[$r]['products_model']){
              $products_model = $products_info_list[$r]['products_model'];
            }else{
              $products_model = zen_get_products_model($products_info_list[$r]['products_id']);
            }
            $products_num =$products_info_list[$r]['products_num'] ? $products_info_list[$r]['products_num']:'--';
            $NW = zen_get_fs_products_net_weight((int)$products_info_list[$r]['products_id']);
            $products_image = zen_get_products_image_of_products_id($products_info_list[$r]['products_id']);
            $wenhao = '';
            $shippingInfo = fs_get_data_from_db_fields_array(array('shipping_brand','shipping_model','shipping_remarks'),'products_instock_shipping_info',"products_shipping_info_id = '{$products_info_list[$r]['id']}'","");
            $wenhoa_content = '<img width="50" height="50" src="../images/'.(($products_image && file_exists('../images/'.$products_image)) ? $products_image : 'no_picture.gif').'"/>';
            if($shippingInfo[0][0]||$shippingInfo[0][1]){
              $wenhoa_content .= '<br/>发货专员备注：';
              $wenhoa_content .= $shippingInfo[0][0]?'<br/>品牌：'.$shippingInfo[0][0]:'';
              $wenhoa_content .= $shippingInfo[0][1]?'<br/>型号名：'.$shippingInfo[0][1]:'';
              $wenhoa_content .= $shippingInfo[0][2]?'<br/>其他：'.$shippingInfo[0][2]:'';
            }
            $wenhao = $fs_manage_html_structure->fs_manage_html_question_text($wenhoa_content);
            //print_r($products_info_list[$r]);
        		/**如果是已经生成退换货的产品，并且是退单*/
        		if($products_info_list[$r]['sales_service'] == 1 && judgeOrder($instock->fields['products_instock_id'])){
    				  //echo $products_info_list[$r]['products_id'];
        			/**如果是退货  sales_service == 1*/
        			echo '<ul style="background:gray" class="instock_products_alignment">'.$products_info_list[$r]['products_id'].' '.$products_model.
        			     '&nbsp;'.'【'.$products_num.'】<b>N.W：</b>'.($NW?$NW:'0.0000').$wenhao.'</ul>';
        		}else{
        			echo '<ul class="instock_products_alignment">'.$products_info_list[$r]['products_id'].' '.$products_model.
        			     '&nbsp;'.'【'.$products_num.'】<b>N.W：</b>'.($NW?$NW:'0.0000').$wenhao.'</ul>';
        		}
          }
      	}
      }else{
      	echo "--";
      }
    	echo "</td>";	
    	
    	/*是否开票*/
    	echo "<td>";
    	for($r=0;$r<sizeof($products_info_list);$r++){
        if(in_array($products_info_list[$r]['is_billing'],$billingStatus)){
      	  if($products_info_list[$r]['is_billing'] == 1){
      	    echo '开专票<br/>';
      	  }elseif ($products_info_list[$r]['is_billing'] == 2){
      	    echo '<span class="label label-important">不开票</span><br/>';
      	  }elseif ($products_info_list[$r]['is_billing'] == 3){
            echo '<span class="label label-info">开普票</span><br/>';
          }else{
      	    echo '-<br/>';
      	  }
    	 }
    	}
    	echo "</td>";
    	echo "<td>";
    	if(sizeof($products_info_list)!==0){
    	  /*** 订单所包含的产品***/
    	  for($r=0;$r<sizeof($products_info_list);$r++){
          $out_info = array();
    	    $out_info = fs_get_data_from_db_fields_array(array('out_num','recheck_admin','recheck_date'),"products_instock_shipping_info","products_shipping_info_id='{$products_info_list[$r]['id']}'","");
    	    /* $wenhao = '';
    	     $shippingInfo = fs_get_data_from_db_fields_array(array('shipping_brand','shipping_model','shipping_remarks'),'products_instock_shipping_info',"products_shipping_info_id = '{$products_info_list[$r]['id']}'","");
    	    $wenhoa_content = '';
    	    if($shippingInfo[0][0]||$shippingInfo[0][1]){
    	    $wenhoa_content .= '发货专员备注：';
    	    $wenhoa_content .= $shippingInfo[0][0]?'<br/>品牌：'.$shippingInfo[0][0]:'';
    	    $wenhoa_content .= $shippingInfo[0][1]?'<br/>型号名：'.$shippingInfo[0][1]:'';
    	    $wenhoa_content .= $shippingInfo[0][2]?'<br/>其他：'.$shippingInfo[0][2]:'';
    	    $wenhao = $fs_manage_html_structure->fs_manage_html_question_text($wenhoa_content);
    	    } */
    	    if($out_info[0][0]>0){
    	      echo '<img title="已收货" alt="已收货" src="./images/greenfs_icon.png">已复检('.$out_info[0][0].') / '.date('Y-m-d',strtotime($out_info[0][2])).' / '.zen_get_admin_name($out_info[0][1]).'<br/>';
    	    }else{
    	      echo '<img title="已收货" alt="已收货" src="./images/redfs_icon.png">未复检<br/>';
    	    }
    	  }
    	}else{
    	  echo "--";
    	}
    	echo "</td>";
     ?>
 
     <td>
     <a href="#myModal_detail_<?php echo $instock->fields['products_instock_id'];?>"  id="<?php echo $instock->fields['products_instock_id'];?>" data-toggle="modal">
               详情</a>             
           <div id="myModal_detail_<?php echo $instock->fields['products_instock_id'];?>" class="modal hide fade instock_sales" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">×</button>
              <h3 id="myModalLabel">产品详情</h3>
            </div>
            <div class="order_product_shipping_manage">
			<?php
			 echo zen_draw_form('net_weight', 'products_instock_shipping_declarant.php', 'action=products_net_weight', 'post');?>
          	<table border="0" cellspacing="0" cellpadding="5" class="order_product_logist">
                  <tr>
				  <th width="10%">产品ID </th>
                  	<th width="10%">图片 </th>
                    <th width="30%">名称/属性/备注</th>
                    <th width="10%">型号 </th>
                    <th width="1o%">数量 </th>
					<th width="10%">产品净重 </th>
					<th width="10%">是否开票</th>
                  </tr>
<?php 
	for($r=0;$r<sizeof($products_info_list);$r++){	
  if(in_array($products_info_list[$r]['is_billing'],$billingStatus)){
	     if($instock->fields['products_model']){
	     $products_model = $instock->fields['products_model'];
	     }else{
	     $products_model =zen_get_products_model($products_info_list[$r]['products_id']);
	     }
		$products_model =zen_get_products_model($products_info_list[$r]['products_id']);
		$products_image = zen_get_products_image_of_products_id($products_info_list[$r]['products_id']);
		$products_num =$products_info_list[$r]['products_num'] ? $products_info_list[$r]['products_num']:'--';
		echo"<tr><td>".$products_info_list[$r]['products_id']."</td><td>";
		echo '<a target="_blank" href=http://www.feisu.com/index.php?main_page=product_info&products_id='.(int)$products_info_list[$r]['products_id'].'>
		  <img width="50" height="50" src="../images/'.(($products_image && file_exists('../images/'.$products_image)) ? $products_image : 'no_picture.gif').'"/>
		  </a>';
		echo"</td><td>";
		echo $instock->fields['products_id'] ? zen_get_products_name($instock->fields['products_id']).'<br />' : $instock->fields['products_name'].'<br />';			
		echo '<span class="products_message_info">特性备注：'.$products_info_list[$r]['products_message'].'</span></td>';
		echo "<td>".$products_model."</td>";	
		echo "<td>".$products_num."</td>";
		echo "<td><input type='text' name='products_net_weight[".$products_info_list[$r]['products_id']."]' value='".zen_get_fs_products_net_weight((int)$products_info_list[$r]['products_id'])."' class='input-mini'></td>";
		echo '<td>';
	    if($products_info_list[$r]['is_billing'] == 1){
	     echo '开专票<br/>';
	     }elseif ($products_info_list[$r]['is_billing'] == 2){
	     echo '不开票<br/>';
	     }elseif ($products_info_list[$r]['is_billing'] == 3){
            echo '开普票<br/>';
        }else{
	     echo '-<br/>';
	    }
       echo '</td>';
		echo "</tr>";		
		}
	}
		?>
		<tr>
		<td></td>
                  	<td></td>
                    <td colspan="4"><input type="submit" name="submit" value="提交" class="btn btn-info"></td>
                   
                  </tr>
              </table>
			  </form>
            </div>  
    </td>
   
<?php     
    if($instock->fields['logistics_admin']){
   		$logs_admin = zen_get_admin_name($instock->fields['logistics_admin']);
  	}else{
		$logs_admin ='--';
	}
    echo "<td>";
    
    $product_transport_admin = zen_get_logistics_admin_name($instock->fields['product_transport_admin']) ? zen_get_logistics_admin_name($instock->fields['product_transport_admin']) :"--";
    
    if(zen_get_delivery_area($instock->fields['products_instock_id'])){
    echo zen_get_delivery_area($instock->fields['products_instock_id']).$product_transport_admin;
    }else{
    echo '--<br />';
    }
    echo "</td>";
   
   if(!empty($instock->fields['shipping_number'])){
	echo "<td>".$instock->fields['shipping_method'].'<br />'.$instock->fields['shipping_number']."</td>";
	}else{
	echo "<td>";
	echo '--<br />';
	echo "</td>";
   }

    echo "<td>". $logs_admin ."</td>";
      
    echo "<td>";
		 echo zen_draw_form('is_ready', 'products_instock_shipping_declarant.php', 'action=change_ready_statu', 'post') .  		 
		      '<input type="hidden"  name="products_instock_id"  value="'.$instock->fields['products_instock_id'].'"  />'.
			  zen_draw_pull_down_menu("is_ready", array(  '0' => array('id' => ' ', 'text' => 'no'), 			                         
			                                              '1' => array('id' => '1', 'text' => 'yes'), 		                                                 
			), $instock->fields['is_ready'] ,' class="input-mini"  onChange="this.form.submit();"'); 
    echo "</form></td>";
    echo "<td>";
    //订单类型切换,A ，B 类型切换
    echo zen_draw_form('declare_type', 'products_instock_shipping_declarant.php', 'action=change_order_type', 'post') .
        '<input type="hidden"  name="products_instock_id"  value="'.$instock->fields['products_instock_id'].'"  />'.
        zen_draw_pull_down_menu("declare_type", array(
            '0' => array('id' => '0', 'text' => 'A类订单'),
            '1' => array('id' => '1', 'text' => 'B类订单'),
        ), $instock->fields['declare_type'] ,' class="input-mini declare_type" ');
    echo "</form></td>";
    ?>

    <td>  
	<div class="input_btn">
          <div class="btn-group">
            <button data-toggle="dropdown" class="btn btn-info">操作<span class="caret"></span></button>
            <ul class="dropdown-menu" style="min-width:90px;">                         
            <li>
			    <a href='products_instock_shipping_declarant_pay_download_excel.php?products_instock_id=<?php echo $instock->fields['products_instock_id'];?> '>
					 出口模板
				</a>
            </li>
            <li>
			    <a href='products_instock_shipping_declarant_download_excel.php?products_instock_id=<?php echo $instock->fields['products_instock_id'];?> '>
					 出口模板(USD)
				</a>
            </li>

            <li>
                <a href='products_instock_shipping_sales_arrival_invoice_download.php?products_instock_id=<?php echo $instock->fields['products_instock_id'];?> '>
		        invoice(原币制)
	            </a>	            
            </li>
               

            <li>
                <a href='us_products_instock_shipping_sales_arrival_invoice_download.php?products_instock_id=<?php echo $instock->fields['products_instock_id'];?> '>
		        invoice(USD)
	            </a>	            
            </li>

            <li>
				<a href="<?php echo zen_href_link('products_instock_shipping_logistics_print.php','shipping=all&instock_id='.$instock->fields['products_instock_id']);?>" target="_blank">                   
			          打印包装清单
			    </a>
            </li>                                  
           </ul>
         </div> 
    </div>
	</td>  
	
        <?php 
			echo '</tr>';
	  $instock->MoveNext();
    }
?>

      <tr>
        <td colspan="12" style="text-align:center;"><?php echo $split->display_links($instock_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'id'))); ?></td>
      </tr>
    </table>
    <?php //}?>
  </div>
</div>
<?php  require(DIR_WS_INCLUDES . 'footer.php');?>

<script type="text/javascript" src="includes/javascript/jquery-1.7.1.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="js/chart.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
<script type="text/javascript" src="js/bootbox.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(".declare_type").on("change",  function() {
            bootbox.confirm({
                message: "确认要更改订单类型?",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (result) {
                    if(result){
                        $(this).parent('form').submit();
                      //  $('form[name="declare_type"]').submit();
                    }
                }
            });
        });
    });

</script>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>