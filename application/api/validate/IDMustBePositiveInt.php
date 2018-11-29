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

//    protected function isPositiveInteger($value, $rule = '', $data=[], $field)
//    {
//         if(is_numeric($value) && is_int($value+0) && ($value > 0)) {
//             return true;
//         } else {
//             return $field.'必须是正整数';
//         }
//    }
}