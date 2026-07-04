<?php
$file = dirname(__DIR__,2) . "/config.json";
$config = file_get_contents($file);
$json = json_decode($config,true);

$type = $_POST["type"] ?? "";
$admin = $_POST["admin"] ?? "";
$password = $_POST["password"] ?? "";

/*
①登录
type = login 
参数:
admin = 账号
password = 密码

②更改
type = set
admin = 账号
password = 密码

*/

switch ($type) {
    case "login":
        if (empty($admin) || empty($password)) {
            echo json_encode([
                "code" => 400,
                "msg" => "缺少参数"
            ],480);
            exit;
        } else {
            $true_admin = $json["admin"] ?? "";
            $true_password = $json["password"] ?? "";
            if ($true_admin == $admin && $true_password == $password) {
                echo json_encode([
                    "code" => 200,
                    "msg" => "登录成功"
                ],480);
                exit;
            } else {
                echo json_encode([
                    "code" => 400,
                    "msg" => "账号或密码错误"
                ],480);
                exit;
            }
        }
        break;
    case "set":
        if (empty($admin) || empty($password)) {
            echo json_encode([
                "code" => 400,
                "msg" => "缺少参数"
            ],480);
            exit;
        } else {
            $json["admin"] = $admin;
            $json["password"] = $password;
            $new_json = json_encode($json,480);
            file_put_contents($file,$new_json);
            echo json_encode([
                "code" => 200,
                "msg" => "更改成功"
            ],480);
            exit;
        }
        break;
}