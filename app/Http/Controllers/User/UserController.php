<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{
    //

    public function user($uid)
    {
        echo $uid;
    }

    public function test()
    {
        print_r($_GET);echo '</pre>';
    }

    public function add()
    {
        $data = [
            'name'      => str_random(5),
            'age'       => mt_rand(20,99),
            'pwd'       =>str_random(11),
            'email'     => str_random(6) . '@gmail.com',
            'reg_time'  => time()
        ];

        $id = UserModel::insertGetId($data);
        var_dump($id);
    }
    public function reg()
    {
        return view('users.reg');
    }

    public function doReg(Request $request)
    {
        $pwd=password_hash($request->input('u_pwd'),PASSWORD_BCRYPT);

        $data = [
            'name'  => $request->input('u_name'),
            'pwd'  => $pwd,
            'age'  => $request->input('u_age'),
            'email'  => $request->input('u_email'),
            'reg_time'  => time(),
        ];

        $uid = UserModel::insertGetId($data);
        if($uid){
            setcookie('uid',$uid,time()+86400,'/','www.shop.com',false,true);
            echo('注册成功');
            header("Refresh:3;url=/login");

        }else{
            echo '注册失败';
        }
    }
    //用户登陆
    public function login(){
        return view('users.login');
    }
    public function doLogin(Request $request){
        $name=$request->input('u_name');
        $pwd=$request->input('u_pwd');
        $where=[
            'name'=>$name
        ];
        $userInfo=UserModel::where($where)->first();
        if($userInfo){
            if(password_verify($pwd,$userInfo->pwd)){
                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('id',$userInfo->id,time()+600,'/','shop.com',false,true);
                setcookie('token',$token,time()+600,'/user','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('u_id',$userInfo->uid);

                echo "登陆成功";
                header("Refresh:3;url=/center");
            }else{
                die('密码不正确');
            }
        }else{
            die('用户不存在');
        }
    }
    public function center(){
        if(empty($_COOKIE['id'])){
            header("Refresh:3;url=/login");
            echo '请先登录';
            exit;
        }else{
            echo 'ID:'.$_COOKIE['id'].'欢迎回来' ;
        }
    }
    //路由中间件
    public function cookie(){
        echo __METHOD__;
    }
    public function index(Request $request)
    {
        echo __METHOD__;
    }
}
