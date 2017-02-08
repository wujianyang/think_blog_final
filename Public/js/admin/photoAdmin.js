$(document).ready(function(){
    //添加数据
    $('#add').click(function(){
        getMember();

        $('#list_div').hide();
        $('#add_div').show();
    });
    //批量删除
    $('#del').click(function(){
        var arr=new Array();
        arr=radio_cheched();
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('该相册的相关相片也会一起删除,确定删除？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/Photo/del",
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
                        console.log('delete>error');
                        console.log(data.responseText);
                    }
                });
            }
        }
    });

    //添加表单验证和提交
    $('#add_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            article_type_name:{
                required:true,
                minlength:2
            },
            member_id:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Admin/Photo/add",
                type:"post",
                data:{"act":"add"},
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
    //编辑表单验证和提交
    $('#edit_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            article_type_name_edit:{
                required:true,
                minlength:2
            },
            member_id_edit:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Admin/Photo/edit",
                type:"post",
                data:{"act":"edit"},
                dataType:"json",
                success:function(data){
                    alert(data.msg);
                    if(data.status=='1'){
                        showList(1,10,'','','eq');
                        $('#list_div').show();
                        $('#edit_div').hide();
                        $('#edit_form')[0].reset();
                    }
                },
                error:function(data){
                    console.log('edit>error');
                    console.log(data.responseText);
                }
            });
        }
    });



    //查看用户信息
    $('.info').live('click', function(){
        var tr=$(this).parents('tr');
        var id= $(this).attr('value');
        if(/\d/.test(id)){
            getMember('edit');
            var host_dir=$('#host_dir').val();
            $.ajax({
                url:host_dir+"Admin/Photo/info",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(result){
                    //取消当前行选中
                    cencelSelected(tr,'tr');
                    if(result.status==1){
                        var data=result.rows;
                        if(data.length!=0){
                            data=data[0];
                            for(var i in data){
                                $("#"+i+"_edit").val(data[i]);
                            }
                            $('#edit_div').show();
                            $('#list_div').hide();
                        }else{
                            alert('没有此数据');
                        }
                    }else if(result.status==0){
                        alert(result.msg);
                    }
                },
                error:function(data){
                    //取消当前行选中
                    tr.removeClass('sel');
                    tr.find('.id').attr('checked',false);
                    console.log(data.responseText);
                    console.log('info>error');
                    alert('请求失败');
                }
            });
        }else{
            alert('用户参数错误');
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
        url:host_dir+"Admin/Photo/index",
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
                        sHtml += '<td width="200"><a class="info" href="javascript:void(0);" value="' + rows[i]["id"] + '">' + rows[i]["photo_title"] + '</a></td>';
                        sHtml += '<td width="100">' + rows[i]["member_id"] + '</td>';
                        sHtml += '<td width="200">' + rows[i]["member_name"] + '</td>';
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
