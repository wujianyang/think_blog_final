<?php
namespace Admin\Controller;
use Think\Controller;

class CommonController extends Controller{
    public function index($className=''){
        if ($className != '') {
            $data = array();
            $data['status']=0;
            $data['msg']='';


            $modelClass = D($className);
            if(IS_AJAX){
                $modelClass->page=I('post.page');
                $modelClass->pageSize=I('post.page_size');
                $modelClass->key=trim(I('post.key'));
                $modelClass->keyItem=I('post.keyItem');
                $modelClass->com=I('post.com');
            }
            $result = $modelClass->index();
            $this->returnResult($result,$data,'rows');
            $resultCount = $modelClass->getCount();
            $this->returnResult($resultCount,$data,'count');
            $data['pageCount'] = ceil($resultCount['count'] / $modelClass->pageSize);

            unset($result);
            unset($resultCount);
            unset($modelClass);
            if (IS_AJAX) {
                $this->ajaxReturn($data);
            } else {
                $this->assign('data', $data);
                $this->assign('empty', C('NODATA'));
                $this->display();
            }
        }
    }

    public function info($className=''){
        if ($className != '') {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $modelClass = D($className);
                $modelClass->id = I('post.id');
                $result = $modelClass->infoData();
                unset($modelClass);
                $this->ajaxReturn($result);
            } else {
                $this->ajaxReturn(array('status' => 0, 'msg' => '请求参数为空'));
            }
        }
    }

    public function del($className=''){
        if ($className != '') {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $modelClass = D($className);
                $modelClass->id = I('post.id');
                $result = $modelClass->delData();
                unset($modelClass);
                $this->ajaxReturn($result);
            } else {
                $this->ajaxReturn(array('status' => 0, 'msg' => '请求参数为空'));
            }
        }
    }

    public function returnResult($arr=array(),&$data=array(),$field='result'){
        if($arr['status']==1){
            $data['status']=1;
            $data['msg']=$arr['msg'];
            $data[$field]=$arr[$field];
        }else{
            $data['msg']=$arr['msg'];
            if(IS_AJAX){
                $this->ajaxReturn($data);
            }
        }
    }
}