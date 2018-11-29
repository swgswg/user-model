<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 16:53
 */

namespace app\lib\exception;


class TestMissException extends BaseException
{
    public $code = 404;
    public $message = '请求Test错误';
    public $errorCode = 40000;


}