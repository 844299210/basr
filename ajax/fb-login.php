<?php
require_once __DIR__ . '/facebook/autoload.php';
require_once __DIR__ . '/fb-function.php';
session_start();
$fb = new Facebook\Facebook([
    'app_id' => '1379938605389614', // Replace {app-id} with your app id
    'app_secret' => '9481f61f190fe261ffb1a52e5bba3be1',
    'default_graph_version' => 'v2.2',
]);
$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('http://cn.fs.com:8000/fb-login-callback.php', $permissions);
//$loginUrl = $helper->getLoginUrl('http://cn.fs.com:8000/fb-index.php', $permissions);
echo '<a href="' . htmlspecialchars($loginUrl) . '">Login with Facebook!</a>';
?>