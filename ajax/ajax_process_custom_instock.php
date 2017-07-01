<?php
require 'includes/application_top.php';
use classes\custom\FsCustomRelate;
if(isset($_GET['request_type'])){
  switch($_GET['request_type']){
    case 'getCustomRelatedInstock':
      $productsId = zen_db_prepare_input($_POST['products_id']);
      $Attr = zen_db_prepare_input($_POST['attr']);
      $length = zen_db_prepare_input($_POST['length']);
      if($productsId && $Attr && $_SESSION['securityToken']==$_POST['token']){
        if(strpos($Attr,',')){
          $Attr = explode(',',$Attr);
        }
        if(is_array($Attr)){
          foreach ($Attr as $k=>$v){
            if(strpos($v,'_')){
              $Attr[$k] = substr($v,0,strpos($v,'_'));
            }
          }
        }elseif(strpos($Attr,'_')){
          $Attr = substr($Attr,0,strpos($Attr,'_'));
        }
       // print_r($Attr);die;
        $class = new FsCustomRelate($productsId,$Attr,$length);
        $excellentMatch = $class->handle();
        if(!$excellentMatch){
          $instockQty = zen_get_products_instock_total_qty_of_products_id($productsId,false);
          echo $instockQty;
        }else{
          $instockQty = zen_get_products_instock_total_qty_of_products_id($excellentMatch[0]);
          echo $instockQty;
        }
      }else{
        echo 'illegalityRequest';
      }
      exit();
      break;
  }
}