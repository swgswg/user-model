<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 16:19
 */

namespace app\lib\exception;


class IllegalOperation extends BaseException
{
    public $code = 401;
    public $message = '非法操作';
    public $errorCode = 10003;
}