<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;

use app\back\validate\AdminValidate;
use app\back\model\Admin;

use think\Controller;
use think\Db;
use think\Session;


class AdminController extends Controller
{

    public function setAction(){

        $id =  input('id');
        $this->assign('id',$id);
        $request = request(); //助手函数


        #写角色列表获取

        $this->assign('role_list',Db::name('role')->select());
        //还有带过去该用户已经有的角色
        $owner_roles = Db::name('admin_role')->where([
            'admin_id'=> $id
        ])->column('role_id');
        $this->assign('checked_roles',$owner_roles);



        ###





        if($request->isGet()){
            if(Session::get('message') =='' && Session::get('data')==''){
                $message = [];
                $data = [];
                if(!empty($id))
                    $data = Db::name('admin')->find($id);  //查询出来数据
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

            $validate = new AdminValidate;
            //check就是验证用户输入的数据是否合理
            //batch(批量验证)
            //我们需要判断用户传递过来的更新操作，还是新增操作。
            //验证场景的概念

            if(!is_null($id)){
                $validate->scene('update');
            }
            if( ! $validate->batch(true)->check($post_data)){
                return $this->redirect('set',[],302,[
                    'message'=> $validate->getError(),
                    'data'=>$post_data
                ]);
            }

            else{





                //如何修改数据
                $model = new Admin;
                if(isset($post_data['id'])){
                    $model = $model->find($post_data['id']);
                }

                $model->allowField(true)->save($post_data);  //插入数据？获取新插入数据的ID  $model->id
                if($request){

                    //获取到用户传递过来roles
                    $roles = input('roles/a',[]);

                    //去判断用户删除了哪些权限，之后在数据库删除
                    //$owner_roles  $roles
                    $deletes = array_diff($owner_roles,$roles);
                    Db::name('admin_role')->where([
                        'admin_id'=>$model->id,
                        'role_id'=>['in',$deletes]
                    ])->delete();

                    //去判断用户新增了哪些权限，洗后在数据库添加
                    $inserts = array_diff($roles,$owner_roles);

                    //[['admin_id'=>1,'role_id'=>1],[],[]]   [1,2,3]
                    $rows = array_map(function ($role_id) use ($model){
                        return [
                            'admin_id'=>$model->id,
                            'role_id'=>$role_id
                        ];
                    },$inserts);




                    Db::name('admin_role')->insertAll($rows);




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

    public function indexAction(){

        $model = new Admin;
        //接收前台传递过来数组的参数
        $filter = input('filter/a',[]);
        $filter_order = [];

        #筛选条件
        

        #判断是否具有username条件
        $filter_username = input('filter_username','');
            if($filter_username!=''){
            $model->where('username','like','%'.$filter_username.'%');
            $filter['filter_username'] =$filter_username;
        }
        //筛选条件

        //排序
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
            'query'=>array_merge($filter,$order)
        ]);
        //计算一下每页开始的那个ID
        //1-1,3
        //2-4,6（4）
        $start = $list->listRows() * ($list->currentPage()-1) +1;
        //最后一页数据  1~1,2  2~3,4  2~3
        $end = min($list->total(),$list -> currentPage() * $list->listRows());

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
        Admin::destroy($selected);

        return $this->redirect('index');

    }

    public function repasswordAction(){
        //需要进行2部操作。
        $request = request();
        $id = input('id');
        $this->assign('id',$id);
        if($request->isGet()){

            //重置密码，需要验证原密码。不需要。  管理员
            if(Session::get('message') =='' && Session::get('data')==''){
                $message = [];
                $data = [];
                if(!empty($id))
                    $data = Db::name('admin')->find($id);  //查询出来数据
            }
            else{
                $message  = Session::get('message');

                $data = Session::get('data');
                //$this->assign( 'id',$data['id']);
            }

            $this->assign('message',$message);
            $this->assign('data',$data);
            return $this->fetch();
        }
        elseif($request->isPost()){
            //post
            //数据入库
            //
            $post_data = input('post.');

            $validate = new AdminValidate;
            //check就是验证用户输入的数据是否合理
            //batch(批量验证)
            //我们需要判断用户传递过来的更新操作，还是新增操作。
            //验证场景的概念

            if(!is_null($id)){
                $validate->scene('repassword');
            }
            if( ! $validate->batch(true)->check($post_data)){
                return $this->redirect('repassword',['id'=>$id],302,[
                    'message'=> $validate->getError(),
                    'data'=>$post_data
                ]);
            }

            else{

                //如何修改数据
                $model = new Admin;
                if(isset($post_data['id'])){
                    $model = $model->find($post_data['id']);
                }

                $model->allowField(true)->save($post_data);
                if($request){

                    return $this->redirect('index');
                }
                else{

                    return $this->redirect('repassword',['id'=>$id]);
                }
            }
        }



    }

    public function loginAction(){



        $request = request();
        if($request->isGet()){
//            if(Session::get('admin')){
//                return $this->redirect('site/index');
//            }

            if(Session::get('message') =='' && Session::get('data')==''){
                $message = '';
                $data = [];

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
            $username = input('username');
            $password = input('password');

            #检测用户输入的用户名和密码是否正确。
            $condition=[
                'username'=>$username,
                'password'=>md5(md5($password))
            ];

            $admin = Admin::where($condition)->find();


            if($admin){
                //设置一下用户的登录状态，用户的登录状态，你在服务器端存储哪里比较好
                Session::set('admin',$admin);
                //跳转到首页
                //用户登录成功
                //我们定义一个路由，然后去判断session是否存储了用户之前访问地址
                //Session::pull('route') get&delete
                $route =Session::has('route')?Session::pull('route'):'site/index';
                return $this->redirect($route);
            }
            else{
                //还是登录页面，同时把错误信息返回到login-get方法里面
                return $this->redirect('login',[],302,[
                    'message'=>'管理员信息错误',
                    'data'=>input()
                ]);
            }

        }
    }

    public function logoutAction(){
        //删除session
        Session::delete('admin');
        return $this->redirect('login');

    }

}