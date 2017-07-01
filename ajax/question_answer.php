<?php
require('includes/application_top.php');

if (isset($_GET['c_id'])) $c_id = (int)$_GET['c_id'];

if (isset($_GET['action']) && $_GET['action']){
	switch ($_GET['action']){
		case 'view':
			$sql = "select * from fiberstore_qa_archive as qa  order by fiberstore_qa_id desc";
			$info =  $db->Execute($sql);
			$info->fields['qa_customer_question'] = str_replace(array('\r\n','\n'),'<br/>',$info->fields['qa_customer_question']);
			$o_info = new objectInfo($info->fields);
			break;

		case 'del':
			$db->Execute("delete from fiberstore_qa_archive where fiberstore_qa_id = " . (int) $c_id);
			$messageStack->add_session('操作成功','success');
			zen_redirect(zen_href_link('question_answer.php'));
			break;

	}
}

if(isset($_GET['type'])){
	if($_GET['type'] == 'update_article'){
        $aid = $_POST['aid'];
		$title= $_POST['title'];
		$description  = mysql_real_escape_string(preg_replace("/(\\r\\n)+[\\t ]*/",'',$_POST['description']));   
	    $update_article = array(
		 			    'qa_admin' => $title ,
		 				'qa_admin_answer' => $description,
	                    'qa_admin_solution_time' => 'now()'
		 			);
		zen_db_perform('fiberstore_qa_archive',$update_article,'update','fiberstore_qa_id='.$aid);
		$messageStack->add_session('更新成功', 'success');
		exit('update_article');
	}
	
}

//get all contact us contents

$sql = "select * from fiberstore_qa_archive order by fiberstore_qa_id";

$result = $db->Execute("select count(fiberstore_qa_id) as total from fiberstore_qa_archive" );
$query_numrows = $result->fields['total'];
$page = 1;
if (isset($_GET['page']) && 1 < (int)$_GET['page']) $page = (int)$_GET['page'];
$split = new splitPageResults($page, 20,$sql,$query_numrows);
$info  = $db->Execute($sql);
?>


<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">

<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
<!--

function fs_confirm(c_id){

	var sure = window.confirm('你确定要删除吗 ?');
	if(sure) window.location = '?action=del&c_id='+c_id;
}
// -->
</script>
</head>
<body >
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<style type="text/css"> 
h4 {
    margin: 0 0 0 4em;
}
</style>

<h4><strong >Question & Answer:</strong></h4>

<br/><br/>
<?php if (isset($_GET['action']) && $_GET['action']){?>
<h4><span> <a href="question_answer.php"> <button class="btn">&lt;&lt; 返回列表 </button></a></span>

<span class="pull-right"> <a href="https://mail.google.com/mail" target="_blank"> <button class="btn"> 登录 Google Gmail 回复邮件 &gt;&gt;  </button></a></span>
</h4>
<?php }?>

<br/><br/>

<?php if (!isset($_GET['action'])){?>

<table border="0" width="80%" cellspacing="0" cellpadding="0"  class=" table-hover" align="center">
<?php if($info->RecordCount()){?>
  <tr >
    
    <th class="dataTableContent" ><strong>产品编号 | 客户名字</strong></th>
    <th class="dataTableContent" ><strong>邮箱地址</strong></th>
    <th class="dataTableContent" ><strong>主题</strong></th>
    <th class="dataTableContent" ><strong>内容</strong></th>
    <th class="dataTableContent" ><strong>操作</strong></th>
    <!--  
    <th class="dataTableContent" ><strong>自动分配发送邮件</strong></th>
    -->
  </tr>
  <?php while (!$info->EOF) {
		
	?>
  <tr class="">
    <td style="text-align:center;"><?php echo $info->fields['fiberstore_qa_products_id']; ?> | <?php echo zen_get_customers_firstname($info->fields['qa_custoemr_id']); ?></td>
    <td style="text-align:center;"><?php echo zen_get_customer_name_email($info->fields['qa_custoemr_id']);?></td>
    <td style="text-align:center;"><?php echo $info->fields['zen_get_customer_email_address'];?></td>
    <td style="text-align:center;"><?php echo $info->fields['qa_customer_question'];?></td>
    
    <td style="text-align:center;">
    <?php  
       echo '<div class="btn-group">
	                <button data-toggle="dropdown" class="btn btn-small dropdown-toggle"><i class="icon-edit"></i>  操作<span class="caret"></span></button>
	                <ul class="dropdown-menu" style="min-width:80px;">
	                  <li><a href="?action=view&c_id='.$info->fields['fiberstore_qa_id'].'"><i class="icon-book"></i> 查看 </a></li>
		              <li><a href="#myModal_'.$info->fields['fiberstore_qa_id'].'"  id="'.$info->fields['fiberstore_qa_id'].'" data-toggle="modal"><i class="icon-pencil"></i> 解答</a></li>
	                  <li><a href="?action=del&c_id='.$info->fields['fiberstore_qa_id'].'" onClick="if(confirm(\'确定删除？\')) return true;else return false; "><i class="icon-trash"></i> 删除</a></li>
	                </ul>
               </div>
               <div id="myModal_'.$info->fields['fiberstore_qa_id'].'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-header">
					    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">×</button>
					    <h3 id="myModalLabel">回复反馈信息</h3>
					  </div>
					  <div class="modal-body">
					    管理员   :  <input type="text" name="article_title" id="article_title_'.$info->fields['fiberstore_qa_id'].'" size="80"
					        value="'.(zen_get_admin_name($_SESSION['admin_id']) ? zen_get_admin_name($_SESSION['admin_id']) : '').'" />
					  </div>					  
					  <div class="modal-body">
					  <textarea name="article_description" id="article_description_'.$info->fields['fiberstore_qa_id'].'" > 
					  '.(zen_get_qa_has_solution($info->fields['fiberstore_qa_id']) ? zen_get_qa_has_solution($info->fields['fiberstore_qa_id']) : '').' </textarea>
					  </div>

					  <div class="modal-footer">
					    <button class="btn" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">Close</button>
					    <button class="btn btn-primary" onClick="update_article('.$info->fields['fiberstore_qa_id'].');">Save changes</button>
					  </div>
					</div>
               ';
            
    ?>
    
  </td>
  <!-- 
  <td align="center">
                <?php 
                /*
                echo zen_draw_form('order_assign', 'contact_us_manage', zen_get_all_get_params(array('action')) . 'action=customer_assign_to', 'post', 
                'onsubmit="return check_form(order_assign);"', true) ;
                echo zen_draw_hidden_field('contact_id',$info->fields['contact_id']); 
                echo zen_draw_hidden_field('customers_name',$info->fields['customers_name']); 
                echo zen_draw_hidden_field('customers_email_address',$info->fields['customers_email_address']); 
                echo zen_draw_hidden_field('phone_number',$info->fields['phone_number']); 
                echo zen_draw_hidden_field('subject',$info->fields['subject']); 
                echo zen_draw_hidden_field('content',$info->fields['content']); 
                
                echo zen_draw_pull_down_menu('admin_id',zen_get_all_sales_admin(),$info->fields['customers_id']);
                 */
                ?>
                <button class="mini btn"> 确定 </button>
                </form>
   </td>
   -->
  </tr>
  <?php 
  $info->MoveNext();
  }?>
  
  
  <tr>
  	<td colspan="5" style="text-align:center;">
  	<?php echo $split->display_links($query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'c_id'))); ?>
  	</td>
  </tr>
  <?php }else {?>
  <tr >
    <th colspan="4">当前还没有客户反馈信息...</th>
  </tr>
  
  <?php }?>
</table>
<?php }else {

	if ('view' == $_GET['action']){
?>

<style type="text/css"> 
fieldset {
    padding-left: 20%;
    width: 60%;
}
</style>

<fieldset>
<legend style="text-align:center;"><strong>客户反馈信息详情:</strong></legend>

<br/>
<small style="color:#999;">Customer Name</small>: &nbsp;&nbsp;<strong><b><?php echo zen_get_customers_firstname($o_info->qa_custoemr_id);?></b></strong>
<br/>
<small style="color:#999;">Customer E-mail</small>: &nbsp;&nbsp;<strong><b><?php echo zen_get_customer_name_email($o_info->qa_custoemr_id);?></b></strong>
<br/>
<small style="color:#999;">Feedback Subject</small>: &nbsp;&nbsp;<strong><b><?php echo $o_info->qa_customer_subject ? $o_info->qa_customer_subject : '客户未填写标题';?></b></strong>
<br />
<small style="color:#999;">Feedback Content:</small>
<br />
<pre>
<?php echo html_entity_decode($o_info->qa_customer_question);?>
</pre>
<br/> 

<legend style="text-align:center;"><strong>FiberStore解答:</strong></legend>
<br />
<small style="color:#999;">Admin Name</small>: &nbsp;&nbsp;<strong><b><?php echo $o_info->qa_admin;?></b></strong>
<br />
<small style="color:#999;">FiberStore Solution:</small>
<br />
<pre>
<?php echo html_entity_decode($o_info->qa_admin_answer);?>
</pre>

</fieldset>
<?php 
}
	
	
}?>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="js/chart.js"></script>

<script type="text/javascript" src="includes/javascript/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../editors/ckeditor-nowatermark/ckeditor.js"></script>
<script type="text/javascript" src="../../../editors/ckeditor-nowatermark/adapters/jquery.js"></script>
<script type="text/javascript">
function update_article(aid){
	var title= $('#article_title_'+aid).val();
	var description= $('#article_description_'+aid).val();
	$.ajax({
		   type: "POST",
		   url: "question_answer.php?type=update_article",
		   data: "&aid="+aid+"&title="+title+"&description="+description,
		   success: function(data){
					  window.location.href="<?php echo zen_href_link(question_answer);?>" ;
		   }
	  });
}

if($("textarea[name^='article_description']").size()){
	$("textarea[name^='article_description']").each(function(){
		$(this).ckeditor({
		    toolbar: 'Full',
		    enterMode : CKEDITOR.ENTER_BR,
		    shiftEnterMode: CKEDITOR.ENTER_P,
		    height: 200,
		    width: 500,
		    toolbar : [
	                  	 [ 'Source' ] ,
		                 [ 'Cut', 'Copy', 'Paste',  '-', 'Undo', 'Redo' ] ,
		                 [ 'Replace', '-', 'SelectAll', '-', 'Scayt','HorizontalRule',  'SpecialChar', 'PageBreak' ],
		                 [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ],
		                 [ 'Link', 'Unlink', 'Anchor' ,'TextColor', 'BGColor','Image','Table'],
		                 [ 'Styles', 'Format', 'Font', 'FontSize' ]

                      ]
		});
		});
	
}

</script>