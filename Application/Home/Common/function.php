<?php
//转义字符转换
function html_decode($arr=array(),$field=''){
    for($i=0;$i<count($arr);$i++){
        $arr[$i][$field]=htmlspecialchars_decode($arr[$i][$field]);
    }
    return $arr;
}

//字符截取
function substr_mb($str='',$start=0,$len=10,$encoding='utf-8'){
    if(mb_strlen($str,'utf-8') > $len){
        return mb_substr($str,$start,$len,$encoding).'...';
    }else{
        return $str;
    }
}

//通过curl请求
function curlRequest($url, $postfields=array()){
    $curl_version=curl_version();	//返回curl版本的数组
    if (((is_string($curl_version)) && (strpos($curl_version,'OpenSSL')!==false)) ||
        ((is_array($curl_version)) && (array_key_exists('ssl_version',$curl_version)))) {
        $url = $url;
    }else{	//若不支持ssl，使用http协议
        $url = str_replace('https','http',$url);
    }
    if(is_array($postfields))
    {
        $postfields = http_build_query($postfields);	//拼接成url参数
    }
    //通过curl来进行post请求
    $ch = curl_init();
    //curl_setopt() 以数组的形式为一个curl设置会话参数
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 跳过主机检查
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array());	//设置 HTTP 头字段
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);	//获取前一个页面的url地址
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);	//获取访问者的操作系统和浏览器信息
    curl_setopt($ch, CURLOPT_POST, true);	//设置请求为post
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	//如果CURLOPT_RETURNTRANSFER选项被设置，函数执行成功时会返回执行的结果，失败时返回 FALSE
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);	//设置请求超时时间
    $response = curl_exec($ch);	//执行一个cURL会话，返回true|false
    if($error=curl_error($ch)){	//返回最近一次cURL会话错误的字符串
        curl_close($ch);	//关闭cURL会话
        $url = str_replace('https','http',$url);
        return $this->curl_request($url,$postfields);
    }
    curl_close($ch);
    return $response;
}

function returnWeatherArr($weather=array()){
    $arr=array();
    if(!empty($weather)){
        $arr['city']=$weather['results'][0]['currentCity'];
        $arr['pm25']=$weather['results'][0]['pm25'];
        $arr['dateT']=$weather['results'][0]['weather_data'][0]['date']; //日期和实时温度
        $arr['weather']=$weather['results'][0]['weather_data'][0]['weather'];
        $arr['wind']=$weather['results'][0]['weather_data'][0]['wind'];
        $arr['temperature']=$weather['results'][0]['weather_data'][0]['temperature'];
        $h=date('G',time());
        if($h<11 || $h==23){ //白天
            $arr['dayPicUrl']=$weather['results'][0]['weather_data'][0]['dayPictureUrl'];
        }else{  //黑夜
            $arr['dayPicUrl']=$weather['results'][0]['weather_data'][0]['nightPictureUrl'];
        }
        $arr['clothIndex']=$weather['results'][0]['index'][0]['zs'];
        $arr['washCarIndex']=$weather['results'][0]['index'][1]['zs'];
        $arr['sportIndex']=$weather['results'][0]['index'][3]['zs'];
        $arr['raysIndex']=$weather['results'][0]['index'][4]['zs'];
    }
    return $arr;
}

// 获取客户端的ip
function getIPaddress(){
    $IPaddress = '';
    if (isset ( $_SERVER ))
    {
        if (isset ( $_SERVER ["HTTP_X_FORWARDED_FOR"] ))
        {
            $IPaddress = $_SERVER ["HTTP_X_FORWARDED_FOR"];
        }
        else if (isset ( $_SERVER ["HTTP_CLIENT_IP"] ))
        {
            $IPaddress = $_SERVER ["HTTP_CLIENT_IP"];
        }
        else
        {
            $IPaddress = $_SERVER ["REMOTE_ADDR"];
        }
    }
    else
    {
        if (getenv ( "HTTP_X_FORWARDED_FOR" ))
        {
            $IPaddress = getenv ( "HTTP_X_FORWARDED_FOR" );
        }
        else if (getenv ( "HTTP_CLIENT_IP" ))
        {
            $IPaddress = getenv ( "HTTP_CLIENT_IP" );
        }
        else
        {
            $IPaddress = getenv ( "REMOTE_ADDR" );
        }
    }
    $ips = explode ( ",", $IPaddress );
    return $ips [0];
}