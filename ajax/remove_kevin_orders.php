<?php
require 'includes/application_top.php';
require DIR_WS_CLASSES . 'fs/orders.php';

echo 'beginning ...' . "<br />";

$fs_orders = new fs_orders();
$fs_orders->delete_orders();

echo 'ending ...';