<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class BrandValidate extends Validate
{
    protected $rule = [
        'title'=>'require|max:32|min:2',
        'site'=>'url',
        'sort'=>'integer'
    ];

    protected $field = [
        'title'=>'品牌名称',
        'site'=>'官网',
        'sort'=>'排序'
    ];

}