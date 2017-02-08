$(document).ready(function(){
//一页显示条数
    $('body').on("change",'#toPageSize',function(){
        var page_size=$(this).val();
        showList(1,page_size);
    });
    //页码跳转
    $('body').on("click",'#toPage',function(){
        var p=$('#page_text').val();
        showList(p,10);
    });

    //点击分页
    $('body').on("click",'#page_div span',function(){
        var a=$(this).find('a');
        var rel=a.attr('rel');
        var p_size=$('#toPageSize').val();
        if(rel){
            showList(rel,p_size);
        }
    });

});
//评论列表显示
function showList(page,page_size,keyItem,key,com){
    page=page||1;
    page_size=page_size||10;
    keyItem=keyItem||'id';
    key=key||'';
    com=com||'eq';
    var host_dir=$('#host_dir').val();
    var article_id=$('#article_id').val();
    var upload=$('#upload').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    $('#comment_list_div').html(sHtml_loading);
    $.ajax({
        url:host_dir+"Home/ArticleComment/getArticleComment",
        type:"post",
        data:{"page":page,"page_size":page_size,article_id:article_id},
        dataType:"json",
        success:function(data){
            if(data.status==1){
                var sHtml='';
                var articleComment=data.articleComment;
                if(articleComment.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < articleComment.length; i++) {
                        sHtml += '<div class="comment_d">';
                        sHtml += '<p><img src="'+upload+articleComment[i]['head_pic']+'" class="comment_member" width="25px" height="25px" /><span><a href="#">'+articleComment[i]['member_name']+'</a></span><span class="comment_time">'+articleComment[i]['comment_time']+'</span></p>';
                        sHtml += '<p>'+articleComment[i]['comment_content']+'</p>';
                        sHtml += '</div>';
                    }
                    $('#comment_list_div').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                }else{
                    $('#comment_list_div').html('<p class="nodata">'+data['msg']+'</p>');
                }
            }else{
                $('#comment_list_div').html('<p class="nodata">'+data['msg']+'</p>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}

//分页条(当前页码,页码总数,总记录数,每页条数)
function getPageBar(cur_page,page_count,total,page_size){
    cur_page=parseInt(cur_page);
    page_count=parseInt(page_count);
    total=parseInt(total);
    page_size=parseInt(page_size);
    var pageStr='';
    if(cur_page>page_count) //页码大于最大页数
        cur_page=page_count;
    if(cur_page<1)  //页码小于1
        cur_page=1;

    if(cur_page==1){    //如果是第一页
        pageStr+='<span class="page"><a href="javascript:void(0);">首页</a></span>';
        pageStr+='<span class="page"><a href="javascript:void(0);">上一页</a></span>';
    }else{
        pageStr+='<span class="page hov"><a href="javascript:void(0);" rel="1">首页</a></span>';
        pageStr+='<span class="page hov"><a href="javascript:void(0);" rel="'+(cur_page-1)+'">上一页</a></span>';
    }

    pageStr+='<label id="curpage">'+cur_page+'</label> / <label id="page_count">'+page_count+'</label>';

    if(cur_page>=page_count){   //如果是最后一页
        pageStr+='<span class="page"><a href="javascript:void(0);">下一页</a></span>';
        pageStr+='<span class="page"><a href="javascript:void(0);">末页</a></span>';
    }else{
        pageStr+='<span class="page hov"><a href="javascript:void(0);" rel="'+(cur_page+1)+'">下一页</a></span>';
        pageStr+='<span class="page hov"><a href="javascript:void(0);" rel="'+page_count+'">末页</a></span>';
    }

    pageStr+='<span><select id="toPageSize">';
    for(var i=1;i<=5;i++){
        if(parseInt(page_size)/10==i){
            pageStr+='<option value="'+i*10+'" selected="selected">'+i*10+'</option>';
        }else{
            pageStr+='<option value="'+i*10+'">'+i*10+'</option>';
        }
    }
    pageStr+='</select></span>';
    pageStr+='<span><input type="text" id="page_text" class="page_text" /><input type="button" value="跳转" id="toPage" /></span>';
    pageStr+='<span>共'+total+'条数据</span>';
    $('#page_div').html(pageStr);
    $('#page_div').show();
}
