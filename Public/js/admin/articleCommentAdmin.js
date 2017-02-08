$(document).ready(function(){
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
                    url:host_dir+"Admin/ArticleComment/del",
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
});
//列表显示
function showList(page,page_size,keyItem,key,com){
    page=page||1;
    page_size=page_size||10;
    keyItem=keyItem||'id';
    key=key||'';
    com=com||'eq';
    var article_id=$('#article_id').val();
    var host_dir=$('#host_dir').val();
    if(article_id!=''){
        var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
        $('#list_table_tbody').html(sHtml_loading);

        $.ajax({
            url:host_dir+"Admin/ArticleComment/index?article_id="+article_id,
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
                            sHtml += '<td width="100">' + rows[i]["member_id"] + '</td>';
                            sHtml += '<td width="150">' + rows[i]["member_name"] + '</td>';
                            sHtml += '<td width="580">' + rows[i]["comment_content"] + '</td>';
                            sHtml += '<td width="150">' + rows[i]["comment_time"] + '</td>';
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
    }else{
        alert('文章ID为空');
    }

}