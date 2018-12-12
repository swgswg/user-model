<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 11:17
 */

namespace app\api\validate\auth;


use app\api\validate\BaseValidate;

class EditAuthValidate extends BaseValidate
{
    protected $rule = [
        'auth_route'         => 'routeReg|length:1,50',
        'auth_route_version' => 'alphaDash|length:1,10',
        'auth_name'          => 'chsDash|length:1,50',
        'auth_desc'          => 'chsDash|length:0,255',
        'auth_order'         => 'isPositiveInteger',
        'auth_status'        => 'in:1,2',
    ];

    protected $message=[
        'auth_route.length'            => '权限路由长度1-50',
        'auth_route_version.alphaDash' => '权限字母和数字,下划线_及破折号-组成',
        'auth_route_version.length'    => '权限版本长度1-50',
        'auth_name.chsDash'            => '权限名称汉字,字母,数字,下划线_及破折号-组成',
        'auth_name.length'             => '权限名称长度1-50',
        'auth_desc.chsDash'            => '权限描述汉字,字母,数字,下划线_及破折号-组成',
        'auth_desc.length'             => '权限描述长度0-255',
        'auth_status.in'               => '权限状态格式不正确,在1,2之间选择',
    ];
}