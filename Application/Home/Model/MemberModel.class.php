<?php
namespace Home\Model;
use Home\Model;
use Think\Verify;
require_once C('ROOT').C('FUNC').'func.php';
class MemberModel extends CommonModel{
    public $id;
    public $id_temp;
    public $table='member';
    public $table_alias='m';
    public $foreign_table='friends';
    public $foreign_table_alias='f';

    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';

    //获取网站首页用户列表
    public function indexMember(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        $arr_where["hitnum"]=array('gt',0);
        $result=$this->field("id,member_name,hitnum,head_pic")->where($arr_where)->order("hitnum desc")->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='用户列表获取成功';
            $data['member']=$result;
        }else{
            $data['msg']='用户列表获取失败';
        }
        return $data;
    }

    //获取验证码
    public function getVerify(){
        //验证码长度4个字符、5分钟过期、使用杂点
        $verifyConfig=array('fontSize'=>36,'length'=>4,'expire'=>600,useNoise=>true);
        $verify=new Verify($verifyConfig);
        $verify->entry();
    }

    //校验验证码
    public function checkVerify($vCode,$id=''){
        $verify = new \Think\Verify();
        $res = $verify->check($vCode, $id);
        unset($verify);
        return $res;
    }

    //注册
    public function register(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata();
        if($vali_result===true){
            $add_data=$this->create_Data();
            $result=$this->data($add_data)->add();
            if($result!==false){
                $data['msg']='注册成功';
                $data['status']=1;
            }else{
                $data['msg']='注册失败';
            }
        }else{
            $data['msg']=$vali_result;
            if(!empty(C('ROOT').C('UPLOAD').$this->head_pic) && file_exists(C('ROOT').C('UPLOAD').$this->head_pic)){
                unlink($this->head_pic);
            }
        }
        unset($add_data);
        unset($result);
        return $data;
    }

    //登录
    public function login(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $member_name=$this->member_name;
        $result=$this->field('id')->where(array('member_name'=>$this->member_name))->select();
        if(count($result) > 0){
            $result=$this->field('id')->where(array('member_name'=>$this->member_name,'passwd'=>md5($this->passwd)))->select();
            if(count($result) > 0){
                $data['status']=1;
                $data['msg']='登录成功';
                $data['member']=array('id'=>$result[0]['id'],'member_name'=>$this->member_name);
                $result=$this->data(array('last_ip'=>get_client_ip(),'last_time'=>date("Y-m-d h:i:s",time())))->where(array('member_name'=>$member_name))->save();

            }elseif(count($result) == 0){
                $data['msg']='用户名或密码错误';
            }
        }elseif(count($result) == 0){
            $data['msg']='用户名不存在';
        }
        unset($result);
        return $data;
    }

    /*
     * 根据用户名验证用户是否冻结
     * 用于用户登录、申诉等验证
     */
    public function isFreeze(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('is_freeze')->where(array('member_name'=>$this->member_name))->select();
        if($result!==false && count($result)>0 && $result[0]['is_freeze']==1){
            $data['status']=1;
        }else{
            $data['msg']='验证冻结失败';
        }
        unset($result);
        return $data;
    }

    //这个方法好像暂时没用
    /*public function getIndexInfo(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->data('id')->where(array('member_name'=>$this->member_name))->select();

        return $data;
    }*/

    //根据ID获取用户个人资料
    public function getInfo(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('id,member_name,sex,email,tel,address,head_pic,question,answer,last_ip,last_time,hitnum')->where(array('id'=>$this->id))->select();

        if($result!==false && count($result)>0){
            $data['status']=1;
            $data['msg']='获取用户资料成功';
            $data['member']=$result[0];
        }else{
            $data['msg']='获取用户资料失败';
        }
        unset($result);
        return $data;
    }

    /*
     * 获取当前用户基本信息
     * 包括用户ID、用户名、头像、访问量
     */
    public function getInfoHeader(){
        $data=array();
        $data['status']=0;
        $data['msg']='';
        $result=$this->field(array('id,member_name,head_pic,hitnum'))->where(array('id'=>$this->id))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户信息成功';
            $data['member']=$result[0];
        }else{
            $data['msg']='获取用户信息失败';
        }
        unset($result);
        return $data;
    }

    /*
     * 根据用户名获取用户ID
     */
    public function getMemberId(){
        $data=array();
        $data['status']=0;
        $data['msg']='';
        $result=$this->field(array('id'))->where(array('member_name'=>$this->member_name))->select();
        if($result!==false && count($result)>0){
            $data['status']=1;
            $data['msg']='获取用户ID信息成功';
            $data['id']=$result[0]['id'];
        }else{
            $data['msg']='获取用户ID信息失败';
        }
        unset($result);
        return $data;
    }

    //获取好友列表
    public function getFriends($f='focus'){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["$this->table_alias.id"]="member_id";
        $arr_field["$this->table_alias.member_name"]="member_name";
        $arr_field["$this->table_alias.sex"]="sex";
        $arr_field["$this->table_alias.head_pic"]="head_pic";
        $arr_field["$this->table_alias.hitnum"]="hitnum";
        //获取粉丝用户是否已关注，关注用户不需要判断
        /*if($f=='fans'){
            $arr_field["COUNT(DISTINCT $this->foreign_table_alias.fans_id)"]="isEach";
        }*/

        $arr_join=array();
        if($f=='focus'){
            $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.id=$this->foreign_table_alias.fans_id AND $this->foreign_table_alias.member_id=$this->id_temp";
        }else{
            $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.id=$this->foreign_table_alias.member_id AND $this->foreign_table_alias.fans_id=$this->id_temp";
        }

        $arr_where=array();
        $arr_where["$this->table_alias.id"]=array('IN',$this->id);

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->group("$this->table_alias.id")->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取关注用户信息成功';
            $data['member']=$result;
        }else{
            $data['msg']='获取关注用户信息失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //获取好友的关注数量
    public function getFriendsFocusCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT(DISTINCT $this->foreign_table_alias.member_id)"]="focus_count";

        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.id=$this->foreign_table_alias.fans_id";

        $arr_where=array();
        $arr_where["$this->table_alias.id"]=array('IN',$this->id);
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->group("$this->table_alias.id")->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取关注用户的关注数量成功';
            $data['focus_count']=array_column($result,'focus_count');
        }else{
            $data['msg']='获取关注用户的关注数量失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //获取好友的粉丝数量
    public function getFriendsFansCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT(DISTINCT $this->foreign_table_alias.fans_id)"]="fans_count";

        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.id=$this->foreign_table_alias.member_id";

        $arr_where=array();
        $arr_where["$this->table_alias.id"]=array('IN',$this->id);
        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->group("$this->table_alias.id")->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取关注用户的粉丝数量成功';
            $data['fans_count']=array_column($result,'fans_count');
        }else{
            $data['msg']='获取关注用户的粉丝数量失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //获取用户名模糊查询好友以及当前用户是否已关注
    public function searchFriends(){
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
        $arr_field=array();
        $arr_field["$this->table_alias.id"]="member_id";
        $arr_field["$this->table_alias.member_name"]="member_name";
        $arr_field["$this->table_alias.sex"]="sex";
        $arr_field["$this->table_alias.head_pic"]="head_pic";
        $arr_field["$this->table_alias.hitnum"]="hitnum";

        $result=$this->alias($this->table_alias)->field($arr_field)->where($arr_where)->group("$this->table_alias.id")->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取条件搜索用户信息成功';
            $data['friends']=$result;
        }else{
            $data['msg']='获取条件搜索用户信息失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    public function searchFriendsCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";

        $arr_where=array();
        $arr_where["$this->keyItem"]=array($this->com,$this->key);

        $result=$this->alias($this->table_alias)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取条件搜索用户信息数量成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='获取条件搜索用户信息数量失败';
        }
        unset($arr_field);
        unset($arr_where);
        unset($arr_join);
        unset($result);
        return $data;
    }

    //修改用户资料
    public function updateInfo(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata('update');
        if($vali_result===true){
            $update_data=$this->create_Data('update');
            $result=$this->data($update_data)->where(array('id'=>$this->id))->save();
            if($result!==false){
                $data['msg']='修改成功';
                $data['status']=1;
            }else{
                $data['msg']='修改失败';
            }
        }else{
            $data['msg']=$vali_result;
            if(!empty(C('ROOT').C('UPLOAD').$this->head_pic) && file_exists(C('ROOT').C('UPLOAD').$this->head_pic)){
                unlink($this->head_pic);
            }
        }
        unset($update_data);
        unset($result);
        return $data;
    }

    //修改密码
    public function updatePasswd($newPasswd=''){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if($newPasswd!=''){
            //验证旧密码是否正确
            $result=$this->field('id')->where(array('id'=>$this->id,'passwd'=>md5($this->passwd)))->select();
            if(count($result)>0){
                $result=$this->data(array('passwd'=>md5($newPasswd)))->where(array('id'=>$this->id))->save();
                if($result!==false){
                    $data['status']=1;
                    $data['msg']='修改密码成功';
                }else{
                    $data['msg']='修改密码失败';
                }
            }else{
                $data['msg']='原始密码错误';
            }
        }else{
            $data['msg']='新密码不能为空';
        }
        unset($result);
        return $data;
    }

    /*
     * 获取用户密码问题
     * 忘记密码页面
     */
    public function getQuestion(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('question')->where(array('member_name'=>$this->member_name))->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='获取用户密码问题成功';
            $data['question']=$result[0]['question'];
        }else{
            $data['msg']='获取用户密码问题失败';
        }
        unset($result);
        return $data;
    }

    //忘记密码修改密码
    public function forgetUpdatePasswd(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->data(array('passwd'=>md5($this->passwd)))->where(array('member_name'=>$this->member_name,'question'=>$this->question,'answer'=>$this->answer))->save();
        if($result!==false && $result>0){
            $data['status']=1;
            $data['msg']='密码修改成功';
        }else{
            $data['msg']='密码修改失败';
        }
        unset($result);
        return $data;
    }

    public function lastIpTime(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->data(array('last_ip'=>$this->last_ip,'last_time'=>$this->last_time))->where(array('member_name'=>$this->member_name))->save();
        if($result!==false && $result>0){
            $data['status']=1;
            $data['msg']='上次登录IP和上次登录时间修改成功';
        }else{
            $data['msg']='上次登录IP和上次登录时间修改失败';
        }
        var_dump($data);

        unset($result);
        return $data;
    }

    /*
     * 访问用户首页访问量+1
     */
    public function accessMemberIndex(){
        $this->where(array('id'=>$this->id))->setInc('hitnum',1);
    }

    /*
     * 为添加和编辑方法
     * 校验提交数据
     */
    public function setValidata($f=''){
        if(empty($this->member_name) && !preg_match("/^[\w]{4,}$/",$this->member_name)){
            return '用户名验证失败';
        }else{
            if($f!='update'){
                if($this->isExistsMemberName()){
                    return "用户名已存在，请重新输入";
                }
            }else{  //编辑时验证
                if(!$this->isSelfMemberName()){
                    if($this->isExistsMemberName()){
                        return "用户名已存在，请重新输入";
                    }
                }
            }
        }
        if($f!='update'){
            if(empty($this->passwd) || !preg_match("/^.{6,}$/",$this->passwd)){
                return "登录密码验证失败";
            }else{
                $this->passwd=md5($this->passwd);
            }
        }

        if(empty($this->sex) && !preg_match("/^[0-1]$/",$this->sex)){
            return "性别验证失败";
        }
        if(empty($this->email) && !preg_match("/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/",$this->email)){
            return "email验证失败";
        }
        if(empty($this->tel) && !preg_match("/(1[\d]{10})|(\d{3,4}-\d{7,8})/",$this->tel)){
            return "电话验证失败";
        }
        if(empty($this->address) && !preg_match("/^[\w ]{4,}$/",$this->address)){
            return "地址验证失败";
        }
        if(empty($this->question) && !preg_match("/^[\w ]{4,}$/",$this->question)){
            return "密码问题验证失败";
        }
        if(empty($this->answer) && !preg_match("/^[\w ]{4,}$/",$this->answer)){
            return "密码答案验证失败";
        }
        if(isset($this->head_pic) && !empty($this->head_pic)) {
            $uploadConfig=array('name' => 'head_pic',
                'maxSize'   =>  10000000,
                'exts'      =>  array('png','jpg','jpeg','gif'),
                'rootPath'  =>  C('ROOT').C('UPLOAD_PATH'),
                'savePath'  =>  'head_pic/',
                'saveName'  =>  'head_pic_'.$this->member_name,
                'autoSub'   =>  false);
            $resultUpload=$this->upload($uploadConfig);
            if($resultUpload['status']==1 && $resultUpload['upload']['head_pic']['savename']!=''){
                $this->head_pic=$uploadConfig['savePath'].$resultUpload['upload']['head_pic']['savename'];
            }else{
                return $resultUpload['msg'];
            }
        }elseif($f!='update'){    //编辑时验证
            return "用户头像验证失败";
        }
        if($f!='update'){ //添加时赋初始值
            $this->hitnum=0;
            $this->is_freeze=0;
        }
        $this->last_ip=get_client_ip();
        $this->last_time=date("Y-m-d h:i:s",time());

        return true;
    }

    /*
     * 为添加和编辑方法
     * 创建提交数据数组
     */
    public function create_Data($f=''){
        $arr=array();
        $arr['member_name']=$this->member_name;
        if($f!='update'){
            $arr['passwd']=$this->passwd;
        }
        $arr['sex']=$this->sex;
        $arr['email']=$this->email;
        $arr['tel']=$this->tel;
        $arr['address']=$this->address;
        $arr['question']=$this->question;
        $arr['answer']=$this->answer;
        if(!empty($this->head_pic)){
            $arr['head_pic']=$this->head_pic;
        }
        if($f!='update'){
            $arr['hitnum']=$this->hitnum;
            $arr['is_freeze']=$this->is_freeze;
        }
        $arr['last_ip']=$this->last_ip;
        $arr['last_time']=$this->last_time;

        return $arr;
    }

    //判断是否已存在用户名
    public function isExistsMemberName(){
        $result=$this->field('id')->where(array('member_name'=>$this->member_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //根据用户ID判断是否存在
    public function isExistsMemberId(){
        $result=$this->field('id')->where(array('id'=>$this->id))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    /*
     * 用户编辑校验数据时判断用户名是否为本身
     */
    public function isSelfMemberName(){
        $result=$this->field('id')->where(array('id'=>$this->id,'member_name'=>$this->member_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }
}