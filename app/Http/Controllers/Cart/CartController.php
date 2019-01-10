<?php
namespace App\Http\Controllers\Cart;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    //购物车展示
    public function index(Request $request){
        $uid = session()->get('uid');
        $cart_goods = CartModel::where(['uid'=>$uid])->get()->toArray();
        if (empty($cart_goods)){
            die('购物车为空');
        }
        if($cart_goods){
            //获取商品最新信息
            foreach($cart_goods as $k=>$v){
                $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $goods_info['num']  = $v['num'];
                //echo '<pre>';print_r($goods_info);echo '</pre>';
                $list[] = $goods_info;
            }
        }

        $data = [
            'list'  => $list
        ];
        return view('cart.index',$data);
    }
    //添加购物车
    public function add2()
    {
        $goods_id=request()->input('goods_id');
        $num=request()->input('num');
        //检查库存
        $goods=GoodsModel::where(['goods_id'=>$goods_id])->first();
        if(empty($goods)){
            $response = [
                'error' => 5001,
                'msg'   => '该商品不存在'
            ];
            return $response;
        }
        //var_dump($goods);exit;
        if($goods['goods_num']<=$num){
            $response = [
                'error' => 5002,
                'msg'   => '库存不足'

            ];
            return $response;

        }
        //查看是否已经加入购物车
        $cart=CartModel::where(['uid'=>session()->get('uid'),'goods_id'=>$goods_id])->first();
        if(empty($cart)){
            //添加
            $data=[
                'goods_id'=>$goods_id,
                'uid'=>session()->get('uid'),
                'num'=>$num,
                'add_time'=>time(),
                'session_token'=>session()->get('u_token')
            ];
            $cid=CartModel::insertGetId($data);
        }else{
            //修改
            $data=[
                'add_time'=>time(),
                'num'=>$num+$cart['num'],
                'session_token'=>session()->get('u_token')
            ];
            $cid=CartModel::where(['id'=>$cart['id']])->update($data);
        }
        //写入购物车列表
        if($cid){
            //减存值
            $rs=GoodsModel::where(['goods_id'=>$goods_id])->decrement('goods_num',$num);
            $response = [
                'error' => 0,
                'msg'   => '加入购物车成功'
            ];
        }else{
            $response = [
                'error' => 5003,
                'msg'   => '加入购物车失败'
            ];
        }
        return $response;
    }
     //添加商品
    public function add($goods_id)
    {

        $cart_goods = session()->get('cart_goods');

        //是否已在购物车中
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                echo '已存在购物车中';
                exit;
            }
        }

        session()->push('cart_goods',$goods_id);

        //减少库存
        $where = ['goods_id'=>$goods_id];
        $store = CartModel::where($where)->value('goods_num');

        if($store<=0){
            echo '库存不足';
            exit;
        }
        $rs = CartModel::where(['goods_id'=>$goods_id])->decrement('goods_num');

        if($rs){
            echo '添加成功';
        }

    }

    /**
     * 删除商品
     */
    public function del($goods_id)
    {
        //判断 商品是否在 购物车中
        $goods = session()->get('cart_goods');
        echo '<pre>';print_r($goods);echo '</pre>';
        if(in_array($goods_id,$goods)){
            //执行删除
            foreach($goods as $k=>$v){
                if($goods_id == $v){
                    session()->pull('cart_goods.'.$k);
                }
            }
        }else{
            //不在购物车中
            die("商品不在购物车中");
        }

    }
    public function del2($abc)
    {
        $rs = CartModel::where(['uid'=>session()->get('uid'),'goods_id'=>$abc])->delete();
        if($rs){
            echo '商品ID:  '.$abc . ' 删除成功';
        }
    }
}
