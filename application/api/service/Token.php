<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 16:33
 */

namespace app\api\service;


class Token
{
    public static function generateToken()
    {
        // 32个字符组成一组随机字符串
        $randChars = getRandChars(32);
        // 用三组字符串进行MD5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        // salt 盐
        $salt = config('secure.token_salt');
        return md5($randChars . $timestamp . $salt);
    }
}