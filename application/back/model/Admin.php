<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 11:33
 */

namespace app\back\model;


use think\Model;

class Admin extends Model
{
    //密码修改器
    public function setPasswordAttr($value){

        return md5(md5($value));

    }
}