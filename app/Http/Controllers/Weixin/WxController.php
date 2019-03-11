<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
    public function check(Request $request){
        echo  $request->input('echostr');exit;

//        file_put_contents("/tmp/aasd.log",$arr,FILE_APPEND);
//      $str=$arr['echostr'];
//        echo $str;
         $request->input('echostr');
        $str = file_get_contents("php://input");
        //echo $str;
        file_put_contents('/tmp/weixin.log', $str, FILE_APPEND);
       $objxml = simplexml_load_string($str);
        $arr = [];
//        $arr['ToUserName'] = $objxml->ToUserName;
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
        }

        if ($objxml->MsgType == 'text') {
            $goods_name = $objxml->Content;
            $goods_info = Goods::where('goods_name','like',"%$goods_name%")->get();
            $time = time();
            $count = count($goods_info);
            if ($count>0) {
                $url = "https://www.baidu.com/";
                $str = "<xml>
			  <ToUserName><![CDATA[$objxml->FromUserName]]></ToUserName>
			    <FromUserName><![CDATA[$objxml->ToUserName]]></FromUserName>
				  <CreateTime>$time</CreateTime>
				    <MsgType><![CDATA[news]]></MsgType>
					  <ArticleCount>$count</ArticleCount>
					    <Articles>";

                foreach ($goods_info as $k => $v) {
                    $picurl = "http://39.96.199.148/uploads/".$v->goods_img;
                    $str .= "<item>
							      <Title><![CDATA[$v->goods_name]]></Title>
								        <Description><![CDATA[$v->goods_desc]]></Description>
										      <PicUrl><![CDATA[$picurl]]></PicUrl>
											        <Url><![CDATA[$url]]></Url>
													    </item>";
                }
                $str .= "</Articles></xml>";
            } else {
                $str_info = "您查询的内容不存在";
                $str = "<xml>
			  <ToUserName><![CDATA[$objxml->FromUserName]]></ToUserName>
			    <FromUserName><![CDATA[$objxml->ToUserName]]></FromUserName>
				  <CreateTime>$time</CreateTime>
				    <MsgType><![CDATA[text]]></MsgType>
					  <Content><![CDATA[$str_info]]></Content>
					  </xml>";

           }
            echo $str;
        }



    }

}
