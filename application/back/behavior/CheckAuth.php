<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/20
 * Time: 15:27
 */

namespace app\back\behavior;


use priv\Privilege;
use think\Session;

class CheckAuth
{
    public function run(&$params){

        $request = request();
        //屏蔽一下特定的action
        $ext = [
            'admin'=>['login','fuck']
        ];
        //需要判断用户当前访问是那个控制器和那个Action
        $controller =  $request->controller();
        if(isset($ext[strtolower($controller)])){
            $action = $request->action();
          if(in_array($action,$ext[strtolower($controller)])){
              return;

          }

        }

        //检测用户是否登录了
        if(!Session::has('admin')){
            //记录用户之前的URL
            Session::set('route',$request->path());
            redirect('back/admin/login')->send();
            die;
        }

        //判断用户是否有权限。访问任意的一个动作。

        if(!Privilege::checkPriv($request->path())){

             redirect('back/admin/login',[],302,[
                  'message'=>'你没有权限'
             ])->send();
              die;
        }



    }
}
