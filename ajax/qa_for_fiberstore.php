<?php
//require 'd:/xampp/htdocs/fiberstore-beta.com/includes/application_top.php';
require '/includes/application_top.php';
 if($_GET['from'] != 'fs'){
 	header('Location:../');
 }
 
 //$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>

<link rel="stylesheet" type="text/css" media="all" href="question-and-answer/bootstrap.min.css" />

 <?php echo zen_draw_form('qa_form', zen_href_link(FILENAME_QA_FOR_FIBERSTORE),'POST',' id="qa_form"').
            zen_draw_hidden_field('from',$_GET['from']).zen_draw_hidden_field('feedurl',$url);
 ?>	
  
   			<div style="padding-left:100px;padding-top:100px;">
   			<p class="lead">Question & Answer of FiberStore</p>
   			<div class="bs-docs-example table table-bordered" style="width:800px;"><table class="table table-hover" width="60%" ><tbody>
               
         	   <tr><td>产品ID: </td><td><input name="products_id"><td></tr>
   				<tr><td>产品名: </td><td><input name="products_name"><td></tr>
   				<tr><td>产品链接: </td><td><input name="products_url"><td></tr>
   				<tr><td>操作人: </td><td><input name="admin_name" ><td></tr>
   				<tr><td>Question: </td><td><input name="qa_question"><td></tr>
   				<tr><td>Answer: </td><td><input name="qa_answer"><td></tr> 
   			    <tr class="info"><td colspan="2">            </td></tr>

         </tbody></table></div></div><div class="ccc"></div>

       <p align="left" style="margin-left:980px;">
               <button type="submit" class="btn">Submit</button>
            </p>
</form>
class="btn">Submit</button>
            </p>
</form>
   <button type="submit" class="btn">Submit</button>
            </p>
</form>
d><input name="products_name"><td></tr>';
   				$html .='<tr><td>产品链接: </td><td><input name="products_url"><td></tr>';
   				$html .='<tr><td>操作人: </td><td><input name="admin_name" ><td></tr>';
   				$html .='<tr><td>Question: </td><td><input name="qa_question"><td></tr>';
   				$html .='<tr><td>Answer: </td><td><input name="qa_answer"><td></tr>';  
   			    $html .='<tr class="info"><td colspan="2">            </td></tr>';

          $html .='</tbody></table></div></div><div class="ccc"></div>';

         echo $html;
?>
       <p align="left" style="margin-left:980px;">
               <button type="submit" class="btn">Submit</button>
            </p>
</form>
