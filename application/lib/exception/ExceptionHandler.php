<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 16:45
 */

namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\facade\Request;
use think\facade\Log;

class ExceptionHandler extends Handle
{
    private $code;
    private $message;
    private $state;
    private $errorCode;
    // 还需要返回客户端当前请求的URL路径

    public function render(Exception $e)
    {
        if($e instanceof BaseException){
            // 如果是自定义异常
            $this->code = $e->code;
            $this->message = $e->message;
            $this->state = $e->state;
            $this->errorCode = $e->errorCode;
        } else {
            if(config('app_debug')){
                return parent::render($e);
            } else {
                $this->code = 500;
                $this->message = '服务器内部错误';
                $this->errorCode = 999;
                $this->recordErrorLog($e);
            }
        }
        $url = Request::url(true);
        $result = [
            'message' => $this->message,
            'state'=>$this->state,
            'error_code' => $this->errorCode,
            'request_url' => $url
        ];
        return json($result,$this->code);
    }


    /**
     *  记录日志
     * @param Exception $e
     */
    private function recordErrorLog(Exception $e){
        Log::init([
            'type'=>'File',
            'level'=>['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}