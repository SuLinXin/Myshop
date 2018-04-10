<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 14:11
 */

namespace app\back\validate;


use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'username'=>'require|unique:admin,username',
        'password'=>'require|min:6|max:18',
        'password-confirm'=>'require|min:6|max:18|confirm:password'
    ];

    protected $field = [
        'id'=>'ID',
        'username'=>'用户名',
        'password'=>'密码',
        'password-confirm'=>'确认密码',
        'sort'=>'排序',
        'create_time'=>'创建时间',
        'update_time'=>'修改时间',
    ];

    protected $scene = [
        'update'=>['username'],
        'repassword'=>['password','password-confirm']
    ];

}