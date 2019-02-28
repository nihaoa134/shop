@extends('layouts.mama')

@section('content')
    <div class="container">
        <h2>微信登录</h2>
        <h3>
            <a href="https://open.weixin.qq.com/connect/qrconnect?appid=wxe24f70961302b5a5&redirect_uri=http%3a%2f%2fmall.77sc.com.cn%2fweixin.php%3fr1%3dhttps%3a%2f%2fpp.lixiaonitongxue.top%2fweixin%2fcode&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect">Login</a>
        </h3>
    </div>
@endsection
@section('footer')
    @parent
@endsection