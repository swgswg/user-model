<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/2
 * Time: 21:48
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class SignUpValidate extends BaseValidate
{
    protected $rule = [
        'mobile' => 'require|isMobile',
        'email'  => 'email',
        'code'        => 'require|number|length:6',
        'nickname'   => 'require|alphaNum|length:4,20',
        'password'   => 'require|isNotEmpty|length:6,25',
        'repassword'  => 'require|isNotEmpty|confirm:password',
        'photo'  => 'checkImageType',
    ];

    protected $message=[
        'mobile.require'  => '手机号码必须传',
        'mobile.isMobile' => '手机号码格式不正确',
        'code.require'         => '验证码必须传',
        'code.number'          => '验证码必须是数字',
        'code.length'          => '验证码必须是6位数',
        'nickname.require'    => '用户名必须传',
        'nickname.length'     => '用户名长度在4-20之间',
        'password.require'    => '密码必须传',
        'password.length'     => '密码长度在6-25之间',
        'repassword.confirm'   => '两次密码要一致',
    ];

}