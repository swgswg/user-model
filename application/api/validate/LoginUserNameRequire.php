<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 17:30
 */

namespace app\api\validate;


class LoginUserNameRequire extends BaseValidate
{
    protected $rule = [
        'user_name' => 'require|isNotEmpty|length:4,25',
        'user_pass' => 'require|isNotEmpty'
    ];

    protected $message=[
        'user_name' => '没有用户名你还想登录!!!',
        'user_pass' => '没有密码你还想登录!!!'
    ];
}