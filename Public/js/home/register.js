$(document).ready(function(){
    var host_url=$('#host_dir').val();
    //添加用户表单验证和提交
    $('#add_form').validate({
        onsubmit:true,// 是否在提交是验证
        onfocusout:false,// 是否在获取焦点时验证
        rules:{
            member_name:{
                required:true,
                minlength:4
            },
            password:{
                required: true,
                minlength: 6
            },
            password2:{
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            email:{
                required: true,
                email:true
            },
            tel:{
                required: true
            },
            address:{
                required: true,
                minlength:4
            },
            question:{
                required: true,
                minlength:4
            },
            answer:{
                required: true,
                minlength:4
            }
        },
        messages:{
            password2:{
                equalTo:"密码不一致"
            }
        },
        submitHandler:function(form){
            $(form).ajaxSubmit({
                url:host_url+'Home/Member/register',
                type:"post",
                dataType:"json",
                success:function(data){
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

    //电话号码验证
    jQuery.validator.addMethod("tel", function(value, element, param) {
        var patt=/([\d]{11})|(\d{3,4}-\d{7,8})/;
        return patt.test(value);
    }, $.validator.format("请输入正确的电话"));
});