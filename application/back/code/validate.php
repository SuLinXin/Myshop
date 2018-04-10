<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class %validate% extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        %label_list%
    ];

}