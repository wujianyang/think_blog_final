<?php
namespace Home\Model;
use Home\Model;

class PhotoModel extends CommonModel{
    public $id;
    public $table='photo';
    public $table_alias='p';
    public $foreign_table='photo_img';
    public $foreign_table_alias='pi';
    public $foreign_table2='member';
    public $foreign_table2_alias='m';

    //分页配置属性
    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';

    /*
     * 获取用户首页的相册分类列表
     */
   public function getPhotoByMemberId(){
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
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="photo_id";
        $arr_field["$this->table_alias.photo_title"]="photo_title";
        $arr_field["COUNT($this->foreign_table_alias.id)"]="photo_count";
        $arr_join=array();
        $arr_join[]="RIGHT JOIN $this->table $this->table_alias ON $this->table_alias.id=$this->foreign_table_alias.photo_id";
        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册分类获取成功';
            $data['photo']=$result;
        }else{
            $data['msg']='用户相册分类获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    /*public function getCountByMemberId(){
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
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";
        $arr_join=array();
        $arr_join[]="RIGHT JOIN $this->table $this->table_alias ON $this->table_alias.id=$this->foreign_table_alias.photo_id";
        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->select();
        if($result!==false){
            if(count($result)>0){
                $data['status']=1;
                $data['msg']='用户相册分类数量获取成功';
                $data['count']=$result[0]['count'];
            }else{
                $data['status']=1;
                $data['msg']='用户相册分类数量没有数据';
            }
        }else{
            $data['msg']='用户相册分类数量获取失败';
        }

        return $data;
    }*/

    public function getPhotoList(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('id,photo_title')->where(array('member_id'=>$this->member_id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册分类获取成功';
            $data['photo']=$result;
        }else{
            $data['msg']='用户相册分类获取失败';
        }
        unset($result);
        return $data;
    }

    public function getMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('member_id')->where(array('id'=>$this->id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户ID获取成功';
            $data['member_id']=$result[0]['member_id'];
        }else{
            $data['msg']='用户ID获取失败';
        }
        unset($result);
        return $data;
    }

    public function getPhoto_op(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('id,photo_title')->where(array('id'=>$this->id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='当前相册分类获取成功';
            $data['photo_op']=$result[0];
        }else{
            $data['msg']='当前相册分类获取失败';
        }
        unset($result);
        return $data;
    }

    //个人相册列表
    public function personIndex(){
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
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="photo_id";
        $arr_field["$this->table_alias.photo_title"]="photo_title";
        $arr_field["COUNT($this->foreign_table_alias.id)"]="photo_count";
        $arr_join=array();
        $arr_join[]="RIGHT JOIN $this->table $this->table_alias ON $this->table_alias.id=$this->foreign_table_alias.photo_id";
        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册分类获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='用户相册分类获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    //个人相册列表数量
    public function personIndexCount(){
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
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";
        $arr_join=array();
        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户相册分类数量获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='用户相册分类数量获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    public function personDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $photoImg=D('photoImg');
        //获取相片数据
        $img_src=$photoImg->field('img_src')->where(array('photo_id'=>array('in',$this->id)))->select();
        $this->startTrans();
        //删除本表数据放在最后，$result和$result2顺序不能改变，否则因外键约束而导致删除失败
        $result=$photoImg->where(array('photo_id'=>array('in',$this->id)))->delete();
        $result2=$this->where(array('id'=>array('in',$this->id)))->delete();
        if($result!==false && $result2!==false){
            $this->commit();
            foreach($img_src as $img_src_arr){  //在空间中删除相片
                if(file_exists(C('ROOT').C('UPLOAD').$img_src_arr['img_src'])){
                    unlink(C('ROOT').C('UPLOAD').$img_src_arr['img_src']);
                }
            }
            $data['msg']='删除成功';
            $data['status']=1;
        }else{
            $this->rollback();
            $data['msg']='删除失败';
        }
        unset($photoImg);
        unset($img_src);
        unset($result2);
        unset($result);
        return $data;
    }

    public function setValidata($f=''){
        if(empty($this->photo_title) && !preg_match("/^.{4,}$/",$this->photo_title)){
            return "相册名称验证失败";
        }
        if(empty($this->member_id) && !preg_match("/^[\d]{1,}$/",$this->member_id)){
            return "用户名验证失败";
        }else{
            if(!$this->isExistsMemberId($this->member_id)){
                return "用户名不存在";
            }
        }
        return true;
    }

    public function create_Data($f=''){
        $arr=array();
        $arr['photo_title']=$this->photo_title;
        $arr['member_id']=$this->member_id;
        return $arr;
    }
}