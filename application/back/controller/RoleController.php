<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;


use app\back\model\Action;
use app\back\validate\RoleValidate;
use app\back\model\Role;

use think\Controller;
use think\Db;
use think\Session;

class RoleController extends Controller
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
                    $data = Db::name('role')->find($id);  //查询出来数据
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

            $validate = new RoleValidate;
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
                $model = new Role;
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

        $model = new Role;
        //接收前台传递过来数组的参数
        $filter = input('filter/a',[]);
        $filter_order = [];

        #筛选条件
        

        #判断是否具有title条件
        $filter_title = input('filter_title','');
            if($filter_title!=''){
            $model->where('title','like','%'.$filter_title.'%');
            $filter['filter_title'] =$filter_title;
        }

        #判断是否具有description条件
        $filter_description = input('filter_description','');
            if($filter_description!=''){
            $model->where('description','like','%'.$filter_description.'%');
            $filter['filter_description'] =$filter_description;
        }
        //筛选条件

        //排序
//        $order = input('order/a');
//
//        if(!is_null($order)){
//            $model->order([$order['field']=>$order['type']]);// $model->order(['title'=>'asc']);
//
//        }
        # 排序
        $order_field = input('order_field', '');
        $order_type = input('order_type', 'asc');
        $order=[];
        if(''!=$order_field){
            $model->order([$order_field=>$order_type]);// $model->order(['title'=>'asc']);
            $order = compact('order_field', 'order_type');
            //['order_field'=>'title','order_type'=>'desc']
            //compact
            //把变量和变量的值给变成数组

        }
        //分页
        $limit =3;
        $list = $model->paginate($limit,false,[
            'query'=>array_merge($filter,$order)   //?????
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
        Role::destroy($selected);

        return $this->redirect('index');

    }

    public function setactionAction(){

        $request = request();
        $id = input('id');
        $role = Role::get($id);
        $this->assign('role',$role);

        //给一个角色添加动作，那么你要首先知道是那个角色。
        //要获取用户已经拥有的动作
        $owner_actions = Db::name('role_action')->where('role_id',$id)->column('action_id');

        if($request->isGet()){
            //获取全部权限
            $action_list = Action::order('id')->select();
            $this->assign('action_list',$action_list);
            //已有的动作
            $this->assign('checked_list',$owner_actions);
            return $this->fetch();
        }

        elseif($request->isPost()){
                //获取用户传递来的值

                $action = input('actions/a',[]);
                //更新role_action
                //如果品牌管理员这个角色以前有1,2 这两个权限
                //在传递的时候，1,3勾选住了，
                //第一个你要删除2，添加3权限，并且不要操作1权限。

                //计算两个数组的差集。
                //问题如果在1,2数组和，1，3数组，然后得到2
                $deletes  = array_diff($owner_actions,$action);//计算差集2

                //删除它没有勾选的
                Db::name('role_action')->where([
                    'role_id'=>$id,
                    'action_id'=>['in',$deletes]
                ])->delete();






                //如果你想要得到3
                $inserts =  array_diff($action,$owner_actions);//计算差集3

                //我想实现一个功能  $inserts数组
                //[3]   ===>  [['role_id'=>1,'action_id'=>3]]
                //批量插入数据怎么搞 //非常重要的点
                $rows = array_map(function ($action_id) use ($id){
                    return [
                        'role_id'=>$id,
                        'action_id'=>$action_id
                    ];
                }, $inserts);
                Db::name('role_action')->insertAll($rows);

                return $this->redirect('setaction',['id'=>$id]);


        }
    }


}