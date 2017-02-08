$(document).ready(function(){
    //添加数据
    $('#add').click(function(){
        getMember();
        var member_id=$('#member_id').val();
        getPhotoByMemberId(member_id);
        $('#list_div').hide();
        $('#add_div').show();
    });
    //选择用户改变相册
    $('body').on('change','#member_id',function(){
        var member_id=$(this).val();
        getPhotoByMemberId(member_id);
    });
    $('body').on('change','#member_id_edit',function(){
        var member_id=$(this).val();
        getPhotoByMemberId(member_id,'edit');
    });
    //全选
    $('#all_id').click(function(){
        var all_id_len=$('.photo_div').length;
        var id_len=$('.op').length;
        if(all_id_len!=id_len){
            $('.photo_div').addClass('op');
        }else{
            $('.photo_div').removeClass('op');
        }
    });
    //单击选中相片
    $('body').on('click','.photo_div',function(){
        if($(this).attr('class').indexOf('op')!=-1){
            $(this).removeClass('op');
        }else{
            $(this).addClass('op');
        }
    });
    //批量删除
    $('#del').click(function(){
        var arr=new Array();
        var op=$('.op');
        for(var i=0;i<op.length;i++){
            arr.push(op.eq(i).find('.id').val());
        }
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('该相册的相关相片也会一起删除,确定删除？')){
                var host_dir=$('#host_dir').val();
                $.ajax({
                    url:host_dir+"Admin/PhotoImg/del",
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
            img_title:{
                required:true,
                minlength:4
            },
            member_id:{
                required: true,
                minlength: 1
            },
            photo_id:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Admin/PhotoImg/add",
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
    //编辑表单验证和提交
    $('#edit_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            img_title_edit:{
                required:true,
                minlength:4
            },
            member_id_edit:{
                required: true,
                minlength: 1
            },
            photo_id_edit:{
                required: true,
                minlength: 1
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Admin/PhotoImg/edit",
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
        var photo_div=$(this).parents('.photo_div');
        var id= $(this).attr('value');
        if(/\d/.test(id)){
            getMember('edit');
            var host_dir=$('#host_dir').val();
            $.ajax({
                url:host_dir+"Admin/PhotoImg/info",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(result){
                    //取消当前行选中
                    cencelSelected(photo_div,'div');
                    if(result.status==1){
                        var data=result.rows;
                        if(data.length!=0){
                            data=data[0];
                            getPhotoByMemberId(data.member_id,'edit');
                            for(var i in data){
                                if(i != 'img_src'){
                                    $("#"+i+"_edit").val(data[i]);
                                }else{
                                    $("#"+i+"_edit").attr('src',host_dir+'Upload/'+data[i]);
                                }
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
    $('#list_table_div').html(sHtml_loading);

    $.ajax({
        url:host_dir+"Admin/PhotoImg/index",
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
                        sHtml+='<div class="photo_div">';
                        sHtml+='<input type="hidden" class="id" name="id[]" value="'+rows[i]['id']+'" />';
                        sHtml+='<div class="img_div"><img src="'+host_dir+'Upload/'+rows[i]['img_src']+'" width="200" height="150" /></div>';
                        sHtml+='<p><a class="info" href="javascript:void(0);" value="'+rows[i]['id']+'" title="'+rows[i]['img_title']+'">相片名称：'+str_sub(rows[i]['img_title'],8)+'</a></p>';
                        sHtml+='<p><span>相册名称</span>：'+rows[i]['photo_title']+'</p>';
                        sHtml+='<p><span>用户名称</span>：'+rows[i]['member_name']+'</p>';
                        sHtml+='</div>';
                    }
                    $('.list_table_div .error').remove();
                    $('#list_table_div').html(sHtml);
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
