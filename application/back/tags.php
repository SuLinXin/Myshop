<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/20
 * Time: 15:23
 */
//执行任意一个控制器里面的方式都会触发的操作
return [
    'action_begin' => [
        'app\\back\\behavior\\CheckAuth'
    ]
];