<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/3
 * Time: 17:16
 */

namespace app\api\validate;


class PageValidate extends BaseValidate
{
    protected $rule = [
        'page'     => 'isPositiveInteger',
        'pageSize' => 'isPositiveInteger'
    ];

    protected $message = [
        'page'     => '分页参数必须是正整数',
        'pageSize' => '分页参数必须是正整数'
    ];
}