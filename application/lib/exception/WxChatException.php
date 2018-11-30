<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/30
 * Time: 14:47
 */

namespace app\lib\exception;


class WxChatException extends BaseException
{
    public $code = 400;
    public $message = '微信服务器接口调用失败';
    public $errorCode = 999;
}