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
//图文推送
        $str = file_get_contents("php://input");

        $objxml = simplexml_load_string($str);
        $ToUserName = $objxml->ToUserName;
        $CreateTime = $objxml->CreateTime;
        $FromUserName = $objxml->FromUserName;
        $MsgType = $objxml->MsgType;
        $Event = $objxml->Event;
        $Content = $objxml->Content;
        $data = DB::table('shop_goods')->where('goods_name','like',"%$Content%")->first();
        $goods_name = $data->goods_name;
        $goods_selfmon=$data->goods_selfmon;
        if($data){
            $goods_img = "http://39.96.32.132/goods_img/$data->goods_img";
            $title = "$goods_name";
            $descriptionl = "$goods_selfmon";
            $time = time();
            $str="<xml>
                <ToUserName><![CDATA[$FromUserName]]></ToUserName>
               <FromUserName><![CDATA[$ToUserName]]></FromUserName>
                <CreateTime>$time</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>
            <item>
            <Title><![CDATA[$title]]></Title>
            <Description><![CDATA[$descriptionl]]></Description>
            <PicUrl><![CDATA[$goods_img]]></PicUrl>
            <Url><![CDATA[$goods_img]]></Url>
            </item>
        </Articles>
        </xml>";
            echo $str;

        }else{

        }
    }
}