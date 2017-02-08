<?php
namespace Home\Model;
use Think\Model;

class CommonModel extends Model{
    /*
     * 上传配置参考
     * array(
           'name'      =>  'img_src',
           'maxSize'   =>  100000,
           'exts'      =>  array('png','jpg','jpeg','gif'),
           'rootPath'  =>  C('ROOT').C('UPLOAD_PATH'),
           'savePath'  =>  'photo_img/',
           'saveName'  =>  'photo_img_'.time(),
           'autoSub'   =>  false   //是否创建子文件夹
       );
     */
    //上传文件代码
    public function upload($config=array()){
        $data=array();
        $data['status']=1;
        $data['msg']='';

        //若存在相同文件，删除(用户更新头像)
        $file=$config['rootPath'].$config['savePath'].$config['saveName'];
        foreach($config['exts'] as $ext){
            if(file_exists($file.'.'.$ext)){
                unlink($file.'.'.$ext);
            }
        }
        $upload=new \Think\Upload($config);
        $info=$upload->upload();
        if(!$info){ //长传失败
            $data['msg']=$upload->getError();
        }else{  //上传成功
            $data['status']=1;
            $data['upload']=$info;
        }
        unset($upload);
        unset($info);
        return $data;
    }

    //个人信息添加
    public function personAdd(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $vali_result=$this->setValidata();
        if($vali_result===true){
            $arr_add=$this->create_Data();
            $result=$this->data($arr_add)->add();
            if($result!==false){
                $data['status']=1;
                $data['msg']='添加成功';
            }else{
                $data['msg']='添加失败';
            }
        }else{
            $data['msg']=$vali_result;
        }
        unset($arr_add);
        unset($result);
        return $data;
    }

    /*
     * 通过ID来获取数据
     */
    public function personInfo(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->where(array('id'=>array('eq',$this->id)))->select();
        if($result!==false){
            $data['msg']='获取数据成功';
            $data['status']=1;
            $data['rows']=$result[0];
        }else{
            $data['msg']='获取数据失败';
        }
        unset($result);
        return $data;
    }

    /*
     * 通过ID修改数据
     */
    public function personEdit(){
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
            }
        }else{
            $data['msg']=$vali_result;
        }
        unset($edit_data);
        unset($result);
        return $data;
    }

    /*
     * 通过ID批量删除数据
     */
    public function personDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $result=$this->where(array('id'=>array('IN',$this->id)))->delete();
        if($result!==false){
            $data['msg']='删除成功';
            $data['status']=1;
        }else{
            $data['msg']='删除失败';
        }
        unset($result);
        return $data;
    }

    /*
     * 验证信息数据
     * $f用来判断是添加还是编辑
    */
    public function setValidata($f=''){
        /*
         * 子类重写
         */
        return false;
    }

    /*
     * 创建添加或编辑的数据
     * $f用来判断是添加还是编辑
    */
    public function create_Data($f=''){
        /*
         * 子类重写
         */
        return array();
    }

    //判断用户ID是否存在
    public function isExistsMemberId($member_id=''){
        $member=D('Member');
        $result=$member->field(array("count(id)"=>"count"))->where(array("id"=>$member_id))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //判断相册ID是否存在
    public function isExistsPhotoId($photo_id=''){
        $photo=D('Photo');
        $result=$photo->field(array("count(id)"=>"count"))->where(array("id"=>$photo_id))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

    //判断文章类别ID是否存在
    public function isExistsArticleTypeId($article_type_id=''){
        $articleType=D('ArticleType');
        $result=$articleType->field(array("count(id)"=>"count"))->where(array("id"=>$article_type_id))->select();
        if(count($result) == 0){
            return false;
        }else{
            return true;
        }
    }

}