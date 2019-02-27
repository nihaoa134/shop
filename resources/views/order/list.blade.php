@extends('layouts.bst')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <h2>我的订单</h2>
    <table class="table table-bordered">
        <tr class="text-center">
            <td>ID</td>
            <td>订单号</td>
            <td>订单金额</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        @foreach($data as $v)
            <tr class="text-center">
                <td>{{$v['order_id']}}</td>
                <td>{{$v['order_sn']}}</td>
                <td>{{$v['order_amount']/100}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td>
                    @if($v['is_pay']==1)
                        已支付
                    @elseif($v['is_pay']==0)
                        <a href="/pay/alipay/order/{{$v['order_id']}}">支付宝支付</a>或者
                        <a href="/weixin/pay/test/{{$v['order_id']}}">微信支付</a>
                    @endif
                    ||
                    @if($v['is_delete']==1)
                        订单已取消
                    @elseif($v['is_delete']==0)
                            <a href="#">取消订单</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection'

@section('footer')
    @parent
    <script src="{{URL::asset('/js/cart/cart.js')}}"></script>
@endsection