<?php
require 'includes/application_top.php';
ini_set('set_time_out', 0);
$sql = "select products_id, products_image from products order by products_id";

$result = $db->Execute($sql);

echo '<h2>Fiberstore products which have no pictures </h2><br/><br/>';
$i = 0;
while (!$result->EOF){
	if (!file_exists(DIR_WS_IMAGES .$result->fields['products_image'])) {
		echo 'products_id: <b style="color:blue;">'.$result->fields['products_id'].'</b>, img:  <b style="color:blue;">'.$result->fields['products_image'].'</b><br/>';
		$i++;
	}
	$result->MoveNext();
}

echo ' <strong> total is: ' .$i.' </strong><br/><br/>';
echo  'check images finished ...<br/><br/>';
