<?php
return array(
	//'配置项'=>'配置值'
    //路由配置
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=> array(
        //''  =>  '',
        '/^login$/'  =>   array('Home/Member/login'),
        '/^register/'  =>   array('Home/Member/register'),
        '/^forgetPasswd/'  =>   array('Home/Member/forgetPasswd'),
        '/^member_(\d{1,})/'  =>   array('Home/Member/index?member_id=:1'),
        '/^info\/member_(\d{1,})/'  =>   array('Home/Member/info?member_id=:1'),
        '/^article_(\d{1,})/'  =>  array('Home/Article/index?article_id=:1'),
        '/^hotList\/member_(\d{1,})/'  =>  array('Home/Article/hotArticleList?member_id=:1'),
        '/^list\/(article_type_(\d{1,})|member_(\d{1,}))/'  =>  array('Home/Article/articleList?article_type_id=:2&member_id=:3'),
        '/^photo\/(photo_(\d{1,})|member_(\d{1,}))/'  =>  array('Home/Photo/index?photo_id=:2&member_id=:3'),
        '/^mess\/member_(\d{1,})/'  =>  array('Home/Mess/index?member_id=:1'),
    ),
);