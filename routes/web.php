<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/test','User\UserController@test');
Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>10086]);


// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');


Route::any('/test/abc','Test\TestController@abc');

//注册
Route::get('/reg','User\UserController@reg');
Route::post('/reg','User\UserController@doreg');

//登陆
Route::get('/login','User\UserController@login');
Route::post('/login','User\UserController@dologin');

Route::get('/center','User\UserController@center')->middleware('check.login');

//中间件
Route::get('checkcookie','User\UserController@cookie')->middleware('checkcookie');

//购物车
Route::get('checklogin','User\UserController@index')->middleware('check.login');
Route::get('/cart','Cart\CartController@index');
Route::get('/cart/add/{goods_id}','Cart\CartController@add');
Route::get('/cart/del/{goods_id}','Cart\CartController@del');
//添加购物车
Route::any('/cart/add2','Cart\CartController@add2');
//删除购物车
Route::any('/cart/del2/{goods_id}','Cart\CartController@del2');

//商品
Route::get('/goods/{goods_id}','Goods\IndexController@index');
//商品列表
Route::any('/goods','Goods\IndexController@show');
//商品展示
Route::any('/goods/index','Goods\IndexController@add2');

//添加订单
Route::any('/order/add','Order\OrderController@add');
//订单展示
Route::any('/order','Order\OrderController@orderList');
//支付
Route::get('/pay/test','Pay\alipayController@test');
//订单支付
Route::get('/pay/{oid}','Pay\alipayController@pay');
//异步
Route::post('/pay/alipay/notify','Pay\alipayController@aliNotify');
//同步
Route::get('/pay/alipay/return','Pay\alipayController@aliReturn');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/pay','Goods\IndexController@pay');
Route::post('/payadd','Goods\IndexController@payadd');

//微信
Route::get('/weixin/check','Weixin\WxController@check');
Route::post('/weixin/valid1','Weixin\WxController@wxEvent');
Route::get('/weixin/test','Weixin\WeixinController@test');
//Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::get('/weixin/createmenu','Weixin\WeixinController@createMenu');        //接收微信服务器事件推送



Route::get('/weixin/sendall','Weixin\WeixinController@sendAll');

Route::get('/weixin/formshow','Weixin\WeixinController@formShow');     //表单页面
Route::post('/weixin/formsave','Weixin\WeixinController@formSave');     //表单处理

Route::get('/weixin/refreshtoken','Weixin\WeixinController@refreshToken');    //刷新access_token

Route::get('/weixin/material/list','Weixin\WeixinController@materialList');     //获取永久素材列表
//Route::get('/weixin/material/upload','Weixin\WeixinController@upMaterial');     //上传永久素材
Route::post('/weixin/material','Weixin\WeixinController@materialMenu');     //创建菜单
//Route::get('/weixin/materialdelete/{id}','Weixin\WeixinController@destroy');     //创建菜单
Route::get('/weixin/kefuchat/{openid}','Weixin\WeixinController@kefuShow');     //客服聊天页面


Route::get('/weixin/kefuchatmsg','Weixin\WeixinController@kefuChat');     //客服聊天页面
Route::post('/weixin/kefuchat','Weixin\WeixinController@kefuChatMsg');     //客服聊天页面

//微信支付
Route::get('/weixin/pay/test/{id}','Weixin\PayController@test');   //微信支付测试
Route::post('/weixin/pay/notice','Weixin\PayController@notice');   //微信支付测试
Route::get('/weixin/pay/wxsuccess','Weixin\PayController@WxSuccess');   //微信支付测试



Route::get('/weixin/login','Weixin\WeixinController@weChatLogin');   //微信登录
Route::get('/weixin/loginsuccess','Weixin\WeixinController@weChatLoginSuccess');   //微信登录成功