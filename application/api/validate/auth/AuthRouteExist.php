<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/8
 * Time: 17:18
 */

namespace app\api\validate\auth;


use app\api\validate\BaseValidate;

class AuthRouteExist extends BaseValidate
{
    protected $rule = [
        'auth_route' => 'require|routeReg|length:1,50',
    ];

    protected $message=[
        'auth_route.require'   => '权限路由必须有',
        'auth_route.length'    => '权限路由长度1-50',
    ];
}