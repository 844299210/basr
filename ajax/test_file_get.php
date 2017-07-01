<?php
require('includes/application_top.php');
require('includes/functions/news_general.php');

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  switch ($action){
  case 'serach_order':
  $customerSQL = $db->Execute("select customers_id from customers where customers_id > 109913 order by customers_id");
  $customers = array();
    if($customerSQL->RecordCount()){
	   while(!$customerSQL->EOF){
	      $customers [] = $customerSQL->fields['customers_id'];
	      $customerSQL->MoveNext();
	   }
	}
	$ResetID = 109914;
	foreach($customers as $v){
	 $ResetID ++;
	 $db->Execute("update address_book set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update admin_to_customers set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update customers set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update customers_basket set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update customers_basket_attributes set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update customers_basket_length set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update customers_info set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update orders set customers_id = $ResetID where customers_id =".$v." ");
	 $db->Execute("update orders_shipping set customers_id = $ResetID where customers_id =".$v." ");
	}
	
	
  break;
  
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo TITLE;?></title>
<link rel="stylesheet" type="text/css" href="http://beta.fiberstore.com/New_Fiberstore_Manager_2015/includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="http://beta.fiberstore.com/New_Fiberstore_Manager_2015/css/stylesheet.css">
<link rel="stylesheet" type="text/css" href="http://beta.fiberstore.com/New_Fiberstore_Manager_2015/css/bootstrap.min.css" media="all" id="hoverJS">
<link rel="stylesheet" type="text/css" href="http://beta.fiberstore.com/New_Fiberstore_Manager_2015/css/style.css" media="all" id="hoverJS">
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
          <div id="productsEditInfo_<?php echo $products_shipping_info_id;?>" class="modal hide fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">×</button>
              <h3 id="myModalLabel">产品备注信息</h3>
            </div>
            
          	<?php echo zen_draw_form('update_returns','test_file_get', zen_get_all_get_params(array('action')) . 'action=serach_order', 'post', '', true);?>

                  <tr>
                    <td>&nbsp;</td>
                    <td>
					 <button class="btn btn-info">提交</button>
                    </td>
                  </tr>
             </table>
            </form>
           </div>
  </body>
<?php  require(DIR_WS_INCLUDES . 'footer.php');?>