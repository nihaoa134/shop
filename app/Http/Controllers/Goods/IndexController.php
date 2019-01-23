<?php
namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class IndexController extends Controller
{
        public function __construct()
        {
                $this->middleware('auth');
        }

    //商品详情
    public function index($goods_id)
    {
        $goods = GoodsModel::where(['goods_id'=>$goods_id])->first();

        //商品不存在
        if(!$goods){
            header('Refresh:2;url=/');
            echo '商品不存在,正在跳转至首页';
            exit;
        }

        $data = [
            'goods' => $goods
        ];
        return view('goods.index',$data);
    }
    public function show(){
        $goods=GoodsModel::all();
        $info=[
            'data'=>$goods,
            'title'=>'商品展示'
        ];
        return view('goods.show',$info);
    }
    public function update(){
            $info = [
              'title'=> '图片上传'
            ];
            return view(goods.$this->update(),$info);
    }
    public function  updateImg(Request $request){
            $img = $request->file('img');
            $ext = $img ->extension();
            $type=['jep','png','gif','bmp','jpg'];
            if (!in_array($ext,$type)){
                die('上传图片格式错误，请上传正确格式..');
            };
            $res = $img ->storeAs(date('Ymd'),str_random(6).'.'.$ext);
            if ($res){
                echo '上传成功';
            }
    }
    public function pay(){
        $links=GoodsModel::paginate(3);
        $data=[
            'title'=>'分页搜索',
            'list'=>$links
        ];
        return view('goods/pay',$data);
    }
    public function payadd(){
        $soso=$_POST['soso'];
        $link=goodsModel::where('goods_name','like',"%$soso%")->paginate(3);
        $data=[
            'title'=>'分页搜索',
            'list'=>$link
        ];
        return view('goods/pay',$data);
    }

}
