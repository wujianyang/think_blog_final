<?php
namespace Home\Controller;
use Think\Controller;
class PhotoImgController extends Controller{

    //用户添加相片
    public function personAdd(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $photoImg=D('PhotoImg');
            $photoImg->member_id=I('session.MEMBER')['id'];
            $photoImg->photo_id=I('post.photo_id');
            $photoImg->img_title=I('post.photoImg')['img_title'];
            if (isset($_FILES['img_src']) && !empty($_FILES['img_src'])) {
                $photoImg->img_src = $_FILES['img_src'];
            }else{
                $data['msg']='请上传相片';
            }
            $result=$photoImg->personAdd();
            $data=$result;
        }else{
            $data['msg']='登录超时';
        }
        unset($result);
        unset($photoImg);
        $this->ajaxReturn($data);
    }

    //个人删除相片
    public function personDel(){
        $common=A('Common');
        $common->personDel('PhotoImg');
    }

    //查看详细相片信息
    public function personInfo(){
        $common=A('Common');
        $common->personInfo('PhotoImg');
    }

    //个人编辑相片
    public function personEdit(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $photoImg=D('PhotoImg');
            $photoImg->member_id=I('session.MEMBER')['id'];
            $photoImg->photo_id=I('post.photo_id');
            $photoImg->id=I('post.photoImg')['id'];
            $photoImg->img_title=I('post.photoImg')['img_title'];
            if (isset($_FILES['img_src']) && !empty($_FILES['img_src'])) {
                $photoImg->img_src = $_FILES['img_src'];
            }else{
                $data['msg']='请上传相片';
            }
            $result=$photoImg->personEdit();
            $data=$result;
        }else{
            $data['msg']='登录超时';
        }
        unset($result);
        unset($photoImg);
        $this->ajaxReturn($data);
    }

}