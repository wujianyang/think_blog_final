<?php
namespace Admin\Controller;
use Think\Controller;

class PhotoImgController extends Controller{
    public function index(){
        if(I('session.ADMIN')!=null){
            $photoImg=A('Common');
            $photoImg->index('PhotoImg');
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
            if(isset($_POST['PhotoImg']) && !empty($_POST['PhotoImg'])){
                $photoImg=D('PhotoImg');
                $photoImg->img_title=I('post.PhotoImg')['img_title'];
                $photoImg->member_id=I('post.PhotoImg')['member_id'];
                $photoImg->photo_id=I('post.PhotoImg')['photo_id'];
                if (isset($_FILES['img_src']) && !empty($_FILES['img_src'])) {
                    $photoImg->img_src = $_FILES['img_src'];
                }
                $result=$photoImg->addData();
                unset($photoImg);
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
            $photoImg=A('Common');
            $photoImg->info('PhotoImg');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function edit(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['PhotoImg']) && !empty($_POST['PhotoImg'])){
                $photoImg=D('PhotoImg');
                $photoImg->id=I('post.PhotoImg')['id'];
                $photoImg->img_title=I('post.PhotoImg')['img_title'];
                $photoImg->member_id=I('post.PhotoImg')['member_id'];
                $photoImg->photo_id=I('post.PhotoImg')['photo_id'];
                if (isset($_FILES['img_src']) && !empty($_FILES['img_src'])) {
                    $photoImg->img_src = $_FILES['img_src'];
                }
                $result=$photoImg->editData();
                unset($photoImg);
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
            $photoImg=A('Common');
            $photoImg->del('PhotoImg');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

}