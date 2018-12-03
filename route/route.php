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
 *  v1/v1/signIn 表示vi版本的接口
 */

// 用户
Route::group('api/:version/', function (){
    Route::post('/signIn', 'api/:version.User/signIn');
    Route::post('/signUp', 'api/:version.User/signUp');
    Route::post('/userNameIsExist', 'api/:version.User/userNameIsExist');
    Route::post('/userMobileIsExist', 'api/:version.User/userMobileIsExist');
    Route::post('/userEmailIsExist', 'api/:version.User/userEmailIsExist');
    Route::post('/getAllUsers', 'api/:version.User/getAllUsers');
});


// 用户详情
Route::group('api/:version/', function (){
    Route::post('/oneUserDetail', 'api/:version.UserDetail/oneUserDetail');
    Route::post('/updateUserDetail', 'api/:version.UserDetail/updateUserDetail');
    Route::post('/editUserDetail', 'api/:version.UserDetail/editUserDetail');
});


// 角色
Route::group('api/:version/', function (){

});

// 权限
Route::group('api/:version/', function (){

});

// 获取微信小程序openid
Route::post('api/:version/miniGetToken', 'api/:version.Token/miniGetToken');

//Pay
Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');  // 断点调试用
Route::post('api/:version/pay/concurrency', 'api/:version.Pay/notifyConcurrency');


//Miss 404
//Miss 路由开启后，默认的普通模式也将无法访问
Route::miss('api/v1.Miss/miss');