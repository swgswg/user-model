<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 16:42
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $message = '用户不存在';
    public $errorCode = 20000;
}