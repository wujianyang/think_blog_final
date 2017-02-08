$(document).ready(function(){
    //添加用户
    $('#add').click(function(){
        $('#list_div').hide();
        $('#add_div').show();
    });

    //批量删除用户
    $('#del').click(function(){
        var del=$('.id');
        var arr=radio_cheched();
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('确定删除？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/Admin/del",
                    type:"post",
                    dataType:"json",
                    data:{id:arr},
                    success:function(data){
                        alert(data.msg);
                        if(data.status=='1'){
                            showList(1,10,'','','eq');
                        }
                    },
                    error:function(data){
                        console.log('del>error');
                        console.log(data.responseText);
                        showList(1,10,'','','eq');
                    }
                });
            }
        }
    });
    //批量冻结用户
    $('#freeze').click(function(){
        var fre=$('.id');
        var arr=radio_cheched();
        if(arr.length==0){
            alert('请选择要冻结的管理员');
        }else{
            if(confirm('确定冻结？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/Admin/freeze",
                    type:"post",
                    dataType:"json",
                    data:{id:arr,is_f:1},
                    success:function(data){
                        alert(data.msg);
                        if(data.status==1){
                            showList(1,10,'','','eq');
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                        alert('请求失败');
                    }
                });
            }
        }
    });

    //批量解除冻结用户
    $('#not_freeze').click(function(){
        var fre=$('.id');
        var arr=radio_cheched();
        if(arr.length==0){
            alert('请选择要激活的管理员');
        }else{
            if(confirm('确定激活？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/Admin/freeze",
                    type:"post",
                    dataType:"json",
                    data:{id:arr,is_f:0},
                    success:function(data){
                        alert(data.msg);
                        if(data.status==1){
                            showList(1,10,'','','eq');
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                        alert('请求失败');
                    }
                });
            }
        }
    });

    //批量重置用户密码
    $('#resetPasswd').click(function(){
        var rep=radio_cheched();
        if(rep.length==0){
            alert('请选择要重置密码的用户');
        }else{
            if(confirm('确定重置密码？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/Member/resetPasswd",
                    type:"post",
                    dataType:"json",
                    data:{id:rep},
                    success:function(data){
                        alert(data.msg);
                        if(data.status==1){
                            showList(1,10,'','','eq');
                        }
                    },
                    error:function(data){
                        console.log(data.responseText);
                        alert('请求失败');
                    }
                });
            }
        }
    });

    //添加用户表单验证和提交
    $('#add_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            admin_name:{
                required:true,
                minlength:4
            },
            password:{
                required: true,
                minlength: 6
            },
            password2:{
                required: true,
                minlength: 6,
                equalTo: "#password"
            }
        },
        messages:{
            password2:{
                equalTo:"密码不一致"
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Admin/Admin/add",
                type:"post",
                dataType:"json",
                success:function(data){
                    alert(data.msg);
                    if(data.status=='1'){
                        showList(1,10,'','','eq');
                        $('#list_div').show();
                        $('#add_div').hide();
                        $('#add_form')[0].reset();
                    }
                },
                error:function(data){
                    console.log('add>error');
                    console.log(data.responseText);
                }
            });
        }
    });
});
//列表显示
function showList(page,page_size,keyItem,key,com){
    page=page||1;
    page_size=page_size||10;
    keyItem=keyItem||'id';
    key=key||'';
    com=com||'eq';
    var host_dir=$('#host_dir').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px"  /></div>';
    $('#list_table_tbody').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Admin/Admin/index",
        type:"post",
        data:{"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1){
                var sHtml='';
                var rows=data.rows;
                if(rows.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < rows.length; i++) {
                        sHtml += '<tr class="tr_line">';
                        sHtml += '<td><input type="checkbox" class="id" name="id[]" value="' + rows[i]["id"] + '" /></td>';
                        sHtml += '<td width="50">' + rows[i]["id"] + '</td>';
                        sHtml += '<td width="150"><a class="info" href="javascript:void(0);" value="' + rows[i]["id"] + '">' + rows[i]["admin_name"] + '</a></td>';
                        sHtml += '<td width="150">' + rows[i]["last_ip"] + '</td>';
                        sHtml += '<td width="150">' + rows[i]["last_time"] + '</td>';
                        if (rows[i]["is_freeze"] == '1')
                            sHtml += '<td width="100">已冻结</td>';
                        else
                            sHtml += '<td width="100">已激活</td>';
                        sHtml += '</tr>';
                    }
                    $('.list_table_div .error').remove();
                    $('#list_table_tbody').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_tbody').html('');
                    $('.page_div').html('');
                    if($('.list_table_div .error').length>0){
                        $('.list_table .error').html('没有数据');
                    }else{
                        $('.list_table').after('<p class="error">没有数据</p>');
                    }
                }
            }else{
                alert(data.msg);
                $('#list_table_tbody').html('');
                $('.page_div').html('');
                if($('.list_table_div .error').length>0){
                    $('.list_table .error').html('没有数据');
                }else{
                    $('.list_table').after('<p class="error">没有数据</p>');
                }
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}