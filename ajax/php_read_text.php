<?php 
$file = 'products.txt';
$content = file_get_contents($file);   //file_get_contents 将整个文件的内容存取到一个字符串中
//echo $content;
$array = explode("\r\n", $content);
//print_r($array);
for($i=0; $i<count($array); $i++)
{
    echo $array[$i].'<br />';
}
?>