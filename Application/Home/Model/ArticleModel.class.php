<?php
namespace Home\Model;
use Home\Model;
require_once C('ROOT').C('FUNC').'func.php';
class ArticleModel extends CommonModel{
    public $id;
    public $table='article';
    public $table_alias='a';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='article_type';
    public $foreign_table2_alias='at';
    public $foreign_table3='article_comment';
    public $foreign_table3_alias='ac';

    //分页和搜索配置信息
    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='id';
    public $com='eq';

    //获取首页文章列表(获取访问量前10的文章)
    public function indexArticle(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //条件数组
        $arr_where=array();
        $arr_where["$this->table_alias.hitnum"]=array('gt','0');
        //字段数组
        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='article_id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field[$this->table_alias.'.content']='content';
        $arr_field[$this->table_alias.'.hitnum']='hitnum';
        $arr_field[$this->table_alias.'.create_time']='create_time';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->table_alias.'.article_type_id']='article_type_id';
        $arr_field[$this->foreign_table2_alias.'.article_type_name']='article_type_name';
        $arr_field["COUNT($this->foreign_table3_alias.id)"]='article_comment_count';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->table $this->table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table3 $this->foreign_table3_alias ON $this->table_alias.id=$this->foreign_table3_alias.article_id";

        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->order("$this->table_alias.hitnum desc")->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='文章列表获取成功';
            $data['article']=$result;
        }else{
            $data['msg']='文章列表获取失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //根据条件搜索文章列表
    public function getArticleByTitle(){
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
        //字段数组date_format(create_time,'%Y-%m-%d') as create_time
        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='article_id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field["date_format($this->table_alias.create_time,'%Y-%m-%d')"]='create_time';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户文章列表获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='用户文章列表获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //根据条件搜索文章列表数量
    public function getArticleCountByTitle(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //条件数组
        $arr_where=array();
        if($this->key!=''){
            $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
        }
        //字段数组
        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]='count';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";

        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户文章列表数量获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='用户文章列表数量获取失败';
        }
        unset($arr_where);
        unset($arr_field);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //获取文章标题
    public function getTitleByArticleId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        $arr_where["id"]=$this->id;
        $result=$this->field("title")->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章标题成功';
            $data['title']=$result[0]['title'];
        }else{
            $data['msg']='获取文章标题失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }


    //根据文章ID获取详细文章信息
    public function getArticleByArticleId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='article_id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field[$this->table_alias.'.content']='content';
        $arr_field[$this->table_alias.'.hitnum']='hitnum';
        $arr_field[$this->table_alias.'.create_time']='create_time';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->table_alias.'.article_type_id']='article_type_id';
        $arr_field[$this->foreign_table2_alias.'.article_type_name']='article_type_name';
        $arr_field["COUNT($this->foreign_table3_alias.id)"]='article_comment_count';

        $arr_where=array();
        $arr_where["$this->table_alias.id"]=$this->id;

        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->table $this->table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table3 $this->foreign_table3_alias ON $this->table_alias.id=$this->foreign_table3_alias.article_id";

        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='文章信息获取成功';
            //解析文章内容
            $result=html_decode($result,'content');
            $data['article']=$result[0];
        }else{
            $data['msg']='文章信息获取失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    /*
     * 获取用户首页的文章列表信息
     */
    public function getArticleByMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //条件数组
        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%$this->key%";
            }
            if($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }

        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        //字段数组
        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='article_id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field[$this->table_alias.'.content']='content';
        $arr_field[$this->table_alias.'.hitnum']='hitnum';
        $arr_field[$this->table_alias.'.create_time']='create_time';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->table_alias.'.article_type_id']='article_type_id';
        $arr_field[$this->foreign_table2_alias.'.article_type_name']='article_type_name';
        $arr_field["COUNT($this->foreign_table3_alias.id)"]='article_comment_count';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->table $this->table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table3 $this->foreign_table3_alias ON $this->table_alias.id=$this->foreign_table3_alias.article_id";

        $result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户文章列表获取成功';
            $data['article']=$result;
        }else{
            $data['msg']='用户文章列表获取失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }
    //根据文章分类ID获取文章列表
    public function getArticleByArticleTypeId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field("id,title,date_format(create_time,'%Y-%m-%d') as create_time")->where(array('article_type_id'=>$this->article_type_id))->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章列表成功';
            $data['article']=$result;
        }else{
            $data['msg']='获取文章列表失败';
        }
        unset($result);
        return $data;
    }

    //根据文章分类ID获取记录条数
    public function getCountByArticleId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field("COUNT(id) as count")->where(array('article_type_id'=>$this->article_type_id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章记录总数成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='获取文章记录总数失败';
        }
        unset($result);
        return $data;
    }

    /*//根据用户ID获取记录条数
    public function getCountByMemberId(){
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
            }elseif($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;

        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";

        //$result=$this->table(array("$this->foreign_table"=>$this->foreign_table_alias))->field("COUNT(id) as count")->join($arr_join)->where($arr_where)->group("$this->table_alias.id")->select();
        $result=$this->alias($this->table_alias)->join($arr_join)->field("COUNT($this->table_alias.id) as count")->where($arr_where)->select();
        if($result!==false){
            if(count($result)>0){
                $data['status']=1;
                $data['msg']='获取文章记录总数成功';
                $data['count']=$result[0]['count'];
            }else{
                $data['status']=1;
                $data['msg']='没有数据';
            }
        }else{
            $data['msg']='获取文章记录总数失败';
        }

        return $data;
    }*/

    //获取用户文章分类信息
    public function getArticleTypeByMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["$this->foreign_table2_alias.id"]='article_type_id';
        $arr_field["$this->foreign_table2_alias.article_type_name"]='article_type_name';
        $arr_field["COUNT($this->table_alias.id)"]='article_count';

        $arr_where=array();
        $arr_where["$this->foreign_table2_alias.member_id"]=$this->member_id;

        $arr_join=array();
        $arr_join[]="RIGHT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->foreign_table2_alias.id=$this->table_alias.article_type_id";

        $result=$this->alias($this->table_alias)->field($arr_field)->join($arr_join)->where($arr_where)->group("$this->foreign_table2_alias.id")->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户文章分类成功';
            $data['article_type']=$result;
        }else{
            $data['msg']='获取用户文章分类失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //获取热门文章排行
    public function getHotArticle(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["id"]="article_id";
        $arr_field["title"]="title";
        $arr_field["hitnum"]="hitnum";
        $arr_field["date_format(create_time,'%Y-%m-%d')"]="create_time";
        $arr_where=array();
        $arr_where["member_id"]=$this->member_id;
        $arr_where["hitnum"]=array('gt','0');
        $result=$this->field($arr_field)->where($arr_where)->order(array("hitnum"=>"desc"))->limit($this->pageSize)->page($this->page)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户热门文章成功';
            $data['rows']=$result;
        }else{
            $data['msg']='获取用户热门文章失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($result);
        return $data;
    }

    //获取热门文章排行记录数
    public function getHotArticleCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where["member_id"]=$this->member_id;
        $arr_where["hitnum"]=array('gt','0');
        $result=$this->field('count(id) as count')->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户热门文章记录数成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='获取用户热门文章记录数失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }

    public function accessArticle(){
        $this->where(array('id'=>$this->id))->setInc('hitnum',1);
    }

    //判断文章ID是否存在
    public function isExistsArticleId(){
        $result=$this->field('id')->where(array('id'=>$this->id))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //个人文章列表
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
            if($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }

        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;
        //字段数组
        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='article_id';
        $arr_field[$this->table_alias.'.title']='title';
        $arr_field[$this->table_alias.'.content']='content';
        $arr_field[$this->table_alias.'.hitnum']='hitnum';
        $arr_field[$this->table_alias.'.create_time']='create_time';
        $arr_field[$this->table_alias.'.member_id']='member_id';
        $arr_field[$this->table_alias.'.article_type_id']='article_type_id';
        $arr_field[$this->foreign_table2_alias.'.article_type_name']='article_type_name';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";


        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户文章列表获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='用户文章列表获取失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //个人文章列表数量
    public function personIndexCount(){
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
            }elseif($this->keyItem=='article_type_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        $arr_where["$this->table_alias.member_id"]=$this->member_id;

        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]='count';
        //多表查询条件数组
        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.article_type_id=$this->foreign_table2_alias.id";


        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取文章记录总数成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='获取文章记录总数失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    public function personDel(){
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

        if(strlen($this->content)<10){
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