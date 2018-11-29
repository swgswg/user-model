<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 *  :version 是版本号
 *  api/v1/signIn 表示vi版本的接口
 */

Route::post('api/:version/signIn', 'api/:version.User/signIn');
Route::post('api/:version/signUp', 'api/:version.User/signUp');