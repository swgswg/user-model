<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 16:53
 */

namespace app\lib\exception;


/**
 *  404时抛出此异常
 * Class MissException
 * @package app\lib\exception
 */
class MissException extends BaseException
{
    public $code = 404;
    public $message = '请求错误';
    public $errorCode = 10001;
}