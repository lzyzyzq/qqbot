<?php
header('Content-Type: application/json');

// 校验登录态
if (!isset($_COOKIE['admin_token'])) {
    echo json_encode(['code' => 401, 'msg' => '未登录'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['code' => 400, 'msg' => '无效的请求数据'], JSON_UNESCAPED_UNICODE);
    exit;
}

$appid = $input['appid'] ?? '';
$type = $input['type'] ?? '';   // c2c 或 group
$target_id = $input['target_id'] ?? '';
$content = trim($input['content'] ?? '');

if (empty($appid) || empty($type) || empty($target_id) || empty($content)) {
    echo json_encode(['code' => 400, 'msg' => '缺少必要参数'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!in_array($type, ['c2c', 'group'])) {
    echo json_encode(['code' => 400, 'msg' => '消息类型无效，仅支持 c2c 或 group'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 读取机器人配置
$mainFile = dirname(__DIR__, 2) . '/main.json';
if (!file_exists($mainFile)) {
    echo json_encode(['code' => 500, 'msg' => '机器人配置文件不存在'], JSON_UNESCAPED_UNICODE);
    exit;
}
$config = json_decode(file_get_contents($mainFile), true);
if (!isset($config[$appid])) {
    echo json_encode(['code' => 404, 'msg' => '未找到该机器人配置'], JSON_UNESCAPED_UNICODE);
    exit;
}

$secret = $config[$appid]['secret'];
$env = $config[$appid]['type'] ?? '正式';  // 正式 / 沙箱
$baseUrl = ($env === '沙箱') ? 'https://sandbox.api.sgroup.qq.com' : 'https://api.sgroup.qq.com';

// 获取 access_token
function getAccessToken($appid, $secret) {
    $url = 'https://bots.qq.com/app/getAppAccessToken';
    $postData = json_encode(['appId' => (string)$appid, 'clientSecret' => $secret]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        return null;
    }
    $data = json_decode($response, true);
    return $data['access_token'] ?? null;
}

$accessToken = getAccessToken($appid, $secret);
if (!$accessToken) {
    echo json_encode(['code' => 500, 'msg' => '获取 access_token 失败，请检查 AppID/Secret'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 构造发送消息的 URL
if ($type === 'c2c') {
    $sendUrl = $baseUrl . "/v2/users/{$target_id}/messages";
} else {
    $sendUrl = $baseUrl . "/v2/groups/{$target_id}/messages";
}

$msgBody = json_encode([
    'content' => $content,
    'msg_type' => 0
]);

$ch = curl_init($sendUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $msgBody);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: QQBot ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $respData = json_decode($response, true);
    echo json_encode([
        'code' => 200,
        'msg' => '发送成功',
        'msg_id' => $respData['id'] ?? ''
    ], JSON_UNESCAPED_UNICODE);
} else {
    $errorMsg = "HTTP {$httpCode}";
    $respArr = json_decode($response, true);
    if ($respArr && isset($respArr['message'])) {
        $errorMsg = $respArr['message'];
    }
    echo json_encode([
        'code' => $httpCode,
        'msg' => "发送失败：{$errorMsg}"
    ], JSON_UNESCAPED_UNICODE);
}