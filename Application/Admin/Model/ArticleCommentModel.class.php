<?php
namespace Admin\Model;
use Admin\Model;

class ArticleCommentModel extends CommonModel{
    public $table='article_comment';
    public $table_alias='ac';
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
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->table_alias.'.comment_content']='comment_content';
        $arr_field[$this->table_alias.'.comment_time']='comment_time';

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

    //获取文章标题信息
    public function getArticleTitle(){
        $article=D('Article');
        $article->id=$this->article_id;
        $articleInfo=$article->getArticleTitle();
        return $articleInfo;
    }
}