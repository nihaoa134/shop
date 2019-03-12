<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Controller;
use App\Model\GoodsModel;

class WxController extends Controller
{
    public function check()
    {
        echo $_GET['echostr'];
/*
                $str = file_get_contents('php://input');

                file_put_contents('/tmp/weixin.k.log', $str, FILE_APPEND);

                $objxml = simplexml_load_string($str);

                $ToUserName = $objxml->ToUserName;

                $FormUserName = $objxml->FromUserName;

                $MsgType = $objxml->MsgType;

                $Event = $objxml->Event;

                $Content = $objxml->Content;

                $CreateTime = $objxml->CreateTime;


                if ($MsgType == 'text') {

                    $goodsList = GoodsModel::where('goods_name', 'like', "%$Content%") -> first();

                    $time = time();

                    $url = "https://pp.lixiaonitongxue.top";

                    $xml = "
                        <xml>
                        <ToUserName><![CDATA[$FormUserName]]></ToUserName>
                        <FromUserName><![CDATA[$ToUserName]]></FromUserName>
                        <CreateTime>$time</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>1</ArticleCount>
                            <Articles>
                                <item>
                                    <Title><![CDATA[{$goodsList -> goods_name}]]></Title>
                                    <Description><![CDATA[{$goodsList -> goods_selfprice}]]></Description>
                                    <PicUrl><![CDATA[{$goodsList -> goods_img}]]></PicUrl>
                                    <Url><![CDATA[$url]]></Url>
                                </item>
                            </Articles>
                    </xml>
                    ";

                    echo $xml;

        }*/


    }

}