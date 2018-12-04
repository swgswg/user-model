<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 22:07
 */

namespace app\api\validate\role;

use app\api\validate\BaseValidate;

class editRoleValidate extends BaseValidate
{
    protected $rule = [
        'id'          => 'require|isPositiveInteger',
        'role_name'   => 'chsDash|length:1,50',
        'role_desc'   => 'chsDash|length:0,255',
        'role_group'  => 'isPositiveInteger',
        'role_order'  => 'isPositiveInteger',
        'role_status' => 'in:1,2',
    ];

    protected $message=[
        'role_name.chsDash' => '角色名称只能是汉字、字母、数字和下划线_及破折号-',
        'user_grade.length' => '角色名长度是1-50',
        'role_desc.chsDash' => '描述只能是汉字、字母、数字和下划线_及破折号-',
        'role_desc.length'  => '描述长度是0-255',
        'role_status.in'    => '角色状态只能选择1或者2',
    ];
}