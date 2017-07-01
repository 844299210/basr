<tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="15">
          <tr><?php echo zen_draw_form('search', 'sale_admin_review_customer', '', 'get', '', true); ?>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="smallText" align="center">
<?php
// show reset search
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
      echo '<a href="' . zen_href_link('sale_admin_review_customer', '', 'NONSSL') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>&nbsp;&nbsp;';
    }
    echo HEADING_TITLE_SEARCH_DETAIL . ' ' . zen_draw_input_field('search') . zen_hide_session_id();
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
      $keywords = zen_db_prepare_input($_GET['search']);
      echo '<br/ >' . TEXT_INFO_SEARCH_DETAIL_FILTER . zen_output_string_protected($keywords);
    }
?>
            </td>
          </form></tr>
        </table></td>
      </tr>
      
      <?php 
        $customers_authorization_array = array(array('id' => '0', 'text' => CUSTOMERS_AUTHORIZATION_0),
                                array('id' => '1', 'text' => CUSTOMERS_AUTHORIZATION_1),
                                array('id' => '2', 'text' => CUSTOMERS_AUTHORIZATION_2),
                                array('id' => '3', 'text' => CUSTOMERS_AUTHORIZATION_3),
                                array('id' => '4', 'text' => CUSTOMERS_AUTHORIZATION_4), // banned
                                );
                     
      ?>
      <?php // var_dump($cInfo->customers_id);exit;  $_GET['cID']?>

      
      
      
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="15">
          <tr>
<?php
// Sort Listing
          switch ($_GET['list_order']) {
              case "id-asc":
              $disp_order = "ci.customers_info_date_account_created";
              break;
              case "firstname":
              $disp_order = "c.customers_firstname";
              break;
              case "firstname-desc":
              $disp_order = "c.customers_firstname DESC";
              break;
              case "group-asc":
              $disp_order = "c.customers_group_pricing";
              break;
              case "group-desc":
              $disp_order = "c.customers_group_pricing DESC";
              break;
              case "lastname":
              $disp_order = "c.customers_lastname, c.customers_firstname";
              break;
              case "lastname-desc":
              $disp_order = "c.customers_lastname DESC, c.customers_firstname";
              break;
              case "company":
              $disp_order = "a.entry_company";
              break;
              case "company-desc":
              $disp_order = "a.entry_company DESC";
              break;
              case "login-asc":
              $disp_order = "ci.customers_info_date_of_last_logon";
              break;
              case "login-desc":
              $disp_order = "ci.customers_info_date_of_last_logon DESC";
              break;
              case "approval-asc":
              $disp_order = "c.customers_authorization";
              break;
              case "approval-desc":
              $disp_order = "c.customers_authorization DESC";
              break;
              case "gv_balance-asc":
              $disp_order = "cgc.amount, c.customers_lastname, c.customers_firstname";
              break;
              case "gv_balance-desc":
              $disp_order = "cgc.amount DESC, c.customers_lastname, c.customers_firstname";
              break;
              default:
              $disp_order = "c.customers_id desc, ci.customers_info_date_account_created DESC";
          }
?>
             <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" align="center" valign="top">
                  <?php echo TABLE_HEADING_ID; ?>
                </td>
                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='lastname' or $_GET['list_order']=='lastname-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_LASTNAME . '</span>' : TABLE_HEADING_LASTNAME); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastname', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='lastname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=lastname-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='lastname-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='firstname' or $_GET['list_order']=='firstname-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_FIRSTNAME . '</span>' : TABLE_HEADING_FIRSTNAME); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstname', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='firstname' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=firstname-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='firstname-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</span>'); ?></a>
                </td>
                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='company' or $_GET['list_order']=='company-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_COMPANY . '</span>' : TABLE_HEADING_COMPANY); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=company', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='company' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=company-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='company-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='id-asc' or $_GET['list_order']=='id-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_ACCOUNT_CREATED . '</span>' : TABLE_HEADING_ACCOUNT_CREATED); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=id-asc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='id-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=id-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='id-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='login-asc' or $_GET['list_order']=='login-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_LOGIN . '</span>' : TABLE_HEADING_LOGIN); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=login-asc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='login-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=login-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='login-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td class="dataTableHeadingContent" align="left" valign="top">
                  <?php echo (($_GET['list_order']=='group-asc' or $_GET['list_order']=='group-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_PRICING_GROUP . '</span>' : TABLE_HEADING_PRICING_GROUP); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=group-asc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='group-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=group-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='group-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

<?php if (MODULE_ORDER_TOTAL_GV_STATUS == 'true') { ?>
                <td class="dataTableHeadingContent" align="left" valign="top" width="75">
                  <?php echo (($_GET['list_order']=='gv_balance-asc' or $_GET['list_order']=='gv_balance-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_GV_AMOUNT . '</span>' : TABLE_HEADING_GV_AMOUNT); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=gv_balance-asc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='gv_balance-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=gv_balance-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='gv_balance-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>
<?php } ?>

                <td class="dataTableHeadingContent" align="center" valign="top">
                  <?php echo (($_GET['list_order']=='approval-asc' or $_GET['list_order']=='approval-desc') ? '<span class="SortOrderHeader">' . TABLE_HEADING_AUTHORIZATION_APPROVAL . '</span>' : TABLE_HEADING_AUTHORIZATION_APPROVAL); ?><br>
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=approval-asc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='approval-asc' ? '<span class="SortOrderHeader">Asc</span>' : '<span class="SortOrderHeaderLink">Asc</b>'); ?></a>&nbsp;
                  <a href="<?php echo zen_href_link(basename($PHP_SELF) . '?list_order=approval-desc', '', 'NONSSL'); ?>"><?php echo ($_GET['list_order']=='approval-desc' ? '<span class="SortOrderHeader">Desc</span>' : '<span class="SortOrderHeaderLink">Desc</b>'); ?></a>
                </td>

                <td class="dataTableHeadingContent" align="right" valign="top"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $search = '';
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
      $keywords = zen_db_input(zen_db_prepare_input($_GET['search']));
      $search = "and c.customers_lastname like '%" . $keywords . "%' or c.customers_firstname like '%" . $keywords . "%' or c.customers_email_address like '%" . $keywords . "%' or c.customers_telephone rlike ':keywords:' or a.entry_company rlike ':keywords:' or a.entry_street_address rlike ':keywords:' or a.entry_city rlike ':keywords:' or a.entry_postcode rlike ':keywords:'";
      $search = $db->bindVars($search, ':keywords:', $keywords, 'regexp');
    }
    
    $admin_id=$_SESSION['admin_id'];
    
    $new_fields=', c.customers_telephone, a.entry_company, a.entry_street_address, a.entry_city, a.entry_postcode, c.customers_authorization, c.customers_referral';
    $customers_query_raw = "select c.customers_id,c.admin_id, c.customers_lastname, c.customers_firstname, c.customers_email_address, c.customers_group_pricing, a.entry_country_id, a.entry_company, ci.customers_info_date_of_last_logon, ci.customers_info_date_account_created " . $new_fields . ",
    cgc.amount
    from " . TABLE_CUSTOMERS . " c left join ".TABLE_ADMIN_TO_CUSTOMERS." atc on c.customers_id = atc.customers_id 
    left join " . TABLE_CUSTOMERS_INFO . " ci on c.customers_id= ci.customers_info_id
    left join " . TABLE_ADDRESS_BOOK . " a on c.customers_id = a.customers_id and c.customers_default_address_id = a.address_book_id " . "
    left join " . TABLE_COUPON_GV_CUSTOMER . " cgc on c.customers_id = cgc.customer_id where atc.admin_id=".$admin_id."  " .
    $search . " order by c.customers_id desc, $disp_order";
//where c.admin_id=$_SESSION['admin_id'];     $search="and "

if (($_GET['page'] == '' or $_GET['page'] == '1') and $_GET['cID'] != '') {
  $check_page = $db->Execute($customers_query_raw);
  $check_count=1;
  if ($check_page->RecordCount() > MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) {
    while (!$check_page->EOF) {
      if ($check_page->fields['customers_id'] == $_GET['cID']) {
        break;
      }
      $check_count++;
      $check_page->MoveNext();
    }
    $_GET['page'] = round((($check_count/MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER)+(fmod_round($check_count,MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER) !=0 ? .5 : 0)),0);
  } else {
    $_GET['page'] = 1;
  }
}

    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $customers_query_raw, $customers_query_numrows);
    $customers = $db->Execute($customers_query_raw);
    while (!$customers->EOF) {
      $sql = "select customers_info_date_account_created as date_account_created,
                                   customers_info_date_account_last_modified as date_account_last_modified,
                                   customers_info_date_of_last_logon as date_last_logon,
                                   customers_info_number_of_logons as number_of_logons
                            from " . TABLE_CUSTOMERS_INFO . "
                            where customers_info_id = '" . $customers->fields['customers_id'] . "'";
      $info = $db->Execute($sql);

      
      // if no record found, create one to keep database in sync
      if (!isset($info->fields) || !is_array($info->fields)) {
        $insert_sql = "insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created)
                       values ('" . (int)$customers->fields['customers_id'] . "', '0', now())";
        $db->Execute($insert_sql);
        $info = $db->Execute($sql);
      }

      if ((!isset($_GET['cID']) || (isset($_GET['cID']) && ($_GET['cID'] == $customers->fields['customers_id']))) && !isset($cInfo)) {
      	
      	
        $country = $db->Execute("select countries_name
                                 from " . TABLE_COUNTRIES . "
                                 where countries_id = '" . (int)$customers->fields['entry_country_id'] . "'");

        $email = $db->Execute("select customers_email_address
                                 from " . TABLE_CUSTOMERS . "
                                 where customers_id = '" . (int)$customers->fields['customers_id'] . "'");
        
        $reviews = $db->Execute("select count(*) as number_of_reviews
                                 from " . TABLE_REVIEWS . " where customers_id = '" . (int)$customers->fields['customers_id'] . "'");

        $customer_info = array_merge($country->fields,$email->fields, $info->fields, $reviews->fields);
		
        //if (!isset($customers->fields['customers_id']))  $customers->fields['customers_id'] = $customers_id;
        
        $cInfo_array = array_merge($customers->fields, $customer_info);
        
        $cInfo = new objectInfo($cInfo_array);
        /**********************************additional*************************************/
        $cInfo->customers_id = $customers->fields['customers_id'];
        
      }

        $group_query = $db->Execute("select group_name, group_percentage from " . TABLE_GROUP_PRICING . " where
                                     group_id = '" . $customers->fields['customers_group_pricing'] . "'");

        if ($group_query->RecordCount() < 1) {
          $group_name_entry = TEXT_NONE;
        } else {
          $group_name_entry = $group_query->fields['group_name'];
        }

//          if (isset($cInfo) && is_object($cInfo) && ($customers->fields['customers_id'] == $cInfo->customers_id)) {
//        echo '          <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link('sale_admin_review_customer', zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id . '&action=review', 'NONSSL') . '\'">' . "\n";
//      } else {
//        echo '          <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link('sale_admin_review_customer', zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $customers->fields['customers_id'] , 'NONSSL') . '\'">'  . "\n";
//      }

              if (isset($cInfo) && is_object($cInfo) && ($customers->fields['customers_id'] == $cInfo->customers_id)) {
        echo '          <tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" >'  . "\n";
      }
      
      
      
      $zc_address_book_count_list = zen_get_customers_address_book($customers->fields['customers_id']);
      $zc_address_book_count = $zc_address_book_count_list->RecordCount();
?>
                <td class="dataTableContent" align="right"><?php echo $customers->fields['customers_id'] . ($zc_address_book_count == 1 ? TEXT_INFO_ADDRESS_BOOK_COUNT . $zc_address_book_count : '<a target="_blank" href="' . zen_href_link('sale_admin_review_customer', 'action=list_addresses' . '&cID=' . $customers->fields['customers_id'] . ($_GET['page'] > 0 ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . TEXT_INFO_ADDRESS_BOOK_COUNT . $zc_address_book_count . '</a>'); ?></td>
                <td class="dataTableContent"><?php echo $customers->fields['customers_lastname']; ?></td>
                <td class="dataTableContent"><?php echo $customers->fields['customers_firstname']; ?></td>
                <td class="dataTableContent"><?php echo $customers->fields['entry_company']; ?></td>
                <td class="dataTableContent"><?php echo zen_date_short($info->fields['date_account_created']); ?></td>
                <td class="dataTableContent"><?php echo zen_date_short($customers->fields['customers_info_date_of_last_logon']); ?></td>
                <td class="dataTableContent"><?php echo $group_name_entry; ?></td>
<?php if (MODULE_ORDER_TOTAL_GV_STATUS == 'true') { ?>
                <td class="dataTableContent" align="right"><?php echo $currencies->format($customers->fields['amount']); ?></td>
<?php } ?>
                <td class="dataTableContent" align="center"><?php echo ($customers->fields['customers_authorization'] == 4 ? zen_image(DIR_WS_IMAGES . 'icon_red_off.gif', IMAGE_ICON_STATUS_OFF) : ($customers->fields['customers_authorization'] == 0 ? '<a href="' . zen_href_link('sale_admin_review_customer', 'action=status&current=' . $customers->fields['customers_authorization'] . '&cID=' . $customers->fields['customers_id'] . ($_GET['page'] > 0 ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image(DIR_WS_IMAGES . 'icon_green_on.gif', IMAGE_ICON_STATUS_ON) . '</a>' : '<a href="' . zen_href_link('sale_admin_review_customer', 'action=status&current=' . $customers->fields['customers_authorization'] . '&cID=' . $customers->fields['customers_id'] . ($_GET['page'] > 0 ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image(DIR_WS_IMAGES . 'icon_red_on.gif', IMAGE_ICON_STATUS_OFF) . '</a>')); ?></td>
                <td class="dataTableContent" align="right">
                <?php if (isset($cInfo) && is_object($cInfo) && ($customers->fields['customers_id'] == $cInfo->customers_id)) { echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . zen_href_link('sale_admin_review_customer', zen_get_all_get_params(array('cID')) . 'cID=' . $customers->fields['customers_id'] . ($_GET['page'] > 0 ? '&page=' . $_GET['page'] : ''), 'NONSSL') . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;
                
              <a href="#myModal_<?php echo $customers->fields['customers_id'];?>" id="<?php echo $cInfo->customers_id;?>" onClick="getOrdersInfo(<?php echo $customers->fields['customers_id'];?>);"
	          role="button" class="btn" data-toggle="modal" >Customer Info</a>
      
      	      <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal hide fade" id="myModal_<?php echo $customers->fields['customers_id'];?>">
	          <div class="modal-header">
              <button aria-hidden="true" data-dismiss="modal" class="close" type="button">X</button>
              <h4 id="myModalLabel" align="center">客户信息:</h4>
              </div>
              <div class="modal-body" align="left">
            <?php
	         
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">名字:&nbsp;&nbsp;&nbsp;" .$customers->fields['customers_firstname']."</label><br />";
	         
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">姓氏:&nbsp;&nbsp;&nbsp;". $customers->fields['customers_lastname']."</label><br />";
	          
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">电子邮件:&nbsp;&nbsp;&nbsp;". $customers->fields['customers_email_address']."</label><br />";
	           
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">详细地址:&nbsp;&nbsp;&nbsp;". $customers->fields['entry_street_address']."</label><br />";
	         
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">附加地址:&nbsp;&nbsp;&nbsp;". $customers->fields['entry_suburb']."</label><br />";
	         
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">邮编:&nbsp;&nbsp;&nbsp;". $customers->fields['entry_postcode']."</label><br />";
	          
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">国家:&nbsp;&nbsp;&nbsp;".zen_draw_pull_down_menu('entry_country_id', zen_get_countries(), $customers->fields['entry_country_id'])."</label><br />";
	          
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">城市:&nbsp;&nbsp;&nbsp;". $customers->fields['entry_city']."</label><br />";
	         
	         echo "<label data-valtype=\"label\" class=\"control-label valtype\">电话号码:&nbsp;&nbsp;&nbsp;" . $customers->fields['customers_telephone']."</label><br />";
	         
            
              ?>

           </div>
	       <div class="modal-footer">
              <button data-dismiss="modal" class="btn">Close</button>
            </div>
          </div>
                </td>
              </tr>
<?php
      $customers->MoveNext();
    }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_CUSTOMER, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td>
                  </tr>
<?php
    if (isset($_GET['search']) && zen_not_null($_GET['search'])) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' . zen_href_link('sale_admin_review_customer', '', 'NONSSL') . '">' . zen_image_button('button_reset.gif', IMAGE_RESET) . '</a>'; ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'confirm':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CUSTOMER . '</b>');

      $contents = array('form' => zen_draw_form('customers', 'sale_admin_review_customer', zen_get_all_get_params(array('cID', 'action', 'search')) . 'cID=' . $cInfo->customers_id . '&action=deleteconfirm', 'post', '', true));
      $contents[] = array('text' => TEXT_DELETE_INTRO . '<br><br><b>' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');
      if (isset($cInfo->number_of_reviews) && ($cInfo->number_of_reviews) > 0) $contents[] = array('text' => '<br />' . zen_draw_checkbox_field('delete_reviews', 'on', true) . ' ' . sprintf(TEXT_DELETE_REVIEWS, $cInfo->number_of_reviews));
      $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link('sale_admin_review_customer', zen_get_all_get_params(array('cID', 'action')) . 'cID=' . $cInfo->customers_id, 'NONSSL') . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($_GET['search'])) $_GET['search'] = zen_output_string_protected($_GET['search']);
      if (isset($cInfo) && is_object($cInfo)) {
        $customers_orders = $db->Execute("select o.orders_id, o.date_purchased, o.order_total, o.currency, o.currency_value,
                                          cgc.amount
                                          from " . TABLE_ORDERS . " o
                                          left join " . TABLE_COUPON_GV_CUSTOMER . " cgc on o.customers_id = cgc.customer_id
                                          where customers_id='" . $cInfo->customers_id . "' order by date_purchased desc");

        $heading[] = array('text' => '<b>' . TABLE_HEADING_ID . $cInfo->customers_id . ' ' . $cInfo->customers_firstname . ' ' . $cInfo->customers_lastname . '</b>');

        $contents[] = array('align' => 'center', 'text' => ' <br />
                                                            ' . ($customers_orders->RecordCount() != 0 ? '<a href="' . zen_href_link('sale_admin_review_order', 'cID=' . $cInfo->customers_id, 'NONSSL') . '">' . zen_image_button('button_orders.gif', IMAGE_ORDERS) . '</a>' : '') . 
                                                           '<a href="' . zen_href_link('sale_admin_send_email', '&customer=' . zen_get_customer_name_email($cInfo->customers_id).'&cID=' . $cInfo->customers_id, 'NONSSL') . '">' . zen_image_button('button_email.gif', IMAGE_EMAIL) . '</a>'
                                          
                           );
        /*
        $contents[] = array('align' => 'center', 'text' => ' <br />
                                                           <a href="' . $cInfo->customers_id . '" id="'. $cInfo->customers_id .'" onClick="'.getOrdersInfo("'.$cInfo->customers_id.'").'" role="button" class="btn" data-toggle="modal">Customer Info</a>');
                                                          
           */    
	  
        $contents[] = array('text' => '<br />' . TEXT_DATE_ACCOUNT_CREATED . ' ' . zen_date_short($cInfo->date_account_created));
        $contents[] = array('text' => '<br />' . TEXT_DATE_ACCOUNT_LAST_MODIFIED . ' ' . zen_date_short($cInfo->date_account_last_modified));
        $contents[] = array('text' => '<br />' . TEXT_INFO_DATE_LAST_LOGON . ' '  . zen_date_short($cInfo->date_last_logon));
        $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_OF_LOGONS . ' ' . $cInfo->number_of_logons);

        $customer_gv_balance = zen_user_has_gv_balance($cInfo->customers_id);
        $contents[] = array('text' => '<br />' . TEXT_INFO_GV_AMOUNT . ' ' . $currencies->format($customer_gv_balance));

        $contents[] = array('text' => '<br />' . TEXT_INFO_GV_AMOUNT . ' ' . $currencies->format($customer_gv_balance));
        
        $contents[] = array('text' => '<br />' . TEXT_INFO_CUSTOMER_EMAIL . ' ' . $cInfo->customers_email_address);
        if ($customers_orders->RecordCount() != 0) {
          $contents[] = array('text' => TEXT_INFO_LAST_ORDER . ' ' . zen_date_short($customers_orders->fields['date_purchased']) . '<br />' . TEXT_INFO_ORDERS_TOTAL . ' ' . $currencies->format($customers_orders->fields['order_total'], true, $customers_orders->fields['currency'], $customers_orders->fields['currency_value']));
        }
        $contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY . ' ' . $cInfo->countries_name);
        $contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_OF_REVIEWS . ' ' . $cInfo->number_of_reviews);
        $contents[] = array('text' => '<br />' . CUSTOMERS_REFERRAL . ' ' . $cInfo->customers_referral);
      }
      break;
  }

  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>