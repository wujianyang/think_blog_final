<?php
namespace Admin\Model;
use Admin\Model;

class ArticleTypeModel extends CommonModel{
    public $table='article_type';
    public $table_alias='a';
    public $foreign_table='member';
    public $foreign_table_alias='m';

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
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='id';
        $arr_field[$this->table_alias.'.article_type_name']='article_type_name';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';

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
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

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

    //批量删除信息
    public function delData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $article=D('Article');
        $articleComment = D('ArticleComment');
        $articleId=$article->field('id')->where(array('article_type_id'=>array('in',$this->id)))->select();
        $articleId=array_column($articleId,'id');
        if(count($articleId)==0){
            $articleId='';
        }
        $this->startTrans();
        //删除本表数据放在最后，$result和$result2顺序不能改变，否则因外键约束而导致删除失败
        $result=$articleComment->where(array('article_id'=>array('in',$articleId)))->delete();
        $result2=$article->where(array('article_type_id'=>array('in',$this->id)))->delete();
        $result3=$this->where(array('id'=>array('in',$this->id)))->delete();
        if ($result!==false && $result2!==false && $result3!==false){
            $this->commit();
            $data['msg']='删除成功';
            $data['status']=1;
        }else{
            $this->rollback();
            $data['msg']='删除失败';
        }
        unset($article);
        unset($articleComment);
        unset($articleId);
        unset($result);
        unset($result2);
        unset($result3);
        return $data;
    }

    public function setValidata($f=''){
        if(empty($this->article_type_name) && !preg_match("/^.{2,}$/",$this->article_type_name)){
            return "文章类别名称验证失败";
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
        $arr['article_type_name']=$this->article_type_name;
        $arr['member_id']=$this->member_id;

        return $arr;
    }

    //获取文章类别ID和文章类别名称
    public function getArticleType(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field(array('id','article_type_name'))->where(array('member_id'=>$this->member_id))->select();
        if($result!==false){
            $data['status']=1;
            $data['rows']=$result;
        }else{
            $data['msg']='获取文章分类错误';
        }

        unset($result);
        return $data;
    }

}