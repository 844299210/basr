<?php
require 'includes/application_top.php';
$sql = "select products_weight as w1, products_weight_for_view  as w2 from products 
		where products_sku = :sku
		";
$sku_str = '';
$sku_array = array(177,131,'131O');

for($i = 185; $i <= 192; $i++){
	$sku_array [] = $i;
}
for($i = 148; $i <= 173; $i++){
	$sku_array [] = $i;
}
for($i = 132; $i <= 147; $i++){
	$sku_array [] = $i;
}
for($i = 126; $i <= 130; $i++){
	$sku_array [] = $i.'J';
}
for($i = 124; $i <= 125; $i++){
	$sku_array [] = $i.'J';
}
//$sku_str = substr($sku_str, 0, strrpos($sku_str,'or'));

echo sizeof($sku_array).'<br/>';

//var_dump($sku_array);

foreach($sku_array as $sku){
		$sku = 'SKU00'.$sku.'Q';
		$sql = $db->bindVars($sql,':sku',$sku,'string');
		$result = $db->Execute($sql);
		echo $sql.'<br/>';
		if ($result->RecordCount()) {
			$w1 = $result->fields['w1'];
			$w2 = $result->fields['w2'];
			$sql = "
			update products set products_weight = '".$w2."', 
					products_weight_for_view = '".$w1."' 
					where products_sku = '".$sku."'
			";
			echo $sql .'<br/>';
			//$db->Execute($sql);
		}
}
exit;


/*


// prefix is FS
$sku_array = array();
for($i = 15; $i <= 22; $i++){
	$sku_array [] = '000'.$i.'FS';
}
foreach($sku_array as $sku){
		$sql = $db->bindVars($sql,':sku',$sku,'string');
		$result = $db->Execute($sql);
		if ($result->RecordCount()) {
			$w1 = $result->fields['w1'];
			$w2 = $result->fields['w2'];
			$sql = "
			update products set products_weight = '".$w2."', 
					products_weight_for_view = '".$w1."' 
					where products_sku = '".$sku."'
			";
			echo $sql .'<br/>';
//			$db->Execute($sql);
		}

}

$sku_array = array();
for($i = 1; $i <= 9; $i++){
	$sku_array [] = '0000'.$i.'FS';
}
for($i = 10; $i <= 14; $i++){
	$sku_array [] = '000'.$i.'FS';
}
for($i = 23; $i <= 90; $i++){
	$sku_array [] = '000'.$i.'FS';
}
for($i = 92; $i <= 99; $i++){
	$sku_array [] = '000'.$i.'FS';
}
for($i = 101; $i <= 115; $i++){
	$sku_array [] = '00'.$i.'FS';
}
foreach($sku_array as $sku){
		$sql = $db->bindVars($sql,':sku',$sku,'string');
		$result = $db->Execute($sql);
		if ($result->RecordCount()) {
			$w1 = $result->fields['w1'];
			$w2 = $result->fields['w2'];
			$sql = "
			update products set products_weight = '".$w2."', 
					products_weight_for_view = '".$w1."' 
					where products_sku = '".$sku."'
			";
			echo $sql .'<br/>';
//			$db->Execute($sql);
		}

}*/