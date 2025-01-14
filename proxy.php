<?php
// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// CORS 设置
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// 如果是 OPTIONS 请求，直接返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 记录请求信息
$request_time = date('Y-m-d H:i:s');
$request_ip = $_SERVER['REMOTE_ADDR'];
$request_params = $_SERVER['QUERY_STRING'];
error_log("[{$request_time}] Request from {$request_ip}: {$request_params}");

try {
    // API 配置
    $api_url = 'https://api.tiger-sms.com/stubs/handler_api.php?' . $_SERVER['QUERY_STRING'];
    
    // 初始化 CURL
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10
    ]);

    // 执行请求
    $response = curl_exec($ch);
    
    // 检查是否有错误
    if (curl_errno($ch)) {
        throw new Exception("Curl error: " . curl_error($ch));
    }
    
    // 获取 HTTP 状态码
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code !== 200) {
        throw new Exception("HTTP error: " . $http_code);
    }

    curl_close($ch);
    
    // 记录响应
    error_log("[{$request_time}] API response for {$request_ip}: {$response}");
    
    echo $response;

} catch (Exception $e) {
    // 记录错误
    error_log("[{$request_time}] Error for {$request_ip}: " . $e->getMessage());
    
    // 返回错误信息
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?> 