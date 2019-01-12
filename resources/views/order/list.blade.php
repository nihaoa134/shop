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
                <td>{{$v['oid']}}</td>
                <td>{{$v['order_sn']}}</td>
                <td>{{$v['order_amount']/100}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td><a href="#">取消订单</a>||<a href="/pay/order/{{$v['oid']}}">支付</a></td>
            </tr>
        @endforeach
    </table>
@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('/js/cart/cart.js')}}"></script>
@endsection