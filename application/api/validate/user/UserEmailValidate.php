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
        'user_mobile' => 'require|isNotEmpty|email',
    ];

    protected $message=[
        'user_name.require' => '没有邮箱',
        'user_name.email' => '邮箱格式错误',
    ];
}