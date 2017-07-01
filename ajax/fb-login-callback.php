<?php
require_once __DIR__ . '/facebook/autoload.php';
require_once __DIR__ . '/fb-function.php';
session_start();

$fb = new Facebook\Facebook([
    'app_id' => '1379938605389614', // Replace {app-id} with your app id
    'app_secret' => '9481f61f190fe261ffb1a52e5bba3be1',
    'default_graph_version' => 'v2.8',
]);

$helper = $fb->getRedirectLoginHelper();
E($_GET);
//todo:获取accessToken
try {
    $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

//todo:没有获取到获取accessToken报错
if (! isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }
    exit;
}

//todo: Logged in  打印accessToken
echo '<h3>Access Token</h3>';
var_dump($accessToken->getValue());


//OAuth 2.0客户端处理程序可帮助我们管理访问令牌
$oAuth2Client = $fb->getOAuth2Client();


//从/ debug_token获取访问令牌元数据
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);

// 验证（这些将失败时抛出FacebookSDKException）
$tokenMetadata->validateAppId(1379938605389614); // Replace {app-id} with your app id

//如果您知道此访问令牌所属的用户ID，可以在此处进行验证
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
        exit;
    }

    echo '<h3>Long-lived</h3>';
    var_dump($accessToken->getValue());
}

//todo:在session中保存accessToken
$_SESSION['fb_access_token'] = (string) $accessToken;

//用户使用长期访问令牌登录。
//您可以将它们重定向到仅限会员的页面。
// header（'Location：https://example.com/members.php'）;
?>