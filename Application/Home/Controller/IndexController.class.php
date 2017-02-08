<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //访问网站首页
    public function index(){
        $article=D('Article');
        $member=D('Member');
        $articleResult=$article->indexArticle();
        $member->pageSize=20;
        $memberResult=$member->indexMember();
        //获取天气信息
        $common=A('Common');
        $weatherResult=$common->getWeather();

        $this->assign('article',$articleResult['article']);
        $this->assign('member',$memberResult['member']);
        $this->assign('weather',$weatherResult);
        unset($articleResult);
        unset($remoteIpResult);
        unset($weatherResult);
        unset($memberResult);
        unset($article);
        unset($articleType);
        unset($photo);
        $this->assign('empty',C('NODATA'));
        $this->display('./index');
    }

    //顶部搜索框搜索
    public function search(){
        if($_GET['keyItem']=='member'){    //搜索用户
            $this->searchFriends();
        }elseif($_GET['keyItem']=='article'){  //搜索文章
            $this->searchArticle();
        }else{
            $this->error('参数错误');
        }
    }

    //条件搜索用户列表
    public function searchFriends(){
        $data=array();
        $data['status']=1;
        $data['msg']='';

        $member=D('Member');
        $member->key=trim(I('get.key'));
        $member->keyItem='member_name';
        $member->com='like';
        //初次访问初始化每页20条数据
        $member->pageSize=20;
        if(IS_AJAX){
            $member->page=I('get.page');
            $member->pageSize=I('get.page_size');
        }
        //传递当前登录用户ID是为了判断搜索用户是否已关注
        $result=$member->searchFriends();
        if($result['status']==1){
            $resultCount=$member->searchFriendsCount();
            //获取查找到的用户ID
            if(count($result['friends'])>0){
                $member->id=array_column($result['friends'],'member_id');
            }else{
                $member->id='';
            }
            //获取当前登录用户的关注ID
            $friends=D('Friends');
            if(I('session.MEMBER')!=null){
                $friends->fans_id=I('session.MEMBER')['id'];
            }else{
                $friends->fans_id=0;
            }
            $resultFocusID=$friends->getFocusIDBySelf();
            $arr_focusID=array_column($resultFocusID['member_id'],'member_id'); //提取用户ID

            //再根据用户ID获取该用户的关注数量 和 粉丝数量
            $resultFocusCount=$member->getFriendsFocusCount();
            $resultFansCount=$member->getFriendsFansCount();
            //最后组合成新的结果集
            foreach($result['friends'] as $k=>$res){
                //判断当前用户是否已关注
                if(in_array($res['member_id'],$arr_focusID)){
                    $result['friends'][$k]['isfocus']=1;
                }else{
                    $result['friends'][$k]['isfocus']=0;
                }
                $result['friends'][$k]['focus_count']=$resultFocusCount['focus_count'][$k];
                $result['friends'][$k]['fans_count']=$resultFansCount['fans_count'][$k];
            }
            $data['rows']=$result['friends'];
            $data['count']=$resultCount['count'];
            $data['pageCount']=ceil($resultCount['count']/$member->pageSize);
            $data['curr_member_id']=I('session.MEMBER')['id'];
        }else{
            $data['msg']=$result['msg'];
        }
        unset($result);
        unset($resultCount);
        unset($resultFocusID);
        unset($arr_focusID);
        unset($resultFocusCount);
        unset($resultFansCount);
        unset($member);
        unset($friends);
        //根据提交类型返回数据
        if(IS_AJAX){
            $this->ajaxReturn($data);
        }else{
            $this->assign('data',$data);
            $this->assign('empty',C('NODATA'));
            $this->display('Member/friends');
        }
    }

    //条件搜索文章列表
    public function searchArticle(){
        $data=array();
        $data['status']=1;
        $data['msg']='';

        $article=D('Article');
        $article->key=trim(I('get.key'));
        $article->keyItem='title';
        $article->com='like';
        //初次访问初始化每页20条数据
        $article->pageSize=20;
        if(IS_AJAX){
            $article->page=I('get.page');
            $article->pageSize=I('get.page_size');
        }
        $result=$article->getArticleByTitle();
        if($result['status']==1){
            $data['status']=1;
            $data['rows']=$result['rows'];
        }else{
            $data['msg']=$result['msg'];
        }
        $resultCount=$article->getArticleCountByTitle();
        if($resultCount['status']==1){
            $data['status']=1;
            $data['count']=$resultCount['count'];
            $data['pageCount']=ceil($resultCount['count']/$article->pageSize);
        }else{
            $data['msg']=$resultCount['msg'];
        }
        unset($result);
        unset($resultCount);
        unset($article);
        //根据提交类型返回数据
        if(IS_AJAX){
            $this->ajaxReturn($data);
        }else{
            $this->assign('data',$data);
            $this->assign('empty',C('NODATA'));
            $this->display('Article/hotArticleList');
        }
    }
}