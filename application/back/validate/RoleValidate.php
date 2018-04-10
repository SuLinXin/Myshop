<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        
        'id'=>'ID',
        'title'=>'名称',
        'description'=>'描述',
        'is_super'=>'是否为超管',
        'sort'=>'排序',
        'create_time'=>'创建时间',
        'update_time'=>'修改时间',
    ];

}