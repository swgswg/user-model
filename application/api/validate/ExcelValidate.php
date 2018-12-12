<?php
/**
 * Created by PhpStorm.
 * User: song
 * Date: 2018/12/11
 * Time: 13:06
 */

namespace app\api\validate;


class ExcelValidate extends BaseValidate
{
    // 'name' => string '005695109356f96.jpg' (length=19)
    // 'type' => string 'image/jpeg' (length=10)
    // 'tmp_name' => string 'C:\Windows\php5AA9.tmp' (length=22)
    // 'error' => int 0
    // 'size' => int 31441

    protected $rule = [
        'size'     => 'require|elt:5242880',       // 5M = 5*1024*1024
        'ext'      => 'require|in:xls,xlsx',       // excel后缀
        'tmp_name' => 'require|isUploadedFile',  // 是否是有效的文件
    ];

    protected $message = [
        'size.egt' => '文件不能大于5M',
        'ext.in'   => '表格只支持xls/xlsx格式'
    ];
}