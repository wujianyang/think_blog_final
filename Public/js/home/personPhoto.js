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
            if(confirm('确定删除？')){
                $.ajax({
                    url:host_dir+"Home/Photo/personDel",
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
            photo_title:{
                required:true,
                minlength:2
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/Photo/personAdd",
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
            photo_title_edit:{
                required:true,
                minlength:2
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/Photo/personEdit",
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
                url:host_dir+"Home/Photo/personInfo",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(data){
                    //取消当前行选中
                    cencelSelected(tr,'tr');
                    if(data.status==1){
                        var photo=data.rows;
                        if(photo.length!=0){
                            for(var i in photo){
                                $("#"+i+"_edit").val(photo[i]);
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
        url:host_dir+"Home/Member/personPhoto",
        type:"post",
        data:{"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var photo=data.rows;
                if(photo.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < photo.length; i++) {
                        sHtml += '<tr class="tr_line">';
                        sHtml += '<td width="50"><input type="checkbox" class="id" name="id[]" value="' + photo[i]["photo_id"] + '" /></td>';
                        sHtml += '<td width="50">' + photo[i]["photo_id"] + '</td>';
                        sHtml += '<td width="200"><a href="javascript:void(0);" class="info" value="'+  photo[i]["photo_id"]+'">' + photo[i]["photo_title"] + '</a></td>';
                        sHtml += '<td width="100"><a href="'+host_dir+'Home/Member/personPhotoImg/photo_id/'+photo[i]["photo_id"]+'.html">' + photo[i]["photo_count"] + '</a></td>';
                        sHtml += '</tr>';
                    }
                    $('#list_table_tbody').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_tbody').html('<tr><td colspan="4" align="center">没有数据</td></tr>');
                }
            }else{
                $('#page_div').hide();
                $('#list_table_tbody').html('<tr><td colspan="4" align="center">没有数据</td></tr>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}