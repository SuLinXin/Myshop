<?php
/**
 * Created by PhpStorm.
 * User: Dedream
 * Date: 2018/3/28
 * Time: 10:52
 */

namespace app\api\Controller;

use app\api\model\Product;
use think\Controller;

class ProductController extends Controller
{
    public function listAction(){
        $product = new Product();
        //获取最新商品
        //首页要上架中的前4条数据
        $limit = input('limit',4);
        $type = input('type','all');//默认值代表获取所有的商品
        $status=1;
        switch ($type){
            case 'new':
                $status=1;
                break;
            case 'old':
                $status = 0;
                break;
            default:
                break;

        }

        $result = $product->where([
             'status'=>$status
         ])->paginate($limit);
        return json($result);
    }
}