<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 15:10
 */

namespace app\api\validate\user;

use app\api\validate\BaseValidate;

class AddUserDetailValidate extends BaseValidate
{
    protected $rule = [
        'id'         => 'require|isPositiveInteger',
        'user_money' => 'money',
        'user_point' => 'between:0,65535',
        'user_grade' => 'between:0,255',
        'birthday'   => 'dateFormat:Y-m-d',
        'sex'        => 'in:0,1,2',
        'real_name'  => 'chs',
        'id_card'    => 'idCard',
    ];

    protected $message=[
        'user_point.between'  => '用户积分在0-65535之间',
        'user_grade.between'  => '用户等级在0-255之间',
        'birthday.dateFormat' => '用户生日格式不正确,正确格式为2018-12-03',
        'sex.in'              => '用户性别格式不正确,在0,1,2之间选择',
        'real_name.chs'       => '用户真实姓名为中文',
        'id_card.idCard'      => '身份证格式不正确',
    ];
}