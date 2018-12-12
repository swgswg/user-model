<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/9
 * Time: 12:29
 */

namespace app\api\validate\auth;


use app\api\validate\BaseValidate;

class AuthWhereValidate extends BaseValidate
{
    protected $rule = [
        'where' => 'isArr',
    ];
}