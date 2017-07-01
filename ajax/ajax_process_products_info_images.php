<?php 
require 'includes/application_top.php';
$tag = $_POST['tag'];
$result = $db->getAll("select * from products_additional_images where additional_images_id = '$tag' limit 1");
if($result[0]['cache_image']){
	echo $result[0]['cache_image'];exit;
}else{
	$original_images_ones = DIR_WS_IMAGES.$result[0]['image'];
	$path_info = pathinfo($original_images_ones);
	$define_large_image = $path_info['dirname'] . '/'. str_replace('.'.$path_info['extension'], '', $path_info['basename']).'_LRG.'.$path_info['extension'];
	$ih_image = new ih_image($define_large_image, 600, 600);
	$original_images_one = $ih_image->get_local_a();
	if($original_images_one){
		$db->query("update products_additional_images set cache_image = '$original_images_one' where additional_images_id='$tag'");
		echo $original_images_one;exit;
	}
}
?>