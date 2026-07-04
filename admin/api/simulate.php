<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);

// 将 notice/warning 静默
set_error_handler(function () { return true; });

// 致命错误兜底
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level()) ob_end_clean();
        echo json_encode(['code' => 200, 'replies' => [], 'msg' => '插件致命错误'], JSON_UNESCAPED_UNICODE);
        exit;
    }
});

if (!isset($_COOKIE['admin_token'])) {
    echo json_encode(['code' => 401, 'msg' => '未登录'], JSON_UNESCAPED_UNICODE);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['code' => 400, 'msg' => '无效的请求数据'], JSON_UNESCAPED_UNICODE);
    exit;
}

$content = trim($input['content'] ?? '');
if (empty($content)) {
    echo json_encode(['code' => 400, 'msg' => '请输入指令'], JSON_UNESCAPED_UNICODE);
    exit;
}

$rootDir = dirname(__DIR__, 2);
$mainFile = $rootDir . '/main.json';
if (!file_exists($mainFile)) {
    echo json_encode(['code' => 200, 'replies' => [], 'msg' => '配置文件不存在'], JSON_UNESCAPED_UNICODE);
    exit;
}

$config = json_decode(file_get_contents($mainFile), true);
if (empty($config)) {
    echo json_encode(['code' => 200, 'replies' => [], 'msg' => '无机器人配置'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ====================== 目录隔离：防止任何文件写入污染项目目录 ======================
$originalCwd = getcwd();                    // 保存当前工作目录（项目根目录）
$tmpDir = sys_get_temp_dir() . '/simulate_' . uniqid();
mkdir($tmpDir, 0777, true);                // 创建临时目录
chdir($tmpDir);                            // 切换工作目录到临时目录

// 脚本结束后自动清理临时目录并恢复原目录
register_shutdown_function(function () use ($originalCwd, $tmpDir) {
    chdir($originalCwd);                   // 恢复目录
    // 递归删除临时目录
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($tmpDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($tmpDir);
});
// ====================== 目录隔离结束 ======================

// 管理员身份模拟，确保可测试所有管理员指令
define('用户', 'EF0E86B9E18341650DFDDFDD56815223');
define('来源', 'EF0E86B9E18341650DFDDFDD56815223');
define('消息', $content);
define('消息来源', '私聊');
define('消息ID', 'simulate_msg_' . time());
define('事件ID', '');

// ====================== 随机数修复：确保每次请求结果不同 ======================
// 覆盖 Cookie 中可能被插件用作种子的值，改为本次请求的唯一随机值
$_COOKIE['PHPSESSID'] = bin2hex(random_bytes(16));   // 或使用 uniqid(mt_rand(), true)
// 强制重新播种 PHP 内置随机数生成器，防止默认种子固定
mt_srand((int) (microtime(true) * 1000000));
// 同时播种旧版 rand，以防某些插件使用
srand((int) (microtime(true) * 1000000));
// ====================================================================

$captured_replies = [];

// ========== 消息发送函数 ==========
function 文字($msg = '') {
    global $captured_replies;
    if (!empty($msg)) $captured_replies[] = ['type' => 'text', 'target' => 来源, 'content' => $msg];
    return true;
}
function MD($md = '', $keyboard = null) {
    global $captured_replies;
    if (!empty($md)) $captured_replies[] = ['type' => 'md', 'target' => 来源, 'content' => $md];
    return true;
}
function 图片($image = '', $content = null) {
    global $captured_replies;
    if (!empty($image)) $captured_replies[] = ['type' => 'image', 'target' => 来源, 'content' => $image];
    if ($content !== null) 文字($content);
    return true;
}
function 视频($video = '') {
    global $captured_replies;
    if (!empty($video)) $captured_replies[] = ['type' => 'video', 'target' => 来源, 'content' => $video];
    return true;
}
function 语音($yy = '') {
    global $captured_replies;
    if (!empty($yy)) $captured_replies[] = ['type' => 'audio', 'target' => 来源, 'content' => $yy];
    return true;
}
function 文卡(...$items) {
    $text = '';
    foreach ($items as $item) {
        if (is_array($item)) {
            $text .= ($item['text'] ?? '') . (isset($item['url']) ? " [链接]{$item['url']}" : '') . "\n";
        }
    }
    return 文字(trim($text));
}
function 大图($t, $st, $url) {
    图片($url, "{$t}\n{$st}");
    return true;
}
function 跳转卡($t, $d, $img, $url) {
    图片($img, "{$t}\n{$d}\n跳转：{$url}");
    return true;
}
function 按钮($key = '') { return 文字("[按钮] ID:{$key}"); }
function 流式(...$msgs) { foreach ($msgs as $msg) 文字($msg); return true; }
function 撤回($id = '') { return true; }
function 文件($data = '', $filename = '') {
    global $captured_replies;
    $captured_replies[] = ['type' => 'text', 'target' => 来源, 'content' => "[文件] {$filename}"];
    return true;
}

// ========== 辅助函数（均已禁用实际文件操作） ==========
function 读($p, $k, $d = null) { return $d; }
function 写($p, $k, $v) { return true; }
function wlog($msg) { return true; }
function 域名大写($d) { return implode('.', array_map('ucfirst', explode('.', strtolower($d)))); }
function 二维码($c) { return 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='.urlencode($c); }
function 头像($uid) {
    return 'https://q.qlogo.cn/qqapp/' . appid . '/' . $uid . '/640';
}
function BOT信息() { return ['name' => '模拟', 'appid' => appid]; }
function markdown转html($md) { return $md; }
function HTML转图($h, $w, $h2) { return ''; }
function 邮箱(...$args) { return false; }

if (!function_exists('curl')) {
    function curl($url, $method = 'GET', $headers = [], $data = null, $timeout = 8) {
        if (!function_exists('curl_init')) return null;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_HTTPHEADER => (array)$headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        if (strtoupper($method) !== 'GET' && $data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }
}

// ========== 插件冲突检测与加载 ==========
$pluginDir = $rootDir . '/plugin';
$existingFuncs = array_map('strtolower', get_defined_functions()['user']);
$pluginFiles = [];

foreach ($config as $appid => $bot) {
    foreach ($bot['plugin'] ?? [] as $name => $on) {
        if ($on) {
            $file = $pluginDir . '/' . $name . '.php';
            if (is_file($file)) {
                $real = realpath($file);
                if ($real && !isset($pluginFiles[$real])) {
                    $pluginFiles[$real] = $name;
                }
            }
        }
    }
}

foreach ($pluginFiles as $real => $name) {
    $code = @file_get_contents($real);
    if (!$code) continue;
    $tokens = @token_get_all($code);
    if (!is_array($tokens)) continue;
    $funcNames = [];
    $count = count($tokens);
    for ($i = 0; $i < $count; $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_FUNCTION) {
            for ($j = $i+1; $j < $count; $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) continue;
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $funcNames[] = $tokens[$j][1];
                    break;
                }
                break;
            }
        }
    }
    $conflict = false;
    foreach ($funcNames as $fn) {
        if (in_array(strtolower($fn), $existingFuncs)) { $conflict = true; break; }
    }
    if ($conflict) continue;

    $botDefined = false;
    foreach ($config as $a => $b) {
        if (isset($b['plugin'][$name]) && $b['plugin'][$name]) {
            define('appid', $a);
            define('secret', $b['secret'] ?? '');
            define('type', $b['type'] ?? '');
            $botDefined = true;
            break;
        }
    }
    if (!$botDefined) continue;

    set_time_limit(30);
    ob_start();
    try {
        @include_once $real;
        $existingFuncs = array_merge($existingFuncs, array_map('strtolower', $funcNames));
    } catch (Throwable $e) {}
    ob_end_clean();
}

// 回到原始目录（保险，实际 shutdown 会处理）
chdir($originalCwd);
while (ob_get_level()) ob_end_clean();

echo json_encode([
    'code' => 200,
    'replies' => $captured_replies,
    'msg' => '完成'
], JSON_UNESCAPED_UNICODE);