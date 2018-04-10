<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/20
 * Time: 14:22
 */

namespace app\back\controller;


use think\Controller;

class SiteController extends Controller
{
        public function indexAction(){
//            $user = session('admin')->toArray();
//            $username =  $user['username'];
//            $this->assign('username',$username);


            return $this->fetch();
        }
}