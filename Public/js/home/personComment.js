$(document).ready(function(){
    //批量删除用户
    $('#del').click(function(){
        var arr=radio_cheched();
        var host_dir=$('#host_dir').val();
        if(arr.length==0){
            alert('请选择要删除的数据');
        }else{
            if(confirm('确定删除？')){

                $.ajax({
                    url:host_dir+"Home/ArticleComment/commentDel",
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
    var host_dir=$('#host_dir').val();
    var article_id=$('#article_id').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    $('#list_table_tbody').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Home/Member/personArticleComment",
        type:"post",
        data:{"article_id":article_id,"page":page,"page_size":page_size,"keyItem":keyItem,"key":key,"com":com},
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var articleComment=data.rows;
                if(articleComment.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < articleComment.length; i++) {
                        sHtml += '<tr class="tr_line">';
                        sHtml += '<td><input type="checkbox" class="id" name="id[]" value="'+articleComment[i]['article_comment_id']+'" /></td>';
                        sHtml += '<td width="50">'+articleComment[i]['article_comment_id']+'</td>';
                        sHtml += '<td width="100">'+articleComment[i]['member_id']+'</td>';
                        sHtml += '<td width="150">'+articleComment[i]['member_name']+'</td>';
                        sHtml += '<td width="580">'+articleComment[i]['comment_content']+'</td>';
                        sHtml += '<td width="150">'+articleComment[i]['comment_time']+'</td>';
                        sHtml += '</tr>';
                    }
                    $('#list_table_tbody').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                    $('.noData').remove();
                }else{
                    $('#list_table_tbody').html('<tr><td colspan="6" align="center">没有数据</td></tr>');
                }
            }else{
                $('#page_div').hide();
                $('#list_table_tbody').html('<tr><td colspan="6" align="center">没有数据</td></tr>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}