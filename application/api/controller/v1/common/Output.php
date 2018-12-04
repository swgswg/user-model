<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 23:49
 */

namespace app\api\controller\v1\common;


use think\Controller;

class Output extends Controller
{
    public static function out($message, $data = '', $flag = false)
    {
        $msg = $message . '成功';
        if($flag){
            $msg = $message;
        }
        if($data !== '' ){
            $result = [
                'message' => $msg,
                'state'=> 1,
                'data'=>$data,
                'error_code' => 'request:ok',
            ];
        } else {
            $result = [
                'message' => $msg,
                'state'=> 1,
                'error_code' => 'request:ok',
            ];
        }
        return json($result);
    }
}