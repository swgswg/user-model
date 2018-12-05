<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 0:31
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class UserMobileValidate extends BaseValidate
{
    protected $rule = [
        'user_mobile' => 'require|isNotEmpty|isMobile',
    ];

    protected $message=[
        'user_name.require' => '没有手机号',
    ];
}