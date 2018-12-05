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
}