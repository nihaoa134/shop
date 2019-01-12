<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    //
    public function add(){
        //查询购物车商品
        $cart_goods=CartModel::where(['uid'=>session()->get('uid')])->orderBy('id','desc')->get()->toArray();
        if(empty($cart_goods)){
            die('购物车中没有商品');
        }
        $order_amount=0;
        foreach($cart_goods as $k=>$v){
            $goodsInfo=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goodsInfo['num']=$v['num'];
            $data[]=$goodsInfo;
            //计算价格
            $order_amount+=$goodsInfo['price']*$v['num'];
        }
        //echo $order_amount;
        //print_r($goodsInfo);
        //生成订单号
        $order_sn=OrderModel::generateOrderSn();
        //echo $order_sn;
        $data=[
            'order_sn'=>$order_sn,
            'uid'=>session()->get('uid'),
            'add_time'=>time(),
            'order_amount'=>$order_amount,
        ];
        $oid=OrderModel::insertGetId($data);
        if($oid){
            echo '下单成功,您的订单号为'.$order_sn;
//            清空购物车
            CartModel::where(['uid'=>session()->get('uid')])->delete();
            header("Refresh:3;url=/order");
        }else{
            echo '生成订单失败';
        }
    }
    //订单展示
    public function orderList(){
        $list=OrderModel::where(['uid'=>session()->get('uid')])->orderBy('o                             
  4id','desc')->get()->toArray();
        $info=[
            'title'=>'我的订单',
            'data'=>$list
        ];
        return view('order.list',$info);
    }
}
