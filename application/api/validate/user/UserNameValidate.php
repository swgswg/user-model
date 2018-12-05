<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/2
 * Time: 23:35
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class UserNameValidate extends BaseValidate
{
    protected $rule = [
        'user_name' => 'require|isNotEmpty|alphaDash|length:4,20',
    ];

    protected $message=[
        'user_name.require' => '没有用户名',
        'user_name.alphaDash' => '用户名格式为字母数字下划线',
        'user_name.length' => '用户名长度在4-20之间',
    ];
}