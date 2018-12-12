<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/10
 * Time: 20:14
 */

namespace app\api\validate;


class ImageValidate extends BaseValidate
{
    protected $rule = [
        'size'     => 'require|elt:5242880',       // 5M = 5*1024*1024
        'ext'      => 'require|in:jpg,jpeg,png',   // excel后缀
        'tmp_name' => 'require|isUploadedFile',    // 是否是有效的文件
    ];

    protected $message = [
        'size.egt' => '文件不能大于5M',
        'ext.in'   => '图片支持jpg,jpeg,png格式'
    ];
}