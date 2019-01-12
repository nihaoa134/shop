@extends('layouts.mama')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <h2>提交订单成功!</h2>
    <table class="table table-bordered">
        <tr class="text-center">
            <td>订单号</td>
            <td>{{$data['order_sn']}}</td>
        </tr>
        <tr class="text-center">
            <td>支付金额</td>
            <td>{{$data['order_amount']/100}}</td>
        </tr>
        <tr class="text-center">
            <td>提交时间</td>
            <td>{{date('Y-m-d H:i:s',$data['add_time'])}}</td>
        </tr>
    </table>
    <a href="/pay/order/{{$data['order_id']}}" id="submit_ysepay" class="btn btn-success btn-block"> 确认支付 </a>
@endsection

@section('footer')
    @parent
@endsection