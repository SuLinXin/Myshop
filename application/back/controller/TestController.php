<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;


use app\back\validate\TestValidate;
use app\back\model\Test;

use think\Controller;
use think\Db;
use think\Session;

class TestController extends Controller
{


    public function setAction(){

        $id =  input('get.id');
        $this->assign('id',$id);
        $request = request(); //助手函数
        if($request->isGet()){
            if(Session::get('message')==''&&Session::get('data')==''){
                $message = [];
                $data = [];
                if(!empty($id))
                    $data = Db::name('test')->find($id);  //查询出来数据
            }
            else{

                $message  = Session::get('message');
                $data = Session::get('data');
            }

            $this->assign('message',$message);
            $this->assign('data',$data);
            return $this->fetch();
        }
        elseif ($request->isPost()){

            //post
            //数据入库
            //
            $post_data = input('post.');

            $validate = new TestValidate;
            //check就是验证用户输入的数据是否合理
            //batch(批量验证)
            if( ! $validate->batch(true)->check($post_data)){
                return $this->redirect('create',[],302,[
                    'message'=> $validate->getError(),
                    'data'=>$post_data
                ]);
            }
            else{

                //如何修改数据
                $model = new Test;
                if(isset($post_data['id'])){
                    $model = $model->find($post_data['id']);
                }


                $model->save($post_data);  //插入数据？
                if($request){
                    return $this->redirect('index');
                }
                else{
                    return $this->redirect('create');
                }
            }

        }
        else{

        }
        return $this->fetch();

    }

    public  function  indexAction(){

        $model = new Test;
        //接收前台传递过来数组的参数
        $filter = input('filter/a',[]);
        $filter_order = [];

        #筛选条件
        %where_list%
        //筛选条件

        //排序
        $order = input('order/a');

        if(!is_null($order)){
            $model->order([$order['field']=>$order['type']]);// $model->order(['title'=>'asc']);

        }
        //分页
        $limit =3;
        $list = $model->paginate($limit,false,[
            'query'=>array_merge($filter_order )   //?????
        ]);
        //计算一下每页开始的那个ID
        //1-1,3
        //2-4,6（4）
        $start = $list->listRows() * ($list->currentPage()-1) +1;
        //最后一页数据  1~1,2  2~3,4  2~3
        $end = min($list->total(),$list->currentPage()*$list->listRows());

        $this->assign('start',$start);
        $this->assign('end',$end);
        $this->assign('filter',$filter);
        $this->assign('order',$order);
        $this->assign('filter_order',$filter_order);
        $this->assign('list',$list);
        //等等的内容

        return $this->fetch();
    }

    public  function  multiAction(){
        $selected = input('selected/a',[]);
        if(empty($selected)){
            return $this->redirect('index');
        }

        //批量删除
        Test::destroy($selected);

        return $this->redirect('index');

    }


}