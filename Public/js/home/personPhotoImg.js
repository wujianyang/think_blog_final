$(document).ready(function(){
    $()
    //添加
    $('#add').click(function(){
        $('#list_div').hide();
        $('#add_div').show();
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
        var host_dir=$('#host_dir').val();
        var arr=new Array();
        var op=$('.op');
        for(var i=0;i<op.length;i++){
            arr.push(op.eq(i).find('.id').val());
        }
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('确定删除？')){
                $.ajax({
                    url:host_dir+"Home/PhotoImg/personDel",
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
            img_title:{
                required:true,
                minlength:2
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            var photo_id=$('#photo_id').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/PhotoImg/personAdd",
                type:"post",
                data:{"photo_id":photo_id},
                dataType:"json",
                success:function(data){
                    console.log(data);
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
            img_title_edit:{
                required:true,
                minlength:2
            }
        },
        submitHandler:function(form){
            var host_dir=$('#host_dir').val();
            var photo_id=$('#photo_id').val();
            $(form).ajaxSubmit({
                url:host_dir+"Home/PhotoImg/personEdit",
                type:"post",
                dataType:"json",
                data:{"photo_id":photo_id},
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
            var host_dir=$('#host_dir').val();
            var root=$('#root').val();
            var upload=$('#upload').val();
            $.ajax({
                url:host_dir+"Home/PhotoImg/personInfo",
                type:"post",
                data:{"id":id},
                dataType:"json",
                success:function(data){
                    //取消当前行选中
                    cencelSelected(photo_div,'div');
                    if(data.status==1){
                        var photoImg=data.rows;
                        if(photoImg.length!=0){
                            for(var i in photoImg){
                                if(i!='img_src'){
                                    $("#"+i+"_edit").val(photoImg[i]);
                                }else{
                                    $("#photo_src").attr('src',upload+photoImg[i]);
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
                    alert('请求失败');
                    console.log(data.responseText);
                    console.log('info>error');
                }
            });
        }else{
            alert('用户参数错误');
        }
    });
    //查看相片大图
    $('body').on('click','.img_div img',function(){
        var img_src=$(this).attr('src');
        $('#pic_div img').attr('src',img_src);
        $('#list_div').hide();
        $('#pic_div').show();
    });
    //关闭相片大图
    $('body').on('click','#pic_div',function(){
        $('#list_div').show();
        $('#pic_div').hide();
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
    var upload=$('#upload').val();
    var photo_id=$('#photo_id').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    $('#list_table_div').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Home/Member/personPhotoImg",
        type:"post",
        data:{"photo_id":photo_id,"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var photoImg=data.rows;
                if(photoImg.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < photoImg.length; i++) {
                        sHtml += '<div class="photo_div">';
                        sHtml += '<input type="hidden" class="id" name="id[]" value="'+photoImg[i]['id']+'" />';
                        sHtml += '<div class="img_div">';
                        sHtml += '<img src="'+upload+photoImg[i]['img_src']+'" width="200" height="150" />';
                        sHtml += '</div>';
                        sHtml += '<p>ID：<a class="info" href="javascript:void(0);" value="'+photoImg[i]['id']+'">'+photoImg[i]['id']+' | '+photoImg[i]['img_title']+'</a></p>';
                        sHtml += '</div>';
                    }
                    $('#list_table_div').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_div').html('<p class="nodata">没有数据</p>');
                }
            }else{
                $('#page_div').hide();
                $('#list_table_div').html('<p class="nodata">没有数据</p>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}