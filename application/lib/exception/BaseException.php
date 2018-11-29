<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 16:48
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    // HTTP 状态码 404,200
    public $code = 400;

    // 错误具体信息
    public $message = '参数错误';

    // 请求转态(请求成功1/请求失败0)
    public $state = 0;

    // 自定义错误码
    public $errorCode = 10000;

    public function __construct($params = [])
    {
        if(!is_array($params)){
            return ;
            // throw new Exception('参数必须是数组');
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('message',$params)){
            $this->message = $params['message'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->errorCode = $params['errorCode'];
        }
    }
}