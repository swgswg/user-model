<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/11/27
 * Time: 13:21
 */

namespace app\api\validate;

class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
        'id'=>'require|isPositiveInteger',
    ];
}