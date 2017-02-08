<?php
namespace Home\Model;
use Home\Model;

class PhotoImgModel extends CommonModel{
    public $id;
    public $table='photo_img';
    public $table_alias='pi';
    public $foreign_table='photo';
    public $foreign_table_alias='p';
    //分页配置属性
    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';

    //根据相册ID获取相片列表
    public function getPhotoImg(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //条件数组
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.photo_id"]=$this->photo_id;
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.photo_id=$this->foreign_table_alias.id";
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="id";
        $arr_field["$this->table_alias.img_title"]="img_title";
        $arr_field["$this->table_alias.img_src"]="img_src";
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册获取成功';
            $data['photoImg']=$result;
        }else{
            $data['msg']='用户相册获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //根据相册ID获取相片数量
    public function getPhotoImgCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //条件数组
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.photo_id"]=$this->photo_id;
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.photo_id=$this->foreign_table_alias.id";
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册记录数获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='用户相册记录数获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //个人删除相片
    public function personDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $img_src=$this->field('img_src')->where(array('id'=>array('in',$this->id)))->select();   //获取相片数据
        $result=$this->where(array('id'=>array('in',$this->id)))->delete();
        if($result!==false){
            foreach($img_src as $img_src_arr){  //在空间中删除相片
                if(file_exists(C('ROOT').C('UPLOAD').$img_src_arr['img_src'])){
                    unlink(C('ROOT').C('UPLOAD').$img_src_arr['img_src']);
                }
            }
            $data['msg']='删除成功';
            $data['status']=1;
        }else{
            $data['msg']='删除失败';
        }

        unset($img_src);
        unset($result);
        return $data;
    }

    //个人相片列表
    public function personIndex(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(IS_AJAX){
            $this->photo_id=I('post.photo_id');
        }else{
            $this->photo_id=I('get.photo_id');
        }
        //条件数组
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.photo_id"]=$this->photo_id;
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.photo_id=$this->foreign_table_alias.id";
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="id";
        $arr_field["$this->table_alias.img_title"]="img_title";
        $arr_field["$this->table_alias.img_src"]="img_src";
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='用户相册获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    //个人相片列表数量
    public function personIndexCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(IS_AJAX){
            $this->photo_id=I('post.photo_id');
        }else{
            $this->photo_id=I('get.photo_id');
        }
        //条件数组
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.photo_id"]=$this->photo_id;
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.photo_id=$this->foreign_table_alias.id";
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册记录数获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='用户相册记录数获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    //验证数据
    public function setValidata($f=''){
        if(empty($this->img_title) && !preg_match("/^[\w]{4,}$/",$this->img_title)){
            return '相片名称验证失败';
        }

        if(isset($this->img_src) && !empty($this->img_src)) {
            $uploadConfig=array('name' => 'img_src',
                'maxSize'   =>  10000000,
                'exts'      =>  array('png','jpg','jpeg','gif'),
                'rootPath'  =>  C('ROOT').C('UPLOAD_PATH'),
                'savePath'  =>  'photo_img/',
                'saveName'  =>  'photo_img_'.time(),
                'autoSub'   =>  false);
            $resultUpload=$this->upload($uploadConfig);
            if($resultUpload['status']==1 && $resultUpload['upload']['img_src']['savename']!=''){
                $this->img_src=$uploadConfig['savePath'].$resultUpload['upload']['img_src']['savename'];
            }else{
                return $resultUpload['msg'];
            }
        }elseif($f!='edit'){    //编辑时验证
            return "相片验证失败";
        }

        return true;
    }

    //创建提交数据数组
    public function create_Data($f=''){
        $arr=array();
        $arr['photo_id']=$this->photo_id;
        $arr['member_id']=$this->member_id;
        $arr['img_title']=$this->img_title;
        if(!empty($this->img_src)){
            $arr['img_src']=$this->img_src;
        }

        return $arr;
    }
}