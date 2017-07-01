<?php
$debug = false;
require 'includes/application_top.php';
if (!empty($_POST['product_id']) && !empty($_POST['price']) && !empty($_POST['qty']) || !empty($_GET['request_type']) == 'checked_box' || !empty($_GET['request_type']) == 'status') {
    $currency_symbol_left = $currencies->currencies[$_SESSION['currency']]['symbol_left'];
    //添加勾选的情况,新增checked状态

    switch ($_GET['request_type']) {
        case 'checked_up';
            $price = $_POST['price'];
            $number = $_POST['number'][0];
            $oProductPre = $_POST['oProductPre'];//数量
            $oProductPic = $_POST['oProductPic'];
            $discountedPrice = $_POST['discountedPrice'];
            if ($_SESSION['languages_id'] == '5') {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
                if (!empty($oProductPic) && $oProductPic != '0') {
                    $oProductPic = str_replace('.', '', $oProductPic);
                    $oProductPic = str_replace(',', '.', $oProductPic);
                }
                $discountedPrice = str_replace('.', '', $discountedPrice);
                $discountedPrice = str_replace(',', '.', $discountedPrice);
            } else {
                $discountedPrice = str_replace(',', '', $discountedPrice);
            }

            $discountedCoin = $discountedPrice + ($oProductPic * $oProductPre);
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
            $discountedCoin = number_format($discountedCoin, 2);
            if ($_SESSION['languages_id'] == '5') {
                $price_filter = str_replace('.', ',', $price_into);
                $count_comma = substr_count($price_filter, ',');
                if (!empty($discountedCoin)) {
                    $discountedCoinPrice = str_replace('.', ',', $discountedCoin);
                    $count_price = substr_count($discountedCoinPrice, ',');
                    if ($count_price > 1) {
                        $discountedCoinPriceExp = explode(',', $discountedCoinPrice);
                        $discountedCoin = $discountedCoinPriceExp[0] . '.' . $discountedCoinPriceExp[1] . ',' . $discountedCoinPriceExp[2];
                    } else {
                        $discountedCoin = $discountedCoinPrice;
                    }
                }
                if ($count_comma > 1) {
                    $price_filter_exp = explode(',', $price_filter);
                    $price_into = $price_filter_exp[0] . '.' . $price_filter_exp[1] . ',' . $price_filter_exp[2];
                } else {
                    $price_into = $price_filter;
                }
            }

            if ($_SESSION['currency'] == 'JPY' || $_SESSION['currency'] == 'MXN') {
                $price_into = substr($price_into, 0, -3);
                $discountedCoin = substr($discountedCoin,0,-3);
            }
            if ($price_into > 0) {
                $coin = $currency_symbol_left . $price_into;
            } else {
                $coin = 0;
            }

            $arr = array('0' => $coin, '1' => $discountedCoin);
            echo json_encode($arr);
            exit;
            break;
        //消除勾选的情况,消除checked状态
        case 'checked_un';
            $price = $_POST['price'];
            $number = $_POST['number'][0];
            $oProductPre = $_POST['oProductPre'];//数量
            $oProductPic = $_POST['oProductPic'];
            $discountedPrice = $_POST['discountedPrice'];
            if ($_SESSION['languages_id'] == '5') {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);
                if (!empty($oProductPic) && $oProductPic != '0') {
                    $oProductPic = str_replace('.', '', $oProductPic);
                    $oProductPic = str_replace(',', '.', $oProductPic);
                }
                $discountedPrice = str_replace('.', '', $discountedPrice);
                $discountedPrice = str_replace(',', '.', $discountedPrice);
            } else {
                $discountedPrice = str_replace(',', '', $discountedPrice);
            }

            $discountedCoin = $discountedPrice - ($oProductPic * $oProductPre);

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
            $discountedCoin = number_format($discountedCoin, 2);
            $price_into = number_format($price_into, 2);
            if ($_SESSION['languages_id'] == '5') {
                $price_filter = str_replace('.', ',', $price_into);
                $count_comma = substr_count($price_filter, ',');
                if (!empty($discountedCoin)) {
                    $discountedCoinPrice = str_replace('.', ',', $discountedCoin);
                    $count_price = substr_count($discountedCoinPrice, ',');
                    if ($count_price > 1) {
                        $discountedCoinPriceExp = explode(',', $discountedCoinPrice);
                        $discountedCoin = $discountedCoinPriceExp[0] . '.' . $discountedCoinPriceExp[1] . ',' . $discountedCoinPriceExp[2];
                    } else {
                        $discountedCoin = $discountedCoinPrice;
                    }
                }
                if ($count_comma > 1) {
                    $price_filter_exp = explode(',', $price_filter);
                    $price_into = $price_filter_exp[0] . '.' . $price_filter_exp[1] . ',' . $price_filter_exp[2];
                } else {
                    $price_into = $price_filter;
                }
            }

            if ($_SESSION['currency'] == 'JPY' || $_SESSION['currency'] == 'MXN') {
                $price_into = substr($price_into, 0, -3);
                $discountedCoin = substr($discountedCoin, 0, -3);
            }
            if ($price_into > 0) {
                $coin = $currency_symbol_left . $price_into;
            } else {
                $coin = 0;
            }

            $arr = array('0' => $coin, '1' => $discountedCoin);
            echo json_encode($arr);
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
    echo "No parameters";
}
