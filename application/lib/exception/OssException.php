<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 17:10
 */

namespace app\lib\exception;


class OssException extends BaseException
{
    public $code = 400;
    public $message = '阿里云OSS接口调用错误';
    public $errorCode = 999;
}