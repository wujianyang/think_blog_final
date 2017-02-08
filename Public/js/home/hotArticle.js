//热门文章列表显示
function showList(page,page_size,keyItem,key,com){
    page=page||1;
    page_size=page_size||20;
    keyItem=keyItem||'id';
    key=key||'';
    com=com||'eq';
    var host_dir=$('#host_dir').val();
    var article_type_id=$('#article_type_id').val();
    var suffix=$('#suffix').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    $('#article_list_div').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Home/Article/articleListPage",
        type:"post",
        data:{"page":page,"page_size":page_size,article_type_id:article_type_id},
        dataType:"json",
        success:function(data){
            if(data.status==1){
                var sHtml='';
                var article=data.article;
                if(article.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < article.length; i++) {
                        sHtml+='<p>';
                        sHtml+='<a href="'+host_dir+'Home/Article/index/article_id/'+article[i]['id']+'.'+suffix+'" title="'+article[i]['title']+'" target="_target">'+str_sub(article[i]['title'],40)+'</a>';
                        sHtml+='<span>'+article[i]['create_time']+'</span>';
                        sHtml+='</p>';
                    }
                    $('#article_list_div').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                }else{
                    $('#article_list_div').html('<p class="nodata">'+data['msg']+'</p>');
                }
            }else{
                $('#article_list_div').html('<p class="nodata">'+data['msg']+'</p>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}