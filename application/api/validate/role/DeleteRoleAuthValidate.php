<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 19:10
 */

namespace app\api\validate\role;


use app\api\validate\BaseValidate;

class DeleteRoleAuthValidate extends BaseValidate
{
    protected $rule = [
        'id'        => 'require|isPositiveInteger',
        'role_auth' => 'isArr'
    ];

    protected $message=[
        'id.require' => 'ID不能缺少',
        'role_auth'  => '角色权限必须是数组'
    ];
}