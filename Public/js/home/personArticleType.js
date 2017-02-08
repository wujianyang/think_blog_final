$(document).ready(function(){
    //添加
    $('#add').click(function(){
        $('#list_div').hide();
        $('#add_div').show();
    });
    //批量删除
    $('#del').click(function(){
        var arr=radio_cheched();
        var host_dir=$('#host_dir').val();
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            console.log(arr);
            if(confirm('确定删除？')){
                $.ajax({
                    url:host_dir+"Home/ArticleType/personDel",
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
                        alert('操作失败');
                        console.log(data.responseText);
                        showList(1,10,'','','eq');
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
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/ArticleType/personAdd",
                type:"post",
                dataType:"json",
                success:function(data){
                    alert(data.msg);
                    if(data.status=='1'){
                        showList(1,10,'','','eq');
                        $('#list_div').show();
                        $('#add_div').hide();
                        $('#add_form')[0].reset();  //清空表单
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
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/ArticleType/personEdit",
                type:"post",
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

    //查看信息
    $('body').on('click','.info',function(){
        var tr=$(this).parents('tr');
        var id= $(this).attr('value');
        if(/\d/.test(id)){
            var host_dir=$('#host_dir').val();
            $.ajax({
                url:host_dir+"Home/ArticleType/personInfo",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(data){
                    //取消当前行选中
                    cencelSelected(tr,'tr');
                    if(data.status==1){
                        var articleType=data.rows;
                        if(articleType.length!=0){
                            for(var i in articleType){
                                $("#"+i+"_edit").val(articleType[i]);
                            }
                            $('#edit_div').show();
                            $('#list_div').hide();
                        }else{
                            alert('没有此用户');
                        }
                    }else if(data.status==0){
                        alert(data.msg);
                    }
                },
                error:function(data){
                    //取消当前行选中
                    tr.removeClass('sel');
                    tr.find('.id').attr('checked',false);
                    alert('请求失败');
                    console.log(data.responseText);
                    console.log('info>error');
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
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    $('#list_table_tbody').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Home/Member/personArticleType",
        type:"post",
        data:{"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var articleType=data.rows;
                if(articleType.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < articleType.length; i++) {
                        sHtml += '<tr class="tr_line">';
                        sHtml += '<td width="50"><input type="checkbox" class="id" name="id[]" value="' + articleType[i]["article_type_id"] + '" /></td>';
                        sHtml += '<td width="50">' + articleType[i]["article_type_id"] + '</td>';
                        sHtml += '<td width="200"><a href="javascript:void(0);" class="info" value="'+  articleType[i]["article_type_id"]+'">' + articleType[i]["article_type_name"] + '</a></td>';
                        sHtml += '</tr>';
                    }
                    $('#list_table_tbody').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_tbody').html('<tr><td colspan="3" align="center">没有数据</td></tr>');
                }
            }else{
                $('#page_div').hide();
                $('#list_table_tbody').html('<tr><td colspan="3" align="center">没有数据</td></tr>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}