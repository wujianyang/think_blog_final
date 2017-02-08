$(document).ready(function(){
    //添加用户
    $('#add').click(function(){
        getArticleType();
        $('#list_div').hide();
        $('#add_div').show();
    });
    //批量删除用户
    $('#del').click(function(){
        var arr=radio_cheched();
        var host_dir=$('#host_dir').val();
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('确定删除？')){

                $.ajax({
                    url:host_dir+"Home/Article/personDel",
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
                        alert('error');
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
            title:{
                required:true,
                minlength:4
            },
            article_type_id:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var content=UE.getEditor('editor').getContent();   //获取文章内容
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/Article/personAdd",
                type:"post",
                data:{"article[content]":content},
                dataType:"json",
                success:function(data){
                    alert(data.msg);
                    if(data.status=='1'){
                        showList(1,10,'','','eq');
                        $('#list_div').show();
                        $('#add_div').hide();
                        $('#add_form')[0].reset();  //清空表单
                        UE.getEditor('editor').setContent(''); //清空文章内容
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
            title_edit:{
                required:true,
                minlength:4
            },
            article_type_id_edit:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var content=UE.getEditor('editor_edit').getContent();   //获取文章内容
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/Article/personEdit",
                type:"post",
                data:{"article[content]":content},
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
                url:host_dir+"Home/Article/personInfo",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(data){
                    //取消当前行选中
                    cencelSelected(tr,'tr');
                    getArticleType('edit');
                    if(data.status==1){
                        var article=data.article;
                        if(article.length!=0){
                            for(var i in article){
                                if(i!='content'){
                                    $("#"+i+"_edit").val(article[i]);
                                }else{
                                    UE.getEditor('editor_edit').setContent(article[i]);
                                }
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
        url:host_dir+"Home/Member/personArticle",
        type:"post",
        data:{"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var article=data.rows;
                if(article.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < article.length; i++) {
                        sHtml += '<tr class="tr_line">';
                        sHtml += '<td><input type="checkbox" class="id" name="id[]" value="' + article[i]["article_id"] + '" /></td>';
                        sHtml += '<td width="50">' + article[i]["article_id"] + '</td>';
                        sHtml += '<td width="200"><a class="info" href="javascript:void(0);" value="' + article[i]["article_id"] + '">' + str_sub(article[i]["title"],40) + '</a></td>';
                        sHtml += '<td width="150">' + article[i]["article_type_name"] + '</td>';
                        sHtml += '<td width="50">' + article[i]["hitnum"] + '</td>';
                        sHtml += '<td width="150">' + article[i]["create_time"] + '</td>';
                        sHtml += '<td width="100"><a href="'+host_dir+'Home/Member/personArticleComment/article_id/'+article[i]['article_id']+'.html">查看评论</a></td>';
                        sHtml += '</tr>';
                    }
                    $('#list_table_tbody').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_tbody').html('<tr><td colspan="7" align="center">没有数据</td></tr>');
                }
            }else{
                $('#page_div').hide();
                $('#list_table_tbody').html('<tr><td colspan="7" align="center">没有数据</td></tr>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}

//获取用户文章类型
function getArticleType(flag){
    flag=flag||'';
    var host_dir=$('#host_dir').val();
        $.ajax({
            url: host_dir+"Home/ArticleType/getPersonArticleType",
            type: "post",
            dataType: "json",
            async: false,
            success: function (data) {
                if(flag!='edit') {
                    $('#article_type_id').html('');
                }else{
                    $('#article_type_id_edit').html('');
                }
                var articleType = data.articleType;
                if (articleType != null) {
                    for (var i = 0; i < articleType.length; i++) {
                        if(flag!='edit'){
                            $('#article_type_id').append('<option value="' + articleType[i]['id'] + '">' + articleType[i]['article_type_name'] + '</option>');
                        }else{
                            $('#article_type_id_edit').append('<option value="' + articleType[i]['id'] + '">' + articleType[i]['article_type_name'] + '</option>');
                        }
                    }
                } else {
                    alert('请先添加文章类别');
                    if(flag!='edit'){
                        $('#article_type_id').html('<option>没有数据</option>');
                    }else{
                        $('#article_type_id_edit').html('<option>没有数据</option>');
                    }
                }
            },
            error: function (data) {
                console.log('getArticleType>error');
                console.log(data.responseText);
                showList(1,10,'','','eq');
            }
        });
}