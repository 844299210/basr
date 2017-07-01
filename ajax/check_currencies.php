<?php
//0 0 1 * * /www/beat.fiberstore.com/FS_beta/check_currencies.php
require('/alidata/www/beat.fiberstore.com/includes/application_top.php');
function convertCurrency($from, $to, $amount){
  $data = file_get_contents("http://www.baidu.com/s?wd={$from}%20{$to}&rsv_spt={$amount}");
  preg_match("/<div>1\D*=(\d*\.\d*)\D*<\/div>/",$data, $converted);
  $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
  return number_format($converted, 6);
}

$currency_query_raw = "select title,currencies_id,code,dvalue,value from currencies"  ;//dvalue差值
$currency = $db->Execute($currency_query_raw);
$email = '';
 while (!$currency->EOF) {
 	if($currency->fields['code'] != 'USD'){
 		$get_cur = convertCurrency('USD',$currency->fields['code'], "1");
 		$d = abs($get_cur-$currency->fields['value']);
 		if($d > $currency->fields['dvalue']){
 			$email .= "<tr><td>".$currency->fields['title']."</td><td>".$currency->fields['code']."</td><td>".$get_cur."</td><td>".$currency->fields['value']."</td><td>".$currency->fields['dvalue']."</td></tr>";
 		}
 	}		
	$currency->MoveNext();	
} 
if($email != ''){	
	$html=zen_get_corresponding_languages_email_common('admin');
    //send to us
    $html_msg['EMAIL_HEADER'] = $html['html_header'];
    $html_msg['EMAIL_FOOTER'] = $html['html_footer'];
    $html_msg['EMAIL_BODY']  = "<table align='center'><tr><td>货币</td><td>代码</td><td>最新汇率</td><td>网站汇率</td><td>浮动范围</td></tr>".$email."<tr><td colspan='5'>请及时到货币管理处申请更新网站汇率</td></tr></table>";	
	zen_mail('myskless', 'peter.qin@szyuxuan.com', '汇率更新', '', STORE_NAME, EMAIL_FROM, $html_msg, 'default');
}

require('/alidata/www/beat.fiberstore.com/includes/application_bottom.php'); 