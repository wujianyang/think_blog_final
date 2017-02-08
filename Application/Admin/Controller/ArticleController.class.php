<?php
namespace Admin\Controller;
use Think\Controller;

class ArticleController extends Controller{
    public function index(){
        if(I('session.ADMIN')!=null){
            $article=A('Common');
            $article->index('Article');
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
            if(isset($_POST['article']) && !empty($_POST['article'])){
                $article=D('Article');
                $article->title=I('post.article')['title'];
                $article->content=I('post.article')['content'];
                $article->member_id=I('post.article')['member_id'];
                $article->article_type_id=I('post.article')['article_type_id'];
                $result=$article->addData();
                unset($article);
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
            $article=A('Common');
            $article->info('Article');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }

    public function edit(){
        if(I('session.ADMIN')!=null){
            if(isset($_POST['article']) && !empty($_POST['article'])){
                $article=D('Article');
                $article->id=I('post.article')['id'];
                $article->title=I('post.article')['title'];
                $article->content=I('post.article')['content'];
                $article->member_id=I('post.article')['member_id'];
                $article->article_type_id=I('post.article')['article_type_id'];
                $result=$article->editData();
                unset($article);
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
            $article=A('Common');
            $article->del('Article');
        }else{
            $this->ajaxReturn(array('status'=>0,'msg'=>C('NOLOGIN')));
        }
    }
}