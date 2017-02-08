<?php
namespace Admin\Model;
use Admin\Model;

class ArticleModel extends CommonModel{
    public $table='article';
    public $table_alias='a';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='article_type';
    public $foreign_table2_alias='at';

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
            }elseif($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="$this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";

        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field[$this->table_alias.'.content']='content';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->foreign_table2_alias.'.article_type_name']='article_type_name';
        $arr_field[$this->table_alias.'.hitnum']='hitnum';
        $arr_field[$this->table_alias.'.create_time']='create_time';

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $result=html_decode($result,'content');
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
            }elseif($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }

        $arr_join=array();
        $arr_join[]="$this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="$this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";

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

        $articleComment = D('ArticleComment');

        //删除本表数据放在最后，顺序不能改变，否则因外键约束而导致删除失败
        $result = $articleComment->where(array('article_id' => array('in', $this->id)))->delete();
        $result2 = $this->where(array('id' => array('in', $this->id)))->delete();
        if ($result!==false && $result2!==false){
            $this->commit();
            $data['status'] = 1;
            $data['msg'] = '删除成功';
        }else{
            $this->rollback();
            $data['msg'] = '删除失败';
        }

        unset($articleComment);
        unset($result);
        unset($result2);
        return $data;
    }

    //获取文章标题信息
    public function getArticleTitle(){
        $arr_where["id"]=array('eq',$this->id);
        $result=$this->field(array('id','title'))->where($arr_where)->select();
        return $result;
    }

    //验证信息数据
    public function setValidata($f=''){
        if(empty($this->title) && !preg_match("/^.{4,}$/",$this->title)){
            return '文章标题验证失败';
        }

        if(empty($this->member_id) && !preg_match("/^[\d]{1,}$/",$this->member_id)){
            return '文章作者验证失败';
        }else{
            if(!$this->isExistsMemberId($this->member_id)){
                return '该作者不存在';
            }
        }

        if(empty($this->article_type_id) && !preg_match("/^[\d]{1,}$/",$this->article_type_id)){
            return '文章类型验证失败';
        }else{
            if(!$this->isExistsArticleTypeId($this->article_type_id)){
                return '该文章类型不存在';
            }
        }

        if(strlen($this->content)<4){
            return '文章内容验证失败';
        }else{
            $this->content=htmlspecialchars_decode($this->content);
        }

        if($f!='edit'){
            $this->hitnum=0;
            $this->create_time=date("Y-m-d h:i:s",time());
        }

        return true;
    }

    //创建提交信息数据数组
    public function create_Data($f=''){
        $arr=array();
        $arr['title']=$this->title;
        $arr['member_id']=$this->member_id;
        $arr['article_type_id']=$this->article_type_id;
        $arr['content']=$this->content;
        if($f!='edit'){
            $arr['hitnum']=$this->hitnum;
            $arr['create_time']=$this->create_time;
        }

        return $arr;
    }
}