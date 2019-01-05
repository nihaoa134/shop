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
        echo '<pre>';print_r($_GET);echo '</pre>';
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


    /**
     * 用户注册
     * 2019年1月3日14:26:56
     * liwei
     */
    public function reg()
    {
        return view('users.reg');
    }

    public function doReg(Request $request)
    {
        //echo __METHOD__;
        //echo '<pre>';print_r($_POST);echo '</pre>';
        $pwd=password_hash($request->input('u_pwd'),PASSWORD_BCRYPT);

        $data = [
            'name'  => $request->input('u_name'),
            'pwd'  => $pwd,
            'age'  => $request->input('u_age'),
            'email'  => $request->input('u_email'),
            'reg_time'  => time(),
        ];

        $uid = UserModel::insertGetId($data);
        //var_dump($uid);

        if($uid){
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
}
