<?php
namespace Admin\Model;
use Admin\Model;
require_once C('ROOT').C('FUNC').'func.php';
class MemberModel extends CommonModel{

    public $id;
    public $table='member';
    public $table_alias='m';
    public $table_foreign='';
    public $table_foreign_alias='';
    public $table_foreign2='';
    public $table_foreign_alias2='';
    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';
    public $sql='';


    //添加用户
    public function addData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata();
        if($vali_result===true){
            $add_data=$this->create_Data();
            $result=$this->data($add_data)->add();
            if($result!==false){
                $data['msg']='添加成功';
                $data['status']=1;
            }else{
                $data['msg']='添加失败';
                $data['status']=0;
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

    //编辑用户
    public function editData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata('edit');
        if($vali_result===true){
            $edit_data=$this->create_Data('edit');
            $result=$this->data($edit_data)->where(array('id'=>$this->id))->save();
            if($result!==false){
                $data['msg']='编辑成功';
                $data['status']=1;
            }else{
                $data['msg']='编辑失败';
                $data['status']=0;
            }
        }else{
            $data['msg']=$vali_result;
            if(!empty(C('ROOT').C('UPLOAD').$this->head_pic) && file_exists(C('ROOT').C('UPLOAD').$this->head_pic)){
                unlink($this->head_pic);
            }
        }
        unset($edit_data);
        unset($result);
        return $data;
    }

    //批量删除信息
    public function delData(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $article = D('Article');
        $articleType = D('ArticleType');
        $articleComment = D('ArticleComment');
        $photo = D('Photo');
        $photoImg = D('PhotoImg');
        $mess = D('Mess');
        $friends = D('Friends');
        $complaint = D('Complaint');

        //获取用户头像，删除用户同时删除头像
        $head_pic=$this->field('head_pic')->where(array('id'=>array('in',$this->id)))->select();
        //获取相片数据，删除用户同时删除相片
        $img_src=$photoImg->field('img_src')->where(array('member_id'=>array('in',$this->id)))->select();
        $articleId=$article->field('id')->where(array('member_id'=>array('in',$this->id)))->select();
        $articleId=array_column($articleId,'id');
        if(count($articleId)==0){
            $articleId='';
        }
        $this->startTrans();

        //删除本表数据放在最后，顺序不能改变，否则因外键约束而导致删除失败
        $result = $articleComment->where(array('member_id' => array('in', $this->id)))->delete();
        $result2 = $articleComment->where(array('article_id' => array('in', $articleId)))->delete();
        $result3 = $article->where(array('member_id' => array('in', $this->id)))->delete();
        $result4 = $articleType->where(array('member_id' => array('in', $this->id)))->delete();
        $result5 = $photoImg->where(array('member_id' => array('in', $this->id)))->delete();
        $result6 = $photo->where(array('member_id' => array('in', $this->id)))->delete();
        $result7 = $mess->where(array('messer_id' => array('in', $this->id)))->delete();
        $result8 = $mess->where(array('messed_id' => array('in', $this->id)))->delete();
        $result9 = $friends->where(array('member_id' => array('in', $this->id)))->delete();
        $result10 = $friends->where(array('fans_id' => array('in', $this->id)))->delete();
        $result11 = $complaint->where(array('member_id' => array('in', $this->id)))->delete();
        $result12 = $this->where(array('id' => array('in', $this->id)))->delete();
        if ($result!==false && $result2!==false && $result3!==false && $result4!==false && $result5!==false && $result6!==false && $result7!==false && $result8!==false && $result9!==false && $result10!==false && $result11!==false && $result12!==false) {
            $this->commit();

            foreach($head_pic as $head_pic_arr){  //在空间中删除头像
                if(file_exists(C('ROOT').C('UPLOAD').$head_pic_arr['head_pic'])){
                    if(!stristr($head_pic_arr['head_pic'],'default')){
                        unlink(C('ROOT').C('UPLOAD').$head_pic_arr['head_pic']);
                    }
                }
            }
            foreach($img_src as $img_src_arr){
                if(file_exists(C('ROOT').C('UPLOAD').$img_src_arr['img_src'])){
                    unlink(C('ROOT').C('UPLOAD').$img_src_arr['img_src']);
                }
            }
            $data['status'] = 1;
            $data['msg'] = '删除成功';
            $data['sql']=$this->getLastSql();
        } else {
            $this->rollback();
            $data['msg'] = '删除失败';
        }
        unset($head_pic);
        unset($img_src);
        unset($articleId);

        unset($article);
        unset($articleType);
        unset($articleComment);
        unset($photo);
        unset($photoImg);
        unset($mess);
        unset($friends);
        unset($complaint);

        unset($result12);
        unset($result11);
        unset($result10);
        unset($result9);
        unset($result8);
        unset($result7);
        unset($result6);
        unset($result5);
        unset($result4);
        unset($result3);
        unset($result2);
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

        $result=$this->data(array('passwd'=>C('USER_PASSWORD_DEFAULT')))->where(array('id'=>array('in',$this->id)))->save();
        if($result!==false){
            $data['msg']='重置成功';
            $data['status']=1;
        }else{
            $data['msg']='重置失败';
        }
        unset($result);
        return $data;
    }

    //验证数据
    public function setValidata($f=''){
        if(empty($this->member_name) && !preg_match("/^[\w]{4,}$/",$this->member_name)){
            return '用户名验证失败';
        }else{
            if($f!='edit'){
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
        if($f!='edit'){
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
        }elseif($f!='edit'){    //编辑时验证
            return "用户头像验证失败";
        }
        if($f!='edit'){ //添加时赋初始值
            $this->hitnum=0;
            $this->is_freeze=0;
        }
        $this->last_ip=get_client_ip();
        $this->last_time=date("Y-m-d h:i:s",time());

        return true;
    }

    //创建提交数据数组
    public function create_Data($f=''){
        $arr=array();
        $arr['member_name']=$this->member_name;
        if($f!='edit'){
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
        if($f!='edit'){
            $arr['hitnum']=$this->hitnum;
            $arr['is_freeze']=$this->is_freeze;
        }
        $arr['last_ip']=$this->last_ip;
        $arr['last_time']=$this->last_time;

        return $arr;
    }

    //判断是否已存在用户名
    public function isExistsMemberName(){
        $result=$this->where(array('member_name'=>$this->member_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //判断编辑用户名是否为自己
    public function isSelfMemberName(){
        $result=$this->where(array('id'=>$this->id,'member_name'=>$this->member_name))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //获取用户ID和用户名
    public function getMember(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->field(array('id','member_name'))->select();
        if($result!==false){
            $data['status']=1;
            $data['rows']=$result;
        }else{
            $data['msg']='获取用户列表错误';
        }


        unset($result);
        return $data;
    }
}