@extends('layouts.bst')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <form method="post" action="/payadd">
        {{csrf_field()}}
    <table class="table table-bordered">
        <input type="text" name="soso">
        <input type="submit" value="soso">
        <tr class="text-center">
            <td>商品id</td>
            <td>商品名称</td>
            <td>商品库存</td>
            <td>商品价格</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        @foreach($list as $k=>$v)
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
    </form>
    {{$list->links()}}
@endsection

@section('footer')
    @parent
@endsection