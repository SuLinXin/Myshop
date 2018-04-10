<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/14
 * Time: 11:26
 */

namespace app\back\controller;


use think\Config;
use think\Controller;
use think\Db;

class MakeController extends Controller
{

    protected $input = [];
    protected $label = [];
    public function  tableAction(){
        return $this->fetch();
    }


    public function infoAction(){
       $table =  input('table');
       //获取备注
       //SELECT TABLE_COMMENT FROM information_schema.`TABLES` WHERE TABLE_SCHEMA='myshop' AND TABLE_NAME='shop_member'
       $sql = "SELECT TABLE_COMMENT FROM information_schema.`TABLES` WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
       $table_schema = Config::get('database.database');
       $table_name = Config::get('database.prefix').$table;

       $tb_result = Db::query($sql,[$table_schema,$table_name]);
        //SELECT TABLE_NAME,COLUMN_NAME,COLUMN_COMMENT FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA='myshop' AND TABLE_NAME='shop_member'

        $sql = "SELECT COLUMN_NAME,COLUMN_COMMENT FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA=? AND TABLE_NAME=?";
        $col = Db::query($sql,[$table_schema,$table_name]);


        return [
          'comment'=>$tb_result[0]['TABLE_COMMENT'],
          'field'=>$col,
          'table_name'=>$table
       ];
    }


    public  function  generateAction(){
        $this->input['table'] = input('table');
        $this->input['comment'] = input('comment');
        $this->input['fields'] = input('fields/a');

        //创造一个模板
        //就是凭空创造出 %validate%   MemberValidate   %model%  Member  %controller% MemberController
        $this->createController();
        $this->createModel();
        $this->createValidate();
        $this->createViewIndex();
        $this->createViewSet();

    }

    protected function createViewSet(){
        //字段输入框
        $table_input_list = '';
        $template_input = file_get_contents(APP_PATH.'back/code/viewSetInput.html');

        foreach ($this->input['fields'] as $field){


            //如果选择设置，就形成表单，如果没选设置，就不生成表单
            if(isset($field['set'])){
                $search = ['%field%','%label%','%required%'];
                $require = '';
                if(isset($field['require'])){
                    $require = 'required';
                }
                $replace = [$field['name'],$field['comment'],$require];
                $table_input_list.=str_replace($search,$replace,$template_input);
            }
            else{

            }

        }

        //大替换，整体替换。

        $template = APP_PATH.'back/code/viewSet.html';
        $search = ['%table_title%',' %table_input_list%'];
        $replace = [$this->input['comment'],$table_input_list];

        $file = APP_PATH.'back/view/'.$this->input['table'].'/set.html';

        $this->replace($template,$search,$replace,$file);

        echo '添加修改视图已经生成'.$file.'<br>';




    }

    protected function createViewIndex(){
        //我们要生成一堆代码  member ---   view/member/index.html
        //搜索区域
        $search_field_list = '';
        $template_search = file_get_contents(APP_PATH.'back/code/viewIndexSearchField.html');

        //表头区域
        $table_head_list = '';
        $template_head = file_get_contents(APP_PATH.'back/code/viewIndexTableHead.html');
        $template_head_order = file_get_contents(APP_PATH.'back/code/viewIndexTableHeadOrder.html');

        //表格主体区域
        $table_data = '';
        $template_data = file_get_contents(APP_PATH.'back/code/viewIndexTableData.html');

        //所有的这三部分内容，都是基于用户在前台勾选的那个复选框来的
        //列表，排序，搜索
        //循环所有的字段
        $cols_number = 1;
        foreach ($this->input['fields'] as $field){
            //拼接字符串，搜索区域
            if(isset($field['search'])){
                $search = ['%field%','%label%'];
                $replace = [$field['name'],$field['comment']];
                $search_field_list.=str_replace($search,$replace,$template_search);
            }
            //拼接字符串,表头区域
            if(isset($field['index'])){
                //这里面有两种情况，第一种需要排序，第二种不需要排序。而他们区分的条件
                //$field['sort']
                if(isset($field['sort'])){
                    $template_head = $template_head_order;
                }
                else{

                }

                $search = ['%field%','%label%'];
                $replace = [$field['name'],$field['comment']];
                $table_head_list.=str_replace($search,$replace,$template_head);


                $search = ['%field%'];
                $replace = [$field['name']];
                $table_data.=str_replace($search,$replace,$template_data);
            }
            //拼接字符串，表主体部分。
            $cols_number++;



        }


        //大替换，整体替换。

        $template = APP_PATH.'back/code/viewIndex.html';
        $search = ['%table_title%',' %search_field_list%','%table_head_list%',' %table_data%','%cols_number%'];
        $replace = [$this->input['comment'],$search_field_list,$table_head_list,$table_data,$cols_number];

        $file = APP_PATH.'back/view/'.$this->input['table'].'/index.html';

       $this->replace($template,$search,$replace,$file);

        echo '视图已经生成'.$file.'<br>';


    }

    //公用的一个替换方法
    protected function replace($template,$search,$replace,$file){

        $template_content = file_get_contents($template);
        $content = str_replace($search,$replace,$template_content);

        //首先判断一下要生成的文件是否存在
        //找到这个文件
        $path = dirname($file);//获取到文件的目录
        if(!is_dir($path)){
            mkdir($path);  //如果目录不存在，创建目录
        }

        //写入文件
        file_put_contents($file,$content);




    }

    protected function createValidate(){
        //获取验证器的名称
        $validate = $this->mkValidate();
        $label_list = '';
        //N个字段，
        $template = APP_PATH.'back/code/validateField.php';
        $template_content = file_get_contents($template);

        foreach ($this->input['fields'] as $field){
            $search = ['%field%','%comment%'];
            $replace = [$field['name'],$field['comment']];

            $label_list .= str_replace($search,$replace,$template_content);
        }



        //替换文件
        $template = APP_PATH.'back/code/validate.php';
        $search = ['%validate%','%label_list%'];

        $replace = [$validate,$label_list];
        $file = APP_PATH.'back/validate/'.$validate.'.php';

        $this->replace($template,$search,$replace,$file);

        echo '验证器已经生成了'.$file.'<br>';

    }



    public function createModel(){
        //模型的名称
        $model = $this->mkModel();  //member -Member

        //模板替换
        $template = APP_PATH.'back/code/model.php';
        $search = ['%model%'];
        $replace = [$model];
        $file = APP_PATH.'back/model/'.$model.'.php';  //  back/model/Member.php
        $this->replace($template,$search,$replace,$file);

        echo '模型已经生成了'.$file.'<br>';

    }

    //生成控制器
    public  function  createController(){
        //就是凭空创造出 %validate%   MemberValidate   %model%  Member  %controller% MemberController
        $model  = $this->mkModel();

        $controller = $this->mkController();

        $validate = $this->mkValidate();

        //替换查询部分内容
        $where_list = '';

        //文件读取
        $template = file_get_contents(APP_PATH.'back/code/indexWhere.php');

        //遍历字段，找到需要搜索，使用子母版。

        foreach ($this->input['fields'] as $field){
            ## 如果不需要搜索字段，不要进行拼接
            if(!isset($field['search'])) continue;
            $search = ['%field%'];
            $replace = [$field['name']];
            $where_list .= str_replace($search,$replace,$template);
        }


        //文件读取
        $template = file_get_contents(APP_PATH.'back/code/controller.php');


        //需要替换里面的内容
        $search = ['%model%','%controller%','%validate%','%where_list%','%table%'];
        $replace = [$model,$controller,$validate,$where_list,$this->input['table']];
        $content = str_replace($search,$replace,$template);  //新的字符串
        //重新生成一个文件
        $file = APP_PATH.'back/controller/'.$controller.'.php';

        file_put_contents($file,$content);
        echo '控制器已经生了',$file,"<br/>";


    }


    public function mkModel(){
       $table =  $this->input['table'];
       //member_user   ['member','user']  MemberUser
        if(!isset($this->label['model'])){
            $this->label['model'] = implode(array_map('ucfirst',explode('_',  $table)));
        }

        return $this->label['model'];
    }

    public function mkController(){
        $table =  $this->input['table'];
        //member_user   ['member','user']  MemberUser
        if(!isset($this->label['controller'])){
            $this->label['controller'] = implode(array_map('ucfirst',explode('_',  $table))).'Controller';
        }

        return $this->label['controller'];
    }

    public function mkValidate(){
        $table =  $this->input['table'];
        //member_user   ['member','user']  MemberUser
        if(!isset($this->label['validate'])){
            $this->label['validate'] = implode(array_map('ucfirst',explode('_',  $table))).'Validate';
        }

        return $this->label['validate'];
    }
}