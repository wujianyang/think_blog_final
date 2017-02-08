<?php
namespace Admin\Model;
use Admin\Model;
require_once C('ROOT').C('FUNC').'func.php';
class PhotoImgModel extends CommonModel{
    public $table='photo_img';
    public $table_alias='pi';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='photo';
    public $foreign_table2_alias='p';

    public function index(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%".$this->key."%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }elseif($this->keyItem=='photo_title'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="$this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.photo_id=$this->foreign_table2_alias.id";

        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='id';
        $arr_field[$this->table_alias.'.img_title']='img_title';
        $arr_field[$this->table_alias.'.img_src']='img_src';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->table_alias.'.photo_id']='photo_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->foreign_table2_alias.'.photo_title']='photo_title';

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='数据获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='数据获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    public function getCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%".$this->key."%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }elseif($this->keyItem=='photo_title'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="$this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.photo_id=$this->foreign_table2_alias.id";

        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='记录数获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='记录数获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    public function addData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata();
        if($vali_result===true){
            $add_data=$this->create_Data();
            $result=$this->data($add_data)->add();
            if($result!==false){
                $data['msg']='添加成功';
                $data['status']=1;
            }else{
                $data['msg']='添加失败';
                $data['status']=0;
            }
        }else{
            $data['msg']=$vali_result;
            if(!empty(C('ROOT').C('UPLOAD').$this->img_src) && file_exists(C('ROOT').C('UPLOAD').$this->img_src)){
                unlink(C('ROOT').C('UPLOAD_PATH').$this->img_src);
            }
        }
        unset($add_data);
        unset($result);
        return $data;
    }

    public function editData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata('edit');
        if($vali_result===true){
            $edit_data=$this->create_Data('edit');
            $result=$this->data($edit_data)->where(array('id'=>$this->id))->save();
            if($result!==false){
                $data['msg']='编辑成功';
                $data['status']=1;
            }else{
                $data['msg']='编辑失败';
                $data['status']=0;
            }
        }else{
            $data['msg']=$vali_result;
            if(!empty(C('ROOT').C('UPLOAD_PATH').$this->img_src) && file_exists(C('ROOT').C('UPLOAD').$this->img_src)){
                unlink(C('ROOT').C('UPLOAD_PATH').$this->img_src);
            }
        }
        unset($edit_data);
        unset($result);
        return $data;
    }

    //批量删除信息
    public function delData(){
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

    //验证数据
    public function setValidata($f=''){
        if(empty($this->img_title) && !preg_match("/^[\w]{4,}$/",$this->img_title)){
            return '相片名称验证失败';
        }
        if(empty($this->member_id) && !preg_match("/^[\d]{1,}$/",$this->member_id)){
            return "用户名验证失败";
        }else{
            if(!$this->isExistsMemberId($this->member_id)){
                return "用户名不存在";
            }
        }
        if(empty($this->photo_id) && !preg_match("/^[\d]{1,}$/",$this->photo_id)){
            return "相册名称验证失败";
        }else{
            if(!$this->isExistsPhotoId($this->photo_id)){
                return "相册名称不存在";
            }
        }

        if(isset($this->img_src) && !empty($this->img_src)) {
            $uploadConfig=array('name' => 'img_src',
                'maxSize'   =>  1000000,
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
        $arr['img_title']=$this->img_title;
        $arr['member_id']=$this->member_id;
        $arr['photo_id']=$this->photo_id;
        if(!empty($this->img_src)){
            $arr['img_src']=$this->img_src;
        }

        return $arr;
    }

    //判断是否已存在用户名
    public function isExistsMemberName(){
        $result=$this->where(array('member_name'=>$this->member_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }
}