<?php
namespace Home\Model;
use Think\Model;

class FriendsModel extends Model{
    public $id;
    public $table='friends';
    public $table_alias='f';
    public $foreign_table='member';
    public $foreign_table_alias='m';

    //分页配置信息
    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';

    /*
     * 判断用户是否已关注，用户首页
     * */
    public function isFocus(){
        $arr_where=array();
        $arr_where['member_id']=$this->member_id;
        $arr_where['fans_id'] = I('session.MEMBER')['id'];
        $result=$this->field('id')->where($arr_where)->select();
        if(count($result) != 0){
            return true;
        }else{
            return false;
        }
    }

    //关注好友
    public function focus(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_data=array();
        $arr_data['member_id']=$this->member_id;
        $arr_data['fans_id']=$this->fans_id;
        $result=$this->data($arr_data)->add();
        if($result!==false){
            $data['status']=1;
            $data['msg']='关注成功';
        }else{
            $data['msg']='关注失败';
        }
        unset($arr_data);
        unset($result);
        return $data;
    }

    //取消关注好友
    public function cencelFocus(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        $arr_where['member_id']=$this->member_id;
        $arr_where['fans_id']=$this->fans_id;
        $result=$this->where($arr_where)->delete();
        if($result!==false){
            $data['status']=1;
            $data['msg']='取消关注成功';
        }else{
            $data['msg']='取消关注失败';
        }
        unset($arr_where);
        unset($result);
        return $data;
    }

    //获取好友列表ID
    public function getFriendsId($f='focus'){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if($f=='focus'){
            $result=$this->field("DISTINCT member_id")->where(array("fans_id"=>$this->fans_id))->page($this->page)->limit($this->pageSize)->select();
        }else{
            $result=$this->field("DISTINCT fans_id")->where(array("member_id"=>$this->member_id))->page($this->page)->limit($this->pageSize)->select();
        }
        if($result!==false){
            if($f=='focus'){
                $data['friends_id']=array_column($result,'member_id');
            }else{
                $data['friends_id']=array_column($result,'fans_id');
            }

            $data['msg']='获取好友用户ID成功';
            $data['status']=1;
        }else{
            $data['msg']='获取好友用户ID失败';
        }
        unset($result);
        return $data;
    }

    public function getFriendsIdCount($f='focus'){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if($f=='focus'){
            $result=$this->field("COUNT(DISTINCT member_id) AS count")->where(array("fans_id"=>$this->fans_id))->select();
        }else{
            $result=$this->field("COUNT(DISTINCT fans_id) AS count")->where(array("member_id"=>$this->member_id))->select();
        }
        if($result!==false){
            $data['count']=$result[0]['count'];
            $data['msg']='获取好友用户数量成功';
            $data['status']=1;
        }else{
            $data['msg']='获取好友用户数量失败';
        }
        unset($result);
        return $data;
    }

    //获取用户关注数量
    public function getFocusCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT(DISTINCT member_id)"]='focus_count';
        $result=$this->field($arr_field)->where(array("fans_id"=>$this->member_id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户关注信息成功';
            $data['focus_count']=$result[0]['focus_count'];
        }else{
            $data['msg']='获取用户关注信息失败';
        }
        unset($arr_field);
        unset($result);
        return $data;
    }

    //获取用户粉丝数量
    public function getFansCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT(DISTINCT fans_id)"]='fans_count';
        $result=$this->field($arr_field)->where(array("member_id"=>$this->member_id))->select();
        if($result!==false && count($result)>0){
            $data['status']=1;
            $data['msg']='获取用户粉丝信息成功';
            $data['fans_count']=$result[0]['fans_count'];
        }else{
            $data['msg']='获取用户粉丝信息失败';
        }
        unset($arr_field);
        unset($result);
        return $data;
    }

    /*
     * 获取当前登录用户的关注好友ID
     * 用于用户列表中判断当前用户是否已关注
     */
    public function getFocusIDBySelf(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        $arr_where["fans_id"]=$this->fans_id;
        $result=$this->field('member_id')->where($arr_where)->select();
        if($result!==false && count($result)>0){
            $data['status']=1;
            $data['msg']='获取当前用户关注用户ID成功';
            $data['member_id']=$result;
        }else{
            $data['msg']='获取当前用户关注用户ID失败';
        }
        return $data;
    }

}