<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

//应用公共文件
//我们这个项目好多解，不是最优解答。尽量使用TP给我提供的方法做，达到熟悉TP框架的目标
function myUrl($route,$params=[],$order=[],$field=null){

    //只需要在这个方法修改配置
    \think\Config::set('url_common_param',true);
    $params['order_field'] = $field;

    //title asc title desc   site desc  site asc
    $params['order_type'] = (isset($order['order_field']) && isset($order['order_type'])&&$order['order_field']==$field && $order['order_type']=='asc' ) ?'desc':'asc'; //'asc';


    return url($route,$params);
}


function classOrder($order=[],$field=null){

    //代码严谨性验证。用户只传入了title没有传递desc|asc  ''

    if(!isset($order['order_field'])&&!isset($order['order_type'])) return '';

    if($order['order_field']==$field){
        return $order['order_type']=='asc' ?'desc':'asc';
    }
    else{
        return '';
    }



}