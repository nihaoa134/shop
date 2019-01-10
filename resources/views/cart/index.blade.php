@extends('layouts.bst')

@section('content')
    <table class="table">
    <tr>
        <td>id</td>
        <td>商品名称</td>
        <td>商品价格</td>
        <td>购买数量</td>
        <td>添加时间</td>
        <td>操作</td>
    </tr>
@foreach($list as $k=>$v)
        <tr>
            <td>{{$v['goods_id']}}</td>
            <td>{{$v['goods_name']}}</td>
            <td>{{$v['price']}}</td>
            <td>{{$v['num']}}</td>
            <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
            <td><a type="button" class="btn btn-danger" href="/cart/del2/{{$v['goods_id']}}" class="del_goods">删除</a></td>
        </tr>

@endforeach
    </table>
    <a href="/order/add" type="button" class="btn btn-primary btn-lg btn-block">提交订单</a>
@endsection

@section('footer')
    @parent
@endsection