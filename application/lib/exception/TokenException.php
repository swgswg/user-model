<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 16:59
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = 200;
    public $message = 'Token已经过期或无效Token';
    public $errorCode = 10001;
}