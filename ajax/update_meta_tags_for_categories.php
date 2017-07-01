<?php
require 'includes/application_top.php';
$sql  = " SELECT categories_id FROM meta_tags_categories_description WHERE language_id =2  ORDER BY categories_id;";
$result = $db->Execute($sql);
$id_str = '';
while (!$result->EOF) {
	$id_str.=$result->fields['categories_id'].', ';
	$result->MoveNext();
}

echo $id_str;