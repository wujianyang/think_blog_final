<?php
namespace Admin\Controller;
use Think\Controller;

class ComplaintController extends \Think\Controller{
    //用户申诉列表
    public function index(){
        if(I('session.ADMIN')!=null){
            $complaint=A('Common');
            $complaint->index('Complaint');
        }else{
            if(IS_AJAX){
                $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
            }else{
                $this->display('./login');
            }
        }
    }

    //通过审核
    public function pass(){
        if(I('session.ADMIN')!=null){
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $complaint= D('Complaint');
                $complaint->id = I('post.id');
                $complaint->admin_id = I('session.ADMIN')['id'];
                $complaint->pass_time = date("Y-m-d h:i:s",time());
                $complaint->isPass = 1;
                $result = $complaint->pass();
                unset($complaint);
                $this->ajaxReturn($result);
            } else {
                $this->ajaxReturn(array('status' => 0, 'msg' => '请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status' => 0, 'msg' => C('NOLOGIN')));
        }
    }

    //忽略申诉
    public function del(){
        if(I('session.ADMIN')!=null){
            $complaint=A('Common');
            $complaint->del('Complaint');
        }else{
            $this->ajaxReturn(array('status' => 0, 'msg' => C('NOLOGIN')));
        }
    }
}