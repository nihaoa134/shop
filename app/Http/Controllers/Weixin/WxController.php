<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    /**
     *首次接入
     */
    public function check()
    {
        echo $_GET['echostr'];
    }

    public function wxEvent()
    {
        $data = file_get_contents("php://input");

        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象

        $event = $xml->Event;
        $openid = $xml->FromUserName;                   //事件类型
        var_dump($xml);
        echo '<hr>';
        //处理用户发送消息
                if(isset($xml->MsgType)){
                    if($xml->MsgType == 'text'){
                        $msg = $xml->Content;
                        $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $msg . date('Y-m-d H:i:s') . ']]></Content></xml>';
                        echo $xml_response;
                    }
            }
                $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
                file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }
}