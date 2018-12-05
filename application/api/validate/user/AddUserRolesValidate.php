<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/5
 * Time: 13:11
 */

namespace app\api\validate\user;


use app\api\validate\BaseValidate;

class AddUserRolesValidate extends BaseValidate
{
    protected $rule = [
        'id'        => 'require|isPositiveInteger',
        'user_role' => 'require|isArr'
    ];
}