$(document).ready(function(){
    //关闭添加用户窗口
    $('#close_div').click(function(){
        $('#list_div').show();
        $('#add_div').hide();
    });
    //关闭编辑用户窗口
    $('#close_edit_div').click(function(){
        $('#list_div').show();
        $('#edit_div').hide();
    });


    //点击全选
    $('#all_id').click(function() {

        if($(this).attr('checked')=='checked'){
            $('.id').attr('checked',true);
        }else{
            $('.id').attr('checked',false);
        }
    });
    //单击行选中
    $('body').on('click','.tr_line',function(){
        var input_id=$(this).find('.id');
        var is_checked=input_id.attr('checked');
        if(is_checked=='checked'){  //已选中，要取消选中状态
            $(this).removeClass('sel');
            input_id.attr('checked',false);
        }else{  //未选中，进行选中状态
            $(this).addClass('sel');
            input_id.attr('checked',true);
        }
        //判断是否全部选中
        var id_len=$('.id').length;
        var id_check_len=$(".id:checked").length;
        if(id_len == id_check_len){
            $('#all_id').attr('checked',true);
        }else{
            $('#all_id').attr('checked',false);
        }
    });
});
function cencelSelected(e,f){
    //e为选中元素,f为判断是tr行还是div块
    var c='sel';
    if(f=='div'){
        c='op';
    }
    if(e.attr('class').indexOf(c)>-1){  //被选中状态
        e.removeClass(c);  //取消选中状态
        if(f=='tr'){
            e.find('.id').attr('checked',false);
        }
    }else{  //被取消选中状态
        e.addClass(c);  //选中状态
        if(f=='tr'){
            e.find('.id').attr('checked',true);
        }
    }

}

//获取用户信息
function getMember(flag) {
    flag = flag || '';
    //获取用户名信息
    $.ajax({
        url: "/think_blog/index.php/Admin/Member/getMember",
        type: "post",
        async:false,
        dataType: "json",
        success: function (data) {

            if(flag!='edit'){
                $('#member_id').html('');
            }else{
                $('#member_id_edit').html('');
            }

            var rows = data.rows;
            if (rows.length > 0) {
                for (var i = 0; i < rows.length; i++) {
                    if (flag != 'edit') {
                        $('#member_id').append('<option value="' + rows[i]['id'] + '">' + rows[i]['member_name'] + '</option>');
                    } else {
                        $('#member_id_edit').append('<option value="' + rows[i]['id'] + '">' + rows[i]['member_name'] + '</option>');
                    }
                }
            } else {
                $('#member_id').html('<option>没有用户数据</option>');
            }
        },
        error: function (data) {
            console.log('clickAdd>error');
            console.log(data.responseText);
            showList(1,10,'','','eq');
        }
    });
}

function getArticleTypeByMemberId(m_id,flag){
    m_id=m_id||'';
    flag=flag||'';
    if(m_id!='') {
        //根据用户名获取文章类别
        $.ajax({
            url: "/think_blog/index.php/Admin/ArticleType/getArticleType",
            type: "post",
            dataType: "json",
            async: false,
            data: {member_id:m_id},
            success: function (data) {
                if(flag!='edit') {
                    $('#article_type_id').html('');
                }else{
                    $('#article_type_id_edit').html('');
                }
                var rows = data.rows;
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        if(flag!='edit'){
                            $('#article_type_id').append('<option value="' + rows[i]['id'] + '">' + rows[i]['article_type_name'] + '</option>');
                        }else{
                            $('#article_type_id_edit').append('<option value="' + rows[i]['id'] + '">' + rows[i]['article_type_name'] + '</option>');
                        }
                    }
                } else {
                    if(flag!='edit'){
                        $('#article_type_id').html('<option>没有数据</option>');
                    }else{
                        $('#article_type_id_edit').html('<option>没有数据</option>');
                    }
                }
            },
            error: function (data) {
                console.log('clickAdd>error');
                console.log(data.responseText);
                showList(1,10,'','','eq');
            }
        });
    }else{
        console.log('用户ID错误');
    }
}
function getPhotoByMemberId(m_id,flag){
    m_id=m_id||'';
    flag=flag||'';
    if(m_id!='') {
        //根据用户名获取相册
        $.ajax({
            url: "/think_blog/index.php/Admin/Photo/getPhoto",
            type: "post",
            dataType: "json",
            async: false,
            data: {member_id:m_id},
            success: function (data) {
                if(flag!='edit') {
                    $('#photo_id').html('');
                }else{
                    $('#photo_id_edit').html('');
                }
                var rows = data.rows;
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        if(flag!='edit'){
                            $('#photo_id').append('<option value="' + rows[i]['id'] + '">' + rows[i]['photo_title'] + '</option>');
                        }else{
                            $('#photo_id_edit').append('<option value="' + rows[i]['id'] + '">' + rows[i]['photo_title'] + '</option>');
                        }
                    }
                } else {
                    if(flag!='edit'){
                        $('#photo_id').html('<option>没有数据</option>');
                    }else{
                        $('#photo_id_edit').html('<option>没有数据</option>');
                    }
                }
            },
            error: function (data) {
                console.log('clickAdd>error');
                console.log(data.responseText);
                showList(1,10,'','','eq');
            }
        });
    }else{
        console.log('用户ID错误');
    }
}

/*function getPhotoByMemberId(m_id,flag){
    m_id=m_id||'';
    flag=flag||'';
    if(m_id!='') {
        //根据用户名获取文章类别
        $.ajax({
            url: "/think_blog/index.php/Admin/Photo/getPhoto",
            type: "post",
            dataType: "json",
            async: false,
            data: {member_id:m_id},
            success: function (data) {
                if(flag!='edit') {
                    $('#photo_id').html('');
                }else{
                    $('#photo_id_edit').html('');
                }
                var rows = data.rows;
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        if(flag!='edit'){
                            $('#photo_id').append('<option value="' + rows[i]['id'] + '">' + rows[i]['article_type_name'] + '</option>');
                        }else{
                            $('#photo_id_edit').append('<option value="' + rows[i]['id'] + '">' + rows[i]['article_type_name'] + '</option>');
                        }
                    }
                } else {
                    if(flag!='edit'){
                        $('#photo_id').html('<option>没有数据</option>');
                    }else{
                        $('#photo_id_edit').html('<option>没有数据</option>');
                    }
                }
            },
            error: function (data) {
                console.log('clickAdd>error');
                console.log(data.responseText);
                showList(1,10,'','','eq');
            }
        });
    }else{
        console.log('用户ID错误');
    }
}*/

//获取多选框选中用户
function radio_cheched(){
    var r=$('.id');
    var arr_r=new Array();
    for(var i=0;i<r.length;i++){
        if(r[i].checked){
            arr_r.push(r[i].value);
        }
    }
    return arr_r;
}

//标题指定长度截取
function str_sub(str,len){
    if(str.length>len){
        return str.substr(0,len)+'...';
    }else{
        return str;
    }
}