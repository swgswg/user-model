<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/1
 * Time: 12:43
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $message = '权限不够';
    public $errorCode = 10002;
}