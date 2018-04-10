<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class CategoryValidate extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        
        'id'=>'ID',
        'title'=>'分类',
        'parent_id'=>'上级分类',
        'sort'=>'排序',
        'is_used'=>'启用',
        'create_time'=>'创建时间',
        'update_time'=>'修改时间',
    ];

}