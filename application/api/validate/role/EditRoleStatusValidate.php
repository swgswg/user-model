<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 13:01
 */

namespace app\api\validate\role;


use app\api\validate\BaseValidate;

class EditRoleStatusValidate extends BaseValidate
{
    protected $rule = [
        'id'=>'require|isPositiveInteger',
        'auth_status' => 'require|in:1,2',
    ];

    protected $message=[
        'id.require'=> 'ID不能缺少',
        'auth_status.require'=> '状态不能缺少',
        'auth_status.in' => '权限状态格式不正确,在1,2之间选择',
    ];
}