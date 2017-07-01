<?php
$debug = false;
require 'includes/application_top.php';
if (!empty($_POST['product_id']) && !empty($_POST['price']) && !empty($_POST['qty']) || !empty($_GET['request_type']) == 'checked_box' || !empty($_GET['request_type']) == 'status') {
    $currency_symbol_left =  $currencies->currencies[$_SESSION['currency']]['symbol_left'];
    //添加勾选的情况,新增checked状态
    switch ($_GET['request_type']) {
        case 'checked_up';
            $price = $_POST['price'];
            $number = $_POST['number'][0];
            if ($_SESSION['languages_id'] == '5') {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
            }

            $price_filter = preg_replace('/[^\.0123456789]/s', '', $price);
            //特殊货币过滤
            if ($_SESSION['languages_id'] != 5) {
                if ($_SESSION['currency'] == 'DKK' || $_SESSION['currency'] == 'NOK' || $_SESSION['currency'] == 'SEK') {
                    $price_filter_exp = explode('.', $price_filter);
                    $countPrice = count($price_filter_exp);
                    switch ($countPrice) {
                        case 2;
                            $price_filter = $price_filter_exp[1];
                            break;
                        case 3;
                            $price_filter = $price_filter_exp[1] . "." . $price_filter_exp[2];
                            break;
                        case 4;
                            $price_filter = $price_filter_exp[1] . $price_filter_exp[2] . "." . $price_filter_exp[3];
                            break;
                        default;
                    }
                }
            }

            if ($_SESSION['languages_id'] == '5') {
                $number = str_replace('.', '', $number);
                $number_int = str_replace(',', '.', $number);
                $price_into = $number_int + $price_filter;
            } else {
                $price_clearing = str_replace(',', '', $price_filter);
                $number_int = str_replace(',', '', $number);
                $price_into = $number_int + $price_clearing;
            }
            $price_into = number_format($price_into, 2);

            if ($_SESSION['languages_id'] == '5') {
                $price_filter = str_replace('.', ',', $price_into);
                $count_comma = substr_count($price_filter, ',');
                //print_r($price_filter);die;
                if ($count_comma > 1) {
                    $price_filter_exp = explode(',', $price_filter);
                    $price_into = $price_filter_exp[0] . '.' . $price_filter_exp[1] . ',' . $price_filter_exp[2];
                } else {
                    $price_into = $price_filter;
                }
            }

            if ($_SESSION['currency'] == 'JPY' || $_SESSION['currency'] == 'MXN') {
                $price_into = substr($price_into, 0, -3);
            }
            if ($price_into > 0) {
                echo $currency_symbol_left.$price_into;
            } else {
                echo 0;
            }
            exit;
            break;
        //消除勾选的情况,消除checked状态
        case 'checked_un';
            $price = $_POST['price'];
            $number = $_POST['number'][0];
            if ($_SESSION['languages_id'] == '5') {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
            }
            $price_filter = preg_replace('/[^\.0123456789]/s', '', $price);
            //特殊货币过滤
            if ($_SESSION['languages_id'] != 5) {
                if ($_SESSION['currency'] == 'DKK' || $_SESSION['currency'] == 'NOK' || $_SESSION['currency'] == 'SEK') {
                    $price_filter_exp = explode('.', $price_filter);
                    $countPrice = count($price_filter_exp);
                    switch ($countPrice) {
                        case 2;
                            $price_filter = $price_filter_exp[1];
                            break;
                        case 3;
                            $price_filter = $price_filter_exp[1] . "." . $price_filter_exp[2];
                            break;
                        case 4;
                            $price_filter = $price_filter_exp[1] . $price_filter_exp[2] . "." . $price_filter_exp[3];
                            break;

                        default;
                    }
                }
            }

            if ($_SESSION['languages_id'] == '5') {
                $number = str_replace('.', '', $number);
                $number_int = str_replace(',', '.', $number);
                $price_into = $number_int - $price_filter;
            } else {
                $price_clearing = str_replace(',', '', $price_filter);
                $number_int = str_replace(',', '', $number);
                $price_into = $number_int - $price_clearing;
            }
            $price_into = number_format($price_into, 2);
            if ($_SESSION['languages_id'] == '5') {
                $price_filter = str_replace('.', ',', $price_into);
                $count_comma = substr_count($price_filter, ',');
                //print_r($price_filter);die;
                if ($count_comma > 1) {
                    $price_filter_exp = explode(',', $price_filter);
                    $price_into = $price_filter_exp[0] . '.' . $price_filter_exp[1] . ',' . $price_filter_exp[2];
                } else {
                    $price_into = $price_filter;
                }
            }

            if ($_SESSION['currency'] == 'JPY' || $_SESSION['currency'] == 'MXN') {
                $price_into = substr($price_into, 0, -3);
            }
            if ($price_into > 0) {
                echo $currency_symbol_left.$price_into;
            } else {
                echo 0;
            }
            exit;
            break;
        case 'status';
            if ($_POST['status'] == 1) {
                foreach ($_SESSION['cart']->contents as $k => $v) {
                    $_SESSION['cart']->contents[$k]['checked'] = 1;
                }
            } else {
                foreach ($_SESSION['cart']->contents as $k => $v) {
                    unset($_SESSION['cart']->contents[$k]['checked']);
                }
            }
            echo 1;
            break;
        default;
    }
} else {
    echo "No parameterss";
}
