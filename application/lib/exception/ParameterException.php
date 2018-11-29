<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 20:06
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $message = '参数错误';
    public $errorCode = 10000;
}