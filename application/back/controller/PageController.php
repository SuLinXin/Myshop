<?php
/**
 * Created by PhpStorm.
 * User: Dedream
 * Date: 2018/3/28
 * Time: 8:36
 */

namespace app\back\controller;

use think\Controller;

class PageController extends Controller
{
    public function indexAction(){
        //通过后台生成静态页面。
        //加载某些内容  从哪里来的问题
        $content = $this->fetch('index@site/index');
        //写入某些内容  往哪里去的问题
        file_put_contents(ROOT_PATH.'public/html/index.html',$content);

        echo '生成成功';
    }
}