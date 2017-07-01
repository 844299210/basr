<?php 
require('includes/application_top.php');
use classes\custom\FsCustomRelate;
if(!isset($countries_code_2)){
  if($_SESSION['countries_code_21']){
    $countries_code_2 = $_SESSION['countries_code_21'];
    if($countries_code_2 == 'CN'){
      $countries_code_2 = 'US';
    }
  }else{
    $countries_code_2 = 'US';
  }
  if($_COOKIE['countries_iso_code']){
    if(class_exists('Encryption')){
    $Encryption = new Encryption;
    $countries_code_2 = $Encryption->_decrypt($_COOKIE['countries_iso_code']);
    }
  }else{
    if(in_array($_SESSION['currency'],array('USD','GBP','AUD','CAD'))){
      if($_SESSION['currency'] == 'GBP'){
        $countries_code_2 = 'GB';
      }elseif($_SESSION['currency'] == 'AUD'){
        $countries_code_2 = 'AU';
      }elseif($_SESSION['currency'] == 'CAD'){
        $countries_code_2 = 'CA';
      }else{
        $countries_code_2 = 'US';
      }
    }
  }
  $countries_code_2 = strtoupper($countries_code_2);
}
if($_GET['products_id']){
  $attr = fs_get_products_default_show_attr((int)$_GET['products_id']);
  print_r($attr);
  
}
$res = $db->Execute('select products_id from products_instock_other_customized_related where customized_id=30976');
while(!$res->EOF){
  if($res->fields['products_id']){
      $instockQty += fs_products_instock_total_qty_of_products_id($res->fields['products_id']);
    }
    $res->MoveNext();
}
echo fs_get_quickfinder_products_instock(30976);

  switch($_GET['action']){
    case 'post':
      //echo(microtime(false));echo '<br>';
      if($_POST['productsId']&&$_POST['AttrId']){
        if(strpos($_POST['AttrId'],',')){
          $attr = explode(',',$_POST['AttrId']);
        }else{$attr=$_POST['AttrId'];}
        $class = new FsCustomRelate();
        $class::$products_id = $_POST['productsId'];
        $class::$optionAttr = $attr;
        $class::$length = $_POST['length'];
        $excellentMatch = $class->handle();
        //echo(microtime(false));
        E($excellentMatch);
        if(!$excellentMatch){
          E($class::getExcellentMatch());
          $instockQty = zen_get_products_instock_total_qty_of_products_id($_POST['productsId']);
          E($instockQty);
        }else{
          $instockQty = zen_get_products_instock_total_qty_of_products_id($excellentMatch[0]);
          E($instockQty);
        }
      }
      break;
  }
  //E($class::getRelatedProductsInstock());
  //echo $countries_code_2;
  //echo zen_get_products_instock_total_qty_of_products_id(16264);
  
?>
<html>
<form action="?action=post" method="post">
  ProductsID:<input type="text" name="productsId"><br>
  OptionsAttrID:<input type="text" name="AttrId"><br>
  length:<input type="text" name="length"><br>
  <input type="submit" value="submit">
</form>
</html>