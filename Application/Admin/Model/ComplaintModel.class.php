<?php
namespace Admin\Model;
use Admin\Model;
require_once C('ROOT').C('FUNC').'func.php';
class ComplaintModel extends CommonModel{
    public $id;
    public $table='complaint';
    public $table_alias='c';
    public $foreign_table='member';
    public $foreign_table_alias='m';
    public $foreign_table2='admin';
    public $foreign_table2_alias='a';

    public $page=1;
    public $pageSize=10;
    public $key='';
    public $keyItem='';
    public $com='eq';

    public function index(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%".$this->key."%";
            }
            if($this->keyItem=='member_id'){
                $arr_where["$this->foreign_table_alias.id"]=array($this->com,$this->key);
            }elseif($this->keyItem=='admin_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        if($this->keyItem=='pass'){
            $arr_where["$this->table_alias.isPass"]=array($this->com,'1');
        }else{
            $arr_where["$this->table_alias.isPass"]=array($this->com,'0');
        }

        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.admin_id=$this->foreign_table2_alias.id";

        $arr_field=array();
        $arr_field[$this->table_alias.'.id']='id';
        $arr_field[$this->foreign_table_alias.'.id']='member_id';
        $arr_field[$this->foreign_table_alias.'.member_name']='member_name';
        $arr_field[$this->table_alias.'.complain_content']='complain_content';
        $arr_field[$this->table_alias.'.complain_time']='complain_time';
        $arr_field[$this->table_alias.'.admin_id']='admin_id';
        $arr_field[$this->foreign_table2_alias.'.admin_name']='admin_name';
        $arr_field[$this->table_alias.'.pass_time']='pass_time';
        $arr_field[$this->table_alias.'.isPass']='isPass';

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->page($this->page)->limit($this->pageSize)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='数据获取成功';
            $data['rows']=$result;
        }else{
            $data['msg']='数据获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    public function getCount(){
        $data=array();
        $data['status']=0;
        $data['msg']='';

        $arr_where=array();
        if($this->key!=''){
            if($this->com=='like'){
                $this->key="%".$this->key."%";
            }
            if($this->keyItem=='member_id'){
                $arr_where["$this->foreign_table_alias.id"]=array($this->com,$this->key);
            }elseif($this->keyItem=='admin_name'){
                $arr_where["$this->foreign_table2_alias.$this->keyItem"]=array($this->com,$this->key);
            }else{
                $arr_where["$this->table_alias.$this->keyItem"]=array($this->com,$this->key);
            }
        }
        if($this->keyItem=='pass'){
            $arr_where["$this->table_alias.isPass"]=array($this->com,'1');
        }else{
            $arr_where["$this->table_alias.isPass"]=array($this->com,'0');
        }

        $arr_join=array();
        $arr_join[]="LEFT JOIN $this->foreign_table $this->foreign_table_alias ON $this->table_alias.member_id=$this->foreign_table_alias.id";
        $arr_join[]="LEFT JOIN $this->foreign_table2 $this->foreign_table2_alias ON $this->table_alias.admin_id=$this->foreign_table2_alias.id";

        $arr_field=array();
        $arr_field["COUNT($this->table_alias.id)"]="count";

        $result=$this->alias($this->table_alias)->join($arr_join)->field($arr_field)->where($arr_where)->select();
        if($result!==false){
            $data['status']=1;
            $data['msg']='记录数获取成功';
            $data['count']=$result[0]['count'];
        }else{
            $data['msg']='记录数获取失败';
        }
        unset($arr_where);
        unset($arr_join);
        unset($arr_field);
        unset($result);
        return $data;
    }

    public function pass(){
        $data=array();
        $data['status']=0;
        $data['msg']='';
        $arr_data=array();
        $arr_data['admin_id']=$this->admin_id;
        $arr_data['pass_time']=$this->pass_time;
        $arr_data['isPass']=$this->isPass;
        $result=$this->data($arr_data)->where(array('id'=>array('IN',$this->id)))->save();
        if($result!=false){
            $data['status']=1;
            $data['msg']='审核通过';
        }else{
            $data['msg']='审核通过失败';
        }
        unset($arr_data);
        unset($result);
        return $data;
    }
}