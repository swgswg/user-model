<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/28
 * Time: 23:58
 */

namespace app\api\validate;


class SignUpDataValidate extends BaseValidate
{
    protected $rule = [
        'user_mobile' => 'require|isMobile',
        'code'        =>'require|number|length:6',
        'user_name'   => 'require|length:4,25',
        'user_pass'   => 'require|length:6,25',
    ];

    protected $message=[
        'user_mobile.require'  => '手机号码必须传',
        'user_mobile.isMobile' => '手机号码格式不正确',
        'code.require'         => '验证码必须传',
        'code.number'          => '验证码必须是数字',
        'code.length'          => '验证码必须是6位数',
        'user_name.require'    => '用户名必须传',
        'user_name.length'     => '用户名长度在4-25之间',
        'user_pass.require'    => '密码必须传',
        'user_pass.length'     => '密码长度在6-25之间',
    ];
}