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
// 用户登录注册
Route::group('api/:version/', function (){
    Route::post('/signIn',            'api/:version.User/signIn');
    Route::post('/signUp',            'api/:version.User/signUp');
    Route::post('/userNameIsExist',   'api/:version.User/userNameIsExist');
    Route::post('/userMobileIsExist', 'api/:version.User/userMobileIsExist');
    Route::post('/userEmailIsExist',  'api/:version.User/userEmailIsExist');
});

// 用户
Route::group('api/:version/user/', function (){
    Route::post('/index',          'api/:version.User/index');
    Route::post('/editStatus',     'api/:version.User/editStatus');
    Route::post('/allRoles',       'api/:version.User/allRoles');
    Route::post('/addUserRoles',   'api/:version.User/addUserRoles');
    Route::post('/deleteUserRole', 'api/:version.User/deleteUserRole');
});

// 管理员登录注册
// /api/v1/admin/signIn
Route::group('api/:version/admin/', function (){
    Route::post('/signIn',            'api/:version.Admin/signIn');
    Route::post('/signUp',            'api/:version.Admin/signUp');
    Route::post('/userNameIsExist',   'api/:version.Admin/userNameIsExist');
    Route::post('/userMobileIsExist', 'api/:version.Admin/userMobileIsExist');
    Route::post('/userEmailIsExist',  'api/:version.Admin/userEmailIsExist');
});

// 管理员
Route::group('api/:version/admin/', function (){
    Route::post('/index',          'api/:version.User/index');
    Route::post('/editStatus',     'api/:version.User/editStatus');
    Route::post('/allRoles',       'api/:version.User/allRoles');
    Route::post('/addUserRoles',   'api/:version.User/addUserRoles');
    Route::post('/deleteUserRole', 'api/:version.User/deleteUserRole');
});


// 用户详情
Route::group('api/:version/user', function (){
    Route::post('/detail',        'api/:version.UserDetail/detail');
    Route::post('/updateDetail',  'api/:version.UserDetail/updateDetail');
    Route::post('/editDetail',    'api/:version.UserDetail/editDetail');
    Route::post('/editStatus',    'api/:version.UserDetail/editDetail');
});


// 角色 /api/v1/role/getAll
Route::group('api/:version/role/', function (){
    Route::post('/index',          'api/:version.Role/index');
    Route::post('/roleNameExist',  'api/:version.Role/roleNameExist');
    Route::post('/create',         'api/:version.Role/create');
    Route::post('/show',           'api/:version.Role/show');
    Route::post('/edit',           'api/:version.Role/edit');
    Route::post('/editStatus',     'api/:version.Role/editStatus');
    Route::post('/delete',         'api/:version.Role/delete');
    Route::post('/addRoleAuth',    'api/:version.Role/addRoleAuth');
    Route::post('/deleteRoleAuth', 'api/:version.Role/deleteRoleAuth');
    Route::post('/getAll',         'api/:version.Role/getAllRolesByWhere');
});

// 权限 /api/v1/auth/allAuths
Route::group('api/:version/auth/', function (){
    Route::post('/index',      'api/:version.Auth/index');
    Route::post('/show',       'api/:version.Auth/show');
    Route::post('/create',     'api/:version.Auth/create');
    Route::post('/routeExist', 'api/:version.Auth/routeExist');
    Route::post('/edit',       'api/:version.Auth/edit');
    Route::post('/editStatus', 'api/:version.Auth/editStatus');
    Route::post('/delete',     'api/:version.Auth/delete');
    Route::post('/authWhere',  'api/:version.Auth/authWhere');
});

//token
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//加解密
//Route::post('api/:version/crypt/encrypt', 'api/:version.Cryptic/encrypt');
//Route::post('api/:version/crypt/decrypt', 'api/:version.Cryptic/decrypt');


// 文件上传 /api/v1/file/dataToExcel
Route::group('api/:version/file/', function (){
    Route::post('/uploadImage',   'api/:version.FileUpload/uploadImage');
    Route::post('/uploadExcel',   'api/:version.FileUpload/uploadExcel');
    Route::post('/excelToDb', 'api/:version.FileUpload/excelToDb');
    Route::post('/dataToExcel', 'api/:version.FileUpload/dataToExcel');
});

// excvel
Route::group('api/:version/excel/', function (){
    Route::post('/excel',     'api/:version.PhpExcel/excel');

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