<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/2
 * Time: 20:18
 */

namespace app\api\behavior;


class CORS
{

    /**
     * 跨域
     */
    public function appInit()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET');
        if(request()->isOptions()){
            exit();
        }
    }
}