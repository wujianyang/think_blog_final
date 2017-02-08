<?php
namespace Home\Controller;
use Think\Controller;

class ArticleController extends Controller{
    /*
     * 根据文章ID访问文章细览
     * 包括文章详细信息 和 文章评论列表首页
     */
    public function index(){
        if(isset($_GET['article_id']) && !empty($_GET['article_id'])){
            $article=D('Article');
            $article->id=I('get.article_id');
            if($article->isExistsArticleId()){
                $article->accessArticle();
                //获取文章信息
                $articleResult=$article->getArticleByArticleId();
                $this->assign('article',$articleResult['article']);
                //获取文章评论数量
                $pageCount=ceil($articleResult['article']['article_comment_count']/10);
                $this->assign('pageCount',$pageCount);
                //获取文章评论列表
                $articleComment=D('ArticleComment');
                $articleComment->article_id=I('get.article_id');
                $articleCommentResult=$articleComment->getArticleComment();
                $this->assign('articleComment',$articleCommentResult['articleComment']);

                unset($article);
                unset($articleComment);
                unset($articleResult);
                unset($articleCommentResult);
                $this->assign('empty','<p class="noData">没有数据</p>');
                $this->display();
            }else{
                unset($article);
                unset($articleComment);
                unset($articleResult);
                unset($articleCommentResult);
                $this->error('文章ID不存在');
            }
        }else{  //文章ID为空
            $this->error('文章ID为空');
        }

    }

    /*
     * 根据用户ID或者文章分类ID查询文章列表
     */
    public function articleList(){
        if(!empty($_GET['article_type_id']) || !empty($_GET['member_id'])){
            $member=D('Member');
            $articleType=D('ArticleType');
            if(!empty($_GET['article_type_id'])){
                $articleType->id=I('get.article_type_id');
                //根据文章类型ID获取用户ID
                $memberIdResult=$articleType->getMemberIdById();
                if($memberIdResult['status']==1){
                    $member->id=$memberIdResult['member_id'];
                    //$member->id=$member_id;
                }else{
                    $this->error($memberIdResult['msg']);
                }
            }elseif(!empty($_GET['member_id'])){
                $member->id=I('get.member_id');
            }
            //获取用户基本信息
            $memberResult=$member->getInfoHeader();
            $this->assign('member',$memberResult['member']);
            //根据用户ID获取文章分类列表
            $articleType->member_id=$member->id;
            $articleTypeResult=$articleType->getArticleTypeByMemberId();
            if($articleTypeResult['status']==1){
                $this->assign('articleType',$articleTypeResult['articleType']);
            }else{
                $this->error($articleTypeResult['msg']);
            }

            $articleType=D('ArticleType');
            if(!empty($_GET['article_type_id'])){
                $articleType->id=I('get.article_type_id');
            }elseif(!empty($_GET['member_id'])){
                //默认选中第一个文章分类
                $articleType->id=$articleTypeResult['articleType'][0]['id'];
            }
            //获取当前文章分类信息
            $articleTypeOpResult=$articleType->getArticleType_op();
            $this->assign('article_type_op',$articleTypeOpResult);

            //根据传递到后台的文章分类获取文章列表
            $article=D('Article');
            $article->pageSize=20;
            $article->article_type_id=$articleType->id;
            $articleResult=$article->getArticleByArticleTypeId();
            if($articleResult['status']==1){
                $this->assign('article',$articleResult['article']);
            }else{
                $this->error($articleResult['msg']);
            }
            //获取文章列表分页信息
            $countResult=$article->getCountByArticleId();
            $pageCount=ceil($countResult['count']/$article->pageSize);
            $this->assign('count',$countResult['count']);
            $this->assign('pageCount',$pageCount);
        }else{
            $this->error('请求参数为空');
        }
        unset($article);
        unset($member);
        unset($articleType);
        unset($articleResult);
        unset($articleTypeResult);
        unset($articleTypeOpResult);
        $this->assign('empty',C('NODATA'));
        $this->display();
    }

    //点击分页，获取文章列表
    public function articleListPage(){
        $data=array();
        $data['status']=0;
        $data['msg']='';
        if(IS_AJAX && $_POST['article_type_id']){
            //根据传递到后台的文章分类获取文章列表
            $article=D('Article');
            $article->article_type_id=I('post.article_type_id');
            $article->page=I('post.page');
            $article->pageSize=I('post.page_size');
            $articleResult=$article->getArticleByArticleTypeId();
            if($articleResult['status']==1){
                $data['status']=1;
                $data['article']=$articleResult['article'];
            }else{
                $data['msg']=$articleResult['msg'];
            }
            //获取文章列表数量
            $countResult=$article->getCountByArticleId();
            $data['count']=$countResult['count'];
        }else{
            $data['msg']='请求参数错误';
        }
        unset($countResult);
        unset($articleResult);
        unset($article);
        $this->ajaxReturn($data);
    }

    /*
     * 访问用户的热门文章列表
     */
    public function hotArticleList(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(!empty($_POST['member_id']) || !empty($_GET['member_id'])){
            $article=D('Article');
            $member=D('Member');

            //通过ajax获取列表，设置列表分页信息
            if(!empty($_POST['member_id'])){
                $article->member_id=I('post.member_id');
                $article->page=I('post.page');
                $article->pageSize=I('post.page_size');
                $article->member_id=I('post.member_id');
                $member->id=I('post.member_id');
            }else{
                $article->member_id=I('get.member_id');
                $article->pageSize=20;
                $member->id=I('get.member_id');
            }
            $memberResult=$member->getInfoHeader();
            if($memberResult['status']==1){
                $data['status']=1;
                $data['member']=$memberResult['member'];
            }else{
                $data['msg']=$memberResult['msg'];
                $this->ajaxReturn($data);
            }
            //获取用户热门文章列表
            $hotArticleResult=$article->getHotArticle();
            if($hotArticleResult['status']==1){
                $data['status']=1;
                $data['rows']=$hotArticleResult['rows'];
            }else{
                $data['msg']=$hotArticleResult['msg'];
                $this->ajaxReturn($data);
            }
            //获取热门文章排行分页条信息
            $countResult=$article->getHotArticleCount();
            if($countResult['status']==1){
                $data['count']=$countResult['count'];
                $data['pageCount']=ceil($countResult['count']/$article->pageSize);
            }else{
                $data['msg']=$countResult['msg'];
                $this->ajaxReturn($data);
            }
            unset($countResult);
            unset($hotArticleResult);
            unset($article);
        }else{
            $data['msg']='请求参数错误';
        }

        if(IS_AJAX){
            $this->ajaxReturn($data);
        }else{
            $this->assign('data',$data);
            $this->assign('empty',C('NODATA'));
            $this->display();
        }
    }

    //个人添加文章
    public function personAdd(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            if($_POST['article']!=null){
                $article=D('Article');
                $article->member_id=I('session.MEMBER')['id'];
                $article->title=I('post.article')['title'];
                $article->article_type_id=I('post.article')['article_type_id'];
                $article->content=I('post.article')['content'];
                $result=$article->personAdd();
                if($result['status']==1){
                    $data['status']=1;
                }
                    $data['msg']=$result['msg'];
            }else{
                $data['msg']='提交参数为空';
            }
        }
        unset($result);
        unset($article);
        $this->ajaxReturn($data);
    }

    //查看个人文章信息
    public function personInfo(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            if($_POST['id']!=null){
                $article=D('Article');
                $article->id=I('post.id');
                $result=$article->getArticleByArticleId();
                if($result['status']==1){
                    $data['status']=1;
                    $data['article']=$result['article'];
                }else{
                    $data['msg']=$result['msg'];
                }
            }else{
                $data['msg']='提交参数为空';
            }
        }
        unset($result);
        unset($article);
        $this->ajaxReturn($data);
    }
    //用户文章编辑
    public function personEdit(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            if($_POST['article']!=null){
                $member_id=I('session.MEMBER')['id'];
                $article=D('Article');
                $article->id=I('post.article')['id'];
                $article->member_id=$member_id;
                $article->title=I('post.article')['title'];
                $article->article_type_id=I('post.article')['article_type_id'];
                $article->content=I('post.article')['content'];
                $result=$article->personEdit();
                if($result['status']==1){
                    $data['status']=1;
                }
                $data['msg']=$result['msg'];
            }else{
                $data['msg']='提交参数为空';
            }
        }
        unset($result);
        unset($article);
        $this->ajaxReturn($data);
    }

    //用户文章删除
    public function personDel(){
        $common=A('Common');
        $common->personDel('Article');
    }

}