<?php
require('includes/application_top.php');
$cPath = $_GET['cID'];
//die($cPath);

$name []= array();
$link []= array();



$default_directory = '/images/categories_banner/';
//upload image
if (is_uploaded_file($_FILES['categories_image']['tmp_name'])){
	$user_path =$_SERVER['DOCUMENT_ROOT'].$default_directory;
	$user_path=iconv("utf-8","gb2312",$user_path);
	die($user_path);
	if (!file_exists($user_path)){
		mkdir($user_path);
	}
	$uploaded_path = $_FILES['categories_image']['tmp_name'];
	$fn_extension =strrchr($_FILES['categories_image']['name'], ".");
	$move_path = $user_path."/".date("Y-m-d@H_i_s")."@".rand(1, 1000).$fn_extension;

	if(false!=(move_uploaded_file($uploaded_path,$move_path))){
		echo "ok";
	}else{
		echo "error";
	}
}

?>

  <script language='javascript'>
   //setTimeout("location.href='javascript:history.go(-1);',5000");
</script>  

<h3><font color="red">special add success,set out goto previous...</font></h3>