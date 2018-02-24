<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function httpCurl($url, $type = 'get', $res = '', $arr = '')
{
    //初始化curl
    $ch = curl_init();
    //设置参数
    curl_setopt($ch, CURLOPT_URL, $url);            //url
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    //设置返回采集到的数据
    if ($type == 'post') {
        curl_setopt($ch, CURLOPT_POST, 1);          //定义POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
    }
    //采集
    $output = curl_exec($ch);
    //关闭
    if (curl_errno($ch)) return curl_errno($ch);
    curl_close($ch);
    if ($res == 'json') {
        return json_decode($output, true);
    }
    return $output;
}