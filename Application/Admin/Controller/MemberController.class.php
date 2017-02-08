<?php
namespace Admin\Controller;
use Think\Controller;

class MemberController extends \Think\Controller {
    public function index(){
        if(I('session.ADMIN')!=null){
            $member=A('Common');
            $member->index('Member');
        }else{
            if(IS_AJAX){
                $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
            }else{
                $this->display('./login');
            }
        }
    }

    public function info(){
        if(I('session.ADMIN')!=null){
            $member=A('Common');
            $member->info('Member');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function del(){
        if(I('session.ADMIN')!=null){
            $member=A('Common');
            $member->del('Member');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function add(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['member_name']) && !empty($_POST['member_name'])) {
                $member=D('Member');
                $member->member_name=I('post.member_name');
                $member->passwd=I('post.passwd');
                $member->sex=I('post.sex');
                $member->email=I('post.email');
                $member->tel=I('post.tel');
                $member->address=I('post.address');
                $member->question=I('post.question');
                $member->answer=I('post.answer');
                if (isset($_FILES['head_pic']) && !empty($_FILES['head_pic'])) {
                    $member->head_pic = $_FILES['head_pic'];
                }
                $result=$member->addData();
                unset($member);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function edit(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['member']) && !empty($_POST['member'])) {
                $member=D('Member');
                $member->id=I('post.member')['id'];
                $member->member_name=I('post.member')['member_name'];
                $member->sex=I('post.member')['sex'];
                $member->email=I('post.member')['email'];
                $member->tel=I('post.member')['tel'];
                $member->address=I('post.member')['address'];
                $member->question=I('post.member')['question'];
                $member->answer=I('post.member')['answer'];
                if (isset($_FILES['head_pic']) && !empty($_FILES['head_pic'])) {
                    $member->head_pic = $_FILES['head_pic'];
                }
                $result=$member->editData();
                unset($member);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function freeze(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['id']) && !empty($_POST['id']) && $_POST['is_f']!='') {
                $member=D('Member');
                $member->id=I('post.id');
                $result=$member->freezeUser( I('post.is_f') );
                unset($member);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function resetPasswd(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['id']) && !empty($_POST['id'])) {
                $member=D('Member');
                $member->id=I('post.id');
                $result=$member->resetPasswdUser();
                unset($member);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function getMember(){
        if(I('session.ADMIN')!=null){
            $member=D('Member');
            $result=$member->getMember();
            unset($member);
            $this->ajaxReturn($result);
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }
}