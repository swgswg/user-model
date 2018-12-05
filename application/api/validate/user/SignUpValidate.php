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
        'user_mobile' => 'require|isMobile',
        'code'        => 'require|number|length:6',
        'user_name'   => 'require|alphaNum|length:4,20',
        'user_pass'   => 'require|isNotEmpty|length:6,25',
        'repassword'  => 'require|isNotEmpty|confirm:password',
        'user_photo'  => 'checkImageType',
    ];

    protected $message=[
        'user_mobile.require'  => '手机号码必须传',
        'user_mobile.isMobile' => '手机号码格式不正确',
        'code.require'         => '验证码必须传',
        'code.number'          => '验证码必须是数字',
        'code.length'          => '验证码必须是6位数',
        'user_name.require'    => '用户名必须传',
        'user_name.length'     => '用户名长度在4-20之间',
        'user_pass.require'    => '密码必须传',
        'user_pass.length'     => '密码长度在6-25之间',
        'repassword.confirm'   => '两次密码要一致',
    ];

}