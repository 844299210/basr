<?php
//require 'd:/xampp/htdocs/fiberstore-beta.com/includes/application_top.php';
require '/includes/application_top.php';
 if($_GET['from'] != 'fs'){
 	header('Location:../');
 }
 
 $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>

<link rel="stylesheet" type="text/css" media="all" href="question-and-answer/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="/includes/templates/fiberstore/css/all.css" />
<script type="text/javascript" src="includes/templates/fiberstore/jscript/1.4.2/jquery.min.js"></script>
<script type="text/javascript" >!window.jQuery && document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"><\/script>');</script>
<script src="http://cdn.fiberstore.s3.amazonaws.com/js/fs_autocomplete.js" type="text/javascript"></script>

 <?php // echo zen_draw_form('qa_form', zen_href_link(FILENAME_QA_FOR_FIBERSTORE),'POST',' id="qa_form"').zen_draw_hidden_field('feedurl',$url);
 ?>	
   <?php echo zen_draw_form('qa_form','','POST','id="qa_form"').zen_draw_hidden_field('qa_from',$_GET['main_page']);?>
 
 
 <a href="<?php echo zen_href_link(FILENAME_DEFAULT);?>">
  <img border="0" src="../images/logo.jpg" alt="FiberStore" title="Back to FiberStore home page">
  </a>
   			<div style="padding-left:100px;padding-top:100px;">
   			<p class="lead">Question & Answer of FiberStore</p>
   			<!-- <div id="send_success" class="contact_cgts_01">Thanks,Your Q&A has Submit.</div> -->
   			<div class="messageStackSuccess larger tishi_01"  id="send_success" style="display:none ">
		    Your Q&A has submit
		    </div>
		    
		    <div class="alert" id="send_error" style="display:none ">
			  <button type="button" class="close" data-dismiss="alert">&times;</button>
			  <strong>Warning!</strong> Please enter the correct information
			</div>

   			
   			<div class="bs-docs-example table table-bordered" style="width:800px;"><table class="table table-hover" width="60%" ><tbody>
               
         	   <tr><td>产品ID: </td><td><input id="products_id" name="products_id" class="big_input" style="height:30px;width:400px;"></td></tr>
   				<tr><td>产品名: </td><td><input id="products_name" name="products_name" class="big_input " style="height:30px;width:400px;"></td></tr>
   				<tr><td>产品链接: </td><td><input id="products_url" name="products_url" class="big_input " style="height:30px;width:400px;"></td></tr>
   				<tr><td>操作人: </td><td><input id="admin_name" name="admin_name" class="big_input " style="height:30px;width:400px;"></td></tr>
   				<tr><td>Question: </td><td>
   				<textarea onkeyup="textCounter(this.form.review_content,this.form.remLen,3000);" onkeydown="textCounter(this.form.review_content,this.form.remLen,3000);" 
   					class="login_014" name="qa_question" id="qa_question"></textarea>
   				<td></tr>
   				<tr><td>Answer: </td><td>
   				<textarea onkeyup="textCounter(this.form.review_content,this.form.remLen,3000);" onkeydown="textCounter(this.form.review_content,this.form.remLen,3000);" 
   					class="login_014" name="qa_answer" id="qa_answer"></textarea>
   				</td></tr> 
   			    <tr class="info"><td colspan="2">            </td></tr>

         </tbody></table></div></div><div class="ccc"></div>

       <p align="left" style="margin-left:980px;">
              <input type="button" id="fs_qa" onclick="submit_qa_form();" class="button_02" value="Submit">
               
            </p>
</form>

<script type="text/javascript">


function submit_qa_form(){

	//$('#fs_qa').addClass('contact_button_01').val('Processing...').attr('disabled',true);
	
$.ajax({
		  type: "POST",
		  url: "ajax_processing_qa_request.php?request_type=fa_submit",
		  dataType: "text",
		  data: $("#qa_form").serialize(),
		  
		  success: function(msg){
		  	 if('success'==msg){
			  		$("#send_success").show();
			  		$("#products_id").val('');  
			  		$("#products_name").val('');  
			  		$("#products_url").val('');  
			  		$("#admin_name").val('');  
			  		$("#qa_question").val('');  
			  		$("#qa_answer").val(''); 
		     	//window.location="http://www.fiberstore-beta.com/qas_for_fiberstore.php?from=fs";
		  	 }
		  	 else if('error' == msg){
			  	$("#send_error").show();
		  	 }else{
			  	$("#send_error").show();
			 }
			  	
		   }
		}); 

}

</script>

