<?php
  //APP接口入口文件
  require('FS_APP/appReturn.php');//数据返回函数
  if (isset ( $_GET ['interface'] ) && $_GET ['interface']) {
    require('FS_APP/application_top.php');//加载后台APP文件
    require('FS_APP/interface.php');//加载接口加载文件
  } else {
    failReturn(403,'InterfaceIsNotAvailable');
  }

?>