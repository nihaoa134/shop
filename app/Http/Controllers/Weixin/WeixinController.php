<?php

namespace App\Http\Controllers\Weixin;

use App\Model\UserModel;
use App\Model\WxChatRecordModel;
use App\Model\WxMaterialModel;
use App\Model\WxMediaModel;
use App\Model\WxUserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use mysql_xdevapi\Collection;


class WeixinController extends Controller
{
    //
    protected $redis_weixin_access_token = 'str:weixin_access_token';  //微信access_token

    /**
     *首次接入
     */
    public function validToken1()
    {
        echo $_GET['echostr'];
    }

    /**
     * 接收微信服务器时间推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");

        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象

        $event = $xml->Event;
        $openid = $xml->FromUserName;                   //事件类型
        //var_dump($xml);echo '<hr>';

        //处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType == 'text'){
                $msg = $xml->Content;
                //$xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . $msg . date('Y-m-d H:i:s') . ']]></Content></xml>';
                //echo $xml_response;
                $info = [
                    'type'      =>  1,
                    'message'   =>  $msg,
                    'msgid'     =>  $xml->MsgId,
                    'add_time'  =>  time(),
                    'open_id'   =>  $openid,
                ];
                WxChatRecordModel::insertGetId($info);

            }elseif ($xml->MsgType == 'image'){
                //$this->saveImg($xml->MediaId);
                //视业务需求是否需要下载保存图片
                if(1){  //下载图片素材
                    //var_dump($xml);
                    $file_name = $this->saveImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'图片保存成功' . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;

                    $this->dbMedia($xml,$openid,$file_name);


                }
            }elseif ($xml->MsgType == 'voice') {
                $file_name = $this->dlVoice($xml->MediaId);

                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . '语音保存成功' . ' >>> ' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;

                $this->dbMedia($xml,$openid,$file_name);
            }elseif ($xml->MsgType == 'video'){
                $file_name = $this->dlVideo($xml->MediaId);

                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.'视频保存成功' . ' >>> ' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;
                $this->dbMedia($xml,$openid,$file_name);

            }elseif ($xml->MsgType == 'event') {

                if($event=='subscribe'){
                    //用户openid
                    $sub_time = $xml->CreateTime;               //扫码关注时间

                    //echo 'openid: '.$openid;echo '</br>';
                    //echo '$sub_time: ' . $sub_time;

                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
                    //echo '<pre>';print_r($user_info);echo '</pre>';

                    //保存用户信息
                    $u = WxUserModel::where(['openid'=>$openid])->first();
                    //var_dump($u);die;
                    if($u){       //用户不存在
                        echo '用户已存在';
                    }else{
                        $user_data = [
                            'openid'            => $openid,
                            'add_time'          => time(),
                            'nickname'          => $user_info['nickname'],
                            'sex'               => $user_info['sex'],
                            'headimgurl'        => $user_info['headimgurl'],
                            'subscribe_time'    => $sub_time,
                        ];
                        //print_r($user_data);
                        //exit;
                        $id = WxUserModel::insertGetId($user_data);      //保存用户信息
                        //var_dump($id);
                    }
                }else if($event=='CLICK'){
                    if($xml->EventKey=='kefu01'){
                        $this->kefu01($openid,$xml->ToUserName);
                    }
                }
            }
        }
        //exit;

        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 下载视频文件
     */
    public function dlVideo($media_id){
        $access_token = $this->getWXAccessToken();
        //echo $access_token;exit;
        //echo $media_id;
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='. $access_token .'&media_id='. $media_id;
        //echo $url;exit;
        //保存语音
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');

        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_video_path = 'wx/video/'.$file_name;

        //保存语音
        $r = Storage::disk('local')->put($wx_video_path,$response->getBody());

        if($r){     //保存成功
            //echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }
        return $file_name;
    }

    /**
     * 下载语音文件
     */
    public function dlVoice($media_id){
        $access_token = $this->getWXAccessToken();
        //echo $access_token;exit;
        //echo $media_id;
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='. $access_token .'&media_id='. $media_id;
        //echo $url;exit;
        //保存语音
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');

        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_voice_path = 'wx/voice/'.$file_name;

        //保存语音
        $r = Storage::disk('local')->put($wx_voice_path,$response->getBody());

        if($r){     //保存成功
            //echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }
        return $file_name;
    }


    /**
     * 接收用户发送图片
     */
    public function saveImg($media_id){
        $access_token = $this->getWXAccessToken();
        //echo $media_id;
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='. $access_token .'&media_id='. $media_id;

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //var_dump($response);
        //$h = $response->getHeaders();

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        //echo $file_info;exit;
        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/images/'.$file_name;
        //echo $wx_image_path;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        //var_dump($r);
        if($r){     //保存成功
            //echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }
        return $file_name;

    }


    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        //echo $access_token;exit;
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';


        //$client = new GuzzleHttp\Client(['base_uri' => $url]);
        //$r = $client->request('GET', $url);


        //$respone_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($respone_arr);echo '</pre>';
        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }

    /**
     *
     */
    public function createMenu()
    {
        //拼接url地址
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getWXAccessToken();


        //post数据
        $data = [
            "button"    => [
                [
                    "type"  => "click",      // click类型
                    "name"  => "客服01",
                    "key"   => "kefu01"
                ],
                [
                    "name" => "直播",
                    "sub_button" => [
                        [
                            "type"  => "view",
                            "name"  => '斗鱼',
                            "url"   => "https://www.douyu.com/"
                        ],
                        [
                            "type"  => "view",
                            "name"  => '虎牙',
                            "url"   => "https://www.huya.com/"
                        ]
                    ]
                ],
                [
                    "name" => "视频播放",
                    "sub_button" => [
                        [
                            "type"  => "view",
                            "name"  => '腾讯视频',
                            "url"   => "https://v.qq.com/"
                        ],
                        [
                            "type"  => "view",
                            "name"  => '爱奇艺视频',
                            "url"   => "https://www.iqiyi.com/"
                        ],
                        [
                            "type"  => "view",
                            "name"  => '哔哩哔哩视频',
                            "url"   => "https://www.bilibili.com/"
                        ]
                    ]
                ]
            ]
        ];

        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $r = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);

        $respone_arr = json_decode($r->getBody(),true);
        echo '<pre>';print_r($respone_arr);echo '</pre>';

        if($respone_arr['errcode'] == 0) {
            echo '创建菜单成功';
        }else{
            echo '创建菜单失败，请重试!';
            echo $respone_arr['errmsg'];
        }
    }

    /**
     * 被动回复
     */
    public function kefu01($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $from . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . 'Hello World, 现在时间' . date('Y-m-d H:i:s') . ']]></Content></xml>';
        echo $xml_response;
    }

    public function sendAll(){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->getWXAccessToken();
        //echo $url;exit;
        //openid
        $wxUserInfo = WxUserModel::get()->toArray();
        //var_dump($wxUserInfo);
        foreach($wxUserInfo as $v){
            $openid[]=$v['openid'];
        }
        //print_r($openid);

        //文本群发消息
        $data = [
            "touser"    =>  $openid,
            "msgtype"   =>  "text",
            "text"      =>  [
                "content"   =>  "这是一个测试文本,当前时间是:".date('Y-m-d H:i:s')
            ]
        ];


        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        $r = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);

        $respone_arr = json_decode($r->getBody(),true);
        echo '<pre>';print_r($respone_arr);echo '</pre>';
    }

    /**
     * 素材入库
     */
    public function dbMedia($xml,$openid,$file_name){
        //var_dump($xml);
        $data = [
            'openid'    =>  $openid,
            'add_time'  =>  time(),
            "msg_type"  =>  $xml->MsgType,
            'msg_id'    =>  $xml->MsgId,
            'media_id'  =>  $xml->MediaId,
            'local_file_name'   => $file_name
        ];
        if($xml->MsgType == 'image'){
            $data['pic_url'] = $xml->PicUrl;
        }elseif($xml->MsgType == 'voice'){
            $data['format'] = $xml->Format;
        }elseif($xml->MsgType == 'video'){
            $data['thumb_media_id'] = $xml->ThumbMediaId;
        }
        //var_dump($data);exit;

        //入库
        $r = WxMediaModel::insertGetId($data);
        var_dump($r);
    }


    /**
     * 刷新access_token
     */
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }

    /**
     * form表单
     */
    public function formShow()
    {
        return view('weixin.form');
    }

    /**
     * 保存永久素材到服务器
     */
    public function formSave(Request $request)
    {
        //保存文件
        $img_file = $request->file('media');
        //print_r($img_file);
        $img_origin_name = $img_file->getClientOriginalName();  //拿文件名
        //echo $img_origin_name;
        $file_ext = $img_file->getClientOriginalExtension();          //获取文件扩展名
        //echo $file_ext;

        //重命名
        $new_file_name = str_random(15).'.'.$file_ext;

        //文件保存路径

        //保存文件
        $save_file_path = $request->media->storeAs('form_media',$new_file_name);
        //echo $save_file_path;

        //上传至微信永久素材
        $file_info = $this->upMaterial($save_file_path);
        $file_info['file_path'] = $save_file_path;
        //素材添加至数据库
        $r = WxMaterialModel::insertGetId($file_info);
        var_dump($r);

    }

    /**
     * 上传至微信永久素材
     */
    public function upMaterial($file_path)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';

        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart'     =>  [
                [
                    'name'      =>  'media',
                    'contents'   =>  fopen($file_path,'r')
                ]
            ]
        ]);
        $body = $response->getBody();
        //echo $body;echo '<hr>';
        $d = json_decode($body,true);
        //echo '<pre>';print_r($d);echo '</pre>';
        return $d;
    }

    /**
     * 获取永久素材列表
     */
    public function materialList()
    {
        $client = new GuzzleHttp\Client();
        //$type = $_GET['type'];
        //$offset = $_GET['offset'];

        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getWXAccessToken();
        $body = [
            "type"      => 'image',
            "offset"    => 0,
            "count"     => 20
        ];
        $response = $client->request('POST', $url, [
            'body' => json_encode($body)
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $arr = json_decode($response->getBody(),true);
        echo '<pre>';print_r($arr);echo '</pre>';
        //return $arr;

    }

    /**
     * 客服接口页面
     */
    public function kefuShow($id)
    {
        $userInfo = WxUserModel::where(['id'=>$id])->first();
        //var_dump($data);exit;
        $info = [
            'title'     => '客服聊天',
            'openid'    => $userInfo->openid,
            'nickname'  => $userInfo->nickname,
        ];
        return view('weixin.kefuchat',$info);
    }

    /**
     * 客服消息处理页面
     * @param Request $request
     * @return array
     * @throws GuzzleHttp\Exception\GuzzleException
     */
    public function kefuChat()
    {
        $openid = $_GET['openid'];  //用户openid
        $pos = $_GET['pos'];        //上次聊天位置

        $msg = WxChatRecordModel::where(['open_id'=>$openid])->where('id','>',$pos)->first();
        if($msg){
            $msg=$msg->toArray();
            $msg['add_time']=date('Y-m-d H:i:s');
            $response = [
                'errno' => 0,
                'data'=>$msg
            ];
        }else{
            $response = [
                'errno' => 50001,
                'data'=>'服务器异常'
            ];
        }
        die(json_encode($response));
    }

    /**
     * 客服消息处理
     */
    public  function kefuChatMsg(Request $request){
        $open_id = $request->input('openid');
        $msg = $request->input('msg');

        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();

        $data = [
            'touser'       =>$open_id,
            'msgtype'      =>'text',
            'text'         =>[
                'content'  =>$msg,
            ]
        ];

        $client = new GuzzleHttp\Client();

        $response = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $body = $response->getBody();
        $arr = json_decode($body,true);
        //加入数据库
        if($arr['errcode']==0){
            $info = [
                'type'      =>  2,
                'message'   =>  $msg,
                'msgid'     =>  0,
                'add_time'  =>  time(),
                'open_id'   =>  $open_id,
            ];
            WxChatRecordModel::insertGetId($info);
        }

        return $arr;
    }

    /**
     * 微信登录
     */
    public function weChatLogin(){
        $uri = 'http://mall.77sc.com.cn/weixin.php?r1=https://www.lixiaonitongxue.top/weixin/loginsuccess';
        $url = 'https://open.weixin.qq.com/connect/qrconnect?appid=wxe24f70961302b5a5&redirect_uri='.urlencode($uri).'&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect';
        echo "<a href=". $url .">WeChat LOGIN</a>";

    }
    public function weChatLoginSuccess(Request $request){
        //print_r($_GET);
        $code = $request->input('code');

        //code换去access——token请求接口
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($user_arr);echo '</pre>';

        //查询数据库中是否存在该账号
        $unionid = $user_arr['unionid'];
        $where = [
            'union_id'   =>  $unionid
        ];
        $wx_user_info = WxUserModel::where($where)->first();
        if($wx_user_info){
            $user_info = UserModel::where(['wechat_id'=>$wx_user_info->id])->first();
        }

        if(empty($wx_user_info)){
            //第一次登录
            $data = [
                'openid'        =>  $user_arr['openid'],
                'nickname'      =>  $user_arr['nickname'],
                'sex'           =>  $user_arr['sex'],
                'headimgurl'    =>  $user_arr['headimgurl'],
                'union_id'      =>  $unionid,
                'add_time'      =>  time()
            ];
            $wechat_id = WxUserModel::insertGetId($data);
            $rs = UserModel::insertGetId(['wechat_id'=>$wechat_id]);
            if($rs){

                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$rs,time()+86400,'/','shop.com',false,true);
                setcookie('token',$token,time()+86400,'/user','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$rs);
                echo '注册成功';
                header("refresh:2,url='/user/center'");
            }else{
                echo '注册失败';
            }
            exit;
        }
        $token=substr(md5(time().mt_rand(1,99999)),10,10);
        setcookie('uid',$user_info->uid,time()+86400,'/','shop.com',false,true);
        setcookie('token',$token,time()+86400,'/user','',false,true);
        $request->session()->put('u_token',$token);
        $request->session()->put('uid',$user_info->uid);
        header("refresh:2,url='/user/center'");
    }
}
