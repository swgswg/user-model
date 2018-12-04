<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 9:53
 */

namespace app\lib\exception;


class AuthException extends BaseException
{
    public $code = 404;
    public $message = '指定auth不存在, 请检查authID';
    public $errorCode = 40000;
}