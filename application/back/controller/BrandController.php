<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;


use app\back\validate\BrandValidate;
use think\Controller;
use app\back\model\Brand as BrandModel;
use think\Db;
use think\Session;

class BrandController extends Controller
{
    //添加操作
    public function createAction(){

        $request = request(); //助手函数
        if($request->isGet()){
            if(Session::get('message')==''&&Session::get('data')==''){
                $message = [];
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
            //post
            //数据入库
            //
           $post_data = input('post.');
           $brand_validate = new BrandValidate();
           //check就是验证用户输入的数据是否合理
            //batch(批量验证)
           if( !$brand_validate->batch(true)->check($post_data)){
              return $this->redirect('create',[],302,[
                  'message'=>$brand_validate->getError(),
                  'data'=>$post_data
              ]);
           }
          else{


           $result = new BrandModel();
            $result->save($post_data);
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

    }

    public function setAction(){

        $id =  input('id');
        $this->assign('id',$id);
        $request = request(); //助手函数
        if($request->isGet()){
            if(Session::get('message')==''&&Session::get('data')==''){
                $message = [];
                $data = [];
                if(!empty($id))
                    $data = Db::name('brand')->find($id);  //查询出来数据
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
            $post_data =$request->post();
            $post_data['logo'] = $request->file('logo');


            $brand_validate = new BrandValidate();
            //check就是验证用户输入的数据是否合理
            //batch(批量验证)
            if( !$brand_validate->batch(true)->check($post_data)){
                return $this->redirect('create',[],302,[
                    'message'=>$brand_validate->getError(),
                    'data'=>$post_data
                ]);
            }
            else{

                //转存文件
                if($post_data['logo']){
                    $info = $post_data['logo']->move(ROOT_PATH.'public/upload/brand');
                    $post_data['logo'] = $info->getSaveName();
                }


                //如何修改数据
                $model = new BrandModel();
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

    public function updateAction(){


            return $this->fetch();


    }


    public  function  indexAction(){
        $model = new BrandModel();
        //接收前台传递过来数组的参数
        $filter = [];


        #判断是否具有title条件
        $filter_title = input('filter_title','');
        if($filter_title!=''){
            $model->where('title','like','%'.$filter_title.'%');
            $filter['filter_title'] =$filter_title;
        }

        #判断是否具有site条件
        $filter_site = input('filter_site','');
        if(''!=$filter_site){
            $model->where('site','like','%'.$filter_site.'%');
            $filter['filter_site'] =$filter_site;
        }


        //筛选条件


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
            'query'=>array_merge($filter,$order)
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
        $this->assign('filter',$filter);
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
        BrandModel::destroy($selected);

       return $this->redirect('index');

    }


}