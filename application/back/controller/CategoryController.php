<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;


use app\back\validate\CategoryValidate;
use app\back\model\Category;

use think\Cache;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Session;

class CategoryController extends Controller
{
    //如何声明一个常亮
    const CACHE_TREE_KEY = 'category_tree';

    public function setAction(){
        $id =  input('id');
        $this->assign('id',$id);
        $request = request(); //助手函数
        if($request->isGet()){
            if(Session::get('message')==''&&Session::get('data')==''){
                $message = [];
                $data = [];
                if(!empty($id))
                    $data = Db::name('category')->find($id);  //查询出来数据
            }
            else{

                $message  = Session::get('message');
                $data = Session::get('data');
            }

            //分配一下全部的category
            $this->assign('category_list',(new Category())->getTree());
            $this->assign('message',$message);
            $this->assign('data',$data);
            return $this->fetch();
        }
        elseif ($request->isPost()){

            //post
            //数据入库
            //
            $post_data = input('post.');

            $validate = new CategoryValidate;
            //check就是验证用户输入的数据是否合理
            //batch(批量验证)

            if( ! $validate->batch(true)->check($post_data)){
                return $this->redirect('set',[],302,[
                    'message'=> $validate->getError(),
                    'data'=>$post_data
                ]);
            }
            else{
                //如何修改数据
                $model = new Category;
                if(isset($post_data['id'])){
                    $model = $model->find($post_data['id']);
                }

                $model->save($post_data);  //插入数据？
                if($request){
                    return $this->redirect('index');
                }
                else{
                    return $this->redirect('set');
                }
            }

        }
        else{

        }
        return $this->fetch();

    }

    public  function  indexAction(){

        $model = new Category;

        //Cache::rm(self::CACHE_TREE_KEY);  删除redis数据
        //存储数据
        if( !($tree =  Cache::get(self::CACHE_TREE_KEY))){
            //没有缓存
            $tree = $model->getTree();
            Cache::set(self::CACHE_TREE_KEY,$tree);
        }
        //$tree  = Cache::get(self::CACHE_TREE_KEY);

        $this->assign('rows', $tree);

        return $this->fetch();
    }

    public  function  multiAction(){
        $selected = input('selected/a',[]);
        if(empty($selected)){
            return $this->redirect('index');
        }

        //批量删除
        Category::destroy($selected);

        return $this->redirect('index');

    }


}