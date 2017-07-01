<?php
    require('includes/application_top.php');
    /**
     * 新增删除标签
     */
    //添加一级标签
    if($_POST['action'] == 'add_top' && $_POST['top_tag']){
        $top = $db->getAll("SELECT * FROM `products_instock_shipping_sales_after_reason` WHERE `reason_name` = ".$_POST['top_tag']);
        $top_tag = array('reason_name' => $_POST['top_tag'], 'reason_type' => '0');
        if(empty($top)){
            zen_db_perform('products_instock_shipping_sales_after_reason',$top_tag);
            $messageStack->add_session('更新成功', 'success');
            zen_redirect(zen_href_link('manage_return_label.php','','NONSSL'));
        }else{
            zen_redirect(zen_href_link('manage_return_label.php','','NONSSL'));
        }
    }
    //添加次级标签
    if($_POST['action'] == 'add' && $_POST['return_tag']){
        foreach ($_POST['return_tag'] as $k => $v){
            if(!empty($v)){
                $exixt = $db->getAll("SELECT * FROM `products_instock_shipping_sales_after_reason` WHERE `reason_name` = ".$v);
                $tag = array('reason_name' => $v, 'reason_type' => $_POST['tag_type']);
                if(empty($exixt)){
                    zen_db_perform('products_instock_shipping_sales_after_reason',$tag);
                }
            }
        }
        $messageStack->add_session('更新成功', 'success');
        zen_redirect(zen_href_link('manage_return_label.php','','NONSSL'));
    }
    //删除标签
    if($_POST['action'] == 'del'){
        foreach ($_POST['return_tag'] as $k => $v){
            if(!empty($v)){
                $exixt = $db->getAll("SELECT * FROM `products_instock_shipping_sales_after_reason` WHERE `reason_name` = ".$v);
                $tag = array('reason_name' => $v, 'reason_type' => $_POST['tag_type']);
                if(empty($exixt)){
                    zen_db_perform('products_instock_shipping_sales_after_reason',$tag);
                }
            }
        }
        $messageStack->add_session('更新成功', 'success');
        zen_redirect(zen_href_link('manage_return_label.php','','NONSSL'));
    }
    //找出所有非顶级分类的其他标签
    $others_tag_sql = "SELECT `reason_id`,`reason_name`,`reason_type` FROM `products_instock_shipping_sales_after_reason` WHERE `reason_name` = '其他' AND `reason_type`<> 0 ";
    $other_tag = $db->getAll($others_tag_sql);
   $other_tag_info  = array();
    foreach ($other_tag as $key => $val){
       $other_tag_info[$val['reason_type']] = $val['reason_id'];
   }
   function getOtherInfo($server_problem, $other_tag_info){
       global $db;
       $info = array();
       $problem_arr = explode(',', $server_problem);
       foreach ($other_tag_info as $key => $val){
           if(in_array($val, $problem_arr)){
               $parentName = $db->Execute("SELECT `reason_name` FROM `products_instock_shipping_sales_after_reason` WHERE `reason_id` = $key ");
               $info[$parentName->fields['reason_name']] = $val;
           }
       }
       return $info;
   }
    /**筛选所有问题为类型为  其他  的产品*/
    $screen_sql = "SELECT `return_info_id`,`service_problem` FROM `products_instock_shipping_sales_after_info`";
    $screen_content = $db->getAll($screen_sql);
    $screen_id = array();
    foreach ($screen_content as $key => $val){
        $status = 0;
        $screen_arr = explode(',', $val['service_problem']);
        foreach ($other_tag_info as $k => $v){
            if(in_array($v, $screen_arr)){
                $status = 1;
            }
        }
        if($status == 1){
            $screen_id[] = $val['return_info_id'];
        }
    }
    $screen_id_str =  join(',', $screen_id);
    //一级标签分类
    $label_sql = "SELECT reason_name,reason_id FROM `products_instock_shipping_sales_after_reason` WHERE reason_type = 0 ORDER BY reason_id DESC ";
    $label_content = $db->getAll($label_sql);
    //一级标签、二级标签
    $content = array();
    foreach ($label_content as $key => $val){
        $result = '';
        $result = $db->getAll("SELECT reason_name,reason_id FROM `products_instock_shipping_sales_after_reason` WHERE reason_type =".$val['reason_id']);
        $content[$val['reason_name']] = $result;
    }
    /**删除标签*/
    if($_POST['action'] == 'del'){
        if($_POST['top_tags']){
            foreach ($_POST['top_tags'] as $k => $v){
                //当前一级分类的子分类
                $son_tags = $db->getAll("SELECT reason_id FROM `products_instock_shipping_sales_after_reason` WHERE reason_type =".$val['reason_id']);
                //
                foreach ($screen_content as $key => $val){
                    //所有产品的标签信息
                    $screen_arr = explode(',', $val['service_problem']);
                    foreach ($other_tag_info as $k => $v){
                        if(in_array($v, $screen_arr)){
                            $status = 1;
                        }
                    }
                }
            }

        }
        print_r($_POST);
        exit;
    }
    //print_r($content);
    //exit;

?>

<!-- 以根目录下入口文件为基准 -->
<html <?php echo HTML_PARAMS; ?> xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html"
                                 xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/javascript/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all" id="hoverJS" />
    <link rel="stylesheet" type="text/css" href="css/style.css" media="all" id="hoverJS" />
</head>
<!--主体-->
<body >
<!--菜单栏-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<div class="fs_middle">
    <div class="fs_middle_con">
        <!--头部信息开始-->
        <div class="link_title">
            <span><a href="index.php">退换流程</a></span> /
            <span><a href="products_sale_after_service_list.php">退换流程标签管理</a></span>
        </div>
        <h2>“其他”标签信息</h2>


        <!--头部信息结束-->

        <!--功能部分开始-->
        <div class="total_screening">
            <span class="left">
		<a href="#New_tag" data-toggle="modal"><button id="customer_tag" class="btn btn-info"> 退换货标签管理 </button></a>&nbsp;&nbsp;

                <!-- 自定义标签管理 -->
    <div id="New_tag" class="modal hide fade instock_sales in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none;max-height: 1000px;">
     <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">×</button>
            <h3 id="myModalLabel">新增标签</h3>
     </div>
        <!--父级分类-->
 <form action="manage_return_label.php" method="post">
    <table width="100%" border="0"  cellspacing="0" class="border_none" style="height: 60px;">
        <tr >
            <td  align="right" width="176px">添加一级分类：</td>
            <td ><input name="top_tag" type="text" /><input type="hidden" value="add_top" name="action"><button class="btn btn-info">确认添加</button></td>
        </tr>
     </table>
</form>
        <!--添加次级分类-->
        <form action="manage_return_label.php" method="post">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="border_none" style="height: 115px;">
        <tr >
            <td align="right">添加次级分类：</td>
            <td>
                <select name="tag_type" class="input-smedium" onchange="show_other_shipping(this)">
                    <option value="" checked="checked">请选择标签分类</option>
                    <?php
                    foreach($label_content as $k => $v){
                        echo '<option value="'.$v['reason_id'].'" selected>'.$v['reason_name'].'</option>';
                    }
                    ?>
                </select>
                <input name="return_tag[]" type="text" />
            </td>

            <td colspan="" align="left">
                <div name="label_add" class="" style="position: absolute; top:122px; right: 92px;">
                    <table id="return_tag">
                        <tr>
                          <td>
                               <input name="return_tag[]" type="text" />
                          </td>
                          <td>
                              <a href="#" onClick="addNewRow();" title="点击增加标签">
                                  <i class="icon_halflings new_plus"></i>添加多个
                              </a>
                          </td>
                        </tr>
                    </table>
              </div>
            </td>
        </tr>
        <tr >
            <td><input type="hidden" value="add" name="action"></td>
            <td>
                <button class="btn btn-info">确认添加</button>
            </td>
        </tr>

 </table>
            </form>

        <!-- 删除标签 -->
<div class="modal-header">
    <h3 id="myModalLabel">删除标签</h3>
 </div>
    <form action="manage_return_label.php" method="post">
    <table width="90%" border="0" cellspacing="0" cellpadding="0" class="border_none" style="height: 260px; margin-left: 10px; ">
        <?php foreach ($content as $key => $value){?>
    <tr class="special_label" align="rigth">
        <td align="rigth"><?php echo $key.'：';?></td>
        <?php foreach ($value as $k => $v){?>
        <td class="service_problem_label" colspan="3" align="rigth">
<!--            --><?php //echo '<label class=""><input type="checkbox" name="tags[]" value="'.$v['reason_id'].'" />'.$v['reason_name'].'</label>';?>
                <?php echo '<input type="checkbox" name="tags[]" value="'.$v['reason_id'].'" /><span class="label label-info">&nbsp;&nbsp;'.$v['reason_name'].'</span>';?>
        </td>
        <?php }?>
     </tr>
    <?php }?>

        <tr>
             <td align="rigth"><?php echo '一级分类'.'：';?></td>
            <?php foreach ($label_content as $k => $v){?>
            <td class="service_problem_label" colspan="3" align="rigth">
                <?php echo '<input type="checkbox" name="top_tags[]" value="'.$v['reason_id'].'" /><span class="label label-info">&nbsp;&nbsp;'.$v['reason_name'].'</span>';?>
            </td>
            <?php }?>
        </tr>
    <tr >
        <td>
            <input type="hidden" name="action" value="del">
            <button   class="btn btn-info">确认删除</button>
        </td>
    </tr>
 </table>
</form>

</div>
            </span>
            <span class="right">
		<?php if(!fs_admin_have_the_power(array(14))) {echo '快速搜索：';}?>
                <?php echo zen_draw_form('search','manage_return_label', '', 'get', '', true);
                    echo '<input type="text" class="input-medium" name="search" placeholder="RMA单号/订单编号" /> ' . ' ' . zen_hide_session_id();
                    echo '<button class="btn btn-info">Search</button>';
                ?>
                </form>
	</span>
        </div>
        <!--功能部分结束-->

        <!--tabel部分开始-->
        <div class="total_content">
            <table width="100% " cellspacing="0" cellpadding="0" border="0" class="total_table">
                <tr>
                    <th width="7%">录入时间</th>
                    <th width="10%">退换货单号</th>
                    <th width="25%">“其他”原因</th>
                    <th width="25%">“其他”标签类别</th>
                    <th width="5%">处理方式</th>
<!--                    <th width="5%">产品详情</th>-->

                    <th width="10%">运单号</th>
                    <th width="5%">操作人</th>

<!--                    <th width="5%">备注</th>-->
<!--                    <th width="15%">状态</th>-->
                    <!--<th>运单号</th>-->
                    <th width="5%">操作</th>
                </tr>

                <?

                //页码
                if(!empty($_GET['page'])){
                    $page = $_GET['page'];
                }else{
                    $page = 1;
                }
                //搜索条件
                $where = '';
                if(!empty($_GET['search'])){
                    $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
                    $where .= " AND (RMA_number = '".$keywords."' OR return_number = '".$keywords."')";
                }
                //筛选  “其他标签”
                if(!empty($screen_id_str)) {
                    $where .= " AND `return_info_id` IN ($screen_id_str)";
                }
                $where = "1".$where;
                //排序
                $order_by = "ORDER BY  p.apply_time DESC";
                /*		$order_by = "ORDER BY CASE
                                                WHEN pi.is_received = 1   THEN  0
                                                ELSE 1
                                                END,p.apply_time DESC";*/
                $table = 'products_instock_shipping_sales_after_info AS pi LEFT JOIN products_instock_shipping_sales_after AS p ON p.return_id = pi.return_id';
                $total_num = fs_db_count_total($table, $where);
                $page_num = ceil($total_num/20);
                $offset = ($page-1)*20;
                $limit = " LIMIT $offset,20";
                //要取得字段，数组
                $field = array(
                    'p.apply_time',
                    'return_number',
                    'pi.return_type',
                    'products_id',
                    'products_num',
                    'RMA_method',
                    'RMA_number',
                    'apply_admin',
                    'return_reason',
                    'purchase_remark',
                    'pi.return_id',
                    'return_info_id',
                    'is_received',
                    'purchase_return_type',
                    'supplier_number',
                    'serial_number',
                    'money_sum',
                    'p.is_agree_all', //17
                    'pi.is_agree',     //18
                    'assistant_id',
                    'sales_remark',
                    'seattle_remark',
                    'finance_remark',
                    'orders_num',     //23
                    'question_number',  //24
                    'products_instock_info_id', //25
                    'new_products_id',  //26
                    'new_products_num',  //27
                    'service_problem' //28
                );

                $returnAllInfo = fs_get_data_from_db_fields_array($field,$table,$where,$order_by.$limit);

                ?>
                <?php foreach($returnAllInfo as $val){
                    switch($val[2]){
                        case 'W':
                            $return_type = '维修';break;
                        case 'T':
                            $return_type = '退货';break;
                        case 'H':
                            $return_type = '换货';break;
                        case 'B':
                            $return_type = '补货';break;
                        default:
                            $return_type = '';break;
                    }

                    ?>

                    <tr>
                        <td><?php echo $val[0];?></td>
                        <td><?php
                            echo $val[1].'<br/>'.$val[23];
                            if($val[24]){
                                echo '<br/><a href="question_answer_purchase.php?search='.$val[24].'" target="_blank">'.$val[24].'</a><br/>';
                            }
                            ?>
                        </td>
                        <td><?php echo $val[8];?></td>
                        <td>
                            <?php
                                $other = getOtherInfo($val[28], $other_tag_info);
                                foreach ($other as $k => $v){
                                    echo '<br><span class="label label-info" style="margin-top: 5px;">'.$k.'</span> ';
                                }
                            ?>
                        </td>
                        <td><?php echo $return_type;?></td>
                        <!--产品详情-->
<!--                        <td>-->
<!--                            <a href="#myModal_Product_--><?php //echo $val[11];?><!--" data-toggle="modal" style=" display: inline-block;margin-top:5px;">详情</a>-->
<!--                            <div id="myModal_Product_--><?php //echo $val[11];?><!--"  class="modal hide fade instock_sales weihu_h" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
<!--                                <div class="modal-header">-->
<!--                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="clearCategories();">×</button>-->
<!--                                    <h3 id="myModalLabel">产品详情(<img src="images/icon_status_red.gif" border="0" alt="原产品和新产品不一致" title="原产品和新产品不一致" width="10" height="10">表示产品有更新)</h3>-->
<!--                                </div>-->
<!---->
<!--                                <div>-->
<!--                                    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="parcel_size" style="border:1px solid #eee">-->
<!--                                        <tbody><tr>-->
<!--                                            <th width="5%">退换货单号</th>-->
<!--                                            <th width="5%">处理方式</th>-->
<!--                                            <th width="30%">原产品ID/型号/数量</th>-->
<!--                                            <th width="30%">新产品ID/型号/数量</th>-->
<!--                                            <th width="5%">问题编号</th>-->
<!--                                            <th width="25%">附件</th>-->
<!--                                        </tr></tbody>-->
<!---->
<!--                                        --><?php
//                                        $num = count($return_info);
//                                        $product_tip = '';
//                                        if($val[26]&&$val[3]!=$val[26]){
//                                            $product_tip = '&nbsp;<img src="images/icon_status_red.gif" border="0" alt="原产品和新产品不一致" title="原产品和新产品不一致" width="10" height="10">';
//                                        }
//                                        echo '<tr>';
//                                        echo '<td>'.$val[1].$product_tip.'</td>';
//                                        echo '<td>'.$return_type.'</td>';
//                                        echo '<td>';
//                                        if($val[3]){
//                                            echo '<a href="http://www.fs.com/index.php?main_page=product_info&products_id='.$val[3].'" target="_blank">'.$val[3].'</a>&nbsp;&nbsp;'.zen_get_products_model($val[3]).'&nbsp;&nbsp;【'.$val[4].'】<br/>';
//                                        }
//                                        echo '</td>';
//
//                                        echo '<td>';
//                                        if($val[26]){
//                                            echo '<a href="http://www.fs.com/index.php?main_page=product_info&products_id='.$val[26].'" target="_blank">'.$val[26].'</a>&nbsp;&nbsp;'.zen_get_products_model($val[26]).'&nbsp;&nbsp;【'.$val[27].'】<br/>';
//                                        }
//                                        echo '</td>';
//                                        echo '<td>';
//                                        if($val[24]){
//                                            echo '<a href="question_answer_purchase.php?search='.$val[24].'" target="_blank">'.$val[24].'</a>';
//                                        }
//                                        echo '</td>';
//                                        echo '<td>';
//                                        $files=$db->getAll("select id,products_file from products_instock_shipping_sales_after_info_file where products_shipping_info_id=".$val[25]);
//                                        if(is_array($files)&&$files){
//                                            foreach($files as $file){
//                                                $link = "images/Return_RemarkFile/".$file['products_file']."";
//                                                echo '<a href="'.$link.'" target="_blank"><img src="'.$link.'" alt="产品图片附件" width="80" height="40" border="0">';
//                                            }
//                                        }
//                                        echo '</td>';
//                                        echo '</tr>';
//
//                                        ?>
<!--                                    </table>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </td>-->

                        <td><?php

                            if($val[6]){
                                echo 'RMA单号:'.$val[6].'#'.$val[7].'<br/>';
                            }
                            $data = get_return_shipping_info($val[11],$type=2);
                            if($data){
                                echo '西雅图退件:';
                                echo $data['shipping_method'].'#'.$data['shipping_number'];
                            }
                            //$number = getRMA($val[10], $val[11]);
                            /*				echo  $val[5].'#'.$val[6].'<br>'.'国内：'.$number['shipping_method'].'#'.$number['shipping_number'];*/?></td>
                        <td><?php echo zen_get_admin_name($val[7]).'/'.zen_get_admin_name($val[19]);?></td>

                        <!--备注-->
<!--                        <td>--><?php ///*echo $val[9];*/?>
<!--                            <a href="#myModal_Edit_--><?php //echo $val[11];?><!--" data-toggle="modal" style=" display: inline-block;margin-top:5px;"><i class="icon_halflings new_see"></i>备注</a>-->
<!---->
<!--                            <div id="myModal_Edit_--><?php //echo $val[11];?><!--"  class="modal hide fade instock_sales weihu_h" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
<!--                                <div class="modal-header">-->
<!--                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="clearCategories();">×</button>-->
<!--                                    <h3 id="myModalLabel">备注</h3>-->
<!--                                </div>-->
<!---->
<!--                                <div>-->
<!--                                    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="parcel_size" style="border:1px solid #eee">-->
<!--                                        <tbody><tr>-->
<!--                                            <th width="5%">退换货单号</th>-->
<!--                                            <th width="5%">处理方式</th>-->
<!--                                            <th width="20%">产品型号/描述</th>-->
<!--                                            <th width="15%">销售</th>-->
<!--                                            <th width="15%">西雅图仓管</th>-->
<!--                                            <th width="20%">采购</th>-->
<!--                                            <th width="20%">财务</th>-->
<!--                                        </tr></tbody>-->
<!---->
<!--                                        --><?php
//                                        echo '<tr>';
//                                        echo '<td>'.$val[1].'</td>
//                                      <td>'.$return_type.'</td>';
//                                        echo '<td>
//                                <a href="http://www.fs.com/index.php?main_page=product_info&products_id='.$val[3].'" target="_blank">'.$val[3].'</a><br/>'.zen_get_products_model($val[3]).'</td>';
//                                        echo '<td>'.$val[20].'</td>';
//                                        echo '<td>'.$val[21].'</td>
//                                <td>'.$val[9].'</td>';
//                                        echo '<td>'.$val[22].'</td>';
//                                        echo '</tr>';
//
//                                        ?>
<!--                                    </table>-->
<!--                                </div>-->
<!--                            </div>-->
<!---->
<!--                        </td>-->
                        <!--状态-->
<!--                        <td>--><?php //echo zen_get_return_order_status_info($val[11],2);?><!--</td>-->
                        <!--操作-->
                        <td >
                            <a  href="#myGoods_Edit_<?php echo $val[11];?>" data-toggle="modal" >
                                <button class="btn btn-info"> 编辑 </button></a>

                            <!--编辑弹窗开始-->
                            <div id="myGoods_Edit_<?php echo $val[11];?>" class="modal hide  fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="max-height:650px;width: 500px;">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="clearCategories();">×</button>
                                    <h3 id="myModalLabel">退件编辑</h3>
                                </div>
                                <table class="return_product_table" width="100%" cellspacing="0" cellpadding="5" border="0" >
                                    <tbody style="border:none">
                                    <tr style="border:none">
                                        <td style="border:none">退件类型：</td>
                                        <td colspan="2" style="border:none">
                                            <select id="purchase_return_type_<?php echo $val[11];?>"  class="input-large" >
                                                <option value='0' >选择退件类型</option>
                                                <option value='T' <?php if($val[13] == 'T'){ echo 'selected = "selected"';}?>>退货</option>
                                                <option value='H' <?php if($val[13] == 'H'){ echo 'selected = "selected"';}?>>换货</option>
                                                <option value='W' <?php if($val[13] == 'W'){ echo 'selected = "selected"';}?>>维修</option>
                                                <option value='N' <?php if($val[13] == 'N'){ echo 'selected = "selected"';}?>>内检</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr style="border:none">
                                        <td style="border:none" >供应商编号：</td>
                                        <td style="border:none">
                                            <input type="text" id="supplier_number_<?php echo $val[11];?>" name="" value="<?php echo $val[14];?>"  />
                                        </td>
                                    </tr>
                                    <tr style="border:none">
                                        <td style="border:none" >产品序列号(非必填项)：</td>
                                        <td style="border:none">
                                            <input type="text" id="serial_number_<?php echo $val[11];?>" name="" value="<?php echo $val[15];?>"  />
                                        </td>
                                    </tr>
                                    <tr style="border:none">
                                        <td style="border:none">金额¥(非必填项): </td>
                                        <td style="border:none">
                                            <input type="text" id="money_sum_<?php echo $val[11];?>" name="" value="<?php echo $val[16];?>"  />
                                        </td>
                                    </tr>

                                    <tr style="border:none">
                                        <td style="border:none"></td>
                                        <td style="border:none">
                                            <button onclick='edit_goods(<?php echo $val[11];?>, <?php echo $_SESSION['admin_id'];?>)' class="btn btn-info">提交</button>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>
                            <!--编辑弹窗结束-->
                        </td>

                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="12">
                        <!--分页-->
                        <?php
                        echo $fs_manage_html_structure->fs_manage_html_new_split_page($page,$page_num);
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>

<!-- 以根目录下入口文件为基准 -->
<?php
require(DIR_WS_INCLUDES . 'footer.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
<script type="text/javascript" src="includes/javascript/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="includes/javascript/jquery-ui.js"></script>
<script type="text/javascript" src="js/chart.js"></script>

<script type="text/javascript" >
    /******点击添加新栏目   input输入框******/
    function addNewRow(){
        var obj=document.getElementById('return_tag');
        var row=obj.insertRow(-1);
        var c0=row.insertCell(0);
        c0.innerHTML='<input name="return_tag[]" type="text" class=""/>';/*input-medium*/
        c0.align='';
        var c1=row.insertCell(1);
        c1.innerHTML='<a href="javascript:void(0)" onclick="removeRow(this)"><i class="icon_halflings new_delete"></i>删除</a>';
        changeFlag = true;
        try
        {
            comm_set_page_height();
        }
        catch (e)
        {
        }
    }
    function removeRow(fontobj){
        var obj=document.getElementById('return_tag');
        var n=fontobj.parentNode.parentNode.rowIndex;
        obj.deleteRow(n);
    }
</script>




