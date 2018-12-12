<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/8
 * Time: 23:12
 */

namespace app\api\validate\role;


use app\api\validate\BaseValidate;

class RoleNameExistValidate extends BaseValidate
{
    protected $rule = [
        'role_name'   => 'require|chsDash|length:1,50',
    ];

    protected $message=[
        'role_name.require'   => '角色名称不能缺少',
        'role_name.chsDash'   => '角色名称只能是汉字、字母、数字和下划线_及破折号-',
    ];
}