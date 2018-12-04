<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 22:53
 */

namespace app\lib\exception;


class RoleException extends BaseException
{
    public $code = 404;
    public $message = '角色不存在';
    public $errorCode = 30000;
}