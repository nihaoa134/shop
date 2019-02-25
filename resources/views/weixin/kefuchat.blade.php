@extends('layouts.mama')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <h2>用户openId：{{$openid}}</h2>

    <div class="chat" id="chat_div">

    </div>
    <hr>

    <form action="" class="form-inline">
        <input type="hidden" id="openid" value="{{$openid}}">
        <input type="hidden" id="msg_pos" value="1">
        <textarea id="send_msg" cols="100" rows="5"></textarea>
        <button class="btn btn-info" id="send_msg_btn">发送</button>
    </form>
@endsection'

@section('footer')
    @parent
    <script>
        $(function () {
            var openid = $('#openid').val();
            //客服发送消息
            $('#send_msg_btn').click(function (e) {
                e.preventDefault();
                var send_msg = $('#send_msg').val().trim();
                //console.log(send_msg);
                //console.log(message);
                //$("#chat_div").append(msg_str);
                $('#send_msg').val('');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:     '/weixin/kefuchat',
                    type:    'post',
                    data:    {openid:openid,msg:send_msg},
                    dataType: 'json',
                    success:   function (a) {
                        if(a.errcode == 0){
                            alert('发送成功');
                        }else{
                            alert('发送失败');
                        }

                    }
                });


                //$('#chatArea').append(_p);
            });
            //刷新聊天数据
            setInterval(function(){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:     '/weixin/kefuchatmsg?openid='+openid+'&pos='+$('#msg_pos').val(),
                    type:    'get',
                    dataType: 'json',
                    success:   function (d) {

                        if(d.errno==0){     //服务器响应正常
                            //数据填充
                            if(d.data.type==1){
                                var msg_str = '<blockquote>' + d.data.add_time +
                                    '<p>' + d.data.message + '</p>' +
                                    '</blockquote>';
                            }else{
                                var msg_str = "<blockquote style='height:70px;'><font style='float: right;clear:both;'>" + d.data.add_time +
                                    '</font><p style="float: right;clear:both;">' + d.data.message + '</p>' +
                                    '</blockquote>';
                            }


                            $("#chat_div").append(msg_str);
                            $("#msg_pos").val(d.data.id)
                        }else{

                        }

                    }
                });
            },2000);
        })
    </script>
@endsection

