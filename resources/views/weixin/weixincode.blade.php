@extends('layouts.mama')

@section('title') {{$title}}    @endsection

@section('nav')
    @parent
@endsection

@section('content')
    <div id="qrcode" align="center">
    </div>
@endsection

@section('footer')
    @parent
    <script src="{{URL::asset('/js/qrcodejs/qrcode.min.js')}}"></script>
    <script>
        (function() {
                var qrcode = new QRCode('qrcode', {
                        text: "{{$code_url}}",
                        width: 256,
                        height: 256,
                        colorDark : '#000000',
                        colorLight : '#ffffff',
                        correctLevel : QRCode.CorrectLevel.H
                    }
                );
            }
        )()
        setInterval(function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:     '/weixin/pay/wxsuccess?order_id='+"{{$order_id}}",
                type:    'get',
                dataType: 'json',
                success:   function (d) {
                    if(d.error == 0){
                        alert(d.msg);
                        location.href = '/order/list'
                    }
                }

            });
        },5000)
    </script>
@endsection

