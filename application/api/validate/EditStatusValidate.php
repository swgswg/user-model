<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/5
 * Time: 10:37
 */

namespace app\api\validate;


class EditStatusValidate extends BaseValidate
{
    protected $rule = [
        'id'=>'require|isPositiveInteger',
        'status' => 'require|in:1,2'
    ];

    protected $message=[
        'id.require'=> 'ID不能缺少',
        'auth_status.require'=> '状态不能缺少',
        'auth_status.in' => '权限状态格式不正确,在1,2之间选择',
    ];
}