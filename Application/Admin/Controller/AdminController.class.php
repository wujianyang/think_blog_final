<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends Controller{
    public function index(){
        if(I('session.ADMIN')!=null){
            $admin=A('Common');
            $admin->index('Admin');
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
            $admin=A('Common');
            $admin->info('Admin');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function del(){
        if(I('session.ADMIN')!=null){
            $admin=A('Common');
            $admin->del('Admin');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function add(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['admin_name']) && !empty($_POST['admin_name'])) {
                $admin=D('Admin');
                $admin->admin_name=I('post.admin_name');
                $admin->passwd=I('post.passwd');
                $result=$admin->addData();
                unset($admin);
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
                $admin=D('Admin');
                $admin->id=I('post.id');
                $result=$admin->freezeUser( I('post.is_f') );
                unset($admin);
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
                $admin=D('Admin');
                $admin->id=I('post.id');
                $result=$admin->resetPasswdUser();
                unset($admin);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'请求参数为空'));
            }
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function updatePasswd(){
        if(I('session.ADMIN')!=null){
            if(IS_POST){
                $newPasswd=I('post.passwd');
                $newPasswd2=I('post.passwd2');
                if($newPasswd==$newPasswd2){
                    $admin=D('Admin');
                    $admin->id=I('session.ADMIN')['id'];
                    $admin->passwd=I('post.old_passwd');
                    $result=$admin->updatePasswd($newPasswd);
                    unset($admin);
                    //密码修改成功需重新登录
                    if($result['status']==1){
                        session('ADMIN',null);
                        $this->redirect('Admin/login');
                    }
                    $this->assign('msg',$result['msg']);
                }
            }
            unset($result);
            $this->display();
        }else{
            $this->display('./login');
        }
    }

    public function login(){
        if(I('session.ADMIN')!=null){
            $this->redirect('index');
        }elseif(IS_POST){
            //验证提交参数是否正确
            if(isset($_POST['admin_name']) && !empty($_POST['admin_name'])){
                $admin=D('Admin');
                $admin->admin_name=I('post.admin_name');
                $admin->passwd=I('post.passwd');
                $admin->vCode=I('post.vCode');
                if($admin->checkVerify(I('post.vCode'))!==false){  //验证验证码是否正确
                    if(!$admin->isFreeze()){    //验证是否冻结
                        $result=$admin->login();
                        unset($admin);
                        if($result['status']==1){   //验证用户名和密码是否匹配
                            session_set_cookie_params(30*60);
                            session_start();
                            session('ADMIN',$result['admin']);
                            if(!empty($_POST['rememberPass'])){
                                setcookie("admin_name",I('post.admin_name'),time()+3600*24*7);
                                setcookie("passwd",md5(I('post.passwd')),time()+3600*24*7);
                            }
                            $this->redirect('Member/index');
                        }else{  //用户名和密码验证失败
                            $this->assign('msg',$result['msg']);
                        }
                    }else{
                        $this->assign('msg','账号已冻结');
                    }
                }else{
                    $this->assign('session',I('session.'));
                    $this->assign('msg','验证码错误');
                }
            }else{
                $this->assign('data',array('status'=>0,'msg'=>'请求参数为空'));
            }
        }
        unset($result);
        $this->display('./login');
    }

    //退出登录
    public function logout(){
        session_start();
        session('ADMIN',null);
        $this->redirect('Admin/login');
    }

    //获取验证码
    public function getVerify(){
        $admin=D('Admin');
        $admin->getVerify();
        unset($admin);
    }
}