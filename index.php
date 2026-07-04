<?php
require("function.php");

$appid = $_SERVER["HTTP_X_BOT_APPID"] ?? "";
    if (empty($appid)) {
        header("Location: admin/");
        exit;
    } else {
        $main = file_get_contents("main.json");
        $main_json = json_decode($main,true);
        $secret = $main_json[$appid]["secret"];
        $type = $main_json[$appid]["type"];
        $plugin_list = $main_json[$appid]["plugin"];
        define("appid",$appid);
        define("secret",$secret);
        define("type",$type);
        define("plugin",$plugin_list);
    }

$raw = file_get_contents("php://input");

  if(empty($raw)) {
      header("Location: admin/");
      exit;
  }

$data = json_decode($raw,true);
$op = $data["op"];

  if($op == 13) {
    sign($data,secret);
    exit;
  }
  
  if($op == 0) {
    $event_id = $data["id"];
    $event = 读("事件判断/".appid."/".date("Y-m-d"),$event_id,false);
     if($event) {
       wlog("重复数据");
       die("重复数据");
     }
    写("事件判断/".appid."/".date("Y-m-d"),$event_id,true);
    wlog($raw);
    Main($raw);
  }


function Main($data){
$raw=json_decode($data,true);
$事件=$raw["t"];
define("数据",$data);
  if($事件=="GROUP_AT_MESSAGE_CREATE"){
   define("消息来源","群聊");
   define("消息ID",$raw["d"]["id"]);
   define("消息",trim($raw["d"]["content"],"/ "));
   define("来源",$raw["d"]["group_id"]);
   define("用户",$raw["d"]["author"]["id"]);
  }elseif($事件=="C2C_MESSAGE_CREATE"){
   define("消息来源","私聊");
   define("消息ID",$raw["d"]["id"]);
   define("消息",trim($raw["d"]["content"],"/ "));
   define("来源",$raw["d"]["author"]["id"]);
   define("用户",$raw["d"]["author"]["id"]);
  }elseif($事件=="GROUP_ADD_ROBOT"){
   define("消息来源","加群");
   define("事件ID",$raw["id"]);
   define("消息","[加群]");
   define("来源",$raw["d"]["group_openid"]);
   define("用户",$raw["d"]["op_member_openid"]);
  }elseif($事件=="GROUP_DEL_ROBOT"){
   define("消息来源","退群");
   define("事件ID",$raw["id"]);
   define("消息","[退群]");
   define("来源",$raw["d"]["group_openid"]);
   define("用户",$raw["d"]["op_member_openid"]);
  }elseif($事件=="MESSAGE_CREATE"){
   define("消息来源","文字子频道");
   define("消息ID",$raw["d"]["id"]);
   define("消息",$raw["d"]["content"]);
   define("来源",$raw["d"]["channel_id"]);
   define("用户",$raw["d"]["author"]["id"]);
   define("昵称",$raw["d"]["author"]["username"]);
   define("头像",$raw["d"]["author"]["avatar"]);
  }
  require("bot.php");
  load();
  exit;
}

function load(){
    $All = glob(__DIR__."/plugin/*.php");
    foreach($All as $name) {
        $plugin_name = basename($name);
        $plugin_name = basename($plugin_name,".php");
        if (plugin[$plugin_name]) {
            try {
                require_once($name);
            } catch (Throwable $e) {
                wlog("插件加载失败: ".$name." 错误: ".$e->getMessage()." 行数: ".$e->getLine());
                continue;
            }
        } else {
            continue;
        }
    }
}