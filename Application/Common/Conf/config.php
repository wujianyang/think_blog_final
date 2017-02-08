<?php
return array(
	//'配置项'=>'配置值'

    'URL_MODEL'=>2,
    //数据库配置
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'think_blog',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口

    //系统配置
    'ROOT'=>$_SERVER['DOCUMENT_ROOT'],
    'HOST'=>$_SERVER['HTTP_HOST'],
    'HOST_DIR'=>'/think_blog/',
    'JS'=>'/think_blog/Public/js/',
    'CSS'=>'/think_blog/Public/css/',
    'IMAGES'=>'/think_blog/Public/images/',
    'PLUGINS'=>'/think_blog/Public/plugins/',
    'FUNC'=>'/think_blog/Public/func/',
    'UPLOAD'=>'/think_blog/Upload/',
    'UPLOAD_PATH'=>'/think_blog/Upload/',
    'DEFAULT_HEAD_PIC'=>'10000000',
    'HEAD_PIC_DEFAULT'=>'head_pic/head_pic_default.png',
    'USER_PASSWORD_DEFAULT'=>md5('blog123456'),
    'ADMIN_PASSWORD_DEFAULT'=>md5('admin123456'),
    'NODATA'    =>  '<p class="noData">没有数据</p>',
    'TITLE'     =>  '博客系统',

    'SHOW_PAGE_TRACE' => true,
    'URL_HTML_SUFFIX' => 'html',
    'URL_CASE_INSENSITIVE'=>false,

    //日志配置
    'LOG_RECORD'    =>  true,
    'LOG_TYPE'      =>  'File',
    'LOG_LEVEL'     =>  'EMERG,ALERT,CRIT,ERR,WARN,NOTICE,INFO,DEBUG,SQL',

    //获取IP地址接口
    'GET_IP_URL'    =>  "http://jsonip.com/?&callback=",
    //ip定位接口
    'REMOTE_IP_URL' =>  "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json",
    //天气接口
    "WEATHER_URL"   =>  "http://api.map.baidu.com/telematics/v3/weather",
    "AK"            =>  "EqrP5ARI0cGoGqXWrPQCweiM",
);