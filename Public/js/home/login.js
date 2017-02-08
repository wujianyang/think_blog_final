$(document).ready(function(){
    var host_url=$('#host_dir').val();
    //添加用户表单验证和提交
    $('#add_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            member_name:{
                required:true,
                minlength:8
            },
            password:{
                required: true,
                minlength: 6
            }
        },
        submitHandler:function(form){
            $(form).ajaxSubmit({
                url:host_url+'Home/Member/register',
                type:"post",
                dataType:"json",
                success:function(data){
                    console.log(data);
                    alert(data.msg);
                    if(data.status=='1'){
                        location.href=host_url+'Home/Member/login';
                    }
                },
                error:function(data){
                    console.log('register>error');
                    console.log(data.responseText);
                }
            });
        }
    });
});
