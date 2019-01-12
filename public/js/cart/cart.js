$("#del_a").click(function(e){
    e.preventDefault();//防止链接打开url
    var goods_id=$(this).attr('goods_id');
    var _this=$(this);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/cart/del',
        type:'post',
        data:{goods_id:goods_id},
        datatype:'json',
        success:function(d){
            if(d.error==301){
                window.location.href=d.url;
            }else{
                alert(d.msg);
                if(d.error==0){
                    _this.parents('tr').remove();
                }
            }
        }
    })
})