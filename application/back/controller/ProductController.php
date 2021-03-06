<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 9:35
 */

namespace app\back\controller;


use app\back\model\Category;
use app\back\validate\ProductValidate;
use app\back\model\Product;

//use CKSource\CKFinder\Image;
use think\Controller;
use think\Db;
use think\Session;
use think\Cache;
use think\Image ;

class ProductController extends Controller
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
                    $data = Db::name('product')->find($id);  //查询出来数据
            }
            else{

                $message  = Session::get('message');
                $data = Session::get('data');
            }

            //分配单位到前台页面view
            $this->assign('unit_list',Db::name('unit')->select());
            //分配品牌到前台页面view
            $this->assign('brand_list',Db::name('brand')->select());


            //分配品牌到前台页面view
            $tree = [];
            //存储数据
            if( !($tree =  Cache::get(self::CACHE_TREE_KEY))){
                //没有缓存
                $tree = (new Category())->getTree();
                Cache::set(self::CACHE_TREE_KEY,$tree);
            }
            $this->assign('category_list',$tree);
            $this->assign('message',$message);
            $this->assign('data',$data);
            return $this->fetch();
        }
        elseif ($request->isPost()){

            //post
            //数据入库
            //
            $post_data = input('post.');


            $validate = new ProductValidate;
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
                $model = new Product;
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

        $model = new Product;
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

        #判断是否具有upc条件
        $filter_upc = input('filter_upc','');
            if($filter_upc!=''){
            $model->where('upc','like','%'.$filter_upc.'%');
            $filter['filter_upc'] =$filter_upc;
        }

        #判断是否具有price条件
        $filter_price = input('filter_price','');
            if($filter_price!=''){
            $model->where('price','like','%'.$filter_price.'%');
            $filter['filter_price'] =$filter_price;
        }

        #判断是否具有status条件
        $filter_status = input('filter_status','');
            if($filter_status!=''){
            $model->where('status','like','%'.$filter_status.'%');
            $filter['filter_status'] =$filter_status;
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
        Product::destroy($selected);
        return $this->redirect('index');
    }

    public function uploadAction(){
      $file =   request()->file('file');
      //文件基本操作 PHP内容
      $info = $file->validate(['size'=>1*1024*1024,'ext'=>'jpg,png,gif'])->move(ROOT_PATH.'public/upload/product');
      if($info){
          //上传成功了
          //生成缩略图
          $image = Image::open(ROOT_PATH.'public/upload/product/'.$info->getSaveName());
          //缩略图
          $thumb_file = ROOT_PATH.'public/thumb/product/'.dirname($info->getSaveName()).'/thumb_'.$info->getFilename();
          //如果目录不存在，就创建目录

          if(!is_dir(dirname($thumb_file))){

              mkdir(dirname($thumb_file),0755,true);
          }

          $image->thumb(60,60,Image::THUMB_FILLED)->save($thumb_file);


          return [
              'image'=> ROOT_PATH.'public/upload/product/'.$info->getSaveName(),
              'image_thumb'=> $thumb_file ,
          ];
      }
      else{
          return [
              'error'=>$file->getError()
          ];
      }


    }


}