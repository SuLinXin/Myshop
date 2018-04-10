<?php
/**
 * Created by PhpStorm.
 * User: Dedream
 * Date: 2018/3/29
 * Time: 11:38
 */

namespace app\index\controller;


use cart\Cart;
use think\Controller;

class CartController extends Controller
{
    public function addAction(){
        Cart::getInstance()->add(input('product_id'),input('buy_quantity'));
        return [
            'success'=>'ok'
        ];
    }

    #获取购物车中所有商品
    public  function infoAction(){

        $product_list = Cart::getInstance()->exportInfo();

        return  $product_list;

    }
}