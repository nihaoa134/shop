<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;

class WxController extends Controller
{
    public function check(Request $request)
    {
              echo  $request->input('echostr');

        //        file_put_contents("/tmp/aasd.log",$arr,FILE_APPEND);
        //      $str=$arr['echostr'];
/*                $str = file_get_contents("php://input");
                //echo $str;
                file_put_contents('/tmp/weixin.log', $str, FILE_APPEND);
                $objxml = simplexml_load_string($str);
                $arr['ToUserName'] = $objxml->ToUserName;
                $arr['FromUserName'] = $objxml->FromUserName;
                $arr['CreateTime'] = $objxml->CreateTime;
                $arr['MsgType'] = $objxml->MsgType;
                $arr['Event'] = $objxml->Event;
                if($objxml->MsgType == 'event'){
                    if($objxml->Event == 'subscribe'){
                        Xml::insert($arr);
                    }else{
                        Xml::where(['FromUserName'=>$objxml->FromUserName,'Event'=>'subscribe'])->delete();
                    }
                }*/


//图文推送
/*        $str = file_get_contents("php://input");

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
*/

        }
    }
}
