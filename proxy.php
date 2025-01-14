<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/plain');

// 记录请求参数
error_log("Request params: " . print_r($_SERVER['QUERY_STRING'], true));

$api_url = 'https://api.tiger-sms.com/stubs/handler_api.php' . '?' . $_SERVER['QUERY_STRING'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

// 记录 curl 错误信息
if(curl_errno($ch)) {
    error_log("Curl error: " . curl_error($ch));
}

curl_close($ch);

// 记录 API 响应
error_log("API response: " . $response);

echo $response;
?> 