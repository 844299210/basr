<?php 
  require('includes/application_top.php');
  require DIR_WS_CLASSES . 'fiberstore_all_categories_list.php';
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
 $admin_level = zen_get_admin_level($_SESSION['admin_id']);
  $admin_id    = $_SESSION['admin_id'] ;

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  
if(!in_array($admin_level,array(1,2,6,13))){
     exit('没有权限操作');
  }


  switch ($action){


	  case 'confirm_receipt':

			$id = (int)$_POST['id'];
			$num = (int)$_POST['num'];
			$db->Execute("update purchase_apply_instock_info set inventory_number = '$num',inventory_status='1' where info_id='$id'");

			echo "ok";exit;


   
    case 'ajax_delete_order_product':
        $info_id = zen_db_prepare_input($_POST['info_id']);
        $apply_id = zen_db_prepare_input($_POST['apply_id']);
     if (zen_not_null($info_id) && zen_not_null($apply_id) ) {
        $db->query('DELETE FROM `purchase_apply_instock_info` WHERE `info_id` ='.$info_id);   
        
        
            $get_shipping_price = $db->Execute(" SELECT shipping_price FROM `purchase_apply_instock`
	                                            WHERE `apply_id`='".$apply_id."'  ");             
            $shipping_price = $get_shipping_price->fields['shipping_price'];

            $get_apply_price = $db->Execute(" SELECT products_price,products_num FROM `purchase_apply_instock_info`
	                                          WHERE `apply_id`='".$apply_id."'  ");  
            $order_price = 0 ;
	        while (!$get_apply_price->EOF){    
                   $order_price += $get_apply_price->fields['products_price']*$get_apply_price->fields['products_num'] ;
                   $get_apply_price->MoveNext();
            }	        
	        zen_db_perform('purchase_apply_instock',array('order_price' => $order_price,
	                                                      'total_price'=>$order_price+$shipping_price) , 'update' ,' `apply_id`='.$apply_id );
        
	        
     	exit('ok');
     }else {  exit('err');  }   
    break ;
  
  //下载excel
  //已付款页面导出excel
 case 'download_no_apply':
  require '../PHPExcel.php';
  require '../PHPExcel/Writer/Excel5.php';  
 
 if($_POST['apply_id']){
   $where =" WHERE pa.apply_id = pi.apply_id and pa.info_id in(".join(',',$_POST['apply_id']).")";
 }
  
 $sql ="SELECT pi.apply_id,pi.apply_number,pi.apply_type,pi.apply_date,pi.order_price,pi.manufacturer,pi.method_payment,pi.shipping_price,pi.order_comment,
              pi.receive_statu,pi.pay_statu,pi.pay_comment,pi.is_invoice,pi.purchase_admin,pi.purchase_vote,pi.finance_vote,pi.finance_admin
       FROM `purchase_apply_instock` pi, `purchase_apply_instock_info` pa".$where."
       GROUP BY pi.apply_id ORDER BY  apply_id DESC"  ;
 
 $purchase_instock = $db->Execute($sql);					   

//设置宽度          
$objPHPExcel = new PHPExcel ();
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);


//设置高度
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);

//第一行标题
$objPHPExcel->getActiveSheet()->setCellValue('A1', '采购订单');
$objPHPExcel->getActiveSheet()->setCellValue('B1', '采购员');
$objPHPExcel->getActiveSheet()->setCellValue('C1', '录入时间');
$objPHPExcel->getActiveSheet()->setCellValue('D1', '产品名称');
$objPHPExcel->getActiveSheet()->setCellValue('E1', '数量');
$objPHPExcel->getActiveSheet()->setCellValue('F1', '采购备注');
     
       
$product_info ='';
$currencies_value  = zen_get_currencies_value_of_code('CNY');
while(!$purchase_instock->EOF){
$product_info = zen_get_purchase_apply_instock_info($purchase_instock->fields['apply_id'],$_POST['apply_id']) ;

			 switch ($purchase_instock->fields['apply_type']){
			 case  '0':
			 $apply_type='库存申请';
			 break;
			 case  '1':
			 $apply_type='退单';
			 break;
			 case  '2':
			 $apply_type='日常订单';
			 break;
			 default :
			 $apply_type='日常订单';
			 break;
			 }
			 
	 switch ($purchase_instock->fields['method_payment']){
	 case '0':
	  $s_method_payment ='未结';
	 break;
	 case '1':
	  $s_method_payment ='月结';
	 break;
	 case '2':
	  $s_method_payment ='现结';
	 break;
	 case '3':
	  $s_method_payment ='半月结';
	 break;
	 case '4':
	  $s_method_payment ='周结';
	 break;
	 case '5':
	  $s_method_payment ='货到付款';
	 break;
	 case '6':
	  $s_method_payment ='快递代收';
	 break;
	 case '7':
	  $s_method_payment ='淘宝拍';
	 break;
	 case '8':
	  $s_method_payment ='先款后货';
	 break;
	 case '9':
	  $s_method_payment ='预付定金';
	 break;
	 default:
	  $s_method_payment ='';
	 break;
	 }
            for ($i = 0,$n=sizeof($product_info); $i < $n; $i++) {
            
			 $numid ++;
			 $now_line = 1+$numid;
			 $row_height=1;            
            
            
            
	          $orders_num = $product_info[$i]['orders_num'] ;
	          $products_name = zen_get_products_name($product_info[$i]['products_id']) .FS_products_name_description($product_info[$i]['products_id'])."\n" ;    	
              $products_num = $product_info[$i]['products_num']."\n";	                                   
              $purchase_comment_info = $product_info[$i]['purchase_info'] ;
              
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$now_line , $purchase_instock->fields['apply_number']."\n" .$apply_type.$orders_num);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$now_line , zen_get_admin_name($purchase_instock->fields['purchase_admin']));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$now_line , date("Y.m.d",strtotime($purchase_instock->fields['apply_date']))."\n" .".".date("H:i",strtotime($purchase_instock->fields['apply_date'])) );
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$now_line , $products_name);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$now_line , $products_num);		
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$now_line , $purchase_comment_info);		
			$objPHPExcel->getActiveSheet()->getRowDimension($now_line)->setRowHeight((25*$row_height));              
              
              
              
            }  
			 

	$purchase_instock->MoveNext();	
}

$filename = '未付款采购单'.date('Y.m.d').".xls";	

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
header('Content-Type: application/vnd.ms-excel'); 
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0'); 
$objWriter->save('php://output'); 

exit; 
break;  
    
  //批量审核
    case  'check_select':
    $apply_id = $_POST['apply_id'];
     if(sizeof($apply_id)){
       for($a=0;$a<sizeof($apply_id);$a++){
         zen_db_perform('purchase_apply_instock',array('receive_statu' => 2,'purchase_vote' => $admin_id) , 'update' ,' `apply_id`='.$apply_id[$a] );
       }
     }
    break;
  
    //批量更新信息
    case  'update_products_info':
      //$pid = $_POST['products'];    
      $pid = $_POST['apply_id'];
      
      if(sizeof($pid)){
           $apply_id_flag = (int)fs_get_data_from_db_fields('apply_id','purchase_apply_instock','apply_id IN ('.join(',',$pid).') AND  purchase_admin !='.$_SESSION['admin_id'] ,'');	
       
           if ($apply_id_flag > 0) {
           $messageStack->add_session('更新失败       不允许勾选别人的订单更新', 'fail');   
           zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass','NONSSL')); 
           }    
      }else{
           $messageStack->add_session('更新失败', 'fail');   
           zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass','NONSSL'));     
      }
      
      
      
      if(sizeof($pid)){
	      for($i=0;$i<sizeof($pid);$i++){
	        $instock_zone = array(
            'manufacturer' => $_POST['manufacturer_'.$pid[$i]],
		    );	
    	   zen_db_perform('purchase_apply_instock',$instock_zone,'update','apply_id='.$pid[$i]);
	     }
      }
   
      $InfoID = $_POST['info_id'];
      if(sizeof($InfoID)){
	      for($d=0;$d<sizeof($InfoID);$d++){
	      
	          $apply_id = fs_get_data_from_db_fields('apply_id','purchase_apply_instock_info','info_id='.$InfoID[$d],'');	          
	          if (!in_array($apply_id,$pid)) {  continue ; }   //未选中的不更新
	      
	         $info = array(
            'products_price' => $_POST['products_price_'.$InfoID[$d]],
	        'shipping_price' => $_POST['shipping_price_'.$InfoID[$d]],
            'products_rate' => $_POST['products_rate_'.$InfoID[$d]],
	        'purchase_info' => $_POST['purchase_info_'.$InfoID[$d]],
		    );	
    	   zen_db_perform('purchase_apply_instock_info',$info,'update','info_id='.$InfoID[$d]);

    	   
    	 // 同步到订单流程  	 只有财务驳回的  才同步    	 
         $receive_statu = fs_get_data_from_db_fields('receive_statu','purchase_apply_instock','apply_id='.$apply_id,'');	 
         if ($receive_statu == -1) {
    	     $products_shipping_info_id = fs_get_data_from_db_fields('products_shipping_info_id','purchase_apply_instock_info','info_id='.$InfoID[$d],'');  	     
    	     if ($products_shipping_info_id) {  	   
    	     $applyInfo = array( 
						 'purchase_price' => $_POST['products_price_'.$InfoID[$d]],
                         'InvoiceValue' =>   $_POST['products_rate_'.$InfoID[$d]],
                         'purchase_shipping_price' =>  $_POST['shipping_price_'.$InfoID[$d]],
    	                 'purchase_info'=>   $_POST['purchase_info_'.$InfoID[$d]],
    	                 'suppliers'    =>   $_POST['manufacturer_'.$apply_id],
                        );
             zen_db_perform('products_instock_shipping_info',$applyInfo,'update','products_shipping_info_id='.$products_shipping_info_id);
    	      }    
         }
         

	      }
      }
      $messageStack->add_session('更新成功', 'success');   
      zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass','NONSSL'));	  
    break;
    
    //采购驳回备注
    case  'purchase_pass':
      $apply_id = $_POST['applyId'];
     if (zen_not_null($apply_id) ) {
     	zen_db_perform('purchase_apply_instock',array('receive_statu' => 0,'purchase_vote' => $admin_id,'pass_comment'=>$_POST['pass_comment']) , 'update' ,' `apply_id`='.$apply_id );
     	//不通过的,在订单流程中变成未下单状态(订单和采购单)
     	  $applysql = $db->Execute("select products_shipping_info_id from purchase_apply_instock_info where apply_id =".(int)$apply_id);
     		 while (!$applysql->EOF) {
     		    if($applysql->fields['products_shipping_info_id']){
			    $db->Execute("update products_instock_shipping_info  set products_is_download=0,is_apply=0 where products_shipping_info_id=".(int)$applysql->fields['products_shipping_info_id']);
     		    }
		     $applysql->MoveNext();
	      }
     	
     	$messageStack->add_session('已驳回采购单', 'success');   
     }else {  exit('err');  } 
     $page= $_POST['page'] ? '&page='.$_POST['page'] : '';
     zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass'.$page,'NONSSL'));
    break;
    
    //更新折扣价
    case  'update_discount_price':
      $pid = $_POST['products'];
      if(sizeof($pid)){
	      for($i=0;$i<sizeof($pid);$i++){
	        $instock_zone = array(
            'discount_price' => $_POST['discount_price_'.$pid[$i]],
		    );	
    	   zen_db_perform('purchase_apply_instock',$instock_zone,'update','apply_id='.$pid[$i]);
	     }
      }

      $messageStack->add_session('更新成功', 'success');   
      zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=submit_pay','NONSSL'));	  
    break;
    
    //取消订单
    case 'cancel_product':
     if ($_POST['a_id']) {
           $db->Execute("update purchase_apply_instock set return_goods =1 where apply_id =".$_POST['a_id']." ");
    
     	exit('ok');
     }
    break;
    
      case 'ajax_delete_order':
     $apply_id = zen_db_prepare_input($_POST['a_id']);

     if (zen_not_null($apply_id) ) {
        $db->query('DELETE FROM `purchase_apply_instock` WHERE `apply_id` ='.$apply_id);
        $db->query('DELETE FROM `purchase_apply_instock_info` WHERE `apply_id` ='.$apply_id);   
     	exit('ok');
     }else {  exit('err');  }   
    break ;
  
  
      //采购运费
    case 'update_shipping_price':
         if($_POST['apply_id']){
           $db->Execute("update purchase_apply_instock set shipping_price ='".$_POST['shipping_price']."' where apply_id =".$_POST['apply_id']." ");
         }
        zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_apply','NONSSL'));	 
    break;
  
  
    case 'receive_product':
     $apply_id = zen_db_prepare_input($_POST['a_id']);
     $statu = zen_db_prepare_input($_POST['statu']);
     if (zen_not_null($apply_id) ) {
     	zen_db_perform('purchase_apply_instock',array('receive_statu' =>2,'purchase_vote' => $admin_id) , 'update' ,' `apply_id`='.$apply_id );
     	//不通过的,在订单流程中变成未下单状态(订单和采购单)
     	exit('ok');
     }else {  exit('err');  }   
    break ;

    case 'move_product':
       if($_POST['a_id']){

          $applysql = $db->Execute("select products_shipping_info_id from purchase_apply_instock_info where apply_id =".(int)$_POST['a_id']);
     		 while (!$applysql->EOF) {
     		    if($applysql->fields['products_shipping_info_id']){
			    $db->Execute("update products_instock_shipping_info  set is_apply=0 where products_shipping_info_id=".(int)$applysql->fields['products_shipping_info_id']);
     		    }
		     $applysql->MoveNext();
	      }
	      
         $db->Execute("delete from purchase_apply_instock where apply_id =".(int)$_POST['a_id']);
         $db->Execute("delete from purchase_apply_instock_info where apply_id =".(int)$_POST['a_id']);	      
	      
       }
    break; 
    
   case 'move_one_product':
       if($_POST['id']){
          $applysql = $db->Execute("select products_shipping_info_id from purchase_apply_instock_info where  info_id =".(int)$_POST['id']);    	       
          if($applysql->fields['products_shipping_info_id']){
          $db->Execute("update products_instock_shipping_info  set is_apply=0 where products_shipping_info_id=".(int)$applysql->fields['products_shipping_info_id']);    	  
          }
          $db->Execute("delete from purchase_apply_instock_info where info_id =".(int)$_POST['id'] );	      
          exit('ok');	      
       }
    break; 
    
   case 'edit_purchase_order':	       
        $apply_id = zen_db_prepare_input($_POST['apply_id']);
        $manufacturer = zen_db_prepare_input($_POST['manufacturer']); 
        $apply_type = zen_db_prepare_input($_POST['apply_type']); 
                
        $shipping_price = zen_db_prepare_input($_POST['shipping_price']); 
        $order_comment = zen_db_prepare_input($_POST['order_comment']); 
                      
        $info_id = zen_db_prepare_input($_POST['info_id']);
	    $products_name = zen_db_prepare_input($_POST['products_name']);
        $products_num = zen_db_prepare_input($_POST['products_num']);
        $products_price = zen_db_prepare_input($_POST['products_price']); 
        $products_rate = zen_db_prepare_input($_POST['products_rate']);   
        
        
	    $new_products_name = zen_db_prepare_input($_POST['new_products_name']);
        $new_products_num = zen_db_prepare_input($_POST['new_products_num']);
        $new_products_price = zen_db_prepare_input($_POST['new_products_price']); 
        $new_products_rate = zen_db_prepare_input($_POST['new_products_rate']);          
        

	  $purchase_order = array(  'manufacturer' => $manufacturer ,
	                            'apply_type' => $apply_type, 
	                            'shipping_price' => $shipping_price, 
	                            'order_comment' => $order_comment );				
	  zen_db_perform('purchase_apply_instock',$purchase_order,'update','apply_id='.$apply_id );          
	  
	  for ($i = 0, $n=sizeof($info_id) ; $i < $n; $i++) {
	      if (zen_not_null($products_name[$i]) && zen_not_null($products_num[$i]) && zen_not_null($products_price[$i])) {	      	 
	      	 zen_db_perform('purchase_apply_instock_info',array( 'products_model' => $products_name[$i] ,  
														      	 'products_num' => $products_num[$i] , 
														      	 'products_price' => $products_price[$i],
	      	                                                     'products_rate' => $products_rate[$i] ),'update','info_id='.$info_id[$i]);		      	       	
	      }	  	
	  }	  
	  	  
	  
	  
	 for ($i = 0, $n=sizeof($new_products_name) ; $i < $n; $i++) {
	      if (zen_not_null($new_products_name[$i]) && zen_not_null($new_products_num[$i]) && zen_not_null($new_products_price[$i])) {	      	 
	      	 zen_db_perform('purchase_apply_instock_info',array( 'apply_id'=> $apply_id ,
	      	                                                     'apply_type' => $apply_type, 
	      	                                                     'products_model' => $new_products_name[$i] ,  
														      	 'products_num' => $new_products_num[$i] , 
														      	 'products_price' => $new_products_price[$i],
	      	                                                     'products_rate' => $new_products_rate[$i] ));		      	       	
	      }	  	
	  }	

	  
	  $get_apply_price = $db->Execute(" SELECT products_price,products_num FROM `purchase_apply_instock_info`
	                                    WHERE `apply_id`='".$apply_id."'  ");  
	  if ($get_apply_price->RecordCount() > 0) {
            $order_price = 0 ;
	        while (!$get_apply_price->EOF) {    
                        $order_price += $get_apply_price->fields['products_price']*$get_apply_price->fields['products_num'] ;
                   $get_apply_price->MoveNext();
            }	        
	        zen_db_perform('purchase_apply_instock',array('order_price' => $order_price,
	                                                      'total_price'=>$order_price+$shipping_price) , 'update' ,' `apply_id`='.$apply_id );
	  }
	 	  
	  $messageStack->add_session('更新成功', 'success');
      zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass','NONSSL'));
    break;	
    
    //生成申请付款单
    case  'submit_pay_order':
      $apply_id = $_POST['apply_id'];
      if(sizeof($apply_id)){
        
        $order_or_instck='';$apply_type='';
        for($i=1;$i<sizeof($apply_id);$i++){
	       // $order_or_instck = fs_get_data_from_db_fields('apply_type','purchase_apply_instock','apply_id="'.(int)$apply_id[$i].'"','');
	        //if($order_or_instck == 2){
	        //$apply_type = ",apply_type =2";
	        //}
          $db->Execute("update purchase_apply_instock_info set apply_id =".(int)$apply_id[0]." where  apply_id =".(int)$apply_id[$i]);
          $db->Execute("delete from purchase_apply_instock where apply_id =".(int)$apply_id[$i]);
        }
        
        $db->Execute("update purchase_apply_instock set apply_number=REPLACE(apply_number,'CG','CF'),return_goods =2".$apply_type." where  apply_id =".(int)$apply_id[0]); 
      }
      $messageStack->add_session('申请付款成功', 'success');
      zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_apply','NONSSL'));
    break;

    //分期支付
    case  'all_payment':

    // price=100&installment=on&id=145
    if($_POST['price']){
      $db->Execute("insert into purchase_apply_instock_pay (apply_id,price,status) values(".$_POST['id'].",".$_POST['price'].",0)");
    }  
    
    if($_POST['installment'] && $_POST['installment']==1 ){
    $db->Execute("update purchase_apply_instock set pay_statu=0,installment = ".(int)$_POST['installment']." where apply_id=".(int)$_POST['id']);
    }else{
    $db->Execute("update purchase_apply_instock set pay_statu=0 where apply_id=".(int)$_POST['id']);  
    }

    break;
    
	case 'add_purchase_order':	
        $manufacturer = zen_db_prepare_input($_POST['manufacturer']); 
        $apply_type = zen_db_prepare_input($_POST['apply_type']); 
                     
	    $products_name = zen_db_prepare_input($_POST['products_name']);
        $products_num = zen_db_prepare_input($_POST['products_num']);
        $products_price = zen_db_prepare_input($_POST['products_price']); 
        $products_rate = zen_db_prepare_input($_POST['products_rate']);   
        $method_payment = $_POST['method_payment'];
        
        $shipping_price = zen_db_prepare_input($_POST['shipping_price']);   
        //$order_comment = zen_db_prepare_input($_POST['order_comment']);   
        $purchase_info = zen_db_prepare_input($_POST['purchase_info']);   
        
        
		// && zen_not_null(implode($products_name))
       if (zen_not_null($manufacturer) && zen_not_null(implode($products_num)) && zen_not_null(implode($products_price)) ) {       	  
       }else { exit('请填写完整'); }	
       
      $apply_number = zen_get_purchase_apply_instock_number();	     
	  $purchase_order = array(  'apply_number' => $apply_number ,
	                            'manufacturer' => $manufacturer ,
	                            'apply_type' => $apply_type ,           
	                            'purchase_admin' => $admin_id ,	                    
	                            'apply_date' => 'now()' ,	  
	                            'receive_statu' => 1 ,	  
	                            'method_payment' => $method_payment ,	  
	                           // 'order_comment' => $order_comment ,	  
	                             );				
	  zen_db_perform('purchase_apply_instock',$purchase_order);
	  $get_apply_id = $db->insert_ID();           
	  
	  //zen_not_null($manufacturer[$i]) &&
	  for ($i = 0, $n=sizeof($products_num) ; $i < $n; $i++) {
	      if (zen_not_null($products_num[$i]) && zen_not_null($products_price[$i])) {	      	 
	      	 zen_db_perform('purchase_apply_instock_info',array( 'apply_id' => $get_apply_id , 
	      	                                                     'apply_type' => $apply_type ,  
	      	                                                     'orders_num' => $apply_number ,  
	      	                                                     'products_id' => (int)$products_name[$i] ,  
														      	 'products_num' => $products_num[$i] , 
														      	 'products_price' => $products_price[$i],
	      	                                                     'shipping_price' => $shipping_price[$i] ,
	      	                                                     'purchase_info' => $purchase_info[$i],
	      	                                                     'products_rate' => $products_rate[$i] ));		      	       	
	      }	  	
	  }	 
	  	  
	  $get_apply_price = $db->Execute(" SELECT products_price,products_num FROM `purchase_apply_instock_info`
	                                    WHERE `apply_id`='".$get_apply_id."'  ");  
	  if ($get_apply_price->RecordCount() > 0) {
            $order_price = 0 ;
	        while (!$get_apply_price->EOF) {    
                        $order_price += $get_apply_price->fields['products_price']*$get_apply_price->fields['products_num'] ;
                   $get_apply_price->MoveNext();
            }	        
	        zen_db_perform('purchase_apply_instock',array('order_price' => $order_price,
	                                                      ) , 'update' ,' `apply_id`='.$get_apply_id );
	  }
	 	  
	  $messageStack->add_session('更新成功', 'success');
      zen_redirect(zen_href_link('products_instock_warehouse_count_storage.php','choice_page=no_pass','NONSSL'));
    break;	

    
    default:  ;   break ;
  }

?>




<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/javascript/jquery-ui.css" />
<script type="text/javascript">
</script>

</head>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div class="fs_middle">
  <div class="fs_middle_con">
  <div class="link_title">
        <span><a href="index.php">管理首页</a></span> / 
        <span><a href="products_instock_warehouse_count_storage.php">采购单收货</a></span>
  </div>
  
<h2>采购单收货</h2>
<!-- <i class="icon-download-alt"></i>&nbsp;<a href="help/采购申请汇总-采购说明.docx">功能使用说明</a> -->
<div class="total_screening">
	 <span class="left">
	  <?php 
      echo fiberstore_categories_class::show_categories('products_instock_warehouse_count_storage_sale.php');?>

	 </span>
     <span class="left">&nbsp;&nbsp;&nbsp;&nbsp;
	    <?php 
	    echo zen_draw_form('search','products_instock_warehouse_count_storage_sale', '', 'get', '', true);
	
	    echo '<input type="text" class="input-smedium" name="search" placeholder="产品ID/采购单号编号"> ' . ' ' . zen_hide_session_id();
	    echo  '<button class="btn btn-info">Search</button>' ;
	    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
	      $keywords = zen_db_prepare_input($_GET['search']);
	      
	    }
	    ?>
      </form>


	 </span>
     <span class="right">                 
    <?php
    //时间段筛选
	/*
    echo zen_draw_form('search_orders', 'products_instock_warehouse_count_storage.php', 'choice_page=submit_pay', 'GET', '', true);
    echo '<input type="hidden" class="input-smedium" name="choice_page" value="submit_pay"> ' ;
    echo '<input type="hidden" class="input-smedium" name="method_payment" value="'. $_GET['method_payment'].'">';
    echo '<input type="hidden" class="input-smedium" name="searchorder" value="'.$_GET['searchorder'].'"> ' ;
    echo '<input type="hidden" class="input-smedium" name="manufacturer" value="'.$_GET['manufacturer'].'"> ' ;
	echo "<div id=\"start\" class=\"input-append\">
    									<span class=\"add-on\"><div class=\"input-append\"><span class=\"btn\">From:</span>".zen_draw_input_field('start',$_GET['start'] ? $_GET['start'] : 'Date','id="from"')."</div></span></div>";
	echo "&nbsp;&nbsp;<div id=\"end\" class=\"input-append\">
    									<span class=\"add-on\"><div class=\"input-append\"><span class=\"btn\">To:</span>".zen_draw_input_field('end',$_GET['end'] ? $_GET['end'] :'Date','id="to"')."</div></span></div>";
	echo '&nbsp;&nbsp;<button type="submit" class="btn btn-info">Search</button>';
	echo '</form>';
    echo  '&nbsp;&nbsp;';   

        echo zen_draw_form('search','products_instock_purchase_apply_finance', 'choice_page=submit_pay', 'get', '', true);
        echo $search_and;
        echo '<input type="text" class="input-smedium" name="searchorder" placeholder="订单编号"> ' . ' ' . zen_hide_session_id();
        echo '<input type="hidden" class="input-smedium" name="choice_page" value="submit_pay"> ' ;
         echo '<input type="hidden" class="input-smedium" name="method_payment" value="'. $_GET['method_payment'].'">';
        echo '<input type="hidden" class="input-smedium" name="manufacturer" value="'.$_GET['manufacturer'].'"> ' ;
        echo '<input type="hidden" class="input-smedium" name="start" value="'.$_GET['start'].'"> ' ;
        echo '<input type="hidden" class="input-smedium" name="end" value="'.$_GET['end'].'"> ' ;

        echo  '<button class="btn btn-info">Search</button></form>' ;
        if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
          $keywords = zen_db_prepare_input($_GET['search']);
          
        }
		*/
       ?>
    
     <!-- </span>                  
     <span class="right">  -->
     <!-- <a  id="download_no_apply" class="btn btn-info">下载数据</a> -->
	 </span> 
</div>


<div class="total_content"> 

 
<!-- <form id="purchase_product_info" name="purchase_product_info" method="post" action="<?php echo zen_href_link('products_instock_purchase_apply_finance','action=update_discount_price','SSL');?>"> -->

  
  <table width="100% " cellspacing="0" cellpadding="0" border="0" class="total_table">
      <thead>
        <tr>       
          <!-- <th><input type="checkbox" title ="all" id="check_all"/></th>          
          <th>采购单号</th>  
          <th width="15%">产品名称/数量</th>  
          <th>入库状态</th>  --> 
		  <th><input type="checkbox" title ="all" id="check_all"/></th>
		  <th>采购单号</th>  
          <th>ID</th>  
          <th>产品名称</th>
		  <th>预计到货时间</th>
		  <th>申请数量</th>
		  <th>已到数量</th>
		  <th>收货状态</th>
          <th>操作</th>
         
        </tr>
      </thead>     
<?php
/*
if(in_array($admin_id,array(139,224))){
    $where = " 1 ";
}else{
    $where = "  purchase_admin =".$_SESSION['admin_id'];
}
*/

  if($_GET['cPath']){
     $search_category_id = explode('_',$_GET['cPath']);
	 $search_category_id = $search_category_id[count($search_category_id)-1];
    }else{
	 $search_category_id = '';
	}
	if($search_category_id){
		if (zen_has_category_subcategories($search_category_id)) {
			$all_subcategories_ids = array();
			zen_get_subcategories($all_subcategories_ids,$search_category_id);
			
			$count_of_subcategories = sizeof($all_subcategories_ids);
			if ($count_of_subcategories){
				
				if (1 < $count_of_subcategories) {
					
					$category_where_sql = "  and pa.products_id in (select products_id from products_to_categories where categories_id in(".join(',',$all_subcategories_ids)."))";
				}else if (1 == $count_of_subcategories) {
					$category_where_sql = " and pa.products_id in (select products_id from products_to_categories where categories_id = ".$all_subcategories_ids[0].")";
				}
			 }else {
					$category_where_sql = " and pa.products_id in (select products_id from products_to_categories where categories_id = ".(int)$search_category_id.")";
			 }
		}else {
			$category_where_sql = " and pa.products_id in (select products_id from products_to_categories where categories_id = ".(int)$search_category_id.")";
		}
     }


    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
      $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
      $modelSQL = "select products_id,products_price from products where (products_model = '" . $keywords . "' or products_MFG_PART = '" . $keywords . "' or products_SKU = '" . $keywords . "')";
      $modelpid = array('products_id','products_price');
      $model_of_pid = zen_get_data_from_db($modelSQL,$modelpid);
      if(sizeof($model_of_pid)){
      foreach($model_of_pid as $sub){
	    $productsID [] = $sub[0];
      }
      $search .= " AND (pa.products_id in(".join(',',$productsID)."))";
      }else{
      $search .= " AND (pa.products_id = '" . $keywords . "' or pi.apply_number = '" . $keywords . "')";
      }
      $search = $db->bindVars($search, ':keywords:', $keywords, 'regexp');
    }


$sql = "select pi.apply_number,pa.* from purchase_apply_instock_info pa,purchase_apply_instock pi where pa.products_id >0 and pi.apply_id = pa.apply_id and pi.receive_statu=2 AND pi.pay_statu=0 and (pi.apply_type = 0 or pi.apply_type = 3) ".$search.$category_where_sql." order by inventory_status ASC";

$split = new splitPageResults($_GET['page'],20, $sql, $instock_query_numrows);
$instock = $db->Execute($sql);

$disabled = '';
while(!$instock->EOF){   

?>


	
	<tr>
	<td>
			<?php  echo '<input name="products[]" type="checkbox" value="'.$instock->fields['info_id'].'" >'; ?>
	</td>
	<td>
	<?php  echo $instock->fields['apply_number']; ?>
	</td>
			  <td>
				<?php  echo $instock->fields['products_id']; ?>

				<?php 
				if($instock->fields['inventory_status'] != 1){
					echo '<span class="label label-important">New</span>';
				}
				
				?>
				
			 </td>
			 <td>
				<?php  
				$productsName = $sales_products_name ? $sales_products_name : zen_get_products_name($instock->fields['products_id']);
			  echo  $productsName;
			   $name_description = FS_products_name_description($instock->fields['products_id']);
			   echo '<div class="track_orders_wenhao">
                            <div class="question_bg"></div>
                             <div class="question_text_01 leftjt"><div class="arrow"></div>
                                <div class="popover-content">
                                  	'.$name_description.'                                
                                </div>
                             </div>
                          </div>';
				?>
				
			 </td>
			  <td><?php echo $instock->fields['products_date'];?></td>
			  <td>
				
				<?php echo $instock->fields['products_num'];?>
			 </td>
			 <td>
				
				<?php echo $instock->fields['inventory_number'];?>
			 </td>

			 <td>
				
				<?php 
				if($instock->fields['inventory_status'] == 1){
					echo "已收货";
				}else{
					echo "未收货";
				}
				
				?>
			 </td>
			  <!-- <td>
				<input type="text" name="ck_num" id="ck_num_<?php echo $instock->fields['info_id'];?>" class="input-mini " value="<?php echo $instock->fields['products_num'];?>">&nbsp;&nbsp;
                <input type="button" name="button" value="确定收货" onClick="confirm_receipt(<?php echo $instock->fields['info_id'];?>)" class="btn btn-info">
				
			 </td> -->
		</tr>
		

  
<?php 
	  $instock->MoveNext();
}
?>     

    </table>
 <!-- </form>  -->  
   </div>
   <div class="total_page">
   <span class="left"><?php echo $split->display_links($instock_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'id'))); ?></span>
   </div>
  </div>
</div>

<script type="text/javascript" src="includes/javascript/jquery-1.7.1.min.js"></script> 
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> 
<script type="text/javascript" src="js/chart.js"></script>
<script type="text/javascript">


function confirm_receipt(id){
	var num = $("#ck_num_"+id).val();
	$.ajax({
		   type: "POST",
		   url: "?action=confirm_receipt",
		   data: "num="+num+"&id="+id,
		   success: function(data){
		     window.location.reload();
		   }
		});
}


$('#check_all').click(function(){
	if($(this).is(':checked')){ $('input[name^="products"]').each(function(){$(this).attr('checked','checked');});}
	else{ $('input[name^="products"]').each(function(){$(this).removeAttr('checked');});}
});

$("#moving_all").click(function(){
	var products = new Array(),$i=0,$len = 0,$products_str = '';
	$('input[name^="products"]').each(function(){
		if($(this).is(':checked')) products[$i++] = $(this).val();
	});
	$len = products.length;
	
	if(1 > $len){alert('请选择需要打印的产品');return false;}

			var _form = $("<form></form>",{						
			'method':'post',
			'action':'products_instock_purchase_download_apply.php',														
			'style':'display:none'
			}).appendTo($("body"));

			for($i=0;$i<$len;$i++){
				_form.append($("<input>",{'type':'hidden','name':'products[]','value':products[$i]}));					
		    }
			_form.trigger("submit");
			_form.remove();		
});

$(function() {
	$( "#from" ).datepicker({
		onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
			$( "#to" ).datepicker('option', {dateFormat: 'yy-mm-dd'});
			$( "#from" ).datepicker('option', {dateFormat: 'yy-mm-dd'});
		}
	});
	$( "#to" ).datepicker({
		onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});

//未付款下载
$("#download_no_apply").click(function(){
	var apply_id = new Array(),$i=0,date = $('#from').val(),todate = $('#to').val(),$len = 0;
	$('input[name^="products"]').each(function(){
		if($(this).is(':checked')) apply_id[$i++] = $(this).val();
	});

	$len = apply_id.length;
	if(1 > $len && 1>date.length){alert('请选择日期或者需要下载的采购单');return false;}
	   var _form = $("<form></form>",{	'method':'post',
        'action':'products_instock_warehouse_count_storage.php?action=download_no_apply',										
		'style':'display:none'
		}).appendTo($("body"));

		for($i=0;$i<$len;$i++){
			_form.append($("<input>",{'type':'hidden','name':'apply_id[]','value':apply_id[$i]}));					
	    }	
				_form.append($("<input>",{'type':'hidden','name':'date','value':date}));
				_form.append($("<input>",{'type':'hidden','name':'todate','value':todate}));
								
			_form.trigger("submit");
			_form.remove();	
	
});

//分期支付
function submit_installment_payment(id){
	var price = $('#pay_price_'+id).val();
	var installment = 0;

	if($('#installment_'+id).is(':checked')){
		installment = 1 ;
    }

    if(price.length < 1){ return  false ; }
    
//	alert(installment);
//	return  false ;
	
	$.ajax({
		   type: "POST",
		   url: "products_instock_warehouse_count_storage.php?action=all_payment",
		   data: "price="+price+"&installment="+installment+"&id="+id,
		   success: function(data){
		     window.location.reload();
		   }
		});
}

function ajax_cancel_product(id){
	if(confirm('确认取消？')) {
		$.ajax({
			   type: "POST",
			   url: "products_instock_warehouse_count_storage.php?action=cancel_product",
			   data: "a_id="+id,
			   success: function(data){
			          window.location.reload();
			   }		   
			}); 
  	}else return false;
}

function delete_receive_product(id,fontobj){
	if(confirm('确认删除？')) {
		$.ajax({
			   type: "POST",
			   url: "products_instock_warehouse_count_storage.php?action=move_product",
			   data: "a_id="+id,
			   success: function(data){
			          window.location.reload();
			   }		   
			}); 
  	}else return false;
}


function delete_one_product(id,fontobj){
	if(confirm('确认删除？')) {
		$.ajax({
			   type: "POST",
			   url: "products_instock_warehouse_count_storage.php?action=move_one_product",
			   data: "id="+id,
			   success: function(data){
			         if(data == 'ok'){
			        	 window.location.reload();	        	 
				     }
			   }		   
			}); 
  	}else return false;
}


</script>



<?php  require(DIR_WS_INCLUDES . 'footer.php');?>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>