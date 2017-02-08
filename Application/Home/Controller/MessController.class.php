<?php
namespace Home\Controller;
use Think\Controller;

class MessController extends Controller{
    public function index(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(!empty($_GET['member_id']) || !empty($_POST['member_id'])){
            $member=D('Member');
            if(IS_AJAX){
                $member->id=I('post.member_id');
            }else{
                $member->id=I('get.member_id');
            }
            //判断用户ID是否存在
            if($member->isExistsMemberId()){
                //获取用户信息
                $memberResult=$member->getInfoHeader();
                if($memberResult['status']==1){
                    $data['status']=1;
                    $data['member']=$memberResult['member'];
                    //获取用户留言板信息
                    $mess=D('Mess');
                    $mess->messed_id=$member->id;
                    if(IS_AJAX){
                        $mess->page=I('post.page');
                        $mess->pagesize=I('post.pagesize');
                    }
                    $messResult=$mess->getMessByMemberId();
                    if($messResult['status']==1){
                        $data['status']=1;
                        $data['rows']=$messResult['mess'];
                        //获取留言板列表数量
                        $countResult=$mess->getMessCount();
                        if($countResult['status']==1){
                            $data['status']=1;
                            $data['count']=$countResult['count'];
                            $data['pageCount']=ceil($countResult['count']/$mess->pageSize);
                        }else{
                            $data['status']=0;
                            $data['msg']=$countResult['msg'];
                        }
                    }else{
                        $data['status']=0;
                        $data['msg']=$messResult['msg'];
                    }
                }else{
                    $data['status']=0;
                    $data['msg']='用户信息获取失败';
                }
            }else{
                $data['msg']='该用户不存在';
            }
        }else{
            $data['msg']='请求参数为空';
        }
        unset($memberResult);
        unset($messResult);
        unset($countResult);
        unset($member);
        unset($mess);
        if(IS_AJAX){
            $this->ajaxReturn($data);
        }else{
            $this->assign('empty',C('NODATA'));
            $this->assign('data',$data);
            $this->assign('msg',$data['msg']);
            $this->display();
        }
    }

    //用户留言
    public function mess(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //验证用户是否登录
        if(I('session.MEMBER')!=null){
            if(!empty(I('post.content'))){
                $mess=D('Mess');
                $mess->messer_id=I('session.MEMBER')['id'];
                $mess->messed_id=I('post.member_id');
                $mess->content=htmlspecialchars_decode(I('post.content'));
                $result=$mess->mess();
                if($result['status']==1){
                    $data['status']=1;
                    $data['msg']=$result['msg'];
                }else{
                    $data['msg']=$result['msg'];
                }
            }else{
                $data['msg']='留言内容不能为空';
            }
        }else{
            $data['msg']='请先登录再留言';
        }

        unset($result);
        unset($mess);
        $this->ajaxReturn($data);
    }

    //用户删除留言
    public function personDel(){
        $common=A('Common');
        $common->personDel('Mess');
    }
}