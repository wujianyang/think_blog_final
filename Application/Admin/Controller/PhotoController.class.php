<?php
namespace Admin\Controller;
use Think\Controller;

class PhotoController extends Controller{
    public function index(){
        if(I('session.ADMIN')!=null){
            $photo=A('Common');
            $photo->index('Photo');
        }else{
            if(IS_AJAX){
                $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
            }else{
                $this->display('./login');
            }
        }
    }

    public function add(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['photo']) && !empty($_POST['photo'])){
                $photo=D('photo');
                $photo->photo_title=I('post.photo')['photo_title'];
                $photo->member_id=I('post.photo')['member_id'];
                $result=$photo->addData();
                unset($photo);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function info(){
        if(I('session.ADMIN')!=null){
            $photo=A('Common');
            $photo->info('Photo');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function edit(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['photo']) && !empty($_POST['photo'])){
                $photo=D('Photo');
                $photo->id=I('post.photo')['id'];
                $photo->photo_title=I('post.photo')['photo_title'];
                $photo->member_id=I('post.photo')['member_id'];
                $result=$photo->editData();
                unset($photo);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function del(){
        if(I('session.ADMIN')!=null){
            $photo=A('Common');
            $photo->del('Photo');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function getPhoto(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['member_id']) && !empty($_POST['member_id'])){
                $photo=D('Photo');
                $photo->member_id=I('post.member_id');
                $result=$photo->getPhoto();
                unset($photo);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'用户ID为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }
}