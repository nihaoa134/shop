@extends('layouts.bst')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
<h2>全部商品</h2>
    <table class="table table-bordered">
        <tr class="text-center">
            <td>商品id</td>
            <td>商品名称</td>
            <td>商品库存</td>
            <td>商品价格</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
@foreach($data as $v)
            <tr class="text-center">
                <td>{{$v['goods_id']}}</td>
                <td>{{$v['goods_name']}}</td>
                <td>{{$v['goods_num']}}</td>
                <td>{{$v['price']/100}}</td>
                <td>{{date('Y-m-d H:i:s',$v['add_time'])}}</td>
                <td><a href="/goods/{{$v['goods_id']}}">详情</a></td>
            </tr>
@endforeach
    </table>
@endsection

@section('footer')
    @parent
@endsection