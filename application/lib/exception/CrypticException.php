<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/6
 * Time: 10:17
 */

namespace app\lib\exception;


class CrypticException extends BaseException
{
//    public $code = 400;
    public $message = '加解密错误';
    public $errorCode = 10006;
}