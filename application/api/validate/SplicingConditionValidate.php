<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/4
 * Time: 22:48
 */

namespace app\api\validate;


class SplicingConditionValidate extends BaseValidate
{
    protected $rule = [
        'page' => 'isPositiveInteger',
        'pageSize' => 'isPositiveInteger',
        'where' => 'isArr',
        'order' => 'isArr',
    ];
}