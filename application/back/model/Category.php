<?php
/**
 * Created by PhpStorm.
 * User: zhen
 * Date: 2018/3/12
 * Time: 11:33
 */

namespace app\back\model;


use think\Model;
use think\Db;

class Category extends Model
{
    #获取一个数据树的代码
    public function getTree(){
        #获取全部分类
        $rows = Db::name('category')->select();
        #带有层级关系的树.
        $tree = $this->tree($rows,0,0);
        return $tree;

    }

    //递归的算法
    //层级深度
    protected function tree($rows,$id=0,$level=0){
        static  $tree=[];
        foreach ($rows as $row){
            if($id==$row['parent_id']){
                $row['level']=$level;
                $tree[] = $row;
                $this->tree($rows,$row['id'],$level+1);
            }
        }

        return $tree;

    }
}