<?php
namespace Home\Controller;
use Think\Controller;

class ArticleCommentController extends Controller{
    /*
     * 获取个人文章评论列表
     * 文章细览页面点击分页
     */
    public function getArticleComment(){
        $data = array();
        $data['status']=0;
        $data['msg']='';
        if(IS_AJAX && isset($_POST['article_id']) && !empty($_POST['article_id'])){
            $article=D('Article');
            $article->id=I('post.article_id');
            if($article->isExistsArticleId()){
                $articleComment=D('ArticleComment');
                $articleComment->article_id=I('post.article_id');
                $articleComment->page=I('post.page');
                $articleComment->pageSize=I('post.page_size');
                $articleComment->key=trim(I('post.key'));
                $articleComment->keyItem=I('post.keyItem');
                $articleComment->com=I('post.com');
                $result=$articleComment->getArticleComment();
                $resultCount=$articleComment->getArticleCommentCount();
                if($result['status']==1){
                    $data['articleComment']=$result['articleComment'];
                    $data['count'] = $resultCount['comment_count'];
                    $data['pageCount'] = ceil((int)$resultCount['comment_count'] / $articleComment->pageSize);
                    $data['status']=1;
                }else{
                    $data['msg']=$result['msg'];
                }
            }else{
                $data['msg']='文章ID不存在';
            }
            unset($result);
            unset($resultCount);
            unset($articleComment);
            $this->ajaxReturn($data);
        }else{
            $data['msg']='请求参数为空';
            $this->ajaxReturn($data);
        }
    }

    //提交评论
    public function comment(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        //验证用户是否登录
        if(I('session.MEMBER')!=null){
            $member_id=I('session.MEMBER')['id'];
            //验证文章ID是否存在
            $article=D('Article');
            $article->id=I('post.article_id');
            if($article->isExistsArticleId()){
                $articleComment=D('ArticleComment');
                $articleComment->member_id=$member_id;
                $articleComment->article_id=I('post.article_id');
                $articleComment->comment_content=I('post.content');
                $articleCommentResult=$articleComment->comment();
                if($articleCommentResult['status']==1){
                    $data['status']=1;
                    $data['msg']='评论成功';
                }else{
                    $data['msg']='评论失败';
                }
            }else{
                $data['msg']='文章不存在';
            }
        }else{
            $data['msg']='请先登录再评论';
        }
        unset($articleCommentResult);
        unset($articleComment);
        $this->ajaxReturn($data);
    }

    public function commentDel(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(IS_AJAX && isset($_POST['id']) && !empty($_POST['id'])){
            $id=I('post.id');
            $articleComment=D('ArticleComment');
            $articleComment->id=$id;
            $result=$articleComment->commentDel();
            if($result['status']==1){
                $data['status']=1;
            }
            $data['msg']=$result['msg'];
        }else{
            $data['msg']='提交参数错误';
        }
        unset($result);
        unset($articleComment);
        $this->ajaxReturn($data);
    }
}