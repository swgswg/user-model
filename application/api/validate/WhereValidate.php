<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 9:47
 */

namespace app\api\validate;


class WhereValidate extends BaseValidate
{
    protected $rule = [
        'where' => 'isArr',
    ];
}