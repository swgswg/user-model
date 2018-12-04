<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 11:53
 */

namespace app\api\validate\role;


use app\api\validate\BaseValidate;

class CreateRoleValidate extends BaseValidate
{
    protected $rule = [
        'role_name'   => 'require|chsDash|length:1,50',
        'role_desc'   => 'chsDash|length:0,255',
        'role_group' => 'require|isPositiveInteger',
        'role_order'  => 'isPositiveInteger',
        'role_status' => 'in:1,2',
        'role_auth'   => 'isArr'
    ];

    protected $message=[
        'role_name.require'   => '角色名称不能缺少',
        'role_name.chsDash'   => '角色名称只能是汉字、字母、数字和下划线_及破折号-',
        'role_desc.chsDash'   => '描述只能是汉字、字母、数字和下划线_及破折号-',
        'role_desc.length'    => '描述长度是0-255',
        'role_group.require' => '角色组必须',
        'role_status.in'      => '角色状态只能选择1或者2',
    ];
}