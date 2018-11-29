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
    public static function out($data, $message, $flag = false)
    {
        $msg = $message . '成功';
        if($flag){
            $msg = $message;
        }
        $result = [
            'message' => $msg,
            'state'=> 1,
            'data'=>$data,
            'error_code' => 'request:ok',
        ];
        return json($result);
    }
}