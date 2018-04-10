<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class MemberValidate extends Validate
{
    protected $rule = [
        #自定义规则
    ];

    protected $field = [
        
        'id'=>'主键',
        'telephone'=>'手机号',
        'email'=>'邮箱',
        'username'=>'用户名',
        'password'=>'密码',
        'hash_str'=>'哈希值',
        'active_time'=>'激活时间',
        'status'=>'状态',
        'sort'=>'排序',
        'create_time'=>'添加时间',
        'update_time'=>'修改时间',
    ];

}