<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller{
    public function Index(){
        if(I('session.ADMIN')!=null){
            //$this->display('./index');
            $this->display('Admin/index');
        }else{
            $this->display('./login');
        }
    }
}