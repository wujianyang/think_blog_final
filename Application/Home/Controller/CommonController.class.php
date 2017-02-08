<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller{

    //获取天气信息
    public function getWeather(){
        //获取当前城市以及天气信息
        $ip=json_decode(curlRequest(C('GET_IP_URL')),true);
        $remoteIpResult=json_decode(curlRequest(C('REMOTE_IP_URL').'&ip='.$ip['ip']),true);
        $url=C('WEATHER_URL')."?location=".$remoteIpResult['city']."&output=json&ak=".C('AK');
        $weatherResult=json_decode(curlRequest($url),true);
        $weatherResult=returnWeatherArr($weatherResult);

        return $weatherResult;
    }
    //用户模块列表
    public function personIndex($className){
        if ($className != '') {
            $data = array();
            $data['status']=0;
            $data['msg']='';

            if($this->isLogin()){
                $modelClass = D($className);
                $modelClass->member_id=I('session.MEMBER')['id'];
                if(IS_AJAX){
                    $modelClass->page=I('post.page');
                    $modelClass->pageSize=I('post.page_size');
                    $modelClass->key=trim(I('post.key'));
                    $modelClass->keyItem=I('post.keyItem');
                    $modelClass->com=I('post.com');
                }
                $result = $modelClass->personIndex();
                $this->returnResult($result,$data,'rows');
                $resultCount = $modelClass->personIndexCount();
                $this->returnResult($resultCount,$data,'count');
                $data['pageCount'] = ceil($resultCount['count'] / $modelClass->pageSize);

                unset($result);
                unset($resultCount);
                unset($modelClass);
                if (IS_AJAX) {
                    $this->ajaxReturn($data);
                }else{
                    $this->assign('data', $data);
                    $this->assign('empty', C('NODATA'));
                    $this->display();
                }
            }else{
                if(!IS_AJAX){
                    $this->redirect('Member/login');
                }else{
                    $data['msg']='登录超时';
                    $this->ajaxReturn($data);
                }
            }
        }
    }

    public function personInfo($modelClass){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $model=D($modelClass);
            $model->id=I('post.id');
            $result=$model->personInfo();
            if($result['status']==1){
                $data['status']=1;
                $data['rows']=$result['rows'];
            }
            $data['msg']=$result['msg'];
        }else{
            $data['msg']='登录超时';
        }

        unset($result);
        unset($model);
        $this->ajaxReturn($data);
    }

    public function personDel($modelClass){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        if(I('session.MEMBER')!=null){
            $model=D($modelClass);
            $model->id=I('post.id');
            $id=I('post.id');
            $result=$model->personDel();
            if($result['status']==1){
                $data['status']=1;
            }
            $data['msg']=$result['msg'];
        }else{
            $data['msg']='登录超时';
        }
        unset($result);
        unset($model);
        $this->ajaxReturn($data);
    }


    //处理返回数据结果
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

    //判断是否登录
    public function isLogin(){
        if(I('session.MEMBER')!=null){
            return true;
        }else{
            $this->redirect('Member/login');
        }
    }
}