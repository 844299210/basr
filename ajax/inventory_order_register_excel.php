<?php
require_once 'includes/application_top.php';
require_once '../PHPExcel.php';
require_once '../PHPExcel/Writer/Excel5.php';

//todo:测试
ini_set("display_errors", "On");
error_reporting(E_ERROR);




/*****************       整单         ****************/
$objPHPExcel = new PHPExcel ();
//设置打印格式
$objPHPExcelActiveSheet = $objPHPExcel->getActiveSheet() ;
$objPHPExcelActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$objPHPExcelActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcelActiveSheet->getPageSetup()->setFitToWidth(1);
$objPHPExcelActiveSheet->getPageSetup()->setFitToHeight(0);

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('销售订单收货登记表');
$objPHPExcel->getActiveSheet()->getTabColor()->setARGB( 'FF0094FF');

//设置宽度
/**A-I共九列*/
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);


//设置高度
/**第一行高度*/
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(32);
/**默认行高*/
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(16);

//设置网格线
/**网格线*/
//$objPHPExcel->getActiveSheet()->setShowGridlines();
/**默认字体*/
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
/**第一行 标题*/
$objPHPExcel->getActiveSheet()->setCellValue('A1', '销售订单收货登记表');
/**第一行标题 字体设置*/
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(20);
/**水平居中对齐*/
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//
////todo:第一行
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
///**给单元格赋值*/
//$objPHPExcel->getActiveSheet()->setCellValue('A2', '录入时间：'.date("Y-m-d H:i:s",time()));
///**水平居中对齐*/
//$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
///**设置行高*/
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
//
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('D2:E2');
///**给单元格赋值*/
//$objPHPExcel->getActiveSheet()->setCellValue('D2', '调货编号：'.'DC11033');
///**水平居中对齐*/
//$objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
///**设置行高*/
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
///**字体设置*/
//$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setBold(true)->setSize(12);
//
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('J2:K2');
///**给单元格赋值*/
//$objPHPExcel->getActiveSheet()->setCellValue('J2', '国家/运输：'.'');
///**水平居中对齐*/
//$objPHPExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
///**设置行高*/
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
//
////条形码
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('K2:O2');
///**图片*/
//$img=new PHPExcel_Worksheet_Drawing();
//$img->setResizeProportional(false);
//$img->setPath('../images/excel_logo.png');//写入图片路径
//$img->setHeight(20);//写入图片高度
//$img->setWidth(480);//写入图片宽度
//$img->setOffsetX(1);//写入图片在指定格中的X坐标值
//$img->setOffsetY(1);//写入图片在指定格中的Y坐标值
//$img->setRotation(1);//设置旋转角度
//$img->getShadow()->setVisible(true);//
//$img->getShadow()->setDirection(50);//
//$img->setCoordinates('K2');//设置图片所在表格位置
//$img->setWorksheet($objPHPExcel->getActiveSheet());//把图片写到当前的表格中
//
//
////todo:第三行
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
///**给单元格赋值*/
//$objPHPExcel->getActiveSheet()->setCellValue('A3', 'US/FFX');
///**水平居中对齐*/
//$objPHPExcel->getActiveSheet()->getStyle('A3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
///**设置行高*/
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
//
///**合并单元格*/
//$objPHPExcel->getActiveSheet()->mergeCells('M3:N3');
///**给单元格赋值*/
//$objPHPExcel->getActiveSheet()->setCellValue('M3','DC11033');
///**水平居中对齐*/
//$objPHPExcel->getActiveSheet()->getStyle('M3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
///**设置行高*/
//$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
///**字体设置*/
//$objPHPExcel->getActiveSheet()->getStyle('M3')->getFont()->setBold(true)->setSize(12);
//
//
////TODO:主要内容开始
////边线
//$styleArray = array(
//    'borders' => array(
//        'allborders' => array(
//            'style' => PHPExcel_Style_Border::BORDER_THIN,
//        ),
//    ),
//);
//
////颜色
//$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFont()->setSize(12);
//$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);
//$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//$objPHPExcel->getActiveSheet()->getStyle('A4:O4')->getFill()->getStartColor()->setARGB('00999999');
////表头
//$objPHPExcel->getActiveSheet()->setCellValue('A4', 'ID');
//$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
//$objPHPExcel->getActiveSheet()->setCellValue('B4', '产品名称');
//$objPHPExcel->getActiveSheet()->setCellValue('C4', '数量');
//$objPHPExcel->getActiveSheet()->mergeCells('D4:E4');
//$objPHPExcel->getActiveSheet()->setCellValue('C4', '数量');
//$objPHPExcel->getActiveSheet()->mergeCells('F4:G4');
//$objPHPExcel->getActiveSheet()->setCellValue('F4', '装箱单标签');
//$objPHPExcel->getActiveSheet()->setCellValue('H4', '付款方式');
//$objPHPExcel->getActiveSheet()->setCellValue('I4', '备货区');
//$objPHPExcel->getActiveSheet()->setCellValue('J4', '是否调用库存');
//$objPHPExcel->getActiveSheet()->mergeCells('L4:M4');
//$objPHPExcel->getActiveSheet()->setCellValue('L4', '库存区');
//$objPHPExcel->getActiveSheet()->setCellValue('N4', '发货助理');
//$objPHPExcel->getActiveSheet()->setCellValue('O4', '物流员');
//
////内容循环
//$index = 5;
//
//$total = 0;
//foreach ($returnAll as $key => $val){
//    $info = Currency($val['products_instock_info_id'], $type = true);  //货币信息
//    $symbol = getSymbol($info['symbol']);
//    //print_r($info);die;
//    $objPHPExcel->getActiveSheet()->setCellValue('A'.$index, 'N/M');
//    $objPHPExcel->getActiveSheet()->setCellValue('B'.$index, '1Pkg');
//    $objPHPExcel->getActiveSheet()->mergeCells('C'.$index.':D'.$index);
//    //TODO:换行
//    $objPHPExcel->getActiveSheet()->getRowDimension($index)->setRowHeight(50);
//    $objPHPExcel->getActiveSheet()->getStyle('C'.$index)->getAlignment()->setWrapText(true);
//    $objPHPExcel->getActiveSheet()->getStyle('C'.$index)->getAlignment()->setShrinkToFit(true); //长度不够显示的时候 是否自动换行
//    //$objPHPExcel->getActiveSheet()->getStyle('C'.$index)->getAlignment()->setShrinkToFit(true); //自动转换显示字体大小,使内容能够显示
//    $objPHPExcel->getActiveSheet()->setCellValue('C'.$index, getDecriptions($val['products_id']));
//    $objPHPExcel->getActiveSheet()->setCellValue('E'.$index, $val['products_num']);
//    $objPHPExcel->getActiveSheet()->setCellValue('F'.$index, $symbol.' '.sprintf("%.2f",$info['price']));
//    $objPHPExcel->getActiveSheet()->setCellValue('G'.$index, $symbol.' '.sprintf("%.2f",$info['totalPrice']));
//    $index++;
//    $total += $info['totalPrice'];
//}




$filename = date('Y.m.d').'-'.'调仓出库单'.".xls";
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');