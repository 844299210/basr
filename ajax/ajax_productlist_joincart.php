<?php
require 'includes/application_top.php';
  $sql = "select p.products_id, pd.products_name,
                  pd.products_description, p.products_model,
                  p.products_quantity, p.products_image,
                  pd.products_url, p.products_price,products_in_stock,
                  p.products_SKU,products_MFG_PART, p.products_compatible_brand,
                  p.products_warranty, p.products_leadtime,
                  pd.products_specifications, pd.products_short_description,pd.products_technical_paper,
                  p.products_tax_class_id, p.products_date_added,
                  p.products_date_available, p.manufacturers_id, p.products_quantity,
                  p.products_weight, p.products_priced_by_attribute, p.product_is_free,
                  p.products_qty_box_status,
                  p.products_quantity_order_max,p.products_instock_show_statu,
                  p.products_discount_type, p.products_discount_type_from, p.products_sort_order, p.products_price_sorter,
                  p.products_attributes_count,p.products_conditions,p.products_statement,pd.products_overview
    			  ,p.is_inquiry,p.is_min_order_qty,p.is_products_arrow
           from   " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
           where  p.products_status = '1'
           and    p.products_id = '" . (int)$_GET['products_id'] . "'
           and    pd.products_id = p.products_id
           and    pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";

    $product_info = $db->Execute($sql);
  if($product_info->fields['products_in_stock'] == 1){
  $products_in_stock = true;
  }

	// by henly 2013-7-3
    $_SESSION['_products_base_price'] = zen_get_products_base_price((int)$_GET['products_id']);
    $_SESSION['_total_products_base_price'] = $_SESSION['_products_base_price'];
    $_SESSION['products_id'] = intval($_GET['products_id']);
	$countries = get_countries();
    if($_GET['products_id']){
    	$sql = "select products_weight from products where products_id = '".$_GET['products_id']."' limit 1";
    	$res = $db->getAll($sql);
    	$cart_quantity =  1;
    	if($res[0]['products_weight']){
    		$products_weight = $res[0]['products_weight'];
    	}
    	if($res){
    		$total_weight = ($products_weight)*$cart_quantity;
    	}

    }
    $total_count = 1;
    require(DIR_WS_CLASSES . 'shipping.php');
    $shipping_modules = new shipping;
    if($_SESSION['countries_code_21']){
    	$countries_code_2 = $_SESSION['countries_code_21'];
    	if($countries_code_2 == 'CN'){
    		$countries_code_2 = 'US';
    	}
    }else{
    	$countries_code_2 = 'US';
    }
    //$port  = $_SESSION['port'] ? $_SESSION['port']:0;
    $keys_number = get_product_category_key(intval($_GET['products_id']));
    $retail = get_retail_status(intval($_GET['products_id']));
    if($keys_number == 2 || ($keys_number == 1 && $retail==0)){
    	$length = '1m';
    }else{
    	$length = '';
    }
    $length_array = array('length'=>$length,'products_id'=>$_GET['products_id'],'qty'=>1);
    $shipping = $shipping_modules->quotes('','',true,$countries_code_2,$length_array);
   	$shipping = get_sort($shipping);
    foreach($shipping as $key=>$v){
    	if($v['id'] == 'dhlzones'){
    		$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

    	}elseif($v['id'] == 'emszones'){

    		$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

    	}elseif($v['id'] == 'fedexzones'){

    		$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

    	}elseif($v['id'] == 'fedexiezones'){

    		$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

    	}elseif($v['id'] == 'airmailzones'){

    		$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

    	}elseif($v['id'] == 'upszones'){

	    	$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

	    }elseif($v['id'] == 'tntzones'){

	    	$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

	    }elseif($v['id'] == 'freezones'){

	    	$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

	    }elseif($v['id'] == 'seazones'){

	    	$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

	    }elseif($v['id'] == 'airliftzones'){

	    	$shipping[$key]['days'] = get_days($countries_code_2,$v['id']);

	    }elseif($v['id'] == 'customzones'){

				$shipping[$key]['days'] = 'N/A';
		}

    }

	// by henly 2013-7-3 end

    $products_price_sorter = $product_info->fields['products_price_sorter'];

    $products_price = $currencies->display_price($product_info->fields['products_price'],
                      zen_get_tax_rate($product_info->fields['products_tax_class_id']));

// set flag for attributes module usage:
    $flag_show_weight_attrib_for_this_prod_type = SHOW_PRODUCT_INFO_WEIGHT_ATTRIBUTES;


    /*bof products attributes acount*/
  $products_attributes_count = $product_info->fields['products_attributes_count'];
  /*eof products attributes acount*/

  require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_ATTRIBUTES));

  $name_standard = array('standard');
  $name_strands = array('strands');
  $d_standard = array('Standard');
  $d_strands = array('Strands');

  $products_name = $product_info->fields['products_name'];

  $products_is_inquiry = $product_info->fields['is_inquiry'];
  $products_is_min_order_qty = $product_info->fields['is_min_order_qty'];
  $is_products_arrow = $product_info->fields['is_products_arrow'];
  $products_model = $product_info->fields['products_model'];
  // if no common markup tags in description, add line breaks for readability:
//  $products_description = (!preg_match('/(<br|<p|<div|<dd|<li|<span)/i', $product_info->fields['products_description']) ? nl2br($product_info->fields['products_description']) : $product_info->fields['products_description']);



 $products_description1 = preg_replace('/<!--(.*)-->/i', '', $product_info->fields['products_description']);
$patterns = array();
$patterns[0] = "/width: 691px; height: 181px;/";

$replacements = array();
$replacements[0] = "/width: 509px; height: 134px;/";


$image_01 = array();
//$image_01[0] = '/750/';
//$image_01[1] = '/447/';

$image_01[0] = "/width: 750px; height: 447px;/";

$image_01_1 = array();
//$image_01_1[2] = '559';
//$image_01_1[1] = '394';

$image_01_1[0] = "/width: 509px; height: 394px;/";



$image_02 = array();
$image_02[0] = "/width: 474px; height: 346px;/";

$image_02_1 = array();
$image_02_1[0] = "/width: 426px; height: 328px;/";

$image_03 = array();
$image_03[0] = "/width: 559px; height: 327px;/";

$image_03_1 = array();
$image_03_1[0] = "/width: 750px; height: 294px;/";

$image_04 = array();
$image_04[0] = "/width: 410px; height: 163px;/";

$image_04_1 = array();
$image_04_1[0] = "/width: 509px; height: 163px;/";

$image_05 = array();
//$image_05[0] = '/750/';
//$image_05[1] = '/394/';

$image_05[0] = "/width: 750px; height: 394px;/";

$image_05_1 = array();
//$image_05_1[2] = '559';
//$image_05_1[1] = '394';

$image_05_1[0] = "/width: 559px; height: 394px;/";

$image_06 = array();
$image_06[0] = "/width: 754px; height: 439px;/";

$image_06_1 = array();
$image_06_1[0] = "/width: 750px; height: 437px;/";


$image_07 = array();
$image_07[0] = "/width: 750px; height: 249px;/";

$image_07_1 = array();
$image_07_1[0] = "/width: 750px; height: 655px;/";

//<img alt="" src="http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 5.jpg" style="width: 300px; height: 300px;" />
//<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 5.jpg\" style="width: 300px; height: 300px;" />

$image_08 = array();
$image_08[0] = "<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 5.jpg\" style=\"width: 300px; height: 300px;\" />";

$image_08_1 = array();
$image_08_1[0] = "<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 5.jpg\" style=\"width: 600px; height: 600px;\" />";

$image_09 = array();
$image_09[0] =   "<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/single- fiber-mux -demux-only-1(11).jpg\" style=\"width: 497px; height: 224px;\" />";

$image_09_1 = array();
$image_09_1[0] = "<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/single- fiber-mux -demux-only-1(11).jpg\" style=\"width: 500px; height: 250px;\" />";


$image_010 = array();
$image_010[0] = "/width: 497px; height: 208px;/";
$image_010[1] = "/width: 497px; height: 224px;/";

$image_010_1 = array();
$image_010_1[0] = "/width: 550px; height: 243px;/";
$image_010_1[2] = "/width: 550px; height: 243px;/";

$description_01 =array();
//<img alt="" src="http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 4.jpg" />
//$description_01[0] = "/MPO CASSETTE 4.jpg/";
$description_01[0] = "<img alt=\"\" src=\"http://cn.fs.com/images/ckfinder/images/MPO CASSETTE 4.jpg\" />";

$description_01_1 =array();
$description_01_1[0] = "/&nbsp;/";


 	?>



 	<?php
 	$description_space = array('&nbsp;');
 	$description_space_01 = array(' ');

 	$description_space_2 = array('	');
 	$description_space_02 = array('');

 	$descirption_strong_1 = array('<tr>
				<td>
					<u><strong>');
 	$descirption_strong_01 = array('<tr>
				<td>
					<u>');

 	$descirption_strong_2 = array('</a></strong></u></td>');
 	$descirption_strong_02 = array('</a></u></td>');

 	$description_strong_3 = array('<u><strong>');
 	$description_strong_03 = array('<u>');

 	$description_strong_4 = array('</a></strong></u>');
 	$description_strong_04 = array('</a></u>');

 	$table_replace = array('</table>
<table>');

 	$table_new = array('');

 	$cdn_url = array('http://fs_products.s3.amazonaws.com/');
 	$cdn_url_update =array('https://fs_products.s3.amazonaws.com/');

 	$cdn_sfp = array('http://sfp-transceiver-modules.com.s3.amazonaws.com');
    //$cdn_sfp_update = array('https://sfp-transceiver-modules.com.s3.amazonaws.com');
    $cdn_sfp_update = array('https://sfp-transceiver-modules.s3.amazonaws.com');

    $cdn_cwdm = array('http://cwdm-dwdm-oadm.com.s3.amazonaws.com');
    //$cdn_cwdm_update = array('https://cwdm-dwdm-oadm.com.s3.amazonaws.com');
    $cdn_cwdm_update = array('https://cwdm-dwdm-oadm.s3.amazonaws.com');

 	$cwdm_factory = '
<div class="p_con_pic">
<div class="p_con_01">Our Factory</div>
<dl>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines06.jpg" alt="FiberStore " title="FiberStore "><br>Production Environment</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines07.jpg" alt="FiberStore " title="FiberStore "><br>Production Environment</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines03.jpg" alt="FiberStore " title="FiberStore "><br>Standardized Production Line</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines05.jpg" alt="FiberStore " title="FiberStore "><br>Standardized Production Line</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines01.jpg" alt="FiberStore " title="FiberStore "><br>Product Testing </dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines08.jpg" alt="FiberStore " title="FiberStore "><br>Product Testing</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines10.jpg" alt="FiberStore " title="FiberStore "><br>Product Package</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines12.jpg" alt="FiberStore " title="FiberStore "><br>Product Details</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines13.jpg" alt="FiberStore " title="FiberStore "><br>Device Assembly</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines14.jpg" alt="FiberStore " title="FiberStore "><br>Device Assembly</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines15.jpg" alt="FiberStore " title="FiberStore "><br>Device Assembly</dd>
<dd><img src="http://cn.fs.com/images/factory/fiberstore-mux-lines16.jpg" alt="FiberStore " title="FiberStore "><br>Device Assembly</dd>
<br class="ccc">
</dl></div>'
 			;
 	$dwdm_specification = '<div class="p_con_01">Specification</div><table border="0"cellpadding="0"cellspacing="0"class="solu_table01"width="100%">
 	<tbody><tr><td class="solu_table_tit01"colspan="6"><span class="pro_float_left">DWDM OADM Specifications</span></td>
 	</tr><tr><td width="34%">Channel Wavelength</td><td align="center"colspan="5">ITU-T DWDM Grid</td></tr><tr>
 	<td bgcolor="#f4f4f4">Channel Spacing(nm)</td><td align="center"colspan="5">100 GHz Channels</td></tr><tr>
 	<td>Number of Channels</td><td align="center">1</td><td align="center">2</td><td align="center">4</td>
 	<td align="center">8</td></tr><tr><td bgcolor="#f4f4f4">Bandwidth @O.5dB(nm)</td><td align="center">&gt;14</td>
 	<td align="center">&gt;14</td><td align="center">&gt;14</td><td align="center">&gt;14</td></tr><tr><td>Passband(nm)
 	</td><td align="center"colspan="5">&lambda;&plusmn;6.5</td></tr><tr><td bgcolor="#f4f4f4">Passband flatness(dB)</td>
 	<td align="center">&le;0.4</td><td align="center">&le;0.4</td><td align="center">&le;0.4</td>
 	<td align="center">&le;0.4</td></tr><tr><td>IL(In&reg;Drop @&lambda;drop)(dB)</td><td align="center">&le;0.6</td>
 	<td align="center">&le;0.9</td><td align="center">&le;2.0</td><td align="center">&le;3.2</td></tr><tr>
 	<td bgcolor="#f4f4f4">IL(Add&reg;Out @&lambda;add)(dB)</td><td align="center">&le;0.6</td><td align="center">NA</td>
 	<td align="center">&le;2.0</td><td align="center">&le;3.2</td></tr><tr><td>IL(In&reg;Out @other&lambda;)(dB)</td>
 	<td align="center">NA</td><td align="center">&le;1.2</td><td align="center">&le;2.5</td><td align="center">&le;4.0</td></tr>
 	<tr><td bgcolor="#f4f4f4">Adjacent isolation(dB)</td><td align="center"colspan="5">30</td></tr><tr>
 	<td>Non-adjacent isolation(dB)</td><td align="center"colspan="5">40</td></tr><tr><td>Isolation(In&reg;Out@&lambda;drop)(dB)</td>
 	<td align="center"colspan="5">28</td></tr><tr><td bgcolor="#f4f4f4">Wavelength thermal stability(nm/℃)</td>
 	<td align="center">&lt;0.002</td><td align="center">&lt;0.002</td><td align="center">&lt;0.002</td>
 	<td align="center">&lt;0.002</td></tr><tr><td>Insertion Loss Thermal Stability(dB/℃)</td><td align="center">&lt;0.006</td>
 	<td align="center">&lt;0.006</td><td align="center">&lt;0.006</td><td align="center">&lt;0.006</td></tr><tr>
 	<td bgcolor="#f4f4f4">PDL(dB)</td><td align="center">&lt;0.15</td><td align="center">&lt;0.15</td>
 	<td align="center">&lt;0.15</td><td align="center">&lt;0.12</td></tr><tr><td>PMD(ps)</td><td align="center">&lt;0.1</td>
 	<td align="center">&lt;0.1</td><td align="center">&lt;0.1</td><td align="center">&lt;0.15</td></tr><tr>
 	<td bgcolor="#f4f4f4">Return Loss</td><td align="center"colspan="5">&gt;45</td></tr><tr><td>Operating Temperature(&deg;C)</td>
 	<td align="center"colspan="5">-5 to 65</td></tr><tr><td bgcolor="#f4f4f4">Storage Temperature(&deg;C)</td>
 	<td align="center"colspan="5">-40 to 85</td></tr><tr><td colspan="6">*Note:Insertion Loss values do not include connector losses.</td></tr>
 	</tbody></table><br/><div class="ccc">&nbsp;</div><div class="p_con_01">DWDM Wavelength Reference Table</div>
 	<table border="0"cellpadding="0"cellspacing="0"class="oem_tabel_01"width="100%"><tbody><tr>
 	<td colspan="4"style="text-align: center; "valign="center">ITU Grid Standard DWDM Wavelength Reference Table</td>
 	</tr><tr><td style="text-align: center; "valign="center">Channel</td><td style="text-align: center; "valign="center">
 	Frequency(THz)</td><td style="text-align: center; "valign="center">Center Wavelength(nm)</td></tr><tr>
 	<td style="text-align: center; "valign="center">C15</td><td style="text-align: center; "valign="center">191.5</td>
 	<td style="text-align: center; "valign="center">1565.50</td></tr><tr><td style="text-align: center; "valign="center">H15</td>
 	<td style="text-align: center; "valign="center">191.55</td><td style="text-align: center; "valign="center">1565.09</td></tr>
 	<tr><td style="text-align: center; "valign="center">C16</td><td style="text-align: center; "valign="center">191.60</td>
 	<td style="text-align: center; "valign="center">1564.68</td></tr><tr><td style="text-align: center; "valign="center">C16</td>
 	<td style="text-align: center; "valign="center">191.60</td><td style="text-align: center; "valign="center">1564.68</td></tr>
 	<tr><td style="text-align: center; "valign="center">H16</td><td style="text-align: center; "valign="center">191.65</td>
 	<td style="text-align: center; "valign="center">1564.27</td></tr><tr><td style="text-align: center; "valign="center">C17</td>
 	<td style="text-align: center; "valign="center">191.7</td><td style="text-align: center; "valign="center">1563.86</td></tr>
 	<tr><td style="text-align: center; "valign="center">H17</td><td style="text-align: center; "valign="center">191.75</td>
 	<td style="text-align: center; "valign="center">1563.46</td></tr><tr><td style="text-align: center; "valign="center">C18</td>
 	<td style="text-align: center; "valign="center">191.8</td><td style="text-align: center; "valign="center">1563.05</td></tr>
 	<tr><td style="text-align: center; "valign="center">H18</td><td style="text-align: center; "valign="center">191.85</td>
 	<td style="text-align: center; "valign="center">1562.64</td></tr><tr><td style="text-align: center; "valign="center">C19</td>
 	<td style="text-align: center; "valign="center">191.9</td><td style="text-align: center; "valign="center">1562.23</td></tr>
 	<tr><td style="text-align: center; "valign="center">H19</td><td style="text-align: center; "valign="center">191.95</td>
 	<td style="text-align: center; "valign="center">1561.83</td></tr><tr><td style="text-align: center; "valign="center">C20</td>
 	<td style="text-align: center; "valign="center">192</td><td style="text-align: center; "valign="center">1561.42</td>
 	</tr><tr><td style="text-align: center; "valign="center">H20</td><td style="text-align: center; "valign="center">192.05</td>
 	<td style="text-align: center; "valign="center">1561.01</td></tr><tr><td style="text-align: center; "valign="center">C21</td>
 	<td style="text-align: center; "valign="center">192.1</td><td style="text-align: center; "valign="center">1560.61</td></tr>
 	<tr><td style="text-align: center; "valign="center">H21</td><td style="text-align: center; "valign="center">192.15</td>
 	<td style="text-align: center; "valign="center">1560.20</td></tr><tr><td style="text-align: center; "valign="center">C22</td>
 	<td style="text-align: center; "valign="center">192.2</td><td style="text-align: center; "valign="center">1559.79</td>
 	</tr><tr><td style="text-align: center; "valign="center">H22</td><td style="text-align: center; "valign="center">192.25</td>
 	<td style="text-align: center; "valign="center">1559.39</td></tr><tr><td style="text-align: center; "valign="center">C23</td>
 	<td style="text-align: center; "valign="center">192.3</td><td style="text-align: center; "valign="center">1558.98</td></tr>
 	<tr><td style="text-align: center; "valign="center">H23</td><td style="text-align: center; "valign="center">192.35</td>
 	<td style="text-align: center; "valign="center">1558.58</td></tr><tr><td style="text-align: center; "valign="center">C24</td>
 	<td style="text-align: center; "valign="center">192.4</td><td style="text-align: center; "valign="center">1558.17</td></tr>
 	<tr><td style="text-align: center; "valign="center">H24</td><td style="text-align: center; "valign="center">192.45</td>
 	<td style="text-align: center; "valign="center">1557.77</td></tr><tr><td style="text-align: center; "valign="center">C25</td>
 	<td style="text-align: center; "valign="center">192.5</td><td style="text-align: center; "valign="center">1557.36</td></tr>
 	<tr><td style="text-align: center; "valign="center">H25</td><td style="text-align: center; "valign="center">192.55</td>
 	<td style="text-align: center; "valign="center">1556.96</td></tr><tr><td style="text-align: center; "valign="center">C26</td>
 	<td style="text-align: center; "valign="center">192.6</td><td style="text-align: center; "valign="center">1556.55</td></tr>
 	<tr><td style="text-align: center; "valign="center">H26</td><td style="text-align: center; "valign="center">192.65</td>
 	<td style="text-align: center; "valign="center">1556.15</td></tr><tr><td style="text-align: center; "valign="center">C27</td>
 	<td style="text-align: center; "valign="center">192.7</td><td style="text-align: center; "valign="center">1555.75</td></tr>
 	<tr><td style="text-align: center; "valign="center">H27</td><td style="text-align: center; "valign="center">192.75</td>
 	<td style="text-align: center; "valign="center">1555.34</td></tr><tr><td style="text-align: center; "valign="center">C28</td>
 	<td style="text-align: center; "valign="center">192.8</td><td style="text-align: center; "valign="center">1554.94</td></tr>
 	<tr><td style="text-align: center; "valign="center">H28</td><td style="text-align: center; "valign="center">192.85</td>
 	<td style="text-align: center; "valign="center">1554.54</td></tr><tr><td style="text-align: center; "valign="center">C29</td>
 	<td style="text-align: center; "valign="center">192.9</td><td style="text-align: center; "valign="center">1554.13</td></tr><tr>
 	<td style="text-align: center; "valign="center">H29</td><td style="text-align: center; "valign="center">192.95</td>
 	<td style="text-align: center; "valign="center">1553.73</td></tr><tr><td style="text-align: center; "valign="center">C30</td>
 	<td style="text-align: center; "valign="center">193</td><td style="text-align: center; "valign="center">1553.33</td></tr><tr>
 	<td style="text-align: center; "valign="center">H30</td><td style="text-align: center; "valign="center">193.05</td>
 	<td style="text-align: center; "valign="center">1552.93</td></tr><tr><td style="text-align: center; "valign="center">C31</td>
 	<td style="text-align: center; "valign="center">193.1</td><td style="text-align: center; "valign="center">1552.52</td></tr><tr>
 	<td style="text-align: center; "valign="center">H31</td><td style="text-align: center; "valign="center">193.15</td>
 	<td style="text-align: center; "valign="center">1552.12</td></tr><tr><td style="text-align: center; "valign="center">C32</td>
 	<td style="text-align: center; "valign="center">193.2</td><td style="text-align: center; "valign="center">1551.72</td></tr><tr>
 	<td style="text-align: center; "valign="center">H32</td><td style="text-align: center; "valign="center">193.25</td>
 	<td style="text-align: center; "valign="center">1551.32</td></tr><tr><td style="text-align: center; "valign="center">C33</td>
 	<td style="text-align: center; "valign="center">193.3</td><td style="text-align: center; "valign="center">1550.92</td></tr><tr>
 	<td style="text-align: center; "valign="center">H33</td><td style="text-align: center; "valign="center">193.35</td>
 	<td style="text-align: center; "valign="center">1550.52</td></tr><tr><td style="text-align: center; "valign="center">C34</td>
 	<td style="text-align: center; "valign="center">193.4</td><td style="text-align: center; "valign="center">1550.12</td></tr><tr>
 	<td style="text-align: center; "valign="center">H34</td><td style="text-align: center; "valign="center">193.45</td>
 	<td style="text-align: center; "valign="center">1549.72</td></tr><tr><td style="text-align: center; "valign="center">C35</td>
 	<td style="text-align: center; "valign="center">193.5</td><td style="text-align: center; "valign="center">1549.32</td></tr><tr>
 	<td style="text-align: center; "valign="center">H35</td><td style="text-align: center; "valign="center">193.55</td>
 	<td style="text-align: center; "valign="center">1548.92</td></tr><tr><td style="text-align: center; "valign="center">C36</td>
 	<td style="text-align: center; "valign="center">193.6</td><td style="text-align: center; "valign="center">1548.51</td></tr><tr>
 	<td style="text-align: center; "valign="center">H36</td><td style="text-align: center; "valign="center">193.65</td>
 	<td style="text-align: center; "valign="center">1548.12</td></tr><tr><td style="text-align: center; "valign="center">C37</td>
 	<td style="text-align: center; "valign="center">193.7</td><td style="text-align: center; "valign="center">1547.72</td></tr><tr>
 	<td style="text-align: center; "valign="center">H37</td><td style="text-align: center; "valign="center">193.75</td>
 	<td style="text-align: center; "valign="center">1547.32</td></tr><tr><td style="text-align: center; "valign="center">C38</td>
 	<td style="text-align: center; "valign="center">193.8</td><td style="text-align: center; "valign="center">1546.92</td></tr><tr>
 	<td style="text-align: center; "valign="center">H38</td><td style="text-align: center; "valign="center">193.85</td>
 	<td style="text-align: center; "valign="center">1546.52</td></tr><tr><td style="text-align: center; "valign="center">C39</td>
 	<td style="text-align: center; "valign="center">193.9</td><td style="text-align: center; "valign="center">1546.12</td></tr><tr>
 	<td style="text-align: center; "valign="center">H39</td><td style="text-align: center; "valign="center">193.95</td>
 	<td style="text-align: center; "valign="center">1545.72</td></tr><tr><td style="text-align: center; "valign="center">C40</td>
 	<td style="text-align: center; "valign="center">194</td><td style="text-align: center; "valign="center">1545.32</td></tr><tr>
 	<td style="text-align: center; "valign="center">H40</td><td style="text-align: center; "valign="center">194.05</td>
 	<td style="text-align: center; "valign="center">1544.92</td></tr><tr><td style="text-align: center; "valign="center">C41</td>
 	<td style="text-align: center; "valign="center">194.1</td><td style="text-align: center; "valign="center">1544.53</td></tr>
 	<tr><td style="text-align: center; "valign="center">H41</td><td style="text-align: center; "valign="center">194.15</td>
 	<td style="text-align: center; "valign="center">1544.13</td></tr><tr><td style="text-align: center; "valign="center">C42</td>
 	<td style="text-align: center; "valign="center">194.2</td><td style="text-align: center; "valign="center">1543.73</td></tr>
 	<tr><td style="text-align: center; "valign="center">H42</td><td style="text-align: center; "valign="center">194.25</td>
 	<td style="text-align: center; "valign="center">1543.33</td></tr><tr><td style="text-align: center; "valign="center">C43</td>
 	<td style="text-align: center; "valign="center">194.3</td><td style="text-align: center; "valign="center">1542.94</td></tr>
 	<tr><td style="text-align: center; "valign="center">H43</td><td style="text-align: center; "valign="center">194.35</td>
 	<td style="text-align: center; "valign="center">1542.54</td></tr><tr><td style="text-align: center; "valign="center">C44</td>
 	<td style="text-align: center; "valign="center">194.4</td><td style="text-align: center; "valign="center">1542.14</td></tr>
 	<tr><td style="text-align: center; "valign="center">H44</td><td style="text-align: center; "valign="center">194.45</td>
 	<td style="text-align: center; "valign="center">1541.75</td></tr><tr><td style="text-align: center; "valign="center">C45</td>
 	<td style="text-align: center; "valign="center">194.5</td><td style="text-align: center; "valign="center">1541.35</td></tr><tr>
 	<td style="text-align: center; "valign="center">H45</td><td style="text-align: center; "valign="center">194.55</td>
 	<td style="text-align: center; "valign="center">1540.95</td></tr><tr><td style="text-align: center; "valign="center">C46</td>
 	<td style="text-align: center; "valign="center">194.6</td><td style="text-align: center; "valign="center">1540.56</td></tr><tr>
 	<td style="text-align: center; "valign="center">H46</td><td style="text-align: center; "valign="center">194.65</td>
 	<td style="text-align: center; "valign="center">1540.16</td></tr><tr><td style="text-align: center; "valign="center">C47</td>
 	<td style="text-align: center; "valign="center">194.7</td><td style="text-align: center; "valign="center">1539.77</td></tr>
 	<tr><td style="text-align: center; "valign="center">H47</td><td style="text-align: center; "valign="center">194.75</td>
 	<td style="text-align: center; "valign="center">1539.37</td></tr><tr><td style="text-align: center; "valign="center">C48</td>
 	<td style="text-align: center; "valign="center">194.8</td><td style="text-align: center; "valign="center">1538.98</td></tr><tr>
 	<td style="text-align: center; "valign="center">H48</td><td style="text-align: center; "valign="center">194.85</td>
 	<td style="text-align: center; "valign="center">1538.58</td></tr><tr><td style="text-align: center; "valign="center">C49</td>
 	<td style="text-align: center; "valign="center">194.9</td><td style="text-align: center; "valign="center">1538.19</td></tr><tr>
 	<td style="text-align: center; "valign="center">H49</td><td style="text-align: center; "valign="center">194.95</td>
 	<td style="text-align: center; "valign="center">1537.79</td></tr><tr><td style="text-align: center; "valign="center">C50</td>
 	<td style="text-align: center; "valign="center">195</td><td style="text-align: center; "valign="center">1537.4</td></tr><tr>
 	<td style="text-align: center; "valign="center">H50</td><td style="text-align: center; "valign="center">195.05</td>
 	<td style="text-align: center; "valign="center">1537.00</td></tr><tr><td style="text-align: center; "valign="center">C51</td>
 	<td style="text-align: center; "valign="center">195.1</td><td style="text-align: center; "valign="center">1536.61</td></tr><tr>
 	<td style="text-align: center; "valign="center">H51</td><td style="text-align: center; "valign="center">195.15</td>
 	<td style="text-align: center; "valign="center">1536.22</td></tr><tr><td style="text-align: center; "valign="center">C52</td>
 	<td style="text-align: center; "valign="center">195.2</td><td style="text-align: center; "valign="center">1535.82</td></tr><tr><td style="text-align: center; "valign="center">H52</td><td style="text-align: center; "valign="center">195.25</td>
 	<td style="text-align: center; "valign="center">1535.43</td></tr><tr><td style="text-align: center; "valign="center">C53</td>
 	<td style="text-align: center; "valign="center">195.3</td><td style="text-align: center; "valign="center">1535.04</td></tr><tr>
 	<td style="text-align: center; "valign="center">H53</td><td style="text-align: center; "valign="center">195.35</td>
 	<td style="text-align: center; "valign="center">1534.64</td></tr><tr><td style="text-align: center; "valign="center">C54</td>
 	<td style="text-align: center; "valign="center">195.4</td><td style="text-align: center; "valign="center">1534.25</td></tr><tr>
 	<td style="text-align: center; "valign="center">H54</td><td style="text-align: center; "valign="center">195.45</td>
 	<td style="text-align: center; "valign="center">1533.86</td></tr><tr><td style="text-align: center; "valign="center">C55</td>
 	<td style="text-align: center; "valign="center">195.5</td><td style="text-align: center; "valign="center">1533.47</td></tr><tr>
 	<td style="text-align: center; "valign="center">H55</td><td style="text-align: center; "valign="center">195.55</td><td style="text-align: center; "valign="center">1533.07</td></tr><tr><td style="text-align: center; "valign="center">C56</td>
 	<td style="text-align: center; "valign="center">195.6</td><td style="text-align: center; "valign="center">1532.68</td></tr><tr>
 	<td style="text-align: center; "valign="center">H56</td><td style="text-align: center; "valign="center">195.65</td>
 	<td style="text-align: center; "valign="center">1532.29</td></tr><tr><td style="text-align: center; "valign="center">C57</td>
 	<td style="text-align: center; "valign="center">195.7</td><td style="text-align: center; "valign="center">1531.9</td></tr><tr>
 	<td style="text-align: center; "valign="center">H57</td><td style="text-align: center; "valign="center">195.75</td>
 	<td style="text-align: center; "valign="center">1531.51</td></tr><tr><td style="text-align: center; "valign="center">C58</td>
 	<td style="text-align: center; "valign="center">195.8</td><td style="text-align: center; "valign="center">1531.12</td></tr><tr>
 	<td style="text-align: center; "valign="center">H58</td><td style="text-align: center; "valign="center">195.85</td>
 	<td style="text-align: center; "valign="center">1539.73</td></tr><tr><td style="text-align: center; "valign="center">C59</td>
 	<td style="text-align: center; "valign="center">195.9</td><td style="text-align: center; "valign="center">1530.33</td></tr>
 	<tr><td style="text-align: center; "valign="center">H59</td><td style="text-align: center; "valign="center">195.95</td>
 	<td style="text-align: center; "valign="center">1529.94</td></tr><tr><td style="text-align: center; "valign="center">C60</td>
 	<td style="text-align: center; "valign="center">196</td><td style="text-align: center; "valign="center">1529.55</td></tr>
 	<tr><td style="text-align: center; "valign="center">H60</td><td style="text-align: center; "valign="center">196.05</td>
 	<td style="text-align: center; "valign="center">1529.16</td></tr><tr><td style="text-align: center; "valign="center">C61</td>
 	<td style="text-align: center; "valign="center">196.1</td><td style="text-align: center; "valign="center">1528.77</td></tr>
 	<tr><td style="text-align: center; "valign="center">H61</td><td style="text-align: center; "valign="center">196.15</td>
 	<td style="text-align: center; "valign="center">1528.38</td></tr><tr><td style="text-align: center; "valign="center">C62</td>
 	<td style="text-align: center; "valign="center">196.20</td><td style="text-align: center; "valign="center">1527.99</td></tr>
 	<tr><td style="text-align: center; "valign="center">H62</td><td style="text-align: center; "valign="center">196.25</td>
 	<td style="text-align: center; "valign="center">1527.61</td></tr></tbody></table><br/>';

 	$cwdm_oadm_specifaction ='<div class="features_tit">Specification</div><table border="0"cellpadding="0"cellspacing="0"class="solu_table01"width="100%">
 	<tbody><tr><td class="solu_table_tit01"colspan="6"><span class="pro_float_left">CWDM OADM Specifications</span>
 	<span class="pro_copyright"></span></td></tr><tr><td width="35%">Channel Wavelength</td><td align="center"colspan="5">ITU-T CWDM Grid</td>
 	</tr><tr><td bgcolor="#f4f4f4">Channel Spacing</td><td align="center"colspan="5">20nm</td></tr><tr><td>Number of Channels</td>
 	<td align="center">1</td><td align="center">2</td><td align="center">4</td><td align="center">8</td></tr><tr><td bgcolor="#f4f4f4">Bandwidth @O.5dB(nm)</td>
 	<td align="center">&gt;14</td><td align="center">&gt;14</td><td align="center">&gt;14</td><td align="center">&gt;14</td></tr>
 	<tr><td>Passband(nm)</td><td align="center"colspan="5">&lambda;&plusmn;6.5</td></tr><tr><td bgcolor="#f4f4f4">Passband flatness(dB)</td>
 	<td align="center">&le;0.4</td><td align="center">&le;0.4</td><td align="center">&le;0.4</td><td align="center">&le;0.4</td></tr>
 	<tr><td>IL(In&reg;Drop @&lambda;drop)(dB)</td><td align="center">&le;0.6</td><td align="center">&le;0.9</td><td align="center">&le;2.0</td>
 	<td align="center">&le;3.2</td></tr><tr><td bgcolor="#f4f4f4">IL(Add&reg;Out @&lambda;add)(dB)</td><td align="center">&le;0.6</td>
 	<td align="center">N/A</td><td align="center">&le;2.0</td><td align="center">&le;3.2</td></tr><tr><td>IL(In&reg;Out @other&lambda;)(dB)</td>
 	<td align="center">NA</td><td align="center">&le;1.2</td><td align="center">&le;2.5</td><td align="center">&le;4.0</td></tr>
 	<tr><td bgcolor="#f4f4f4">Adjacent isolation(dB)</td><td align="center"colspan="5">&gt;30</td></tr><tr><td>Non-adjacent isolation(dB)</td>
 	<td align="center"colspan="5">&gt;40</td></tr><tr><td bgcolor="#f4f4f4">Isolation(In&reg;Out @&lambda;drop)(dB)</td>
 	<td align="center"colspan="5">&gt;25</td></tr><tr><td>Wavelength thermal stability(nm/℃)</td><td align="center">&lt;0.002</td>
 	<td align="center">&lt;0.002</td><td align="center">&lt;0.002</td><td align="center">&lt;0.002</td></tr><tr><td bgcolor="#f4f4f4">Insertion Loss Thermal Stability(dB/℃)</td>
 	<td align="center">&lt;0.006</td><td align="center">&lt;0.006</td><td align="center">&lt;0.006</td><td align="center">&lt;0.007</td></tr>
 	<tr><td>PDL(dB)</td><td align="center">&lt;0.15</td><td align="center">&lt;0.15</td><td align="center">&lt;0.15</td><td align="center">&lt;0.2</td></tr>
 	<tr><td bgcolor="#f4f4f4">PMD(ps)</td><td align="center">&lt;0.1</td><td align="center">&lt;0.1</td><td align="center">&lt;0.1</td>
 	<td align="center">&lt;0.15</td></tr><tr><td>Return Loss(dB)</td><td align="center"colspan="5">&gt;45</td></tr><tr><td bgcolor="#f4f4f4">Operating Temperature(&deg;C)</td>
 	<td align="center"colspan="5">-5 to 65</td></tr><tr><td>Storage Temperature(&deg;C)</td><td align="center"colspan="5">-40 to 85</td></tr>
 	<tr><td colspan="6">*Note:Insertion Loss values do not include connector losses.</td></tr></tbody></table>';
 //style="width: 691px; height: 181px; "
//  $image_style = array ("width: 691px; height: 181px;");
//  $replace = array ("width: 509px; height: 134px;");
 $products_description2 = preg_replace ($patterns, $replacements, $products_description1);
  $products_description3 = preg_replace ($image_01, $image_01_1, $products_description2);

  $products_description4 = preg_replace ($image_02, $image_02_1, $products_description3);
  $products_description5 = preg_replace ($image_03, $image_03_1, $products_description4);
  $products_description6 = preg_replace ($image_04, $image_04_1, $products_description5);
  $products_description7 = preg_replace ($image_05, $image_05_1, $products_description6);
  $products_description8 = preg_replace ($image_06, $image_06_1, $products_description7);
   $products_description9 = preg_replace ($image_07, $image_07_1, $products_description8);
   $products_description10 = preg_replace ($description_01, $description_01_1, $products_description9);
   $products_description11 = preg_replace ($image_08, $image_08_1, $products_description10);
   if($cPath_array[2] == 177 || $cPath_array[2] == 178 || $cPath_array[2] == 179 || $cPath_array[2] == 180){
   $products_description = $products_description1.'<br /> '.$cwdm_factory;
   $products_description = str_replace($cdn_cwdm, $cdn_cwdm_update, $products_description);
   }else{
  $products_description = preg_replace ($image_010, $image_010_1, $products_description11);
   // $products_description = str_replace($fictory_old, $fictory_new, $products_description);

    $products_description = str_replace($description_space, $description_space_01, $products_description);
    $products_description = str_replace($description_space_2, $description_space_02, $products_description);
    $products_description = str_replace($descirption_strong_1, $descirption_strong_01, $products_description);
    $products_description = str_replace($descirption_strong_2, $descirption_strong_02, $products_description);
    $products_description = str_replace($description_strong_3, $description_strong_03, $products_description);
    $products_description = str_replace($description_strong_4, $description_strong_04, $products_description);

    $products_description = str_replace($table_replace, $table_new, $products_description);

    $products_description = str_replace($cdn_url, $cdn_url_update, $products_description);
    $products_description = str_replace($cdn_sfp, $cdn_sfp_update, $products_description);
    $products_description = str_replace($cdn_cwdm, $cdn_cwdm_update, $products_description);


   }

    //$products_description = str_replace($table_replace_1, $table_replace_01, $products_description);
// $products_description = preg_replace('/<!--(.*)-->/i', '', $product_info->fields['products_description']);
 //replace local image to cdn resources
 $targets =  array(
 		'http://cn.fs.com/images/ckfinder/images/dwdmoptical01.jpg',
 		'http://cn.fs.com/images/ckfinder/images/Dual fiber DWDM-OADM1-FS.jpg',
 		'http://cn.fs.com/images/ckfinder/images/1U-LGX-Cassette(30).jpg',
 		'http://cn.fs.com/images/ckfinder/images/pic.jpg',
 		'http://cn.fs.com/images/ckfinder/images/DWDM Multiplexer Demultiplexer-2(1).jpg',
 		'http://cn.fs.com/images/ckfinder/images/2CH DWDM  innter struction(3)(1).jpg',
 		'http://cn.fs.com/images/ckfinder/images/DWDM MUX DEMUX-1(1).jpg',
 		'http://cn.fs.com/images/ckfinder/images/DWDM MUX 1U Rack(20).jpg',

 		'http://cn.fs.com/images/ckfinder/images/FS-FMC-14DP-12.JPG',
 		'http://cn.fs.com/images/ckfinder/images/FS-FMC-14DP-15.JPG',
 		'http://cn.fs.com/images/ckfinder/images/FS-FMC-14DP-16.JPG',
 		'http://cn.fs.com/images/ckfinder/images/FS-FMC-14DP-18.JPG',

 		'http://cn.fs.com/images/ckfinder/images/IMG_2246(1).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2258(1).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2246(4).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2258(4).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2246(5).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2258(5).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2246(14).JPG',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2258(14).JPG',

 		'http://cn.fs.com/images/ckfinder/images/multi-mode double-mode(2).jpg',
 		'http://cn.fs.com/images/ckfinder/images/RS422 fiber modem 2(7).jpg',
 		'http://cn.fs.com/images/ckfinder/images/2(39).jpg',
 		'http://cn.fs.com/images/ckfinder/images/3(61).jpg',
 		'http://cn.fs.com/images/ckfinder/images/IMG_0688(9).jpg',

 		'http://cn.fs.com/images/ckfinder/images/ST3000PRO kit(1).jpg',
 		'http://cn.fs.com/images/ckfinder/images/CCTV security tester detail 1.JPG',
 		'http://cn.fs.com/images/ckfinder/images/CCTV security tester detail 2.JPG',
 		'http://cn.fs.com/images/ckfinder/images/CCTV security tester package(1).JPG',

 		'http://cn.fs.com/images/ckfinder/images/XIMG_2449(2).jpg',
 		'http://cn.fs.com/images/ckfinder/images/IMG_2445(2).jpg',
 		'http://cn.fs.com/images/ckfinder/images/xi IMG_2435(2).jpg',
 		'http://cn.fs.com/images/ckfinder/images/FBT Splitter with ABS Box 4.jpg',
 		'http://cn.fs.com/images/ckfinder/images/Gray.jpg',
 		'http://cn.fs.com/images/ckfinder/images/3(62).jpg',

 		'http://cn.fs.com/images/ckfinder/images/Leviton LC style.jpg',
 		'http://cn.fs.com/images/ckfinder/images/Leviton SC style.jpg',
 		'http://cn.fs.com/images/ckfinder/images/Leviton ST style.jpg',
 		'http://cn.fs.com/images/ckfinder/images/Leviton blank style.jpg',

 );
 $replaces = array(

 		'https://d2tc3bc5yupkik.cloudfront.net/images/dwdmoptical01.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/Dual fiber DWDM-OADM1-FS.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/1U-LGX-Cassette(30).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/pic.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/DWDM Multiplexer Demultiplexer-2(1).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/2CH DWDM  innter struction(3)(1).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/DWDM MUX DEMUX-1(1).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/DWDM MUX 1U Rack(20).jpg',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/FS-FMC-14DP-12.JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/FS-FMC-14DP-15.JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/FS-FMC-14DP-16.JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/FS-FMC-14DP-18.JPG',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2246(1).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2258(1).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2246(4).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2258(4).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2246(5).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2258(5).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2246(5).JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2258(5).JPG',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/multi-mode double-mode(2).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/RS422 fiber modem 2(7).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/2(39).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/3(61).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_0688(9).jpg',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/ST3000PRO kit(1).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/CCTV security tester detail 1.JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/CCTV security tester detail 2.JPG',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/CCTV security tester package(1).JPG',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/XIMG_2449(2).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/IMG_2445(2).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/xi IMG_2435(2).jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/FBT Splitter with ABS Box 4.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/Gray.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/3(62).jpg',

 		'https://d2tc3bc5yupkik.cloudfront.net/images/Leviton LC style.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/Leviton SC style.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/Leviton ST style.jpg',
 		'https://d2tc3bc5yupkik.cloudfront.net/images/Leviton blank style.jpg',

 );

 $products_description = str_replace($targets, $replaces, $products_description);



  $email_content = ' sales@fiberstore.com ';
  $email_content_replace = ' <a href="mailto:sales@fiberstore.com">sales@fiberstore.com</a> ';


  if($cPath_array[2] == 1140){
  /*
  $content_3_10 = array('Two other clearly visible features are the noses and guide grooves (keys) on the top side. The Female and Male need to be used together.</td>');
  $content_3_10_new = array('Two other clearly visible features are the noses and guide grooves (keys) on the top side. The Female and Male need to be used together.</td>
</tr>
</tbody>
</table>
<div class="p_con_01">
Pre-terminated MTP Technology-Polarity Type B (Cross)</div>
<div class="p_con_02" style="text-align:center;">
<img alt="" src="images/MPO-MTP-Fiber-Cables/MTP_Technology-Polarity01.jpg" /></div>
<div class="p_con_02">
The application above is for the MTP-LC harness cable connecting a QSFP+ port to (4)SFP+ ports directly.
<br />
<br />
In many existing DC environments, LC-MTP cables are used to support high density fiber trunks between switches, rack patch panels and main distribution cabling panels (MDA).</div>
<br />
<div class="p_con_02">
<b>For example:</b></div>
<div class="p_con_02" style="text-align:center;">
<img alt="" src="images/MPO-MTP-Fiber-Cables/MTP_Technology-Polarity02.jpg" /></div>
<div class="p_con_02">
The above is the Eight-Fiber MTP-LC Assembly uses a pinless connector on one end for interfacing with the QSFP port on the Cisco Nexus 6000series, The other end contains four LC uniboot connectors, which provide connectivity to the SFP+ ports on the switch. The polarity of the assembly pairs the fibers from the MTP Connector from the outside to the inside; thus, fiber 1 and fiber 12 are a duplex pair, as are fiber 2 and fiber 11, etc.</div>
<br />');
  $mpo_image_new_429 = array('Two other clearly visible features are the noses and guide grooves (keys) on the top side. The Female and Male need to be used together.</td>
</tr>
</tbody>
</table>
<div class="p_con_01">
Pre-terminated MTP Technology-Polarity Type B (Cross)</div>
<div class="p_con_02" style="text-align:center;">
<img alt="" src="images/MPO-MTP-Fiber-Cables/MTP_Technology-Polarity01.jpg" /></div>
<div class="p_con_02">
The application above is for the MTP-LC harness cable connecting a QSFP+ port to (4)SFP+ ports directly.
<br />
<br />
In many existing DC environments, LC-MTP cables are used to support high density fiber trunks between switches, rack patch panels and main distribution cabling panels (MDA).</div>
<br />
<div class="p_con_02">
<b>For example:</b></div>
<div class="p_con_02" style="text-align:center;">
<img alt="" src="images/MPO-MTP-Fiber-Cables/MPO_Technology-Polarity02.jpg" /></div>
<div class="p_con_02">
The above is the Eight-Fiber MPO-LC Assembly uses a pinless connector on one end for interfacing with the QSFP port on the Cisco Nexus 6000series, The other end contains four LC uniboot connectors, which provide connectivity to the SFP+ ports on the switch. The polarity of the assembly pairs the fibers from the MPO Connector from the outside to the inside; thus, fiber 1 and fiber 12 are a duplex pair, as are fiber 2 and fiber 11, etc.</div>
<br />');
  */
  /*
  if($_GET['products_id'] != 31090){
  $mpo_image_arr = array(25884,31051,31061,31062,31063,31064,31102,31136,31135,31133,31138,31137,31141,31144,
31143,31142,31145,31146,31140,31151,31147,31148,31149,31150,31156,31152,31153,31161,
31158,31159,31160,31157,31164,31165,31166,31162,31163,31191,31184,31167,31179,31174,
31192,31185,31168,31180,31175,31193,31186,31181,31176,31194,31187,31170,31177,
31195,31188,31183,31178);
  if(in_array($_GET['products_id'], $mpo_image_arr)){
  $products_description = str_replace($content_3_10, $mpo_image_new_429, $products_description);
  }else{
  $products_description = str_replace($content_3_10, $content_3_10_new, $products_description);
  }
  }
  */
  $products_description = str_replace($email_content, $email_content_replace, $products_description);


  }


  if($cPath_array[2] == 1135){
  $products_description = str_replace($email_content, $email_content_replace, $products_description);
  }

  $wavalength_array = array(763,220,278,304,1322,1326,1325,1323);
  if(in_array($cPath_array[2],$wavalength_array)){
  $old_wavalength = array('1310,1510');
  $new_wavalength = array('850,1310');
  $products_description = str_replace($old_wavalength, $new_wavalength, $products_description);
  }


  /*********2015-0403  tom*****************/


  if($cPath_array[2] == 1081){
  $pigtails_length_info = "The default length for branch leg is 0.5m";
  }
  $products_SKU = $product_info->fields['products_SKU'];
  $products_MFG_PART = $product_info->fields['products_MFG_PART'];
  $products_compatible_brand = $product_info->fields['products_compatible_brand'];
  $products_compatible_brand = str_replace("Compatible", "", $products_compatible_brand);
  $products_warranty = $product_info->fields['products_warranty'];

  //2014-5-12

  if($cPath_array[1] == 6){
   if($products_in_stock){
   $products_leadtime = 'Shipping today';
   }else{
  $products_leadtime = '2-3 Weeks';
   }
  }else if($cPath_array[2] == 1150){
  if($products_in_stock){
  $products_leadtime = 'Shipping today';
  }else{
  $products_leadtime = '3-4 Weeks';
  }
  }
  else{
  $products_leadtime = $product_info->fields['products_leadtime'];
  }
  if(!$products_leadtime){
  $products_leadtime = '2-5 days';
  }

  $products_short_description = strip_tags($product_info->fields['products_short_description']);
  $products_technical_paper = $product_info->fields['products_technical_paper'];
  $products_technical_paper = str_replace($cdn_url, $cdn_url_update, $products_technical_paper);

   	$table_replace_1 = array('</table>
	<table>');
 	$table_replace_01 = array('');


 	  $cwdm_specification='<table class="solu_table01" width="100%" cellpadding="8" cellspacing="1" bgcolor="#CCCCCC">
  <tbody>
    <tr>
      <td bgcolor="#666666"><font color="#FFFFFF"><b>Parameters</b></font></td>
      <td bgcolor="#666666"><font color="#FFFFFF"><b>Unit</b></font></td>
      <td colspan="5" bgcolor="#666666"><font color="#FFFFFF"><b>CWDM Module</b></font></td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Wavelength Range</td>
      <td bgcolor="#FFFFFF">nm</td>
      <td colspan="5" bgcolor="#FFFFFF">1260  ~ 1620</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Channel Center Wavelength</td>
      <td bgcolor="#FFFFFF">nm</td>
      <td colspan="5" bgcolor="#FFFFFF">1270 / 1290 /&nbsp;… / 1610 or 1271 / 1291 /&nbsp;… /1611</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Channel Spacing</td>
      <td bgcolor="#FFFFFF">nm</td>
      <td colspan="5" bgcolor="#FFFFFF">20</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Channel Passband</td>
      <td bgcolor="#FFFFFF">nm</td>
      <td colspan="5" bgcolor="#FFFFFF">λc ± 7.5</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Channel No.</td>
      <td bgcolor="#FFFFFF">λ</td>
      <td bgcolor="#FFFFFF">2</td>
      <td bgcolor="#FFFFFF">4</td>
      <td bgcolor="#FFFFFF">8</td>
      <td bgcolor="#FFFFFF">16</td>
      <td bgcolor="#FFFFFF">18</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Insertion Loss</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td bgcolor="#FFFFFF">≤0.8</td>
      <td bgcolor="#FFFFFF">≤1.5</td>
      <td bgcolor="#FFFFFF">≤2.5</td>
      <td bgcolor="#FFFFFF">≤5.2</td>
      <td bgcolor="#FFFFFF">≤6.4</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Adjacent Channel Isolation</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td colspan="5" bgcolor="#FFFFFF">≥ 30</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Non-adjacent Channel Isolation</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td colspan="5" bgcolor="#FFFFFF">≥ 40</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Wavelength thermal stability</td>
      <td bgcolor="#FFFFFF">nm/℃</td>
      <td colspan="5" bgcolor="#FFFFFF">≤ 0.003</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Insertion loss thermal stability</td>
      <td bgcolor="#FFFFFF">dB/℃</td>
      <td width="216" colspan="3" bgcolor="#FFFFFF">≤    0.005</td>
      <td bgcolor="#FFFFFF">≤    0.007</td>
      <td bgcolor="#FFFFFF">≤    0.008</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">PDL</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td bgcolor="#FFFFFF">≤    0.1</td>
      <td bgcolor="#FFFFFF">≤0.15</td>
      <td bgcolor="#FFFFFF">≤    0.15</td>
      <td bgcolor="#FFFFFF">≤    0.20</td>
      <td bgcolor="#FFFFFF">≤    0.25</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Polarization mode dispersion</td>
      <td bgcolor="#FFFFFF">ps</td>
      <td colspan="3" bgcolor="#FFFFFF">≤ 0.1</td>
      <td colspan="2" bgcolor="#FFFFFF">≤    0.15</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Directivity</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td colspan="5" bgcolor="#FFFFFF">≥ 50</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Return loss</td>
      <td bgcolor="#FFFFFF">dB</td>
      <td colspan="5" bgcolor="#FFFFFF">≥ 45</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Optical Power</td>
      <td bgcolor="#FFFFFF">mW</td>
      <td colspan="5" bgcolor="#FFFFFF">≤ 500</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Operating Temperature</td>
      <td bgcolor="#FFFFFF">℃</td>
      <td colspan="5" bgcolor="#FFFFFF">-10 ~    +70</td>
    </tr>
    <tr>
      <td bgcolor="#FFFFFF">Storage Temperature</td>
      <td bgcolor="#FFFFFF">℃</td>
      <td colspan="5" bgcolor="#FFFFFF">-40 ~    +85</td>
    </tr>
    <tr>
      <td bgcolor="#f4f4f4">Relative Humidity</td>
      <td bgcolor="#FFFFFF">%</td>
      <td colspan="5" bgcolor="#FFFFFF">5 ~    95</td>
    </tr>
    <tr height="22">
      <td height="22" bgcolor="#FFFFFF">Dimension</td>
      <td bgcolor="#FFFFFF">　</td>
      <td colspan="5" bgcolor="#FFFFFF">ABS    Box or LGX Box or 1U(2U) Rackmount</td>
    </tr>
    <tr height="54">
      <td width="586" height="54" colspan="7" bgcolor="#FFFFFF">Note: 1. Customization is available.<br>
        2. We can make low insertion loss solution as well. For more information, please feel free to contact at sales@fiberstore.com.<br>
        3. Specified without connector, and add an additional 0.2dB loss per connector.</td>
    </tr>
  </tbody>
</table>';


   if($cPath_array[2] == 177){
   if($product_info->fields['products_specifications']){
   $products_specifications = $product_info->fields['products_specifications'];
   }else{
   $products_specifications = $cwdm_specification;
   }
   }else if($cPath_array[2] ==180){
   $products_specifications = $dwdm_specification;
   }else if($cPath_array[2] ==179){
   $products_specifications = $cwdm_oadm_specifaction;
   }
   else{
   $products_specifications = preg_replace('/<!--(.*)-->|border=\"1\"/i', '', $product_info->fields['products_specifications']);
   }
  $products_specifications = preg_replace('/<[[:space:]]*\/?[[:space:]]*p(.*)>/i', '', $products_specifications);

  $products_specifications = str_replace($table_replace_1, $table_replace_01, $products_specifications);
  $products_specifications = str_replace($description_strong_3, $description_strong_03, $products_specifications);
  $products_specifications = str_replace($description_strong_4, $description_strong_04, $products_specifications);

  $products_specifications = str_replace($cdn_url, $cdn_url_update, $products_specifications);

  $mpo = array('<b>About P/N: </b>FS=Fiberstore; 12= 12 Fiber; SM= Single-mode; 2=12 standard; M=MPO; x= Length.<br />');
  $mtp = array('<b>About P/N: </b>FS=Fiberstore; 12= 12 Fiber; SM= Single-mode; 2=12 standard; M=MTP; x= Length.<br />');
  $products_specifications = str_replace($mpo, ' ', $products_specifications);
  $products_specifications = str_replace($mtp, ' ', $products_specifications);

  $old_spe = array('<span style="width:20%; display:inline-block">Furcation length (B): </span> <span style="width:79%; display:inline-block">0.8m</span></div>');
  $new_spe = array('<span style="width:20%; display:inline-block">Furcation length (B): </span> <span style="width:79%; display:inline-block">0.5m/0.3m for 1m total length</span></div>');

  if($cPath_array[2] == 1140){
   $products_specifications = str_replace($old_spe, $new_spe, $products_specifications);
   }

  if ($product_info->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == '1') {
    $products_image = PRODUCTS_IMAGE_NO_IMAGE;
  } else {
    $products_image = $product_info->fields['products_image'];
  }
 if($cPath_array[2] == 66 || $cPath_array[2] == 65){
 $spectral = $product_info->fields['products_short_description'];
 $optical_characteristics =$product_info->fields['products_specifications'];
 $technical_support =$product_info->fields['products_technical_paper'];
 }

//   $products_date_available = $product_info->fields['products_date_available'];
//   $products_date_added = $product_info->fields['products_date_added'];
//   $products_manufacturer = $manufacturers_name;
//   $products_weight = $product_info->fields['products_weight'];
//   $products_quantity = $product_info->fields['products_quantity'];


//   $products_qty_box_status = $product_info->fields['products_qty_box_status'];
//   $products_quantity_order_max = $product_info->fields['products_quantity_order_max'];

  $products_base_price = $currencies->display_price(zen_get_products_base_price((int)$_GET['products_id']),
                      zen_get_tax_rate($product_info->fields['products_tax_class_id']));

  $google_base_price = $currencies->display_goole_price(zen_get_products_base_price((int)$_GET['products_id']),
                      zen_get_tax_rate($product_info->fields['products_tax_class_id']));

//   $product_is_free = $product_info->fields['product_is_free'];

//   $products_tax_class_id = $product_info->fields['products_tax_class_id'];

//   $module_show_categories = PRODUCT_INFO_CATEGORIES;
//   $module_next_previous = PRODUCT_INFO_PREVIOUS_NEXT;


  $products_discount_type = $product_info->fields['products_discount_type'];
  $products_discount_type_from = $product_info->fields['products_discount_type_from'];

  $products_instock_show_statu = $product_info->fields['products_instock_show_statu'];


//  $in_stock = true;
//  if(zen_check_stock(intval($_GET['products_id']), 1)){
//  	$in_stock = false;
//  }
/**
 * Load product-type-specific main_template_vars
 */
  $prod_type_specific_vars_info = DIR_WS_MODULES . 'pages/' . $current_page_base . '/main_template_vars_product_type.php';
  if (file_exists($prod_type_specific_vars_info)) {
    include_once($prod_type_specific_vars_info);
  }
  $zco_notifier->notify('NOTIFY_MAIN_TEMPLATE_VARS_PRODUCT_TYPE_VARS_PRODUCT_INFO');


/**
 * Load all *.PHP files from the /includes/templates/MYTEMPLATE/PAGENAME/extra_main_template_vars
 */
  $extras_dir = $template->get_template_dir('.php', DIR_WS_TEMPLATE, $current_page_base . 'extra_main_template_vars', $current_page_base . '/' . 'extra_main_template_vars');
  if ($dir = @dir($extras_dir)) {
    while ($file = $dir->read()) {
      if (!is_dir($extras_dir . '/' . $file)) {
        if (preg_match('/\.php$/', $file) > 0) {
          $directory_array[] = '/' . $file;
        }
      }
    }
    $dir->close();
  }
?>

<div class="list_pro_attribute_tit">
  <h1 itemprop="name"><?php echo $products_name;?></h1>
</div>
<div class="box_close"><a href="javascript:" ></a></div>
<div class="product_03_01"><span class="product_03_02"> <?php echo $_GET['customers_id'];?> 商品编号:</span> <span itemprop="sku"><?php echo $products_SKU;?></span>
  <div class="ccc"></div>
</div>
<?php  if ($products_model){ ?>
<div class="product_03_01 product_mode01"><span class="product_03_02">型号:</span> <span itemprop="model"> <?php echo $products_model;?> </span>
  <?php if(transceivers_categories($cPath_array[2]) && $cPath_array[2] != 1215){ ?>
  <div class="question_text">
    <div class="question_bg" ></div>
    <div class="question_text_01 leftjt">
      <div class="arrow"></div>
      <div class="popover-content"> xx 代表兼容品牌。（如CO= Cisco, JU=Juniper, FD=Foundry, EX=Extreme, NE=Netgear等等，xx兼容品牌的缩写） </div>
    </div>
  </div>
  <?php }elseif ('33174' == $_GET['products_id'] || '33187' == $_GET['products_id'] || '33188' == $_GET['products_id']){ ?>
  <div class="question_text">
    <div class="question_bg"></div>
    <div class="question_text_01 leftjt">
      <div class="arrow"></div>
      <div class="popover-content"> A表示光纤类型，有单模和多模可供选择。 B表示不同的波长。 C表示2种光纤直径可供选择。 D表示3种光纤长度可供选择。 E表示连接头可选。请参考下面的选项和订购信息。 </div>
    </div>
  </div>
  <?php }elseif ('628' == $cPath_array[2] && '33174' != $_GET['products_id']&& '33187' != $_GET['products_id']&& '33188' != $_GET['products_id']){?>
  <div class="question_text">
    <div class="question_bg"></div>
    <div class="question_text_01 leftjt">
      <div class="arrow"></div>
      <div class="popover-content"> 根据底部试图定制您的特定光开关。</div>
    </div>
  </div>
  <?php }elseif (isset($is_products_arrow) && $is_products_arrow != null){?>
  <div class="question_text">
    <div class="question_bg" ></div>
    <div class="question_text_01 leftjt">
      <div class="arrow"></div>
      <div class="popover-content"> <?php echo $is_products_arrow;?></div>
    </div>
  </div>
  <?php }?>
  <div class="ccc"></div>
</div>
<?php } ?>
<?php
                    if($cPath_array[0] != 209){
                    if ($products_MFG_PART){?>
<div class="product_03_01"><span class="product_03_02">原厂商型号:</span> <span><?php echo $products_MFG_PART;?></span>
  <div class="ccc"></div>
</div>
<?php } }?>

<div class="product_03_01 product_03_13"> <span class="product_03_02"><strong>价格：</strong></span>
  <div itemprop="offers" itemscope itemtype="http://schema.org/Offer"> <span class="product_03_04" id="productsbaseprice">
    <?php
							  echo $currencies->display_price(get_customers_products_level_final_price(zen_get_products_base_price((int)$_GET['products_id'])),0);
					 ?>
    </span>
    <?php
					if('34747' == $_GET['products_id'] || '34760' == $_GET['products_id'] || '34778' == $_GET['products_id']){ ?>
    <div class="question_text">
      <div class="question_bg"></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content"> 仅为空机架的价格。</div>
      </div>
    </div>
    <?php	 }elseif ('17346' == $_GET['products_id']) {?>
    <div class="question_text">
      <div class="question_bg"></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content"> 只是单价，此设备应成对使用。</div>
      </div>
    </div>
    <?php	 }elseif ('34946' == $_GET['products_id'] || '34849' == $_GET['products_id'] || '34947' == $_GET['products_id']) {?>
    <?php

							if('34946' == $_GET['products_id']){
								$p_des = "价格仅为fs-p-otp800-2s架安装，不包括任何服务卡。";
							}elseif('34849' == $_GET['products_id']){
								$p_des = "价格仅为fs-p-otp800-4s架安装，不包括任何服务卡。";
							}elseif('34947' == $_GET['products_id']){
								$p_des = "价格仅为fs-p-otp800-11s架安装，不包括任何服务卡。";
							}

							?>
    <div class="question_text">
      <div class="question_bg"></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content"> <?php echo $p_des;?></div>
      </div>
    </div>
    <?php } ?>
    <?php echo $google_base_price;?>
    <?php
					//if products under cable categories
					if (573 == $cPath_array[0]){?>
    <span class="product_info_per_meter" id="products_base_price_per_meter"> ( 每米 ) </span>
    <?php }?>
    <?php
					if(($cPath_array[0] == 9 || $cPath_array[1] == 918) && $_GET['products_id'] !=14488 && $_GET['products_id'] !=14489 && $_GET['products_id'] !=35171){
					echo '
					<meta itemprop="availability" content="http://schema.org/InStock" />
					<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
					<span class="products_in_stock">现货</span>
					';
					}else{
					if($products_in_stock){
					echo '
					<meta itemprop="availability" content="http://schema.org/InStock" />
					<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />
					<span class="products_in_stock">现货</span>
					';}else{
					echo '<meta itemprop="availability" content="http://schema.org/PreOrder" />
					<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />';
					}
					}
					?>
    <?php if($product_price_result[0]['products_price_description']){
							if($cPath_array[2] != 1187){
							?>
    <div class="question_text">
      <div class="question_bg" ></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content"><?php echo $product_price_result[0]['products_price_description'];?></div>
      </div>
    </div>
    <?php
							}
							}else{ ?>
    <?php if(strpos($products_name,'Plenum')){ ?>
    <div class="question_text">
      <div class="question_bg" ></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content">库存变化频繁，请与我们联系sales@fs.com订购之前</div>
      </div>
    </div>
    <?php } ?>
    <?php } ?>
  </div>
  <div class="ccc"></div>
</div>
<?php if ($products_compatible_brand){?>
<div class="product_03_01"><span class="product_03_02">兼容品牌:</span> <a href="javascript:void(0);"><?php echo $products_compatible_brand;?></a>
  <div class="ccc"></div>
</div>
<?php }?>
<!-- <div class="product_03_01"><span class="product_03_02">运费:</span>
					<span> -->
<?php  //require($template->get_template_dir('/tpl_modules_shipping.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_shipping.php'); ?>
<!-- </span></div> -->
<!-- bof cart -->
 <!--bof Attributes Module -->
  <?php
						if ($products_instock_show_statu != 3){
						  if ($pr_attr->fields['total'] > 0) {
						?>
  <?php
						  //require($template->get_template_dir('/tpl_modules_attributes.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_attributes.php'); ?>
  <?php }?>
  <!--eof Attributes Module -->
  <?php echo zen_draw_form('cart_form', zen_href_link(FILENAME_SHOPPING_CART,'action=add_product&number_of_uploads=1'),'POST','enctype="multipart/form-data" id="cart_form"').zen_draw_hidden_field('products_id',intval($_GET['products_id']));?>
  <div class="product_03_07 list_pro_attribute">

  <?php if($status){ ?>
  <?php  if($productLengthInfo){
						$keys_number = get_product_category_key($_GET['products_id']);
						$retail = get_retail_status($_GET['products_id']);
						?>
  <div class="product_03_09 product_03_12"><span class="product_03_02 product_03_15">
    <?php if($cPath_array[1] == 899){
						echo "总长度:";
						 }else{
						echo "长度:";
						 }
						 ?>
    <!-- <div class="question_text"><a href="javascript:void(0)"><s class="question_bg"></s></a>
			<div class="question_text_01 leftjt"><div class="arrow"></div><div class="popover-content">Vivamus sagittis lacus vel augue laoreet rutrum faucibus.Vivamus sagittis lacus vel augue laoreet rutrum faucibus.</div></div></div> -->
    </span> <span class="product_03_08">
    <select id="length" name="length" onchange="custom_select(<?php echo $_GET['products_id'];?>)" class="login_country">
      <?php foreach($productLengthInfo as $key=>$v){ ?>
      <?php if($v['sign'] == 1){

											?>
      <option value="<?php echo $v['id'];?>"><?php echo $v['length'];?></option>
      <?php }else{  ?>
      <?php if($keys_number == 2 || ($keys_number == 1 && $retail==0) ){ ?>
      <option value="<?php echo $v['id'];?>"><?php echo $v['length'];?>
      <?php if($v['length_price'] <> '0.00'){ ?>
      ( <?php echo $v['price_prefix'];?><?php echo  $currencies->format($v['length_price']);?> )
      <?php } ?>
      </option>
      <?php }else{ ?>
      <option value="<?php echo $v['id'];?>"><?php echo $v['length'];?> ( <?php echo round(substr($v['length'],0,-1)/0.3048,2);?> feet ) ( <?php echo $v['price_prefix'];?><?php echo  $currencies->format($v['length_price']);?> ) </option>
      <?php } ?>
      <?php } ?>
      <?php } ?>
      <?php if($custom_status){ ?>
      <option class="custom_01" value="">定制</option>
      <?php } ?>
    </select>
    <?php if($keys_number == 2 || ($keys_number == 1 && $retail==0) ){ ?>
    <?php }else{
									if($cPath_array[0] != 573){
								?>
    <div class="question_text">
      <div class="question_bg"></div>
      <div class="question_text_01 leftjt">
        <div class="arrow"></div>
        <div class="popover-content">
          <?php if($cPath_array[1] == 899){
						echo "请注意，总长度包括breackout长度，它可以定制你所需要的任何长度。";
						 }else{
						echo "电缆长度可以根据你所需要的任何长度来定制。";
						 }?>
          <?php //echo '&nbsp;'.$pigtails_length_info;?>
        </div>
      </div>
    </div>
    <?php } ?>
    <?php } ?>
    <div class="ccc"></div>
    <p class="product_04_22"> <span id="error_text" style="display:none"></span> <span id="custom_text" style="display:none">
      <?php //对光缆调整的产品 现在调整对光缆产品不参加计算
									$custom_on = 1;
									if($status[0]['unit_price'] == '0.00'){
											$custom_on = 0;
									}
									?>
      <input type="text" id="custom_length" name="custom_length" maxlength ="4" class = "p_07  product_05_21" size=5 onblur="my_onblur(<?php echo $_GET['products_id'];?>,<?php echo $custom_on;?>)">
      <?php if($keys_number == 2){ ?>
      <!-- &nbsp;&nbsp;KM &nbsp;&nbsp;(the MOQ length ≥ 1 km) -->
      &nbsp;&nbsp;KM &nbsp;&nbsp;(每卷的长度为1 5公里之间)
      <?php }elseif($keys_number == 1 && $retail==0){ ?>
      &nbsp;&nbsp;KM &nbsp;&nbsp;(每卷的长度为1 5公里之间)
      <?php }else{ ?>
      &nbsp;&nbsp;米&nbsp;&nbsp;Or&nbsp;&nbsp;
      <?php } ?>
      <?php if($keys_number == 2){ ?>
      <?php }elseif($keys_number == 1 && $retail==0){ ?>
      <?php }else{ ?>
      <input type="text" id="custom_length_feet" name="custom_length_feet" maxlength ="8" class= "p_07  product_05_21" size=5 onblur="my_onblur_feet(<?php echo $_GET['products_id'];?>,<?php echo $custom_on;?>)">
      &nbsp;&nbsp;Feet
      <?php if($keys_number == 4){ ?>
      &nbsp;&nbsp;(长度必须大于或等于10米)
      <?php }elseif($keys_number == 5){ ?>
      &nbsp;&nbsp;(长度必须大于或等于100米)
      <?php } ?>
      <?php } ?>
      </span> <span id="custom_price" style="display:none"></span> </p>
    </span>
    <div class="ccc"></div>
  </div>
  <?php } ?>
  <?php } ?>
  <?php
		  if ($pr_attr->fields['total'] > 0) {
		?>
  <?php  for($i=0;$i<sizeof($options_name);$i++) {
						if(option_custom($options_name[$i],1,(int)$_GET['products_id'])){


						 if($act=fiber_optic_network($options_name[$i])){  ?>
  <div style="display:none" id="fiber_<?php echo $act;?>" class="product_03_09 product_03_12 fiber_optic_network">
    <?php }else{  ?>
    <div class="product_03_09 product_03_12">
      <?php	}
									?>
      <div style="display:none">
        <?php $options_name[$i].'<br>'?>
      </div>
      <span class="product_03_02 product_03_15"><?php echo $options_name[$i];?>: </span><span class="product_03_08"> <?php echo $options_menu[$i];?>
      <?php if ('34107' == $_GET['products_id']){?>
      <div class="question_text">
        <div class="question_bg" ></div>
        <div class="question_text_01 leftjt">
          <div class="arrow"></div>
          <div class="popover-content"> 标准FC的连接器。如果你想要其他连接器，请更改模型，如st800k，st800k-u等等。</div>
        </div>
      </div>
      <?php }?>
      <?php if ('28977' == $_GET['products_id'] && "<label class=\"attribsSelect\" for=\"attrib-185\">Package</label>" == $options_name[$i]){?>
      <div class="question_text">
        <div class="question_bg" ></div>
        <div class="question_text_01 leftjt">
          <div class="arrow"></div>
          <div class="popover-content"> 卡类型必须装入4U机架。</div>
        </div>
      </div>
      <?php }?>
      <?php if ('32623' == $_GET['products_id'] && "<label class=\"attribsSelect\" for=\"attrib-240\">Data Port</label>" == $options_name[$i]){?>
      <div class="question_text">
        <div class="question_bg" ></div>
        <div class="question_text_01 leftjt">
          <div class="arrow"></div>
          <div class="popover-content"> 一旦添加RS232或RS422端口的数据，该设备将作为1ur机架式定制。</div>
        </div>
      </div>
      <?php }?>
      <?php if ('32827' == $_GET['products_id'] && "<label class=\"attribsSelect\" for=\"attrib-240\">Data Port</label>" == $options_name[$i]){?>
      <div class="question_text">
        <div class="question_bg" ></div>
        <div class="question_text_01 leftjt">
          <div class="arrow"></div>
          <div class="popover-content"> 一旦添加RS232或RS422端口的数据，设备的尺寸是210 x 180 x 30mm。</div>
        </div>
      </div>
      <?php }?>
      <?php if($options_comment[$i]){ ?>
      <?php if($cPath_array[0] == 1){?>
      <div class="track_orders_wenhao">
        <?php }else{ ?>
        <div class="question_text">
          <?php } ?>
          <div class="question_bg"></div>
          <div class="question_text_01 leftjt">
            <div class="arrow"></div>
            <div class="popover-content"><?php echo $options_comment[$i];?></div>
          </div>
        </div>
        <?php } ?>
        </span>
        <div class="ccc"></div>
      </div>
      <?php  }
						}

                        }?>
      <!--  Custom attribute -->
      <?php  $category_pic = category_custom($_GET['products_id']);

?>
      <?php //if(isset($custom_info[0]['is_custom']) && $custom_info[0]['is_custom']==1){ ?>
      <?php if(option_custom_status($_GET['products_id'])){ ?>
      <script type="text/javascript">
$(function(){
$("#customAttrContents input[type='text']").attr("class",'big_input');
$("#customAttrContents select").attr("disabled","disabled");
$("#customAttrContents input").attr("disabled","disabled");
$("#customAttr").click(function(){
	if($(this).is(":checked")){
		$("#customAttrContents").show();
		$("#customAttrContents select").attr("disabled",false);
		$("#customAttrContents input").attr("disabled",false);
	}else{
		$("#customAttrContents").hide();
		$("#customAttrContents select").attr("disabled","disabled");
		$("#customAttrContents input").attr("disabled","disabled");
	}
})
});

</script>
      <div class="Custom_none">
        <label>
          <input type="checkbox" id="customAttr" class="aaa"/>
          <?php if($cPath_array[1] == 1147){ ?>
          连接器定制
          <?php }else{ ?>
          产品定制
          <?php } ?>
        </label>
        <?php if(($category_pic['custom_description'])){?>
        <div class="question_text">
          <div class="question_bg"></div>
          <div class="question_text_01 leftjt">
            <div class="arrow"></div>
            <div class="popover-content"><?php echo $category_pic['custom_description'];?></div>
          </div>
        </div>
        <?php } ?>
        <div class="ccc"></div>
      </div>
      <div id="customAttrContents" style="display:none;">
        <div class="attribute_details">
          <?php if($category_pic) //echo $category_pic['image_description'];?>
        </div>
        <div class="aaa">
          <?php
						  if ($pr_attr->fields['total'] > 0) {
						?>
          <?php  for($i=0;$i<sizeof($options_name);$i++) {
                        	echo '<div style="display:none">'.option_custom($options_name[$i],-1,(int)$_GET['products_id']).'</div>';
						if(option_custom($options_name[$i],-1,(int)$_GET['products_id'])){
						?>
          <?php //var_dump($options_menu);?>
          <div class="product_03_09 product_03_12"><span class="product_03_02 product_03_15"><?php echo $options_name[$i];?>: </span> <span class="product_03_08"><?php echo $options_menu[$i];?>
            <?php if($options_comment[$i]){ ?>
            <div class="question_text">
              <div class="question_bg"></div>
              <div class="question_text_01 leftjt">
                <div class="arrow"></div>
                <div class="popover-content"><?php echo $options_comment[$i];?></div>
              </div>
            </div>
            <?php } ?>
            </span> </span>
            <div class="ccc"></div>
          </div>
          <?php } ?>
          <?php	}  ?>
          <?php   } ?>
          <!-- <div class="product_03_09 product_03_12" style="line-height:16px;"><span class="product_03_02 product_03_15"></span>
                        	<span class="product_03_08">Please email us at sales@fiberstore.com for your additional customized requirements.
							</span>
                            <div class="ccc"></div>

                        </div> -->
        </div>
        <?php if(!in_array($cPath_array[1],array(609,1108,1109,1110))){?>
        <!-- <div class="bbb custom_right_01"><a href="javascript:void($('#fs_overlays_custom,#basic-modal-content_custom').show())"><img src="<?php // if($category_pic) echo $category_pic['image'];?>" /><i></i></a></div> -->
        <div class="ccc"></div>
        <?php }  ?>
      </div>
      <div style="display: none;" id="fs_overlays_custom" class="ui-overlay">
        <div style="filter: alpha(opacity=30);" class="ui-widget-overlay"></div>
      </div>
      <div id="basic-modal-content_custom" class="ui-widget ui-widget-content ui-corner-all ui-corner_pop" style="display:none;"> <img src="<?php if($category_pic) echo $category_pic['image_big'];?>">
        <div class="box_close"><a href="javascript: ;" onclick="$('#fs_overlays_custom,#basic-modal-content_custom').hide();"></a></div>
      </div>
      <?php } ?>
      <!-- Custom attribute end  -->
      <div class="product_03_09 product_03_12"> <span class="product_03_02 product_03_15">
        <?php if(isset($keys_number) && isset($retail)){ ?>
        <?php if(($keys_number == 1 && $retail==0) || $keys_number == 2){ ?>
        Roll(s):
        <?php }else { ?>
        数量:
        <?php }
					 }else{?>
        数量:
        <?php }?>
        </span>
        <div class="product_03_08 product_03_24"> <a href="javascript:void(cart_quantity_change(0));" class="cart_qty_reduce cart_reduce"></a>
          <input type="text" value="<?php if($products_is_min_order_qty){echo $products_is_min_order_qty;} else { echo (isset($_GET['cart_quantity']) && 0 < (int)$_GET['cart_quantity']) ? (int)$_GET['cart_quantity'] : '1';} ?>" class="p_07 product_03_10" name="cart_quantity" maxlength = "4" id="cart_quantity" onblur="this.style.border='';">
          <a href="javascript:void(cart_quantity_change(1));" class="cart_qty_add"></a>
          <?php $pcs = $_GET['products_id'] == '34645' ? '  pkg':'  pcs'?>
          <?php if($products_is_min_order_qty){echo '<label id="products_moq">MOQ: '.$products_is_min_order_qty.$pcs.'</label>';}?>
        </div>
        <div class="ccc"></div>
      </div>
      </div>
      <div class="product_03_21 list_pro_attribute_btn">
        <input type="submit" onClick="return add_to_carts(<?php echo get_product_category_key($_GET['products_id']);?>,<?php echo get_retail_status($_GET['products_id']);?>);" id="add_to_cart" value="加入购物车" name="Add to Cart" class="button_02 button_10">
        <?php if(!isset($_SESSION['customer_id'])){?>
        <a class="button_01 button_11" href="<?php echo 'javascript:void(showLogin());';?>">我的收藏</a>
        <?php }else{?>
        <input type="submit" onClick="add_list()" id="add_to_wishlist" value= '加入收藏夹' name="ADD TO WISHLIST" class="button_01 button_11">
        <?php } ?>
        <div class="ccc"></div>
      </div>
      </form>
      <?php }else{?>
      <div class="no_stock">缺货</div>
      <div class="ccc"></div>
      <?php }?>



    <?php echo zen_special_product_price_url_description($_GET['products_id'],$cPath_array[0]);?>
    <div class="ccc"></div>
    <?php // }?>
    <div class="ccc"></div>
  </div>
</div>
<script>
					function add_list(){
	var login_in = 24;
	var $js = "http://cn.fs.com/index.php?main_page=manage_wishlists&type=addToWishlist";
	var $login = "https://cn.fs.com/index.php?main_page=login";

	if(login_in){
		$("#cart_form").append('<input type="hidden" name="type" value="add"/>').attr("action",$js);
		return true;
	}else{
		var to_login = window.confirm('You need to login to FiberStore, click ok to login in !');
		if(to_login){
			$("#cart_form").attr("action",$login);
			return true;
		}else	return false;
	}

}
function cart_quantity_change(type){
	var qty = parseInt($("#cart_quantity").val());

	if(!isNaN(qty)){
		switch(type){
			case 0:

					if(qty >=2)
					$("#cart_quantity").val(qty-1);
					else notice('数量不能为空 !');

				break;
			case 1:
				$("#cart_quantity").val(qty+1);
				break;
		}

	}else{

		notice('Please enter a number !');

		$("#cart_quantity").val(1);

		return false;

	}

}
$('.box_close').click(function(){
	$('#fs_overlays_custom').hide();
			$('#basic-modal-content_custom').hide();
})


					</script>
