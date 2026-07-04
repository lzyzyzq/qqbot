<?php
$file = dirname(__DIR__,2)."/main.json";
$main = file_get_contents($file);
$main = json_decode($main,true);
$type = $_REQUEST["type"] ?? "";

    if (empty($type)) {
        $json = [
            "code" => 400,
            "msg" => "未传入数据"
        ];
        echo json_encode($json,480);
        exit;
    }
    
    switch ($type) {
        case "add":
            $appid = $_REQUEST["appid"] ?? "";
            $secret = $_REQUEST["secret"] ?? "";
            $environment = $_REQUEST["environment"] ?? "";
            $main[$appid] = [
                "secret" => $secret,
                "type" => $environment
            ];
            $json = json_encode($main,480);
            file_put_contents($file,$json);
            $json = [
                "code" => 200,
                "msg" => "添加成功"
            ];
            echo json_encode($json,480);
            break;
        case "del":
            $appid = $_REQUEST["appid"] ?? "";
            unset($main[$appid]);
            $json = json_encode($main,480);
            file_put_contents($file,$json);
            $json = [
                "code" => 200,
                "msg" => "删除成功"
            ];
            echo json_encode($json,480);
            break;
    }