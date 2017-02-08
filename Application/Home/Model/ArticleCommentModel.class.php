<?php
namespace Home\Model;
use Think\Model;
require_once C('ROOT').C('FUNC').'func.php';
class ArticleCommentModel extends Model{
    public $id;
    public $table='article_comment';
    public $table_alias='ac';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='article';
    public $foreign_table2_alias='a';

    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='id';
    public $com='eq';

    //获取文章详细页面的文章评论列表
    public function getArticleComment(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.article_id"]=$this->article_id;
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="article_comment_id";
        $arr_field["$this->table_alias.member_id"]="member_id";
        $arr_field["$this->foreign_table_alias.member_name"]="member_name";
        $arr_field["$this->foreign_table_alias.head_pic"]="head_pic";
        $arr_field["$this->table_alias.comment_content"]="comment_content";
        $arr_field["$this->table_alias.comment_time"]="comment_time";
        $arr_join=array();
        $arr_join[]="INNER JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            //htmlspecialchars_decode
            //转义字符转换
            $result=html_decode($result,'comment_content');
            $data['status']=1;
            $data['msg']='文章评论信息获取成功';
            $data['articleComment']=$result;
        }else{
            $data['msg']='文章评论信息获取失败';
        }
        unset($result);
        return $data;
    }

    //获取文章详细页面的文章评论列表数量
    public function getArticleCommentCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.article_id"]=$this->article_id;
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]='count';
        $arr_join=array();
        $arr_join[]="INNER JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['comment_count']=$result[0]['count'];
        }else{
            $data['msg']='文章评论数量获取失败';
        }
        unset($result);
        return $data;
    }

    public function comment(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_data=array();
        $arr_data["member_id"]=$this->member_id;
        $arr_data["article_id"]=$this->article_id;
        $arr_data["comment_content"]=$this->comment_content;
        $arr_data["comment_time"]=date("Y-m-d h:i:s",time());
        $result=$this->data($arr_data)->add();
        if($result!==false){
            $data['status']=1;
            $data['msg']='评论成功';
        }else{
            $data['msg']='评论失败';
        }
        unset($arr_data);
        unset($result);
        return $data;
    }

    //用户删除评论
    public function commentDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        $arr_where["id"]=array("IN",$this->id);
        $result=$this->where($arr_where)->delete();
        if($result!==false){
            $data['status']=1;
            $data['msg']='删除成功';
        }else{
            $data['msg']='删除失败';
        }
        unset($result);
        return $data;
    }


    //个人文章评论列表
    public function personIndex(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(IS_AJAX){
            $this->article_id=I('post.article_id');
        }else{
            $this->article_id=I('get.article_id');
            $result=$this->table($this->foreign_table2)->field('id as  article_id,title')->where(array('id'=>$this->article_id))->select();
            $data['article_id']=$result[0]['article_id'];
            $data['title']=$result[0]['title'];

        }
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.article_id"]=$this->article_id;
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="article_comment_id";
        $arr_field["$this->table_alias.member_id"]="member_id";
        $arr_field["$this->foreign_table_alias.member_name"]="member_name";
        $arr_field["$this->foreign_table_alias.head_pic"]="head_pic";
        $arr_field["$this->table_alias.comment_content"]="comment_content";
        $arr_field["$this->table_alias.comment_time"]="comment_time";
        $arr_join=array();
        $arr_join[]="INNER JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            //htmlspecialchars_decode
            //转义字符转换
            $result=html_decode($result,'comment_content');
            $data['status']=1;
            $data['msg']='文章评论信息获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='文章评论信息获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //个人文章评论数量
    public function personIndexCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(IS_AJAX){
            $this->article_id=I('post.article_id');
        }else{
            $this->article_id=I('get.article_id');
        }
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            if($this->keyItem=='member_name'){
                $arr_where["$this->foreign_table_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.article_id"]=$this->article_id;
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]='count';
        $arr_join=array();
        $arr_join[]="INNER JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='文章评论数量获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }
}