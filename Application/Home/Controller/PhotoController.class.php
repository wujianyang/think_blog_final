<?php
namespace Home\Controller;
use Think\Controller;

class PhotoController extends Controller{
    public function index(){
        if(!empty($_GET['member_id']) || !empty($_GET['photo_id'])){
            $member=D('Member');
            $photo=D('Photo');
            //获取用户ID
            if(!empty($_GET['photo_id'])){
                $photo->id=I('get.photo_id');
                $memberIdResult=$photo->getMemberId();
                if($memberIdResult['status']==1){
                    $member->id=$memberIdResult['member_id'];
                }else{
                    $this->error($memberIdResult['msg']);
                }
            }elseif(!empty($_GET['member_id'])){
                $member->id=I('get.member_id');
            }
            //获取用户基本信息
            $memberResult=$member->getInfoHeader();
            if($memberResult['status']==1){
                $this->assign('member',$memberResult['member']);
            }else{
                $this->error($memberResult['msg']);
            }
            //根据用户ID获取相册分类列表
            $photo->member_id=$member->id;
            $photoResult=$photo->getPhotoList();
            if($photoResult['status']==1){
                $this->assign('photo',$photoResult['photo']);
            }else{
                $this->error($photoResult['msg']);
            }
            //获取当前相册分类信息
            if(!empty($_GET['photo_id'])){
                $photo->id=I('get.photo_id');
            }elseif(!empty($_GET['member_id'])){
                $photo->id=$photoResult['photo'][0]['id'];
            }
            $photoResult_op=$photo->getPhoto_op();
            $this->assign('photo_op',$photoResult_op['photo_op']);
            //根据相册ID获取相片列表
            $photoImg=D('PhotoImg');
            $photoImg->photo_id=$photo->id;
            $photoImgResult=$photoImg->getPhotoImg();
            if($photoImgResult['status']==1){
                $this->assign('photoImg',$photoImgResult['photoImg']);
            }else{
                $this->error($photoImgResult['msg']);
            }
            //获取相册图片数量
            $countResult=$photoImg->getPhotoImgCount();
            if($countResult['status']==1){
                $this->assign('count',$countResult['count']);
            }else{
                $this->error($countResult['msg']);
            }
        }else{
            $this->error('请求参数为空');
        }
        unset($memberIdResult);
        unset($memberResult);
        unset($countResult);
        unset($photoResult);
        unset($photoResult_op);
        unset($member);
        unset($photo);
        unset($photoImg);
        $this->assign('empty','<p class="noData">没有数据</p>');
        $this->display();
    }

    //个人添加相册分类
    public function personAdd(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $member_id=I('session.MEMBER')['id'];
            $photo=D('Photo');
            $photo->member_id=$member_id;
            $photo->photo_title=I('post.photo')['photo_title'];
            $result=$photo->personAdd();
            if($result['status']==1){
                $data['status']=1;
            }
            $data['msg']=$result['msg'];
        }else{
            $data['msg']='登录超时';
        }

        unset($result);
        unset($photo);
        $this->ajaxReturn($data);
    }


    //个人删除相册分类
    public function personDel(){
        $common=A('Common');
        $common->personDel('Photo');
    }

    public function personInfo(){
        $common=A('Common');
        $common->personInfo('Photo');
    }

    //个人编辑文章分类
    public function personEdit(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $member_id=I('session.MEMBER')['id'];
            $photo=D('Photo');
            $photo->member_id=$member_id;
            $photo->id=I('post.photo')['id'];
            $photo->photo_title=I('post.photo')['photo_title'];
            $result=$photo->personEdit();
            if($result['status']==1){
                $data['status']=1;
            }
            $data['msg']=$result['msg'];
        }else{
            $data['msg']='登录超时';
        }
        unset($result);
        unset($photo);
        $this->ajaxReturn($data);
    }
}