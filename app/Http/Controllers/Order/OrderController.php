<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    //

    public function index()
    {
        echo __METHOD__;
    }

    /**
     * 下单
     */
    public function add(Request $request)
    {
        //查询购物车商品
        $cart_goods = CartModel::where(['uid'=>session()->get('uid')])->orderBy('id','desc')->get()->toArray();
        if(empty($cart_goods)){
            die("购物车中无商品");
        }
        $order_amount = 0;
        foreach($cart_goods as $k=>$v){
            $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goods_info['num'] = $v['num'];
            $list[] = $goods_info;

            //计算订单价格 = 商品数量 * 单价
            $order_amount += $goods_info['price'] * $v['num'];
        }

        //echo '<pre>';print_r($list);echo '</pre>';die;
        $order_sn = OrderModel::generateOrderSN();  //生成订单号

        $data = [
            'order_sn'      => $order_sn,
            'uid'           => session()->get('uid'),
            'add_time'      => time(),
            'order_amount'  => $order_amount
        ];

        //写入订单表
        $oid = OrderModel::insertGetId($data);
        if($oid){
            //echo '下单成功,您的订单号为'.$order_sn.'.跳转支付!';
//            清空购物车
            CartModel::where(['uid'=>Auth::id()])->delete();
            $data['order_id']=$oid;
            $info=[
                'data'=>$data,
                'title'=>'确认支付'
            ];
            return view('order.yespay',$info);
        }else{
            echo '生成订单失败';
        }

        //清空购物车
        CartModel::where(['uid'=>session()->get('uid')])->delete();
    }


    //订单列表
    public function orderList()
    {
        $list = OrderModel::where(['uid'=>session()->get('uid'),'is_pay'=>0])->orderBy('oid','desc')->get()->toArray();
        $data = [
            'list'  => $list,
        ];
        return view('order.list',$data);
    }
}

