$(document).ready(function(){
    //搜索
    $('#search').click(function(){
        var key=$('#key').val();
        var keyItem=$('#keyItem').val();
        var com=keyItem.split('+')[1];
        keyItem=keyItem.split('+')[0];
        showList(1,10,keyItem,key,com);
    });
    //一页显示条数
    $('body').on("change",'#toPageSize',function(){
        var key=$('#key').val();
        var keyItem=$('#keyItem').val();
        var com=keyItem.split('+')[1];
        keyItem=keyItem.split('+')[0];
        var page_size=$(this).val();
        showList(1,page_size,keyItem,key,com);
    });
    //页码跳转
    $('body').on("click",'#toPage',function(){
        var p=$('#page_text').val();
        var key=$('#key').val();
        var keyItem=$('#keyItem').val();
        var com=keyItem.split('+')[1];
        keyItem=keyItem.split('+')[0];
        showList(p,10,keyItem,key,com);
    });

    //点击分页
    $('body').on("click",'#page_div span',function(){
        var a=$(this).find('a');
        var rel=a.attr('rel');
        var p_size=$('#toPageSize').val();
        var k=$('#key').val();
        var keyItem=$('#keyItem').val();
        var com=keyItem.split('+')[1];
        keyItem=keyItem.split('+')[0];
        if(rel){
            showList(rel,p_size,keyItem,k,com);
        }
    });
});
//分页条
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