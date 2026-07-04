<?php
function BOT凭证(){
       $time=读("function/".appid,"time",0);
       if (time() < $time) {
         return 读("function/".appid,"Access",0);
       } else {
         $url="https://bots.qq.com/app/getAppAccessToken";
         $appid=appid;
         $secret=secret;
         $json=json_encode([
         "appId"=>"{$appid}",
         "clientSecret"=>$secret
         ]);
         $header=['Content-Type: application/json'];
         $fw=curl($url,"POST",$header,$json);
         写(1,1,$json);
         $fw=json_decode($fw,true);
         $Access=$fw["access_token"];
         $time=$fw["expires_in"];
         写("function/".appid,"time",time()+$time);
         写("function/".appid,"Access",$Access);
         return $Access;
      }
}

function BOTAPI($Address,$me,$json){
    $urls=[
    "正式"=>"https://api.sgroup.qq.com",
    "沙箱"=>"https://sandbox.api.sgroup.qq.com"
    ];
    $url = $urls[type].$Address;
    $header = ["Authorization: QQBot ".BOT凭证(), 'Content-Type: application/json'];
    $curl=curl($url,$me,$header,$json);
    return $curl;
}

function 文字($content) {
   switch (消息来源) {
     case "群聊":
        $json = json_encode([
        "content" => "\n{$content}",
        "msg_type" => 0,
        "msg_id" => 消息ID,
        "msg_seq" => rand(1,99999)
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
     case "私聊":
        $json = json_encode([
        "content" => "{$content}",
        "msg_type" => 0,
        "msg_id" => 消息ID,
        "msg_seq" => rand(1,99999)
         ]);
         return BOTAPI("/v2/users/".来源."/messages","POST",$json);
         break;
     case "加群":
     case "退群":
     case "互动":          // 新增互动事件支持
        $json = json_encode([
        "content" => "{$content}",
        "msg_type" => 0,
        "event_id" => 事件ID,
        "msg_seq" => rand(1,99999)
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
     case "文字子频道":
         $json = json_encode([
         "content" => $content,
         "msg_id" => 消息ID
         ]);
         return BOTAPI("/channels/".来源."/messages","POST",$json);
         break;
   }
}

// 富媒体：支持图片(1)、视频(2)、语音(3)、文件(4)，增加 $name 参数
function 富媒体($type,$image,$name = null) {
    $types = ["图片" => 1, "视频" => 2, "语音" => 3, "文件" => 4];
    $t = $types[$type] ?? 1;
    if (preg_match('/^http(s)?:\/\//i', $image)) {
        $jsonData = [
            "file_type" => $t,
            "url" => $image,
            "srv_send_msg" => false
        ];
        if ($name !== null) $jsonData["file_name"] = $name;
    } else {
        $jsonData = [
            "file_type" => $t,
            "file_data" => base64_encode($image),
            "srv_send_msg" => false
        ];
        if ($name !== null) $jsonData["file_name"] = $name;
    }
    $json = json_encode($jsonData);
        switch (消息来源) {
           case "加群":
           case "退群":
           case "互动":
           case "群聊":
               return json_decode(BOTAPI("/v2/groups/".来源."/files", "POST",$json),true);
               break;
           case "私聊":
               return json_decode(BOTAPI("/v2/users/".来源."/files", "POST",$json),true);
               break;
        }
}

function 图片($image,$content=null) {
   switch (消息来源) {
     case "群聊":
        $file_info =富媒体("图片",$image);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "content" => $content !== null ? "\n{$content}" : "",
        "msg_type" => 7,
        "msg_id" => 消息ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
        break;
     case "私聊":
        $file_info =富媒体("图片",$image);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "content" => "{$content}",
        "msg_type" => 7,
        "msg_id" => 消息ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/users/".来源."/messages","POST",$json);
        break;
     case "加群":
     case "退群":
     case "互动":
        $file_info =富媒体("图片",$image);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "content" => "{$content}",
        "msg_type" => 7,
        "event_id" => 事件ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
        break;
     case "文字子频道":
         $json = json_encode([
             "content" => $content,
             "file_image" => $image,
             "msg_id" => 消息ID
         ]);
         return BOTAPI("/channels/".来源."/messages","POST",$json);
         break;
   }
}

function silk($link){
    $link = str_replace("&","%26",$link);
    $url = "https://oiapi.net/API/Mp32Silk?url=".$link;
    $r = json_decode(curl($url,"GET",[],''), true);
    return $r["message"] ?? '';
}

function 本地语音($yy) {
   switch (消息来源) {
     case "群聊":
        $file_info = 富媒体("语音",$yy);
        if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "msg_id" => 消息ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
     case "私聊":
       $file_info = 富媒体("语音",$yy);
         if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "msg_id" => 消息ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/users/".来源."/messages","POST",$json);
         break;
     case "加群":
     case "退群":
     case "互动":
      $file_info = 富媒体("语音",$yy);
          if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "event_id" => 事件ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
   }
}

function 语音($yy) {
   switch (消息来源) {
     case "群聊":
        $silk = silk($yy);
        $file_info = 富媒体("语音",$silk);
        if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "msg_id" => 消息ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
     case "私聊":
        $silk = silk($yy);
        $file_info = 富媒体("语音",$silk);
        if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "msg_id" => 消息ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/users/".来源."/messages","POST",$json);
         break;
     case "加群":
     case "退群":
     case "互动":
        $silk = silk($yy);
        $file_info = 富媒体("语音",$silk);
        if (isset($file_info['message'])) {
         return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
          "msg_type" => 7,
          "event_id" => 事件ID,
          "msg_seq" => mt_rand(1, 9999),
          "media" => ["file_info" => $file]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
   }
}

// 新增：发送文件（支持 URL 或 base64 数据，可指定文件名）
function 文件($data, $filename) {
   $file_info = 富媒体("文件", $data, $filename);
   if (isset($file_info['message'])) {
       return 文字($file_info['message']);
   }
   $file = $file_info['file_info'];
   $json = [
        "msg_type" => 7,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
   ];
   switch (消息来源) {
     case "群聊":
        $json["msg_id"] = 消息ID;
        return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
        break;
     case "私聊":
        $json["msg_id"] = 消息ID;
        return BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
        break;
     case "加群":
     case "退群":
     case "互动":
        $json["event_id"] = 事件ID;
        return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
        break;
   }
}

function 视频($video) {
   switch (消息来源) {
     case "群聊":
        $file_info =富媒体("视频",$video);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "msg_type" => 7,
        "msg_id" => 消息ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
        break;
     case "私聊":
        $file_info =富媒体("视频",$video);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "msg_type" => 7,
        "msg_id" => 消息ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/users/".来源."/messages","POST",$json);
        break;
     case "加群":
     case "退群":
     case "互动":
        $file_info =富媒体("视频",$video);
        if (isset($file_info['message'])) {
          return 文字($file_info['message']);
        }
        $file = $file_info['file_info'];
        $json = json_encode([
        "msg_type" => 7,
        "event_id" => 事件ID,
        "msg_seq" => mt_rand(1, 9999),
        "media" => ["file_info" => $file]
        ]);
        return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
        break;
   }
}

function 按钮($key) {
   switch (消息来源) {
     case "群聊":
         $json = json_encode([
         "msg_type" => 2,
         "msg_id" => 消息ID,
         "msg_seq" => mt_rand(1, 9999),
         "keyboard" => [
           "id" => $key
           ]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
     case "私聊":
        $json = json_encode([
         "msg_type" => 2,
         "msg_id" => 消息ID,
         "msg_seq" => mt_rand(1, 9999),
         "keyboard" => [
           "id" => $key
           ]
         ]);
         return BOTAPI("/v2/users/".来源."/messages","POST",$json);
         break;
     case "加群":
     case "退群":
     case "互动":
        $json = json_encode([
         "msg_type" => 2,
         "event_id" => 事件ID,
         "msg_seq" => mt_rand(1, 9999),
         "keyboard" => [
           "id" => $key
           ]
         ]);
         return BOTAPI("/v2/groups/".来源."/messages","POST",$json);
         break;
   }
}

function 头像($id){
   return "https://q.qlogo.cn/qqapp/".appid."/{$id}/640";
}

function BOT信息(){
  return BOTAPI("/users/@me","GET",0);
}

function 文卡(...$items) {
    $list_items = [];
    foreach ($items as $item) {
        if (isset($item['url'])) {
            $list_items[] = [
                "obj_kv" => [
                    ["key" => "desc", "value" => $item['text']],
                    ["key" => "link", "value" => $item['url']]
                ]
            ];
        } else {
            $list_items[] = [
                "obj_kv" => [
                    ["key" => "desc", "value" => $item['text']]
                ]
            ];
        }
    }
    $json = [
        "msg_type" => 3,
        "msg_seq" => mt_rand(1, 9999),
        "ark" => [
            "template_id" => 23,
            "kv" => [
                ["key" => "#DESC#", "value" => "一直❤爱你"],
                ["key" => "#PROMPT#", "value" => "一直❤爱你"],
                ["key" => "#LIST#", "obj" => $list_items]
            ]
        ]
    ];
    switch (消息来源) {
         case "群聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
         case "私聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
         break;
         case "加群":
         case "退群":
         case "互动":
           $json["event_id"] = 事件ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
    }
}

function 大图($title,$xtitle,$iurl){
    $json = [
        "msg_type" => 3,
        "msg_seq" => mt_rand(1, 9999),
        "ark" => [
            "template_id" => 37,
            "kv" => [
                ["key" => "#METATITLE#", "value" => $title],
                ["key" => "#METASUBTITLE#", "value" => $xtitle],
                ["key" => "#PROMPT#", "value" => "一直❤爱你"],
                ["key" => "#METACOVER#", "value" => $iurl]
            ]
        ]
    ];
    switch (消息来源) {
         case "群聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
         case "私聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
         break;
         case "加群":
         case "退群":
         case "互动":
           $json["event_id"] = 事件ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
    }
}

function 跳转卡($title,$desc,$image,$tz){
    $json = [
        "msg_type" => 3,
        "msg_seq" => mt_rand(1, 9999),
        "ark" => [
            "template_id" => 24,
            "kv" => [
                ["key" => "#DESC#", "value" => "一直❤爱你"],
                ["key" => "#PROMPT#", "value" => "一直❤爱你"],
                ["key" => "#TITLE#", "value" => $title],
                ["key" => "#METADESC#", "value" => $desc],
                ["key" => "#IMG#", "value" => $image],
                ["key" => "#LINK#", "value" => $tz],
                ["key" => "#SUBTITLE#", "value" => "一直❤爱你"]
            ]
        ]
    ];
    switch (消息来源) {
         case "群聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
         case "私聊":
           $json["msg_id"] = 消息ID;
           return BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
         break;
         case "加群":
         case "退群":
         case "互动":
           $json["event_id"] = 事件ID;
           return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
         break;
    }
}

function 流式(...$msgs){
    $id = null;
    $index = 0;
    $total = count($msgs);
    foreach ($msgs as $msg) {
        $isLast = ($index === $total - 1);
        $json = [
            "content" => (string)$msg,
            "msg_id" => 消息ID,
            "msg_seq" => rand(1, 99999),
            "stream" => [
                "state" => $isLast ? 10 : 1,
                "id" => $id,
                "index" => $index,
                "reset" => false
            ]
        ];
        $curl = BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
        $json = json_decode($curl, true);
        $id = $json["id"];
        $index++;
    }
    return $curl;
}

function 撤回($id){
   $type = [
      "群聊"=>"groups",
      "私聊"=>"users"
   ];
   $type = $type[消息来源];
   return BOTAPI("/v2/{$type}/".来源."/messages/".$id,"DELETE","");
}

// 新增：发送 Markdown 消息（支持可选按钮模板）
function MD($md, $keyboard = null) {
   $json = [
       "content" => "",
       "msg_type" => 2,
       "msg_seq" => rand(1, 9999),
       "markdown" => [
           "content" => $md,
       ],
       "keyboard" => [
           "id" => $keyboard
       ]
   ];
   switch (消息来源) {
     case "群聊":
        $json["msg_id"] = 消息ID;
        return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
        break;
     case "私聊":
        $json["msg_id"] = 消息ID;
        return BOTAPI("/v2/users/".来源."/messages", "POST", json_encode($json));
        break;
     case "加群":
     case "退群":
     case "互动":
        $json["event_id"] = 事件ID;
        return BOTAPI("/v2/groups/".来源."/messages", "POST", json_encode($json));
        break;
   }
}