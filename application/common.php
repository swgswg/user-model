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

/**
 *  curl get请求
 * @param $url  get请求地址
 * @param int $httpCode  返回状态码
 * @return mixed
 */
function curl_get($url, &$httpCode = 0)
{
    //创建一个新的CURL资源赋给变量$ch;
    $ch = curl_init();
    //设置URL 及其他选项
    curl_setopt($ch, CURLOPT_URL, $url);
    //设置获取的内容但不输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    //设置输出的头信息
    // curl_setopt($ch, CURLOPT_HEADER, 0);

    // 不做证书校验, 部署在linux环境下改为true
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //执行 获取url内容并输出到浏览器
    $output = curl_exec($ch);
    //释放资源
    curl_close($ch);


    //返回获取的网页内容
    return $output;
}

function curl_post($url, $data)
{
    //创建一个新的CURL资源赋给变量$ch
    $ch = curl_init();

    //设置要访问的url地址
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置获取的内容但不输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // 发送一个post的请求
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // post提交的数据包
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

//    $data_string = json_encode(array $data);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//    curl_setopt(
//        $ch, CURLOPT_HTTPHEADER,
//        array(
//            'Content-Type: application/json'
//        )
//    );

    //执行操作
    $output = curl_exec($ch);
    //关闭curl
    curl_close($ch);
    //返回数据
    return $output;
}


function curl_post_raw($url, $rawData)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rawData);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER,
        array(
            'Content-Type: text'
        )
    );
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}



/**
 *  随机字符串
 * @param $length 字符串长度
 * @return null|string
 */
function getRandChars($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}