<?php 
$Shortcut = "[InternetShortcut] 
URL=http://beta.fiberstore.com/ 
IDList= 
[{000214A0-0000-0000-C000-000000000046}] 
Prop3=19,2 
"; 
Header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=123456.url;"); 
echo $Shortcut; 
?>