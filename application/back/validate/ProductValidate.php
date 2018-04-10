<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class ProductValidate extends Validate
{
    protected $rule = [
        #自定义规则
        'title'=>'require',
        'inventory'=>'require|integer',
        'mininum'=>'integer'
    ];

    protected $field = [
        
        'id'=>'ID',
        'title'=>'名称',
        'upc'=>'条码',
        'image'=>'缩略图',
        'inventory'=>'库存',
        'mininum'=>'最小起售',
        'price'=>'售价',
        'price_orign'=>'原价',
        'is_shopping'=>'配送支持',
        'date_avaliable'=>'起送时间',
        'status'=>'',
        'brand_id'=>'品牌',
        'descrition'=>'描述',
        'category_id'=>'类别',
        'sort'=>'排序',
        'create_time'=>'',
        'update_time'=>'',
    ];

}