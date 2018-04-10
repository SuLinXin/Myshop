<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 11:33
 */

namespace app\back\model;


use think\Model;

class Product extends Model
{
    protected function setUpcAttr($value){
        if(is_null($value)||$value=='')
            return uniqid();
    }
}