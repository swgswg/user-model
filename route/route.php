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
 *  v1/signIn 表示v1版本的接口
 */

Route::group('api/:version/', function (){
    Route::post('/signIn', 'api/:version.User/signIn');
    Route::post('/signUp', 'api/:version.User/signUp');
    Route::post('/userNameIsExist', 'api/:version.User/userNameIsExist');
    Route::post('/userMobileIsExist', 'api/:version.User/userMobileIsExist');
    Route::post('/userEmailIsExist', 'api/:version.User/userEmailIsExist');
});


// 用户
Route::group('api/:version/user/', function (){
    Route::post('/index', 'api/:version.User/getAllUsers');
    Route::post('/editStatus', 'api/:version.User/editStatus');
    Route::post('/allRoles', 'api/:version.User/allRoles');
    Route::post('/addUserRoles', 'api/:version.User/addUserRoles');
    Route::post('/deleteUserRole', 'api/:version.User/deleteUserRole');
});


// 用户详情
Route::group('api/:version/', function (){
    Route::post('/oneUserDetail', 'api/:version.UserDetail/oneUserDetail');
    Route::post('/updateUserDetail', 'api/:version.UserDetail/updateUserDetail');
    Route::post('/editUserDetail', 'api/:version.UserDetail/editUserDetail');
});


// 角色
Route::group('api/:version/role/', function (){
    Route::post('/index', 'api/:version.Role/index');
    Route::post('/create', 'api/:version.Role/create');
    Route::post('/show', 'api/:version.Role/show');
    Route::post('/edit', 'api/:version.Role/edit');
    Route::post('/editStatus', 'api/:version.Role/editStatus');
    Route::post('/delete', 'api/:version.Role/delete');
});

// 权限
Route::group('api/:version/auth/', function (){
    Route::post('/index', 'api/:version.Auth/index');
    Route::post('/show', 'api/:version.Auth/show');
    Route::post('/create', 'api/:version.Auth/create');
    Route::post('/edit', 'api/:version.Auth/edit');
    Route::post('/editStatus', 'api/:version.Auth/editStatus');
    Route::post('/delete', 'api/:version.Auth/delete');
});

//token
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

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