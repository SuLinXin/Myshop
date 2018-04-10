<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class ActionValidate extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        
        'id'=>'ID',
        'title'=>'名称',
        'rule'=>'动作路径',
        'sort'=>'排序',
        'create_time'=>'创建时间',
        'update_time'=>'修改时间',
    ];

}