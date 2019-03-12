<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    public function check(Request $request)
    {
              echo  $request->input('echostr');
            $str = file_get_contents("php://input");

            $objxml = simplexml_load_string($str);
            $ToUserName = $objxml->ToUserName;
            $CreateTime = $objxml->CreateTime;
            $FromUserName = $objxml->FromUserName;
            $MsgType = $objxml->MsgType;
            $Event = $objxml->Event;

            $arr=array(
                "ToUserName" => $ToUserName,
                "FromUserName" => $FromUserName,
                "CreateTime" => $CreateTime,
                "MsgType" => $MsgType,
                "Event" => $Event
            );
       #file_put_contents("/tmp/weixin.k.log",$str,FILE_APPEND);
    //连接数据库
       file_put_contents("/tmp/wx.log",$arr,FILE_APPEND);
       $mysqli = mysqli_connect('39.96.32.132','root','root','test')or('连接失败');
       $sql = "insert into vx(ToUserName,FromUserName,CreateTime,MsgType,Event) values ('$ToUserName','$FromUserName','$CreateTime','$MsgType','$Event')";
       $res = mysqli_query($mysqli,$sql);
       $arr = @mysqli_fetch_assoc($res);/*
//图文推送
        $str = file_get_contents("php://input");

        $objxml = simplexml_load_string($str);
        $ToUserName = $objxml->ToUserName;
        $CreateTime = $objxml->CreateTime;
        $FromUserName = $objxml->FromUserName;
        $MsgType = $objxml->MsgType;
        $Event = $objxml->Event;
        $Content = $objxml->Content;
        $data = DB::table('shop_goods')->where('goods_name', 'like', "%$Content%")->first();
        $goods_name = $data->goods_name;
        $goods_selfmon = $data->goods_selfmon;
        if ($data) {
            $goods_img = "http://www.lixiaonitongxue.top/wx/images/$data->goods_img";
            $title = "$goods_name";
            $descriptionl = "$goods_selfmon";
            $time = time();
            $str = "<xml>
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

        }*/
    }
}
