<?php
namespace Admin\Model;
use Admin\Model;
use Think\Verify;
require_once C('ROOT').C('FUNC').'func.php';

class AdminModel extends CommonModel{
    public $id;
    public $table='admin';
    public $table_alias='a';

    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';
    public $sql='';

    //获取验证码
    public function getVerify(){
        //验证码长度4,5分钟过期,使用杂点
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

    //登录
    public function login(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('id')->where(array('admin_name'=>$this->admin_name))->select();
        if(count($result) > 0){
            $result=$this->field('id')->where(array('admin_name'=>$this->admin_name,'passwd'=>md5($this->passwd)))->select();
            if(count($result) > 0){
                $data['status']=1;
                $data['msg']='登录成功';
                $data['admin']=array('id'=>$result[0]['id'],'admin_name'=>$this->admin_name);
            }elseif(count($result) == 0){
                $data['msg']='用户名或密码错误';
            }
        }elseif(count($result) == 0){
            $data['msg']='用户名不存在';
        }
        unset($result);
        return $data;
    }


    //批量冻结或激活用户
    public function freezeUser($f=1){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if($f==1){
            $result=$this->data(array('is_freeze'=>1))->where(array('id'=>array('in',$this->id)))->save();
            if($result!==false){
                $data['msg']='冻结成功';
                $data['status']=1;
                $data['sql']=$this->getLastSql();
            }else{
                $data['msg']='冻结失败';
            }
        }elseif($f==0){
            $result=$this->data(array('is_freeze'=>0))->where(array('id'=>array('in',$this->id)))->save();
            if($result!==false){
                $data['msg']='激活成功';
                $data['status']=1;
            }else{
                $data['msg']='激活失败';
            }
        }
        unset($result);
        return $data;
    }

    //批量重置用户密码
    public function resetPasswdUser(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->data(array('passwd'=>C('ADMIN_PASSWORD_DEFAULT')))->where(array('id'=>array('in',$this->id)))->save();
        if($result!==false){
            $data['msg']='重置成功';
            $data['status']=1;
        }else{
            $data['msg']='重置失败';
        }
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

    //创建提交数据数组
    public function create_Data($f=''){
        $arr=array();
        $arr['admin_name']=$this->admin_name;
        if($f!='edit'){
            $arr['passwd']=$this->passwd;
        }
        if($f!='edit'){
            $arr['is_freeze']=$this->is_freeze;
        }
        $arr['last_ip']=$this->last_ip;
        $arr['last_time']=$this->last_time;

        return $arr;
    }

    //验证数据
    public function setValidata($f=''){
        if(empty($this->admin_name) && !preg_match("/^[\w]{4,}$/",$this->admin_name)){
            return '用户名验证失败';
        }else{
            if($f!='edit'){
                if($this->isExistsAdminName()){
                    return "用户名已存在，请重新输入";
                }
            }else{  //编辑时验证
                if(!$this->isSelfAdminName()){
                    if($this->isExistsAdminName()){
                        return "用户名已存在，请重新输入";
                    }
                }
            }
        }
        if($f!='edit'){
            if(empty($this->passwd) || !preg_match("/^.{6,}$/",$this->passwd)){
                return "登录密码验证失败";
            }else{
                $this->passwd=md5($this->passwd);
            }
        }

        if($f!='edit'){ //添加时赋初始值
            $this->is_freeze=0;
        }
        $this->last_ip=get_client_ip();
        $this->last_time=date("Y-m-d h:i:s",time());

        return true;
    }

    //判断是否已存在用户名
    public function isExistsAdminName(){
        $result=$this->where(array('admin_name'=>$this->admin_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //判断编辑用户名是否为自己
    public function isSelfAdminName(){
        $result=$this->where(array('id'=>$this->id,'admin_name'=>$this->admin_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //验证用户是否冻结
    public function isFreeze(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field('is_freeze')->where(array('admin_name'=>$this->admin_name))->select();
        if($result!==false && count($result)>0 && $result[0]['is_freeze']==1){
            return true;
        }else{
            return false;
        }
    }
}