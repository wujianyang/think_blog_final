$(document).ready(function(){
    //关注用户
    $('body').on('click','.focus',function(){
        var member_id=$(this).attr('rel');
        var host_dir=$('#host_dir').val();
        $.ajax({
            url:host_dir+"Home/Member/focusFriends",
            type:"post",
            data:{"member_id":member_id},
            dataType:"json",
            success:function(data){
                if(data.status==1){
                    showList(1,20);
                }else{
                    alert(data.msg);
                    if(data.msg=='登录超时'){
                        location.href=host_dir+"Home/Member/login.html";
                    }
                }
            },
            error:function(data){
                console.log('focus>error');
                console.log(data.responseText);
            }
        });
    });

    //取消关注用户
    $('body').on('click','.cencelFocus',function(){
        var member_id=$(this).attr('rel');
        var host_dir=$('#host_dir').val();
        $.ajax({
            url:host_dir+"Home/Member/focusFriends",
            type:"post",
            data:{"member_id":member_id,isCencel:'cencel'},
            dataType:"json",
            success:function(data){
                if(data.status==1){
                    showList(1,20);
                }else{
                    alert(data.msg);
                    if(data.msg=='登录超时'){
                        location.href=host_dir+"Home/Member/login.html";
                    }
                }
            },
            error:function(data){
                console.log('focus>error');
                console.log(data.responseText);
            }
        });
    });
});
//列表显示
function showList(page,page_size,keyItem,key,com){
    page=page||1;
    page_size=page_size||20;
    keyItem=keyItem||'id';
    key=key||'';
    com=com||'eq';
    var f=$('#f').val();
    var host_dir=$('#host_dir').val();
    var upload=$('#upload').val();
    var f=$('#f').val();
    var page_search=$('#page_search').val();
    var key_search=$('#keys_search').val();
    var keyItem_search=$('#keyItem_search').val();
    var member_id=$('#member_id').val();
    var sHtml_loading='<div class="loading"><img src="'+host_dir+'Public/images/loading.gif" width="100px" /></div>';
    var url=host_dir+"Home/Member/friends/f/"+f+"/member_id/"+member_id;
    var type='post';
    $('#list').html(sHtml_loading);
    var ajax_data='{';
    ajax_data+='page:"'+page+'",page_size:"'+page_size+'"';
    if(page_search=='search'){
        url=host_dir+'Home/Index/search';
        ajax_data+=',key:"'+key_search+'",keyItem:"'+keyItem_search+'"';
        type='get';
    }
    ajax_data+='}';
    $.ajax({
        url:url,
        type:type,
        data:eval('(' + ajax_data + ')'),
        dataType:"json",
        success:function(data){
            if(data.status==1 && data.rows!=null){
                var sHtml='';
                var friends=data.rows;
                if(friends.length>0) {
                    //拼接数据列表
                    for (var i = 0; i < friends.length; i++) {
                        sHtml += '<li>';
                        sHtml += '<p><img src="'+upload+friends[i]['head_pic']+'" width="30" height="30" /><span><a href="'+host_dir+'Home/Member/index/member_id/'+friends[i]['member_id']+'">'+friends[i]['member_name']+'</a></span></p>';
                        if(friends[i]['sex']==1){
                            sHtml += '<p><label>性别：<span></span>男</label></p>';
                        }else if(friends[i]['sex']==0){
                            sHtml += '<p><label>性别：<span></span>女</label></p>';
                        }
                        sHtml+= '<p><label>访问量：<span>'+friends[i]['hitnum']+'</span></label></p>';
                        sHtml += '<p><span><a href="'+host_dir+'Home/Member/index/f/focus/member_id/'+friends[i]['member_id']+'">关注('+friends[i]['focus_count']+')</a></span><span><a href="'+host_dir+'Home/Member/index/f/fans/member_id/'+friends[i]['member_id']+'">粉丝('+friends[i]['fans_count']+')</a></span></p>';
                        if(friends[i]['isfocus']==1){
                            sHtml += '<div class="btn cencelFocus" rel="'+friends[i]['member_id']+'">取消关注</div>';
                        }else{
                            sHtml += '<div class="btn focus" rel="'+friends[i]['member_id']+'">关注</div>';
                        }
                        sHtml += '</li>';
                    }
                    sHtml += '<div class="clear"></div>';
                    $('#list').html(sHtml);
                    //生成分页条
                    getPageBar(page,Math.ceil(data.count/page_size),data.count,page_size);
                }else{
                    $('#list').html('<p class="noData">没有数据</p>');
                }
            }else{
                $('#page_div').hide();
                $('#list').html('<p class="noData">没有数据</p>');
            }
        },
        error:function(data){
            console.log('showList>error');
            console.log(data.responseText);
        }
    });
}