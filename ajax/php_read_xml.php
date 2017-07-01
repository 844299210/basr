<?php
//对象 XML解析函数 描述 
//元素 xml_set_element_handler() 元素的开始和结束 
//字符数据 xml_set_character_data_handler() 字符数据的开始 
//外部实体 xml_set_external_entity_ref_handler() 外部实体出现 
//未解析外部实体 xml_set_unparsed_entity_decl_handler() 未解析的外部实体出现 
//处理指令 xml_set_processing_instruction_handler() 处理指令的出现 
//记法声明 xml_set_notation_decl_handler() 记法声明的出现 
//默认 xml_set_default_handler() 其它没有指定处理函数的事件

//$handle_rb = fopen ("http://www.example.com/", "rb");
//$contents = "";
//while (!feof($handle_rb)) {
//  $contents .= fread($handle_rb, 8192);
//}
//fclose($handle_rb);

//$filename = "/usr/local/something.txt";
//$handle = fopen($filename, "r");

//$filename = "c:\\files\\somepic.gif";
//$handle = fopen($filename, "rb");
//$contents = fread($handle, filesize ($filename));
//fclose($handle); 

/*   --------------------------------------------------------------------------------   */

$parser = xml_parser_create(); //创建一个parser编辑器
xml_set_element_handler($parser, "startElement", "endElement");       //设立标签触发时的相应函数 这里分别为startElement和endElenment
xml_set_character_data_handler($parser, "characterData");             //设立数据读取时的相应函数
$xml_file="sitemapindex.xml";                                                 //指定所要读取的xml文件,可以是url
$filehandler = fopen($xml_file, "r");                                 //打开XML文件
 
while ($data = fread($filehandler, 4096))                             // file_get_contents -- 将整个文件读入一个字符串  性能比较好一点  但是是将一个文件的内容读入到一个字符串中

{ 
    xml_parse($parser, $data, feof($filehandler));                    //每次取出4096个字节进行处理
}
fclose($filehandler);
xml_parser_free($parser);                                             //关闭和释放parser解析器

$name=false;
$position=false;
function startElement($parser_instance, $element_name, $attrs)        //起始标签事件的函数
 {
   global $name,$position;  
   if($element_name=="LOC") //xml中要读取的元素 name
   {
   $name=true;
   $position=false;
   echo "URL:";
  }
  if($element_name=="LASTMOD")  //XML中要读取的元素position
   {$name=false;
   $position=true;
   echo "TIME:";
  }
}
function characterData($parser_instance, $xml_data)                  //读取数据时的函数 
{
   global $name,$position;
   if($position)
    echo $xml_data."<br>";
    if($name)
     echo $xml_data."<br>";
}
function endElement($parser_instance, $element_name)                 //结束标签事件的函数
{
 global $name,$position; 
$name=false;
$position=false;  
}

/*
 * xml文件代码如下：
<?xml version="1.0"?>
<employees>
<employee>
<name>张三</name>
<position age="45">经理</position>
</employee>
<employees>
<employee>
<name>李四</name>
<position age="45">助理</position>
</employee>
</employees>
*/

/* result
 * --------------------------------------------------------------------------------
名字：张三 职位：经理
名字：李四 职位：助理
*/

?>