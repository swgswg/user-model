<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 0:33
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class UserEmailValidate extends BaseValidate
{
    protected $rule = [
        'email' => 'require|isNotEmpty|email',
    ];

    protected $message=[
        'email.require' => '没有邮箱',
        'email.email' => '邮箱格式错误',
    ];
}