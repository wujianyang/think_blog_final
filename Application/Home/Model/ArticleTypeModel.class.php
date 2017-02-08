<?php
namespace Home\Model;
use Home\Model;

class ArticleTypeModel extends CommonModel{
    public $id;
    public $table='article_type';
    public $table_alias='at';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='article';
    public $foreign_table2_alias='a';
    public $page=1;
    public $pageSize=10;

    //根据文章类型ID获取用户ID
    public function getMemberIdById(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('member_id')->where(array('id'=>$this->id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户ID成功';
            $data['member_id']=$result[0]['member_id'];
        }else{
            $data['msg']='获取用户ID失败';
        }
        unset($result);
        return $data;
    }

    //文章列表页面-根据用户ID获取文章分类
    public function getArticleTypeByMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);

        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        if($this->page==0){
            $result=$this->alias($this->table_alias)->field('id,article_type_name')->where($arr_where)->select();
        }else{
            $result=$this->alias($this->table_alias)->field('id,article_type_name')->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        }
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章分类成功';
            $data['articleType']=$result;
        }else{
            $data['msg']='获取文章分类失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }

    /*public function getArticleTypeCountByMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $result=$this->alias($this->table_alias)->field("COUNT(id) AS count")->where($arr_where)->select();
        if($result!==false){
            if(count($result)>0){
                $data['status']=1;
                $data['msg']='获取文章分类总记录数成功';
                $data['count']=$result[0]['count'];
            }else{
                $data['status']=1;
                $data['msg']='获取文章分类总记录没有数据';
            }
        }else{
            $data['msg']='获取文章分类总记录数失败';
        }

        return $data;
    }*/

    //根据文章分类ID获取当前文章分类信息
    public function getArticleType_op(){
        $result=$this->field('id,article_type_name')->where(array('id'=>$this->id))->select();
        return $result[0];
    }

    //个人文章分类列表
    public function personIndex(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["$this->table_alias.id"]="article_type_id";
        $arr_field["$this->table_alias.article_type_name"]="article_type_name";
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $result=$this->alias($this->table_alias)->field($arr_field)->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章分类成功';
            $data['rows']=$result;
        }else{
            $data['msg']='获取文章分类失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }

    //个人文章分类数量
    public function personIndexCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        $result=$this->alias($this->table_alias)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章分类总记录数成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='获取文章分类总记录数失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }

    public function personDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $article=D('Article');
        $articleComment = D('ArticleComment');
        $articleId=$articleComment->field('id')->where(array('article_type_id'=>array('in',$this->id)))->select();
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
}