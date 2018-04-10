<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class LengthUnitValidate extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        
        'id'=>'',
        'title'=>'名称',
        'sort'=>'排序',
        'create_time'=>'创建时间',
        'update_time'=>'更新时间',
    ];

}