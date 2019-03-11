@extends('layouts.mama')

@section('content')
    <form action="/weixin/biaoqian" method="post">
    <table border = '1'>
        <tr>
            <td>添加标签</td>
            <td>id</td>
            <td>姓名</td>
            <td>性别</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        @foreach($list as $v)
        <tr>
            <td><input type="checkbox" value="{{$v['openid']}}"></td>
            <td width="50">{{$v['id']}}</td>
            <td width="50">{{$v['nickname']}}</td>
            <td width="50"><?php if($v['sex']==1){echo '男';}else{echo '女';} ?></td>
            <td width="150"><?php echo date('Y-m-d H:i:s',$v['add_time'])?></td>
            <td width="50"><a href="/weixin/blick/{{$v['openid']}}">拉黑</a></td>
        </tr>
        @endforeach

    </table>
    <input type="submit" value="提交">
    </form>
    {{$list->links()}}
@endsection
@section('footer')
    @parent
@endsection